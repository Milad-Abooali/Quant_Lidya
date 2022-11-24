<?php

/**
 * App Functions Class
 */

include_once '../app/config-over.php';

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Check Session
function checkSession() {
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sess']) ) $output->e = 'sess expected';
    if( !isset($_REQUEST['id']) ) $output->e = 'id expected';
    if(!$output->e) {
        if($_REQUEST['sess'] === session_id()) {
            if($_REQUEST['id'] === $_SESSION['id'] || $_REQUEST['id']==0){
                $output->res=true;
            } else {
                $output->e = 'Your session needs to sync with the server!!';
            }
        } else {
            $output->e = 'Your session needs to update!';
        }
    }
    echo json_encode($output);
}

// Get Session
function getSession() {
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if(!$output->e) {
        eFun::sessionJump($_REQUEST['sessionId']);
        $output->res = $_SESSION;
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM Login
function crmLogin() {
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if( !isset($_REQUEST['username']) ) $output->e = 'username expected';
    if( strlen($_REQUEST['username'])<7 ) $output->e = 'username is short';
    if( !isset($_REQUEST['password']) ) $output->e = 'password expected';
    if( strlen($_REQUEST['password'])<5 ) $output->e = 'password is short';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);
        global $db;
        global $sess;
        $username = $db->escape($_REQUEST['username']);
        $password = $db->escape($_REQUEST['password']);
        if(!$sess->IS_LOGIN) $sess->login(180, $username, $password, true, false);
        if($sess->IS_LOGIN) {
            global $userManager;
            // $output->session = $userManager->get($_SESSION['id']);
            $output->id = $_SESSION['id'];
            $output->role = appSession::checkRole();
            $where = "type='avatar' AND user_id=".$_SESSION['id'];
            $media = $db->selectRow('media',$where);
            if( $media['media'] ) $output->avatar = 'media/'.$media['media'];
        } else {
            $output->e = $sess->ERROR;
        }
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM Logout
function crmLogout() {
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);
        global $sess;
        $sess->logout(false);
        appSession::checkRole();
        $output->res = $_SESSION;
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM Password Recovery
function crmRecovery() {
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if( !isset($_REQUEST['username']) ) $output->e = 'username expected';
    if( strlen($_REQUEST['username'])<7 ) $output->e = 'username is short';
    if( !filter_var($_REQUEST['username'], FILTER_VALIDATE_EMAIL)) $output->e = 'username is not valid email';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);
        global $db;
        $username = $db->escape($_REQUEST['username']);
        $where = "email='$username' AND unit IN (".Broker['units'].")";
        $user = $db->selectRow('users',$where);
        if ($user) {
            $up_token['token'] = bin2hex(random_bytes(50));
            $db->updateId('users', $user['id'], $up_token);

            // Send Email
            global $_Email_M;
            $receivers[] = array (
                'id'    =>  $user['id'],
                'email' =>  $user['email'],
                'data'  =>  array(
                    'token' =>  $up_token['token']
                )
            );
            $subject = $theme = 'CRM_Rest_Password';
            $_Email_M->send($receivers, $theme, $subject);

        } else {
            $output->e = "Sorry, no user exists on our system with that email";
        }
        // Add actLog
        global $actLog; $actLog->add('Recover Pass', ($user['id'] ?? false), boolval($output->e), json_encode(array($_REQUEST)));
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM Register
function crmRegister() {
    $output = new stdClass();
    $output->e = false;
    global $db;
    global $sess;

    // Check All Input
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if( !isset($_REQUEST['fname']) ) $output->e = 'first name expected';
    if( !isset($_REQUEST['lname']) ) $output->e = 'last name expected';
    if( !isset($_REQUEST['email']) ) $output->e = 'email expected';
    if( !filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) $output->e = 'email is not valid';
    if( !isset($_REQUEST['phone']) ) $output->e   = 'phone expected';
    if( strlen($_REQUEST['phone'])<5 ) $output->e = 'phone is short';
    if( !isset($_REQUEST['country']) ) $output->e = 'country expected';
    if( !isset($_REQUEST['source']) ) $output->e  = 'source expected';
    if( !isset($_REQUEST['unit_id']) ) $output->e = 'unit expected';

    // Check for duplicate phone
    $phone_10 = $db->escape(substr($_REQUEST['phone'],-10));
    $where = " RIGHT(phone,10)='$phone_10'";
    if($db->exist('user_extra', $where)) $output->e = "You have an other account in our site with this phone number!";

    // Check for duplicate email
    $where = "email='".$_REQUEST['email']."' AND unit IN (".Broker['units'].")";
    if($db->exist('users',$where)) $output->e = "You have an other account in our site with this email address!";

    // Check if login
    if ($sess->IS_LOGIN) $output->e = 'You need logout first!';

    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        $fname      = GF::charReplace('tr', $_REQUEST['fname']);
        $lname      = GF::charReplace('tr', $_REQUEST['lname']);
        $phone      = $_REQUEST['phone'];
        $country    = $_REQUEST['country'];
        $unit_id    = $_REQUEST['unit_id'];
        $unit_name  = $db->selectId('units', $unit_id,'name')['name'];
        $date       = date('Y-m-d H:i:s');
        $source     = $_REQUEST['source'];
        $campaign   = $_REQUEST['campaign'];
        $affiliate  = $_REQUEST['affiliate'];
        $ip         = GF::getIP();
        $email      = $_REQUEST['email'];
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
        $user_id = $db->insert('users', $insert_user);

        if($user_id) {
            // Insert to users_extra
            $insert_extra['user_id']    = $user_id;
            $insert_extra['ip']         = $ip;
            $insert_extra['fname']      = $fname;
            $insert_extra['lname']      = $lname;
            $insert_extra['phone']      = $phone;
            $insert_extra['country']    = $country;
            $insert_extra['unit']       = $unit_id;
            if(isset($affiliate)){
                $where = "id=$affiliate";
                if($db->exist('staff_list', $where)){
                    $insert_extra['conversion'] = $affiliate;
                }
            }
            $insert_extra['status']     = 1;
            $insert_extra['type']       = 1;
            $insert_extra['followup']   = $date;
            $insert_extra['created_at'] = $date;
            $insert_extra['created_by'] = $user_id;
            $insert_extra['updated_at'] = $date;
            $insert_extra['updated_by'] = $user_id;
            $db->insert('user_extra', $insert_extra);

            // Insert to user_fx
            $insert_fx['user_id']    = $user_id;
            $insert_fx['exp_fx']     = 1;
            $insert_fx['exp_cfd']    = 1;
            $insert_fx['created_at'] = $date;
            $insert_fx['created_by'] = $user_id;
            $insert_fx['updated_at'] = $date;
            $insert_fx['updated_by'] = $user_id;
            $db->insert('user_fx',$insert_fx);

            // Insert to user_gi
            $insert_gi['user_id']       = $user_id;
            $insert_gi['created_at']    = $date;
            $insert_gi['created_by']    = $user_id;
            $insert_gi['updated_at']    = $date;
            $insert_gi['updated_by']    = $user_id;
            $db->insert('user_gi',$insert_gi);

            // Insert to user_marketing
            $insert_marketing['user_id']       = $user_id;
            $insert_marketing['lead_src']      = $source;
            $insert_marketing['lead_camp']     = $campaign;
            $insert_marketing['affiliate']     = $affiliate;
            $insert_marketing['created_at']    = $date;
            $insert_marketing['created_by']    = $user_id;
            $insert_marketing['updated_at']    = $date;
            $insert_marketing['updated_by']    = $user_id;
            $db->insert('user_marketing',$insert_marketing);

            // Send Email
            try{
                global $_Email_M;
                $receivers[] = $act_detail = array (
                    'id'    =>  $user_id,
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
                global $actLog; $actLog->add('New Lead', $user_id, 1, json_encode($act_detail));
            }
            catch(Exception $e){
                $output->e = 'Error: '. $e->getMessage();
            } finally {
                // Autologin
                $sess->login(180, $email, $pass, true, false);
                if($sess->IS_LOGIN) {
                    global $userManager;
                    $output->id = $_SESSION['id'];
                    $output->role = appSession::checkRole();
                } else {
                    $output->e = $sess->ERROR;
                }
            }
        }
        else {
            $output->e = $db->log();
        }

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Check Permit
function checkPermit(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if( !isset($_REQUEST['target']) ) $output->e = 'target expected';
    if( !isset($_REQUEST['act']) ) $output->e = 'act expected';
    if(!$output->e) {
        eFun::sessionJump($_REQUEST['sessionId']);
        global $APP;
        if($_REQUEST['echo'] ?? false){
            ob_start();
            $output->res = $APP->checkPermit($_REQUEST['target'], $_REQUEST['act'],1);
            $output->error = ob_get_contents();
            ob_end_clean();
        }
        else{
            $output->res = $APP->checkPermit($_REQUEST['target'], $_REQUEST['act']);
        }
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Get Screen
function getScreen(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if( !isset($_REQUEST['screen']) ) $output->e = 'screen expected';

    if(!$output->e) {
        eFun::sessionJump($_REQUEST['sessionId']);
        global $APP;
        ob_start();
        $check_permit = $APP->checkPermit($_REQUEST['screen'], 'view',1);
        if(!$check_permit) $output->e = ob_get_contents();
        ob_end_clean();
        if($check_permit){
            $output->res = blocks::screen($_REQUEST['screen'], $_REQUEST['params']);
        }
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Get Form
function getForm(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if( !isset($_REQUEST['name']) ) $output->e = 'form name expected';
    if(!$output->e) {
        eFun::sessionJump($_REQUEST['sessionId']);
        $req_form = explode("_", $_REQUEST['name']);
        global $APP;
        ob_start();
        $check_permit = $APP->checkPermit($req_form[0], $req_form[1],1);
        if(!$check_permit) $output->e = ob_get_contents();
        ob_end_clean();
        if($check_permit){
            $output->res = blocks::form($_REQUEST['name'], $_REQUEST['params']);
        }
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Get Wizard
function getWizard(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if( !isset($_REQUEST['name']) ) $output->e = 'wizard name expected';
    if(!$output->e) {
        eFun::sessionJump($_REQUEST['sessionId']);
        $req_wizard = explode("_", $_REQUEST['name']);
        global $APP;
        ob_start();
        $check_permit = $APP->checkPermit('wizard', $req_wizard[0],1);
        if(!$check_permit) $output->e = ob_get_contents();
        ob_end_clean();
        if($check_permit){
            $output->res = blocks::wizard($_REQUEST['name']);
        }
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Get Profile
function crmGetProfile(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);
        $output->profile = main::getProfile();
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM Update Profile General
function crmUpdateProfileG() {
    $output = new stdClass();
    $output->e = false;
    global $db;

    // Check All Input
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if( !isset($_REQUEST['fname']) ) $output->e = 'first name expected';
    if( !isset($_REQUEST['lname']) ) $output->e = 'last name expected';
    if( !isset($_REQUEST['phone']) ) $output->e   = 'phone expected';
    if( strlen($_REQUEST['phone'])<5 ) $output->e = 'phone is short';
    if( !isset($_REQUEST['country']) ) $output->e = 'country expected';

    // Check for duplicate phone
    $phone_10 = $db->escape(substr($_REQUEST['phone'],-10));
    $where = " RIGHT(phone,10)='$phone_10' AND user_if!=".$_SESSION['id'];
    if($db->exist('user_extra', $where)) $output->e = "This phone number is assigned to another user!";

    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);
        // Check if login
        if ( !isset($_SESSION['id']) ) {
            $output->e = 'You need login first!';
        }
        else{
            $profile = main::getProfile();
            $old = array(
                'fname'     => $profile->General['fname'],
                'lname'     => $profile->General['lname'],
                'phone'     => $profile->General['phone'],
                'country'   => $profile->General['country']
            );
            $update_extra['fname']      = GF::charReplace('tr', $_REQUEST['fname']);
            $update_extra['lname']      = GF::charReplace('tr', $_REQUEST['lname']);
            $update_extra['phone']      = $_REQUEST['phone'];
            $update_extra['country']    = $_REQUEST['country'];
            $update_extra['updated_at'] = date('Y-m-d H:i:s');
            $update_extra['updated_by'] = $_SESSION['id'];

            $where = 'user_id='.$_SESSION['id'];
            $output->res = $db->updateAny('user_extra', $update_extra, $where);

            $archive = array(
                'REQUEST' => $_REQUEST,
                'old' => $old
            );
            // Add actLog
            global $actLog;
            $actLog->add('Update Profile', $_SESSION['id'],$output->res, json_encode($archive), $_SESSION['id']);
        }
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM Update Profile Extra
function crmUpdateProfileE() {
    $output = new stdClass();
    $output->e = false;
    global $db;

    // Check All Input
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';

    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);
        // Check if login
        if ( !isset($_SESSION['id']) ) {
            $output->e = 'You need login first!';
        }
        else{
            $profile = main::getProfile();
            $old = array(
                'city'          => $profile->Extra['city'],
                'address'       => $profile->Extra['address'],
                'interests'     => $profile->Extra['interests'],
                'hobbies'       => $profile->Extra['hobbies'],
                'job_cat'       => $profile->Extra['job_cat'],
                'job_title'     => $profile->Extra['job_title'],
                'exp_fx_year'   => $profile->Extra['exp_fx_year'],
                'exp_cfd_year'  => $profile->Extra['exp_cfd_year'],
                'income'        => $profile->Extra['income'],
                'investment'    => $profile->Extra['investment'],
                'strategy'      => $profile->Extra['strategy']
            );

            $update_extra = array(
                'city'          => $_REQUEST['city'],
                'address'       => $_REQUEST['address'],
                'interests'     => $_REQUEST['interests'],
                'hobbies'       => $_REQUEST['hobbies'],
                'updated_at'    => date('Y-m-d H:i:s'),
                'updated_by'    => $_SESSION['id']

            );
            $update_fx = array(
                'job_cat'       => $_REQUEST['job_cat'],
                'job_title'     => $_REQUEST['job_title'],
                'exp_fx'        => boolval($_REQUEST['exp_fx_year']),
                'exp_fx_year'   => $_REQUEST['exp_fx_year'],
                'exp_cfd'       => boolval($_REQUEST['exp_cfd_year']),
                'exp_cfd_year'  => $_REQUEST['exp_cfd_year'],
                'income'        => $_REQUEST['income'],
                'investment'    => $_REQUEST['investment'],
                'strategy'      => $_REQUEST['strategy'],
                'updated_at'    => date('Y-m-d H:i:s'),
                'updated_by'    => $_SESSION['id']
            );

            $where = 'user_id='.$_SESSION['id'];
            $output->res_extra = $db->updateAny('user_extra', $update_extra, $where);
            $output->res_fx = $db->updateAny('user_fx', $update_fx, $where);

            $archive = array(
                'REQUEST' => $_REQUEST,
                'old' => $old
            );
            // Add actLog
            global $actLog;
            $actLog->add('Update Profile', $_SESSION['id'], ($output->res_extra && $output->res_fx), json_encode($archive), $_SESSION['id']);
        }
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM Update Profile Agreement
function crmUpdateProfileAgreement() {
    $output = new stdClass();
    $output->e = false;
    global $db;

    // Check All Input
    if( !isset($_REQUEST['sessionId']) ) $output->e = 'sessionId expected';
    if( strlen($_REQUEST['sessionId'])<16 ) $output->e = 'sessionId is short';
    if( !isset($_REQUEST['agree']) ) $output->e = 'do agree expected';

    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);
        // Check if login
        if ( !isset($_SESSION['id']) ) {
            $output->e = 'You need login first!';
        }
        else{
            $update_extra['date_approve']   = date('Y-m-d H:i:s');
            $update_extra['updated_at']     = date('Y-m-d H:i:s');
            $update_extra['updated_by']     = $_SESSION['id'];
            $where = 'user_id='.$_SESSION['id'];
            $output->res = $db->updateAny('user_extra', $update_extra, $where);

            // Add actLog
            global $actLog;
            $actLog->add('Update Profile', $_SESSION['id'],$output->res, json_encode($_REQUEST), $_SESSION['id']);
        }
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM - Get Platform Groups
function crmGetPlatformGroups(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['server']) ) $output->e = 'server expected';
    if( !isset($_REQUEST['type']) ) $output->e = 'type expected';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);
        global $db;
        $where = 'unit = '.$_SESSION['unitn'].' AND type = '.$_REQUEST['type'].' AND server = '.$_REQUEST['server'];
        $result = $db->select('mt_groups', $where);
        $html='';
        if($result) foreach ($result as $group){
            $html .= '<option>'.$group['name'].'</option>';
        }
        $output->options = $html;
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM - Meta Open TP
function crmMetaOpenTP(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['platform']) ) $output->e = 'platform expected';
    if( !isset($_REQUEST['type']) ) $output->e = 'type expected';
    if( !isset($_REQUEST['group']) ) $output->e = 'group expected';
    if( !isset($_REQUEST['amount']) ) $output->e = 'amount expected';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        global $db;
        global $DB_admin;
        global $userManager;
        $user_data = $userManager->get($_SESSION['id']);

        $userId     = $_SESSION['id'];
        $type       = $_REQUEST['type'];
        $platform   = $_REQUEST['platform'];
        $group      = $_REQUEST['group'];
        $amount     = $_REQUEST['amount'];
        $name       = $user_data['extra']['fname'].' '.$user_data['extra']['lname'];
        $uname      = $user_data['extra']['fname'];
        $usname     = $user_data['extra']['lname'];
        $email      = $user_data['email'];

        $date = date('Y-m-d\TH:i:s\Z');

        // MT5
        if($platform == "2"){
            $request = new CMT5Request();
            if($request->Init('mt5.tradeclan.co.uk:443') && $request->Auth(1000,"@Sra7689227",1950,"WebManager"))
            {
                $main_pass = main::randString(8);
                $investor_pass = main::randString(8);
                // Real
                if($type == "2"){
                    $prefixgroup = "real\\";
                }
                // Demo
                else {
                    $prefixgroup = "demo\\";
                }

                // USER GET State
                $code = '/user_add?pass_main='.$main_pass.'&pass_investor='.$investor_pass.'&group='.$prefixgroup.''.$group.'&name=test&email='.$email.'&leverage=200';
                $result=$request->Get($code);
                if($result!=false)
                {
                    $json=json_decode($result);
                    $login_5 = $json->answer->Login;
                    if($type == "1"){
                        $result2=$request->Get('/trade_balance?login='.$login_5.'&type=2&balance='.$amount.'&comment=Deposit');
                    }
                }
            }
            $request->Shutdown();
        }
        // MT4
        else {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://auth.cplugin.net/connect/token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>"grant_type=client_credentials&scope=webapi&client_id=cd205448-0fb9-4dd8-9abf-9e0687b149ac&client_secret=7fa5cbbd-13c2-43a4-97b8-842fbc54f0f1",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/x-www-form-urlencoded"
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response);

            if($type == "1"){
                $curl2 = curl_init();
                curl_setopt_array($curl2, array(
                    CURLOPT_URL => "https://mywebapi.com/api/MT4/5f601116-2a15-448c-afea-66e7b7d7c6c5/UserRecordGet/".$userId,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "api-version: 1.0",
                        "Authorization: Bearer ".$data->access_token.""
                    ),
                ));
                $response2 = curl_exec($curl2);
                curl_close($curl2);
                $user = json_decode($response2);
                $curl3 = curl_init();
                curl_setopt_array($curl3, array(
                    CURLOPT_URL => "https://mywebapi.com/api/MT4/5f601116-2a15-448c-afea-66e7b7d7c6c5/UserRecordNew",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS =>'{"enable": 1,"leverage": 200,"group":"'.$group.'","name": "'.$name.'","country": "'.$country.'","email": "'.$email.'"}',
                    CURLOPT_HTTPHEADER => array(
                        "Api-Version: 1.0",
                        "Content-Type: application/json",
                        "Authorization: Bearer ".$data->access_token.""
                    ),
                ));
                $response3 = curl_exec($curl3);
                $tp = json_decode($response3);
                curl_close($curl3);
                $output->res = $response3;
            }
            else if ($type == "2") {
                $curl2 = curl_init();
                curl_setopt_array($curl2, array(
                    CURLOPT_URL => "https://mywebapi.com/api/MT4/895a45cf-8c17-4c16-80ad-79958f133358/UserRecordGet/".$userId,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "api-version: 1.0",
                        "Authorization: Bearer ".$data->access_token.""
                    ),
                ));
                $response2 = curl_exec($curl2);
                curl_close($curl2);
                $user = json_decode($response2);
                $curl3 = curl_init();
                curl_setopt_array($curl3, array(
                    CURLOPT_URL => "https://mywebapi.com/api/MT4/895a45cf-8c17-4c16-80ad-79958f133358/UserRecordNew",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS =>'{"enable": 1,"leverage": 200,"group":"'.$group.'","name": "'.$name.'","country": "'.$country.'","email": "'.$email.'"}',
                    CURLOPT_HTTPHEADER => array(
                        "Api-Version: 1.0",
                        "Content-Type: application/json",
                        "Authorization: Bearer ".$data->access_token.""
                    ),
                ));
                $response3 = curl_exec($curl3);
                $tp = json_decode($response3);
                curl_close($curl3);
                $output->res = $response3;
            }
        }


        $date = date('Y-m-d H:i:s');
        $platform_name =  '';
        if($platform==1)
            $platform_name = 'MT4';
        if($platform==2)
            $platform_name = 'MT5';
        $sqlPass = "INSERT INTO tp (user_id,login,password,group_id,server,created_at,created_by,updated_at,updated_by) VALUES ('$userId','$login_5','$main_pass','$type','$platform_name','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";

        // MT5
        if($platform == "2"){
            $request1 = new CMT5Request();
            if($request1->Init('mt5.tradeclan.co.uk:443') && $request1->Auth(1000,"@Sra7689227",1950,"WebManager"))
            {
                $result1=$request1->Get('/user_get?login='.$login_5);
                if($result1!=false)
                {
                    $json1=json_decode($result1);
                    if((int)$json1->retcode==0)
                    {
                        $user1=$json1->answer;
                        //--- Changing The Details
                        $user1->FirstName=$uname;
                        $user1->LastName=$usname;
                        $result1=$request1->Post('/user_update',json_encode($user1));
                    }
                }
                $request1->Shutdown();
            }

            mysqli_query($DB_admin, $sqlPass);

            $inserted_id = mysqli_insert_id($DB_admin);
            // Add actLog
            global $actLog; $actLog->add('MyWebAPI',$userId,1,'{"action":"New TP","user_id":"'.$userId.'","TP ID":"'.$inserted_id.'"}');

            // Send Email
            global $_Email_M;
            $where = "email='$email' AND unit IN (".Broker['units'].")";
            $receivers[] = array (
                'id'    =>  $db->selectRow('users',$where)['id'],
                'email' =>  $email,
                'data'  =>  array(
                    'fname' =>  $uname,
                    'lname' =>  $usname,
                    'login' =>  $login_5,
                    'pass' =>  $main_pass,
                    'ipass' =>  $investor_pass
                )
            );
            $subject = $theme = 'TP_New_Account';
            $_Email_M->send($receivers, $theme, $subject);

            $output->tp = array(
                'Login' => $login_5,
                'Password' => $main_pass,
                'Investor_Password' => $investor_pass,
            );


        }
        // MT4
        else {
            mysqli_query($DB_admin, $sqlPass);

            $inserted_id = mysqli_insert_id($DB_admin);
            // Add actLog
            global $actLog; $actLog->add('MyWebAPI',$userId,1,'{"action":"New TP","user_id":"'.$userId.'","TP ID":"'.$inserted_id.'"}');

            // Send Email
            global $db;
            global $_Email_M;
            $where = "email='$email' AND unit IN (".Broker['units'].")";
            $receivers[] = array (
                'id'    =>  $db->selectRow('users',$where)['id'],
                'email' =>  $email,
                'data'  =>  array(
                    'fname' =>  $uname,
                    'lname' =>  $usname,
                    'login' =>  $tp->login,
                    'pass' =>  $tp->password,
                    'ipass' =>  ''
                )
            );
            $subject = $theme = 'TP_New_Account';
            $_Email_M->send($receivers, $theme, $subject);

            $output->tp = array(
                'Login' => $tp->login,
                'Password' => $tp->password
            );
        }

        // Add actLog
        global $actLog; $actLog->add('App - Open TP', $userId,1, json_encode($_POST));
        mysqli_close($DB_admin);

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// CRM - Meta Update Login Password
function crmUpdateLoginPassword(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['login']) ) $output->e = 'login expected';
    if( !isset($_REQUEST['mpass']) ) $output->e = 'main pass expected';
    if( !isset($_REQUEST['ipass']) ) $output->e = 'investor pass expected';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);
        // Meta
        $mt5api = new mt5API();
        if(strlen($_REQUEST['mpass'])) {
            $api_params['login']    = $_REQUEST['login'];
            $api_params['type']     = 'main';
            $api_params['password'] = $_REQUEST['mpass'];
            $mt5api->get('/api/user/change_password', $api_params);
            $e = $mt5api->Error;
            $output->main = $mt5api->Response->retcode;
            if($output->main == "0 Done"){
                // CRM
                global $db;
                $update['password'] = $_REQUEST['mpass'];
                $where = 'login='.$_REQUEST['login'];
                $db->updateAny('tp',$update, $where);
            }
        }
        if(strlen($_REQUEST['ipass'])) {
            $api_params['login']    = $_REQUEST['login'];
            $api_params['type']     = 'investor';
            $api_params['password'] = $_REQUEST['ipass'];
            $mt5api->get('/api/user/change_password', $api_params);
            $e = $mt5api->Error;
            $output->investor = $mt5api->Response->retcode;
        }
        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Meta - Get Login Positions
function getLoginPositions(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['login']) ) $output->e = 'login is expected';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        $mt5api = new mt5API();


        $api_params['login']  = $_REQUEST['login'];
        $mt5api->get('/api/position/get_total', $api_params);
        $total  = array(
            'e'      => $mt5api->Error,
            'api'    => $mt5api->Response,
        );
        $api = $mt5api->Response;
        if($api->retcode==="0 Done"){
            $output->total = $total['api']->answer->total;
        }
        else {
            $output->e = $total['e'];
        }
        if(is_numeric($output->total) ?? false){
            $output->data = array();
            $api_params['login']  = $_REQUEST['login'];
            $mt5api->get('/api/position/get_batch', $api_params);
            $e = $mt5api->Error;
            $api = $mt5api->Response;
            $output->api = $api;
            if($api->retcode==="0 Done"){
                for ($i = 0; $i < $output->total; $i++){
                    $output->data[] = array(
                        'Close'         =>    '<button type="button" data-tp="'.$_REQUEST['login'].'" data-position="'.$api->answer[$i]->Position.'" class="doA-close-position btn btn-sm btn-danger">Close</button>',
                        'Position'      =>    $api->answer[$i]->Position,
                        'Symbol'        =>    $api->answer[$i]->Symbol,
                        'Action'        =>    ($api->answer[$i]->Action ==0) ? 'Buy' : 'Sell',
                        'TimeCreate'    =>    date('Y-m-d H:i:s', strtotime("@".$api->answer[$i]->TimeCreate." -2 hours")),
                        'Volume'        =>    $api->answer[$i]->Volume,
                        'PriceOpen'     =>    $api->answer[$i]->PriceOpen,
                        'PriceSL'       =>    $api->answer[$i]->PriceSL,
                        'PriceTP'       =>    $api->answer[$i]->PriceTP,
                        'PriceCurrent'  =>    $api->answer[$i]->PriceCurrent,
                        'Storage'       =>    $api->answer[$i]->Storage,
                        'Profit'        =>    $api->answer[$i]->Profit
                    );
                }
            }
            else {
                $output->e = $e;
            }


        }

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Meta - Get Login History
function getLoginHistory(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['login']) ) $output->e = 'login is expected';
    if( !isset($_REQUEST['from']) ) $output->e = 'Start date is expected';
    if( !isset($_REQUEST['to']) ) $output->e = 'end date is expected';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        $mt5api = new mt5API();

        $startDate = strtotime($_REQUEST['from']);
        $endDate = strtotime($_REQUEST['to']);

        $api_params['login']  = $_REQUEST['login'];
        $api_params['from']  = $startDate;
        $api_params['to']  = $endDate;
        $mt5api->get('/api/deal/get_total', $api_params);
        $total  = array(
            'e'      => $mt5api->Error,
            'api'    => $mt5api->Response,
        );
        $api = $mt5api->Response;
        if($api->retcode==="0 Done"){
            $output->total = $total['api']->answer->total;
        }
        else {
            $output->e = $total['e'];
        }
        if(is_numeric($output->total) ?? false){
            $output->data = array();
            $mt5api->get('/api/deal/get_batch', $api_params);
            $e = $mt5api->Error;
            $api = $mt5api->Response;
            $output->api = $api_params;
            if($api->retcode==="0 Done"){
                $output->test[] = $api;

                for ($i = 0; $i < $output->total; $i++){
                    $output->data[] = array(
                        'Deal'      =>    $api->answer[$i]->Deal,
                        'Symbol'        =>    $api->answer[$i]->Symbol,
                        'Action'        =>    ($api->answer[$i]->Action ==0) ? 'Buy' : 'Sell',
                        'Time'    =>    date('Y-m-d H:i:s', strtotime("@".$api->answer[$i]->Time." -2 hours")),
                        'Volume'        =>    $api->answer[$i]->Volume,
                        'Price'         =>    $api->answer[$i]->Price,
                        'PriceSL'       =>    $api->answer[$i]->PriceSL,
                        'PriceTP'       =>    $api->answer[$i]->PriceTP,
                        'PriceCurrent'  =>    $api->answer[$i]->PriceCurrent,
                        'Storage'       =>    $api->answer[$i]->Storage,
                        'Profit'        =>    $api->answer[$i]->Profit
                    );
                }
            }
            else {
                $output->e = $e;
            }


        }

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Meta - Get Login Statistics
function getLoginStatistics(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['login']) ) $output->e = 'login is expected';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        $mt5api = new mt5API();

        $api_params['login']  = $_REQUEST['login'];
        $mt5api->get('/api/user/account/get', $api_params);
        $total  = array(
            'e'      => $mt5api->Error,
            'api'    => $mt5api->Response,
        );
        $api = $mt5api->Response;
        if($api->retcode==="0 Done"){
            $number_digit = $api->answer->CurrencyDigits;
            $output->data = array(
                'Balance'           =>    GF::nf($api->answer->Balance, $number_digit),
                'Equity'            =>    GF::nf($api->answer->Equity, $number_digit),
                'Margin'            =>    GF::nf($api->answer->Margin, $number_digit),
                'MarginLevel'       =>    GF::nf($api->answer->MarginLevel, $number_digit),
                'MarginFree'        =>    GF::nf($api->answer->MarginFree, $number_digit),
                'MarginLeverage'    =>    GF::nf($api->answer->MarginLeverage, $number_digit),
                'Profit'            =>    GF::nf($api->answer->Profit, $number_digit)
            );
        }
        else {
            $output->e = $total['e'];
        }

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Meta - Get Market Prices
function getMarketPrices(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['login']) ) $output->e = 'login is expected';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        $mt5api = new mt5API();

        $api_params['login']  = $_REQUEST['login'];
        $mt5api->get('/api/user/get', $api_params);
        $e = $mt5api->Error;
        $api = $mt5api->Response;
        $login_Group = $api->answer->Group;
        if($login_Group){
            $api_params=[];
            $api_params['symbol']  = '*';
            $api_params['group']  = $login_Group;
            $api_params['trans_id']  = 0;
            $mt5api->get('/api/tick/last_group', $api_params);
            $e = $mt5api->Error;
            $api = $mt5api->Response;
            if($api->retcode==="0 Done"){
                $output->symbols = $api->answer;
            }
            else {
                $output->e = $e;
            }
        }
        else {
            $output->e = $e;
        }

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Meta - Get Symbol Chart
function getSymbolChart(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['symbol']) ) $output->e = 'symbol is expected';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        $mt5api = new mt5API();
        $api_params['symbol'] = $_REQUEST['symbol'];
        $api_params['from'] = time()-3600;
        $api_params['to'] = time();
        $api_params['data'] = 'dohlc';
        $mt5api->get('/api/chart/get', $api_params);
        $e = $mt5api->Error;
        $api = $mt5api->Response;

        if($api->retcode==="0 Done"){
            $output->chartData = array_map("eFun::epoch2date", $api->answer);
        }
        else {
            $output->e = $e;
        }

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Meta - Close Position
function closePosition(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['login']) ) $output->e = 'login is expected';
    if( !isset($_REQUEST['position']) ) $output->e = 'position is expected';
    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        $mt5api = new mt5API();

        $api_params['ticket']  = $_REQUEST['position'];
        $mt5api->get('/api/position/get_batch', $api_params);
        $e = $mt5api->Error;
        $output->position = $position = $mt5api->Response;
        if(!$e && $position->answer[0]->Login === $_REQUEST['login']){
            $type = ($position->answer[0]->Action) ? 0 : 1;

            // Close Position
            $path = '/api/dealer/send_request';
            $request_close['Action']       = 200;
            $request_close['Login']        = $position->answer[0]->Login;
            $request_close['Symbol']       = $position->answer[0]->Symbol;
            $request_close['Volume']       = $position->answer[0]->Volume;
            $request_close['TypeFill']     = 1;
            $request_close['Type']         = $type;
            $request_close['PriceOrder']   = $position->answer[0]->PriceCurrent;
            $request_close['Position']     = $position->answer[0]->Position;
            $request_close['Digits']       = $position->answer[0]->Digits;
            $request_close['Comment']      = 'Q.APP|Close_Pos|S:'.$_SESSION['sess_id'].'|U:'.$_SESSION['id'];

            $is_open = eFun::isTradeOpenByLogin($position->answer[0]->Symbol, $_REQUEST['login']);
            if($is_open){
                $output->body = $request_close;
                $mt5api->post($path, null, json_encode($request_close));
                $e = $mt5api->Error;
                $output->close = $mt5api->Response;
                if(!$e){
                    $identifiers = $output->close->answer->id;

                    // Check Request
                    $data['id'] = $identifiers;
                    $path = '/api/dealer/get_request_result';
                    $mt5api->post($path, $data);
                    $e = $mt5api->Error;
                    $output->request = $mt5api->Response;
                }
            } else {
                $output->e = 'Market is Closed!';
            }
        }
        else {
            $output->e = 'The position is not on the same login';
        }

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Meta - Simple Order
function simpleOrder(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['login']) ) $output->e  = 'login is expected';
    if( !isset($_REQUEST['symbol']) ) $output->e = 'symbol is expected';
    if( !isset($_REQUEST['type']) ) $output->e = 'action is expected';
    if( !isset($_REQUEST['volume']) ) $output->e = 'volume is expected';
    if( !isset($_REQUEST['takeProfit']) ) $output->e = 'take profit is expected';
    if( !isset($_REQUEST['stopLoss']) ) $output->e = 'stop loss is expected';
    $is_open = eFun::isTradeOpenByLogin($_REQUEST['symbol'], $_REQUEST['login']);
    if( !$is_open ) $output->e = 'Market is Closed!';

    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        $mt5api = new mt5API();

        // Request Position-
        $request_open['Action']        = 200; // TA_DEALER_POS_EXECUTE
        $request_open['Login']         = $_REQUEST['login'];
        $request_open['Symbol']        = $_REQUEST['symbol'];
        $request_open['Volume']        = $_REQUEST['volume']*10000;
        if($_REQUEST['takeProfit'] != 0)
            $request_open['PriceTP']       = $_REQUEST['takeProfit'];
        if($_REQUEST['stopLoss'] != 0)
            $request_open['PriceSL']   = $_REQUEST['stopLoss'];
        $request_open['Type']          = $_REQUEST['type'];
        $request_open['TypeFill'] = 1;
        $request_open['Digits'] = 5;
        $request_open['Comment'] = 'Q.APP|Add_Order|S:'.$_SESSION['sess_id'].'|U:'.$_SESSION['id'];

        $output->request_body = $request_open;

        $path = '/api/dealer/send_request';
        $mt5api->post($path, null, json_encode($request_open));
        $e = $mt5api->Error;
        $output->request = $mt5api->Response;
        if(!$e){
            $identifiers = $output->request->answer->id;

            // Check Request
            $data_result['id'] = $identifiers;
            $path = '/api/dealer/get_request_result';
            $mt5api->post($path, $data_result);
            $e = $mt5api->Error;
            $output->result = $mt5api->Response;


            // Check Request
            $data_total['login'] = $_REQUEST['login'];
            $path = '/api/order/get_total';
            $mt5api->get($path, $data_total);
            $e = $mt5api->Error;
            $output->orders = $mt5api->Response;

        } else {
            $output->req_error = $e;
        }

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}

