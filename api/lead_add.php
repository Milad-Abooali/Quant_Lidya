<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "../config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    // Check if the user is logged in, if not then redirect him to login page
    if($_REQUEST['api_key'] !== "7689227" || !$_POST['email']){
        header("location: ../login.php");
        exit;
    }

    $turkish = array("ı", "ğ", "ü", "ş", "ö", "ç", "Ğ", "İ", "Ş", "Ö", "Ü", "Ç");//turkish letters
    $english = array("i", "g", "u", "s", "o", "c", "G", "I", "S", "O", "U", "C");//english cooridinators letters

    $fullname = htmlspecialchars($_POST['fullname'], ENT_NOQUOTES, "UTF-8");
    $parts = explode(" ", $fullname);
    if(count($parts) > 1) {
        $lname = array_pop($parts);
        $fname = implode(" ", $parts);
    }
    else
    {
        $fname = $fullname;
        $lname = " ";
    }
	//$fname = htmlspecialchars($_POST['fname'], ENT_NOQUOTES, "UTF-8");
	$fname = str_replace($turkish, $english, $fname);
    //$lname = htmlspecialchars($_POST['lname'], ENT_NOQUOTES, "UTF-8");
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
    
    $noteText = $_POST['note'];
    
	$date = date('Y-m-d H:i:s');
	
	//$leadusername = $_POST['leadusername'];
	$email = $_POST['email'];
	$email = str_replace($turkish, $english, $email);
	
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
	
	$ips = GF::getIP();
	//echo $ips;
	
	$sql4 = "INSERT INTO users (username,password,email,unit,type,pa,created_at) VALUES ('$email','$password','$email','$unit','$typeN','$pass','$date')";

    $email = $db->escape($email);

    $force_update = $_POST['fupdate'] ?? false;
    $where = "email='$email' AND unit IN (".Broker['units'].")";
    $exist = $db->exist('users',$where);

    if($exist && $force_update){
        $where = "email='$email' AND unit IN (".Broker['units'].")";
        $user_id = $db->selectRow('users',$where)['id'];
        $userManager->delete($user_id);
    }

    if(!$exist ||  $force_update) {
        $phone = $db->escape($phone);
        $where = "phone='$phone'";
        $exist = $db->exist('user_extra',$where);
    }
    if($exist && $force_update){
        $where = "phone='$phone'";
        $user_id = $db->selectRow('user_extra',$where)['user_id'];
        $userManager->delete($user_id);
    }

    if (!$exist || $force_update) {
        if (mysqli_query($DB_admin, $sql4)) {
            //echo $ips;
            $sql3 = "SELECT id FROM users WHERE email = '$email' AND unit = '$unit'";
            $userLeadNew = $DB_admin->query($sql3);
            while ($rowLeadNew = mysqli_fetch_array($userLeadNew)) {
                $user_id = $rowLeadNew['id'];
                //echo $user_id;
                $sql5 = "INSERT INTO user_extra (user_id,ip,fname,lname,phone,country,city,address,interests,hobbies,unit,retention,conversion,status,type,followup,created_at,created_by,updated_at,updated_by,language) VALUES ('$user_id','$ips','$fname','$lname','$phone','$country','$city','$address','$interests','$hobbies','$userunit','$retention','$conversion','$status','$type','$date','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."','".LANGUAGE_NAME."')";
                $sql6 = "INSERT INTO user_fx (user_id,job_cat,job_title,exp_fx,exp_fx_year,exp_cfd,exp_cfd_year,income,investment,strategy,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$job_cat','$job_title','$exp_fx','$exp_fx_year','$exp_cfd','$exp_cfd_year','$income','$investment','$strategy','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                $sql7 = "INSERT INTO user_gi (user_id,bd,whatsapp,telegram,facebook,instagram,twitter,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$bd','$whatsapp','$telegram','$facebook','$instagram','$twitter','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                $sql8 = "INSERT INTO user_marketing (user_id,lead_src,lead_camp,affiliate,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$source','$campaign','$affiliate','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
                if(mysqli_query($DB_admin, $sql5) && mysqli_query($DB_admin, $sql6) && mysqli_query($DB_admin, $sql7) && mysqli_query($DB_admin, $sql8)){
                    GF::profileRateCal(GF::getUserProfile($user_id));
                    echo json_encode(array("statusCode"=>200));

                    // Send Email
                    //global $_Email_M;
                    //$receivers[] = $act_detail = array (
                    //    'id'    =>  $user_id,
                    //    'email' =>  $email,
                    //    'data'  =>  array(
                    //        'fname'     =>  $fname,
                    //        'lname'     =>  $lname,
                    //        'email'     =>  $email,
                    //        'pass'      =>  $pass
                    //    )
                    //);
                    //$subject = $theme = 'CRM_New_Account';
                    //$_Email_M->send($receivers, $theme, $subject);

                    // Add actLog
                    global $actLog; $actLog->add('New Lead', $user_id, 1, json_encode($act_detail));

                } else {
                    echo json_encode(array("statusCode"=>201));
                }
            }
        } else {
            echo json_encode(array("statusCode"=>202));
        }
    }
    else {
        $sql3 = "SELECT id FROM users WHERE email = '$email' AND unit = '$unit'";
        $userLeadNew = $DB_admin->query($sql3);
        while ($rowLeadNew = mysqli_fetch_array($userLeadNew)) {
            $user_id = $rowLeadNew['id'];
            $sql4 = "UPDATE user_marketing SET campaign_extra = CONCAT(campaign_extra,',','$campaign') WHERE user_id = '$user_id'";
            $sql5 = "UPDATE user_extra SET status = 1 WHERE user_id = '$user_id'";
            if(mysqli_query($DB_admin, $sql4)){
                if(mysqli_query($DB_admin, $sql5))
                echo json_encode(array("statusCode"=>200));
                // Add actLog
                global $actLog; $actLog->add('New Lead', $user_id, 1, '{"email":"'.$email.'"}');
            } else {
                echo json_encode(array("statusCode"=>201));
            }
            $note['note'] = GF::charReplace('tr', $noteText);
            $note['note_type']  = "Other";
            $note['user_id']    = $user_id;
            $note['created_at'] = $date;
            $note['created_by'] = $_SESSION["id"];
            $note['updated_at'] = $date;
            $note['updated_by'] = $_SESSION["id"];
            $insert = $db->insert('notes',$note);
            if($insert) {
                $update['lastnotedate'] = $date;
                $where = "user_id =".$user_id;
                $db->updateAny('user_extra', $update, $where);
                // Add actLog
                global $actLog; $actLog->add('Note', $insert,1, json_encode(array("user_id"=>$user_id,"note_id"=>$insert)));
            } else {
                global $actLog; $actLog->add('Note',null,0, json_encode(array("user_id"=>$user_id,"note_id"=>$noteText)));
            }
        }
        
        echo json_encode(array("statusCode"=>203));
    }
	mysqli_close($DB_admin);

?>