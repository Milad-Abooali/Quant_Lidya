<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    // Update FTD Time
    if($_POST['user_id'] ?? false) {
        $sqlTPS = 'SELECT * FROM tp WHERE user_id ='.$_POST['user_id'];
        $resultTPS = $DB_admin->query($sqlTPS);
        if($resultTPS) while ($rowTP = mysqli_fetch_array($resultTPS)) {
            if($rowTP['server'] == "MT4" AND $rowTP['group_id'] == "2"){
                $sqlTPU = 'UPDATE `tp` SET `ftd`=(SELECT MIN(OPEN_TIME) AS FTD FROM lidyapar_mt4.MT4_TRADES WHERE CMD = 6 AND LOGIN = "'.$rowTP['login'].'" GROUP BY LOGIN), `retention`="'.$_POST['retention'].'", `conversion`="'.$_POST['conversion'].'" WHERE login = "'.$rowTP['login'].'"';
                $resultTPU = $DB_admin->query($sqlTPU);
            } else {
                $sqlTPU = 'UPDATE `tp` SET `ftd`=(SELECT MIN(Time) AS FTD FROM lidyapar_mt5.mt5_deals WHERE Action = 2 AND Login = "'.$rowTP['login'].'" AND mt5_deals.Comment NOT IN ("Zeroing","Carried Balance From MT4") GROUP BY Login), `retention`="'.$_POST['retention'].'", `conversion`="'.$_POST['conversion'].'" WHERE login = "'.$rowTP['login'].'"';
                $resultTPU = $DB_admin->query($sqlTPU); 
            }
        }
    }

    $error = false;
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
    {   // Email validator
        $error = 'Email is not valid address!';
    } 
    else 
    {    // Is exist (based on email & unit)
        global $db;
        $where = "email='".$_POST['email']."' AND unit IN (".Broker['units'].")";
        $exist = $db->exist('users', $where);
        if($exist) $error = 'There is another account with this email address!';
    }

    // Check for duplicate phone
    $phone_10 = $db->escape(substr($_POST['phone'],-10));
    $where = " RIGHT(phone,10)='$phone_10'";
    $exist_phone = $db->exist('user_extra', $where);
    if($exist_phone) $error = "You have an other account in our site with this phone number!";

    if ($error) {
        $output['statusCode'] = 201;
        $output['error'] = $error;
        echo json_encode($output);
        $output['post'] = $_POST;
        global $actLog; $actLog->add('New Lead','', 0, json_encode($output));
        die();
    }
    
    $turkish = array("ı", "ğ", "ü", "ş", "ö", "ç", "Ğ", "İ", "Ş", "Ö", "Ü", "Ç");//turkish letters
    $english = array("i", "g", "u", "s", "o", "c", "G", "I", "S", "O", "U", "C");//english cooridinators letters


	$fname = htmlspecialchars($_POST['fname'], ENT_NOQUOTES, "UTF-8");
	$fname = str_replace($turkish, $english, $fname);
    $lname = htmlspecialchars($_POST['lname'], ENT_NOQUOTES, "UTF-8");
    $lname = str_replace($turkish, $english, $lname);
    $phone = $_POST['phone'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $interests = $_POST['interests'];
    $hobbies = $_POST['hobbies'];
    $userunit = $_POST['userunit'];
    $sqlUNIT = 'SELECT name FROM units WHERE id ='.$userunit;
    $units = $DB_admin->query($sqlUNIT);
    while ($rowUNIT = mysqli_fetch_array($units)) {
        $unit = $rowUNIT['name'];   
    }
    $retention = $_POST['retention'];
    $conversion = $_POST['conversion'];
    $status = $_POST['status'];
    $type = $_POST['type'];
    $sqlTYPE = 'SELECT name FROM type WHERE id ='.$type;
    $types = $DB_admin->query($sqlTYPE);
    while ($rowTYPE = mysqli_fetch_array($types)) {
        $typeN = $rowTYPE['name'];   
    }
    $followup = $_POST['followup'];
    
    $job_cat = $_POST['job_cat'];
    $job_title = $_POST['job_title'];
    if($_POST['exp_fx'] == "on"){
        $exp_fx = "1";
    } else {
        $exp_fx = "0";
    }
    $exp_fx_year = $_POST['exp_fx_year'];
    if($_POST['exp_cfd'] == "on"){
        $exp_cfd = "1";
    } else {
        $exp_cfd = "0";
    }
    $exp_cfd_year = $_POST['exp_cfd_year'];
    $income = $_POST['income'];
    $investment = $_POST['investment'];
    $strategy = $_POST['strategy'];
    
    $bd = $_POST['bd'];
    $whatsapp = $_POST['whatsapp'];
    $telegram = $_POST['telegram'];
    $facebook = $_POST['facebook'];
    $instagram = $_POST['instagram'];
    $twitter = $_POST['twitter'];
    
    $source = $_POST['source'];
    $campaign = $_POST['campaign'];
    $affiliate = $_POST['affiliate'];
    
	$date = date('Y-m-d H:i:s');
	
	//$leadusername = $_POST['leadusername'];
	$email = $_POST['email'];
	$email = str_replace($turkish, $english, $email);
	
	$pass = "123456";
	$password = password_hash($pass, PASSWORD_DEFAULT);
	   
	$ips = GF::getIP();
	
	$sql4 = "INSERT INTO users (username,password,email,unit,type,pa,created_at) VALUES ('$email','$password','$email','$unit','$typeN','$pass','$date')";

    global $broker_units;
    $email = $db->escape($email);
    $where = "email='$email' AND unit IN ($broker_units)";
    $exist = $db->exist('users',$where);
    if(!$exist) {
        $phone = $db->escape($phone);
        $where = "phone='$phone'";
        $exist = $db->exist('user_extra',$where);
    }

    if (!$exist) {
        if (mysqli_query($DB_admin, $sql4)) {
            //echo $ips;
            $sql3 = "SELECT id FROM users WHERE email = '$email' AND unit = '$unit'";
            $userLeadNew = $DB_admin->query($sql3);
            while ($rowLeadNew = mysqli_fetch_array($userLeadNew)) {
                $user_id = $rowLeadNew['id'];
                //echo $user_id;
                global $_L;

                $sql5 = "INSERT INTO user_extra (user_id,ip,fname,lname,phone,country,city,address,interests,hobbies,unit,retention,conversion,status,type,followup,created_at,created_by,updated_at,updated_by,language) VALUES ('$user_id','$ips','$fname','$lname','$phone','$country','$city','$address','$interests','$hobbies','$userunit','$retention','$conversion','$status','$type','$date','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."','".LANGUAGE_NAME."')";
                $sql6 = "INSERT INTO user_fx (user_id,job_cat,job_title,exp_fx,exp_fx_year,exp_cfd,exp_cfd_year,income,investment,strategy,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$job_cat','$job_title','$exp_fx','$exp_fx_year','$exp_cfd','$exp_cfd_year','$income','$investment','$strategy','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                $sql7 = "INSERT INTO user_gi (user_id,bd,whatsapp,telegram,facebook,instagram,twitter,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$bd','$whatsapp','$telegram','$facebook','$instagram','$twitter','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                $sql8 = "INSERT INTO user_marketing (user_id,lead_src,lead_camp,affiliate,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$source','$campaign','$affiliate','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                if(mysqli_query($DB_admin, $sql5) && mysqli_query($DB_admin, $sql6) && mysqli_query($DB_admin, $sql7) && mysqli_query($DB_admin, $sql8)){
                    GF::profileRateCal(GF::getUserProfile($user_id));
                    echo json_encode(array("statusCode"=>200));

                    // Send Email
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

                } else {
                    echo json_encode(array("statusCode"=>201));
                }
            }
        } else {
            echo json_encode(array("statusCode"=>202));
        }
    } else {
        echo json_encode(array("statusCode"=>203));
    }
	//echo $sql5;


	mysqli_close($DB_admin);