// Meta - Pending Order
function pendingOrder(){
    $output = new stdClass();
    $output->e = false;
    if( !isset($_REQUEST['login']) ) $output->e  = 'login is expected';
    if( !isset($_REQUEST['PriceOrder']) ) $output->e  = 'PriceOrder is expected';
    if( !isset($_REQUEST['EnOrderTime']) ) $output->e  = 'EnOrderTime is expected';
    if( !isset($_REQUEST['symbol']) ) $output->e = 'symbol is expected';
    if( !isset($_REQUEST['type']) ) $output->e = 'action is expected';
    if( !isset($_REQUEST['volume']) ) $output->e = 'volume is expected';
    if( !isset($_REQUEST['takeProfit']) ) $output->e = 'take profit is expected';
    if( !isset($_REQUEST['stopLoss']) ) $output->e = 'stop loss is expected';
    $is_open = eFun::isTradeOpenByLogin($_REQUEST['symbol'], $_REQUEST['login']);
    if( !$is_open ) $output->e = 'Market is Closed!';

    if(!$output->e){
        eFun::sessionJump($_REQUEST['sessionId']);

        $mt5api = new mt5API();

        // Request Position-
        $request_open['Action']        = 201; // TA_DEALER_ORD_PENDING
        $request_open['Login']         = $_REQUEST['login'];
        $request_open['Symbol']        = $_REQUEST['symbol'];
        $request_open['PriceOrder']    = $_REQUEST['PriceOrder'];
        $request_open['IMTOrder::EnOrderTime'] = $_REQUEST['EnOrderTime'];
        $request_open['Volume']        = $_REQUEST['volume']*10000;
        if($_REQUEST['takeProfit'] != 0)
            $request_open['PriceTP']       = $_REQUEST['takeProfit'];
        if($_REQUEST['stopLoss'] != 0)
            $request_open['PriceSL']   = $_REQUEST['stopLoss'];
        $request_open['Type']          = $_REQUEST['type'];
        $request_open['TypeFill'] = 1;
        $request_open['Digits'] = 5;
        $request_open['Comment'] = 'Q.APP|Add_Order|S:'.$_SESSION['sess_id'].'|U:'.$_SESSION['id'];

        $output->request_body = $request_open;

        $path = '/api/dealer/send_request';
        $mt5api->post($path, null, json_encode($request_open));
        $e = $mt5api->Error;
        $output->request = $mt5api->Response;
        if(!$e){
            $identifiers = $output->request->answer->id;

            // Check Request
            $data_result['id'] = $identifiers;
            $path = '/api/dealer/get_request_result';
            $mt5api->post($path, $data_result);
            $e = $mt5api->Error;
            $output->result = $mt5api->Response;


            // Check Request
            $data_total['login'] = $_REQUEST['login'];
            $path = '/api/order/get_total';
            $mt5api->get($path, $data_total);
            $e = $mt5api->Error;
            $output->orders = $mt5api->Response;

        } else {
            $output->req_error = $e;
        }

        eFun::sessionJumpBack();
    }
    echo json_encode($output);
}
