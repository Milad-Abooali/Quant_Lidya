<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

###############        This file will not use anymore 2022-3-23

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();
    global $db;

    if (Broker['captcha'] && ($_POST['captcha'] != $_SESSION['captcha']['code'])) {
        echo json_encode(array("statusCode"=>201,"error"=>"You have entered the wrong captcha !"));
        exit();
    }

    $turkish = array("ı", "ğ", "ü", "ş", "ö", "ç", "Ğ", "İ", "Ş", "Ö", "Ü", "Ç");//turkish letters
    $english = array("i", "g", "u", "s", "o", "c", "G", "I", "S", "O", "U", "C");//english cooridinators letters

	$fname = htmlspecialchars($_POST['fname'], ENT_NOQUOTES, "UTF-8");
	$fname = str_replace($turkish, $english, $fname);
    $lname = htmlspecialchars($_POST['lname'], ENT_NOQUOTES, "UTF-8");
    $lname = str_replace($turkish, $english, $lname);
    $phone = $_POST['phone'];
    $country = $_POST['country'];
    $city = "";
    $address = "";
    $interests = "";
    $hobbies = "";
    $userunit = intval($_POST['userunit']);
    $sqlUNIT = 'SELECT name FROM units WHERE id ='.$userunit;
    $units = $DB_admin->query($sqlUNIT);
    if($units) while ($rowUNIT = mysqli_fetch_array($units)) {
        $unit = $rowUNIT['name'];   
    }
    $retention = "0";
    $conversion = "0";
    $status = "1";
    $type = "1";
    $date = date('Y-m-d H:i:s');
    $followup = $date;
    
    $job_cat = "";
    $job_title = "";
    $exp_fx = "1";
    $exp_fx_year = "";
    $exp_cfd = "1";

    $exp_cfd_year = "";
    $income = "";
    $investment = "";
    $strategy = "";
    
    $bd = "";
    $whatsapp = "";
    $telegram = "";
    $facebook = "";
    $instagram = "";
    $twitter = "";
    
    $source = $_POST['source'];
    $campaign = $_POST['campaign'];
    $affiliate = $_POST['affiliate'];
	
	//$leadusername = $_POST['leadusername'];
	$email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array("statusCode"=>201,"Error"=>'Not Valid Email Address!'));
        die();
    }

	function generateStrongPassword($length = 8, $add_dashes = false, $available_sets = 'lud')
    {
    	$sets = array();
    	if(strpos($available_sets, 'l') !== false)
    		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
    	if(strpos($available_sets, 'u') !== false)
    		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    	if(strpos($available_sets, 'd') !== false)
    		$sets[] = '23456789';
    	if(strpos($available_sets, 's') !== false)
    		$sets[] = '!@#$%&*?';
    
    	$all = '';
    	$password = '';
    	foreach($sets as $set)
    	{
    		$password .= $set[array_rand(str_split($set))];
    		$all .= $set;
    	}
    
    	$all = str_split($all);
    	for($i = 0; $i < $length - count($sets); $i++)
    		$password .= $all[array_rand($all)];
    
    	$password = str_shuffle($password);
    
    	if(!$add_dashes)
    		return $password;
    
    	$dash_len = floor(sqrt($length));
    	$dash_str = '';
    	while(strlen($password) > $dash_len)
    	{
    		$dash_str .= substr($password, 0, $dash_len) . '-';
    		$password = substr($password, $dash_len);
    	}
    	$dash_str .= $password;
    	return $dash_str;
    }
	$pass = generateStrongPassword();
	$password = password_hash($pass, PASSWORD_DEFAULT);
	
	function getUserIpAddr(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
	
	$ips = getUserIpAddr();
	//echo $ips;
	
	$sql4 = "INSERT INTO users (username,password,email,unit,type,pa,created_at) VALUES ('$email','$password','$email','$unit','Leads','$pass','$date')";

	global $broker_units;
    $email = $db->escape($email);
    $where = "email='$email' AND unit IN ($broker_units)";
	$exist = $db->exist('users',$where);
	if(!$exist) {
        $phone_10 = $db->escape(substr($phone));
        $where = " RIGHT(phone,10)='$phone_10'";
        $exist = $db->exist('user_extra', $where);
    }
	//echo $sql5;
	if (!$exist) {
        if(mysqli_query($DB_admin, $sql4)) {
            //echo $ips;
            $sql3 = "SELECT id FROM users WHERE email = '$email' AND unit = '$unit'";
            $userLeadNew = $DB_admin->query($sql3);
            while ($rowLeadNew = mysqli_fetch_array($userLeadNew)) {
                $user_id = $rowLeadNew['id'];
                //echo $user_id;
                $sql5 = "INSERT INTO user_extra (user_id,ip,fname,lname,phone,country,city,address,interests,hobbies,unit,retention,conversion,status,type,followup,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$ips','$fname','$lname','$phone','$country','$city','$address','$interests','$hobbies','$userunit','$retention','$conversion','$status','$type','$date','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                $sql6 = "INSERT INTO user_fx (user_id,job_cat,job_title,exp_fx,exp_fx_year,exp_cfd,exp_cfd_year,income,investment,strategy,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$job_cat','$job_title','$exp_fx','$exp_fx_year','$exp_cfd','$exp_cfd_year','$income','$investment','$strategy','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                $sql7 = "INSERT INTO user_gi (user_id,bd,whatsapp,telegram,facebook,instagram,twitter,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$bd','$whatsapp','$telegram','$facebook','$instagram','$twitter','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                $sql8 = "INSERT INTO user_marketing (user_id,lead_src,lead_camp,affiliate,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$source','$campaign','$affiliate','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                if(mysqli_query($DB_admin, $sql5) && mysqli_query($DB_admin, $sql6) && mysqli_query($DB_admin, $sql7) && mysqli_query($DB_admin, $sql8)){

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

                    global $sess;
                    $sess->login($_POST['timeoffset'], $email, $pass,0,false);
                    echo json_encode(array("statusCode"=>200,"userCode"=>$user_id,"unit"=>$userunit));
                } else {
                    echo json_encode(array("statusCode"=>201));
                }
            }
        } else {
            echo json_encode(array("statusCode"=>201));
        }
	} else {
		echo json_encode(array(
            "statusCode"=>201,
            "Error" =>  'There is an user exist on this phone or email address !'
        ));
	}

	mysqli_close($DB_admin);
?>