<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";

global $userManager;

//$params["Login"] = 101078;
//$params["Name"] = "John Smith";
//$params["LastName"] = "Jones";
//$userManager->syncMT5($params);

/**
 * Escape User Input Values POST & GET
 */
GF::escapeReq();

if ($_SESSION["captcha_force"] && (strtoupper($_POST['captcha']) != $_SESSION['captcha']['code'])) {
    $_SESSION['captcha_length']++;
    echo json_encode(array("statusCode"=>201,"error"=>"You have entered the wrong captcha !"));
    exit();
}
    // Escape All
    GF::escapeReq();

    // Act LOG
    if($_POST['user_id'] ?? false) {
        $_act_old_detail = $db->selectId('users',$_POST['user_id']);
        $where = 'user_id='.$_POST['user_id'];
        $_act_old_detail = array_merge($_act_old_detail, $db->selectRow('user_extra',$where));
        $_act_old_detail = array_merge($_act_old_detail, $db->selectRow('user_fx',$where));
        $_act_old_detail = array_merge($_act_old_detail, $db->selectRow('user_gi',$where));
        $_act_old_detail = array_merge($_act_old_detail, $db->selectRow('user_marketing',$where));
    }

    if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent")
    {
        $user_id = $_POST['user_id'] ?? false;
        $retention = $_POST['retention'] ?? 0;
        $conversion = $_POST['conversion'] ?? 0;
        $affiliate = $_POST['affiliate'] ?? 0;
        
        GF::updateFtd($user_id, $retention, $conversion, null, null, $affiliate);
        
        $turkish = array("ı", "ğ", "ü", "ş", "ö", "ç", "Ğ", "İ", "Ş", "Ö", "Ü", "Ç");//turkish letters
        $english = array("i", "g", "u", "s", "o", "c", "G", "I", "S", "O", "U", "C");//english cooridinators letters

        if(in_array($_SESSION["type"] ,Broker['edit_email'])){
            $email = $_POST['email'];
        } else {
            $email = $_act_old_detail['email'];
        }
    
    	$fname = htmlspecialchars($_POST['fname'], ENT_NOQUOTES, "UTF-8");
    	$fname = str_replace($turkish, $english, $fname);
        $lname = htmlspecialchars($_POST['lname'], ENT_NOQUOTES, "UTF-8");
        $lname = str_replace($turkish, $english, $lname);

        if(!$userManager->ftdCheck($_POST['user_id'])){
            $params["Name"] = $fname." ".$lname;
            $userManager->syncMT5($_POST['user_id'],$params,'{"FirstName":'.$fname.',"LastName":'.$lname.'}');
        }


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
    	$user_id = $_POST['user_id'];
    	
    	$sql5 = "UPDATE user_extra SET fname = '$fname', lname = '$lname', phone = '$phone', country = '$country', city = '$city', address = '$address', interests = '$interests', hobbies = '$hobbies', unit = '$userunit', retention = '$retention', conversion = '$conversion', status = '$status', type = '$type', followup = '$followup', updated_at = '$date', updated_by = '".$_SESSION["id"]."' WHERE user_id = '$user_id'";
        $sql6 = "INSERT INTO user_fx (user_id,job_cat,job_title,exp_fx,exp_fx_year,exp_cfd,exp_cfd_year,income,investment,strategy,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$job_cat','$job_title','$exp_fx','$exp_fx_year','$exp_cfd','$exp_cfd_year','$income','$investment','$strategy','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."') ON DUPLICATE key UPDATE job_cat = '$job_cat', job_title = '$job_title', exp_fx = '$exp_fx', exp_fx_year = '$exp_fx_year', exp_cfd = '$exp_cfd', exp_cfd_year = '$exp_cfd_year', income = '$income', investment = '$investment', strategy = '$strategy', updated_at = '$date', updated_by = '".$_SESSION["id"]."'";
    	$sql7 = "INSERT INTO user_gi (user_id,bd,whatsapp,telegram,facebook,instagram,twitter,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$bd','$whatsapp','$telegram','$facebook','$instagram','$twitter','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."') ON DUPLICATE key UPDATE bd = '$bd', whatsapp = '$whatsapp', telegram = '$telegram', facebook = '$facebook', instagram = '$instagram', twitter = '$twitter', updated_at = '$date', updated_by = '".$_SESSION["id"]."'";
    	//$sql7 = "UPDATE user_gi SET bd = '$bd', whatsapp = '$whatsapp', telegram = '$telegram', facebook = '$facebook', instagram = '$instagram', twitter = '$twitter', updated_at = '$date', updated_by = '".$_SESSION["id"]."' WHERE user_id = '$user_id'";
    	$sql8 = "INSERT INTO user_marketing (user_id,lead_src,lead_camp,affiliate,created_at,created_by,updated_at,updated_by) VALUES ('$user_id','$source','$campaign','$affiliate','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."') ON DUPLICATE key UPDATE lead_src = '$source', lead_camp = '$campaign', affiliate = '$affiliate', updated_at = '$date', updated_by = '".$_SESSION["id"]."'";

    	$sql9 = "UPDATE users SET username='$email', email='$email', type = '$typeN', unit = '$unit' WHERE id = '$user_id'";

    	//echo $sql7;
    	if (mysqli_query($DB_admin, $sql5) && mysqli_query($DB_admin, $sql6) && mysqli_query($DB_admin, $sql7) && mysqli_query($DB_admin, $sql8) && mysqli_query($DB_admin, $sql9)) {
            $_act_status = 1;
            echo json_encode(array("statusCode"=>200));
    	} 
    	else {
            $_act_status = 0;
            echo mysqli_error($DB_admin);
    		echo json_encode(array("statusCode"=>201));
    	}
    	mysqli_close($DB_admin);

        
        	
    } else {
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
        if ($units) while ($rowUNIT = mysqli_fetch_array($units)) {
            $unit = $rowUNIT['name'];   
        }
        $retention = $_POST['retention'];
        $conversion = $_POST['conversion'];
        $status = $_POST['status'];
        $type = $_POST['type'];
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
    	$user_id = $_POST['user_id'];
    	
    	$sql5 = "UPDATE user_extra SET fname = '$fname', lname = '$lname', phone = '$phone', country = '$country', city = '$city', address = '$address', interests = '$interests', hobbies = '$hobbies', followup = '$followup', updated_at = '$date', updated_by = '".$_SESSION["id"]."' WHERE user_id = '$user_id'";
        $sql6 = "UPDATE user_fx SET job_cat = '$job_cat', job_title = '$job_title', exp_fx = '$exp_fx', exp_fx_year = '$exp_fx_year', exp_cfd = '$exp_cfd', exp_cfd_year = '$exp_cfd_year', income = '$income', investment = '$investment', strategy = '$strategy', updated_at = '$date', updated_by = '".$_SESSION["id"]."' WHERE user_id = '$user_id'";
    	$sql7 = "UPDATE user_gi SET bd = '$bd', whatsapp = '$whatsapp', telegram = '$telegram', facebook = '$facebook', instagram = '$instagram', twitter = '$twitter', updated_at = '$date', updated_by = '".$_SESSION["id"]."' WHERE user_id = '$user_id'";
    	
    	//echo $sql5;
    	if (mysqli_query($DB_admin, $sql5) && mysqli_query($DB_admin, $sql6) && mysqli_query($DB_admin, $sql7)) {
    	    $_act_status = 1;
    		echo json_encode(array("statusCode"=>200));
    	} 
    	else {
            $_act_status = 0;
            echo mysqli_error($DB_admin);
    		echo json_encode(array("statusCode"=>201));
    	}
    	mysqli_close($DB_admin);
    }

    // Profile Rate
    if ($_act_status) GF::profileRateCal(GF::getUserProfile($_POST['user_id']));

    // Act LOG
    if (is_array($_act_old_detail)) $_act_changes = array_diff($_act_old_detail, $_POST);
    if(($_act_changes) ?? false) {
        foreach ($_act_changes as $k =>$v) {
            if (($_act_old_detail[$k]!=0 || $_act_old_detail[$k]!=null) && ($_POST[$k]!=0 || $_POST[$k]!=null)) $_act_detail[$k] = array('old'=>$_act_old_detail[$k],'New'=>$_POST[$k]);
        }
        unset($_act_detail['id']);
        unset($_act_detail['username']);
        unset($_act_detail['password']);
        unset($_act_detail['email']);
        unset($_act_detail['platform']);
        unset($_act_detail['platform_d']);
        unset($_act_detail['pa']);
        unset($_act_detail['cid']);
        unset($_act_detail['token']);
        unset($_act_detail['pincode']);
        unset($_act_detail['profile_rate']);
        unset($_act_detail['groups']);
        unset($_act_detail['ip']);
        unset($_act_detail['lastnotedate']);
        unset($_act_detail['assigned_date']);
        unset($_act_detail['assigned_date_ret']);
        unset($_act_detail['created_by']);
        unset($_act_detail['created_at']);
        unset($_act_detail['updated_by']);
        unset($_act_detail['updated_at']);
        unset($_act_detail['need_update']);
    }
    global $actLog;
    if($_act_detail['conversion']) {
        $_act_detail_['conversion'] = $_act_detail['conversion'];
        $_act_detail_ = (json_encode($_act_detail_)) ?? null;
        $actLog->add('Assign', $_POST['user_id'], $_act_status, $_act_detail_);
        unset($_act_detail['conversion']);
    }
    if($_act_detail['retention']) {
        $_act_detail_['retention'] = $_act_detail['retention'];
        $_act_detail_ = (json_encode($_act_detail_)) ?? null;
        $actLog->add('Assign', $_POST['user_id'], $_act_status, $_act_detail_);
        unset($_act_detail['retention']);
    }
    if($_act_detail) {
        $_act_detail = (json_encode($_act_detail)) ?? null;
        $actLog->add('Edit', $_POST['user_id'], $_act_status, $_act_detail);
    }

    //  Follow up Temp Table


    $times['now']        = date('Y-m-d H:i:s');
    $followup_and_offset = strtotime($_POST['followup'])-$_SESSION["timeoffset"];
    $times['followup']   = date('Y-m-d H:i:s', $followup_and_offset);

    if($times['now'] > $times['followup']) {
        $data['user_id'] = $_POST['user_id'];
        $data['followup'] = $times['followup'];
        $db->insert('user_followup',$data);
    }

?>