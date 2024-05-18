<?php

/**
 * Users Class
 * 11:13 AM Wednesday, November 18, 2020 | M.Abooali
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Get user
function get() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $usermanager = new usermanager();
    if (!$output->e) $output->res = $usermanager->get($_POST['name']);
    echo json_encode($output);
}

// Delete user
function delete() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $usermanager = new usermanager();
    if (!$output->e) $output->res = $usermanager->delete($_POST['id']);
    echo json_encode($output);
}

// Register user
function register() {
    global $db;
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

            // Check for duplicate email
            $where = "email='".$_POST['email']."' AND unit IN (".Broker['units'].")";
            $exist_email = $db->exist('users',$where);

            // Check for duplicate phone
            $phone_10 = $db->escape(substr($_POST['phone'],-10));
            $where = " RIGHT(phone,10)='$phone_10'";
            $exist_phone = $db->exist('user_extra', $where);

            if ($exist_email) {
                $output->e = "You have an other account in our site with this email address!";
            }
            else if($exist_phone) {
                $output->e = "You have an other account in our site with this phone number!";
            }
            else {

                $fname      = GF::charReplace('tr', $_POST['fname']);
                $lname      = GF::charReplace('tr', $_POST['lname']);
                $phone      = $_POST['phone'];
                $country    = $_POST['country'];
                $unit_id    = $_POST['unit_id'];
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
                    $insert_extra['created_by'] = $insert_id;
                    $insert_extra['updated_at'] = $date;
                    $insert_extra['updated_by'] = $insert_id;
                    $insert_extra['language'] = LANGUAGE_NAME;
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

                    $output->res = $insert_id;
                }

            }

        }

    }
    echo json_encode($output);
}

// Merge Users
function merge() {
    global $db;
    $output = new stdClass();
    // Check if not login
    if ($_SESSION["loggedin"]) $output->e = 'You need logout first!';
    // Check Captcha
    if (Broker['captcha'] && (strtoupper($_POST['captcha']) != $_SESSION['captcha']['code'])) {
        $_SESSION['captcha_length']++;
        $output->e = "You have entered the wrong captcha !";
    } else {
        $output->e = (($_POST['update']) ?? false) ? false : "Please check the selected data!";
        $output->e = (($_POST['users']) ?? false) ? false : "Please check the selected users!";
    }
    if(!$output->e){
        $main_data = $_POST['update'];
        $users = $_POST['users'];
        if(in_array($main_data['users']['id'], $users)) $main_user = $main_data['users']['id'];
        if(isset($main_user)){
            $userManager = new usermanager();
            // Update Main User
            $tps = $main_data['tp'];
            unset($main_data['tp']);
            $output->updateMainUser = boolval($userManager->setCustom($main_user, $main_data));
            // update tp
            if($tps) foreach ($tps as $tp){
                $tp = json_decode($tp,1);
                $update['user_id'] = $main_user;
                $where = 'login='.$tp['login']." AND server='".$tp['server']."'";
                $output->UpdateTp[$tp['login']] = $db->updateAny('tp',$update,$where);
            }
            // Drop Other users
            foreach ($users as $user){
                if($user==$main_user || $user<1 || $user==$_SESSION['id']) continue;
                $output->deleteUsers[$user] = $userManager->delete($user);
            }
        }
    }
    echo json_encode($output);
}

// Move Comments
function moveComments() {
    global $db;
    $output = new stdClass();
    // Check if not login
    if ($_SESSION["loggedin"]) $output->e = 'You need logout first!';
    // Check Captcha
    if (Broker['captcha'] && (strtoupper($_POST['captcha']) != $_SESSION['captcha']['code'])) {
        $_SESSION['captcha_length']++;
        $output->e = "You have entered the wrong captcha !";
    } else {
        $output->e = (($_POST['mainUser']) ?? false) ? false : "Please check the selected data!";
        $output->e = (($_POST['users']) ?? false) ? false : "Please check the selected users!";
    }
    if(!$output->e){
        $main_user = $_POST['mainUser'];
        $users = $_POST['users'];
        if(isset($main_user)){
            $userManager = new usermanager();
            // Update Main User
            if($users) {
                $users = implode(",", $users);
                $update['user_id'] = $main_user;
                $where = "user_id IN ($users)";
                $output->UpdateComments = $db->updateAny('notes', $update, $where);
            }
        }
    }
    echo json_encode($output);
}

// Move Emails
function moveEmails() {
    global $db;
    $output = new stdClass();
    // Check if not login
    if ($_SESSION["loggedin"]) $output->e = 'You need logout first!';
    // Check Captcha
    if (Broker['captcha'] && (strtoupper($_POST['captcha']) != $_SESSION['captcha']['code'])) {
        $_SESSION['captcha_length']++;
        $output->e = "You have entered the wrong captcha !";
    } else {
        $output->e = (($_POST['mainUser']) ?? false) ? false : "Please check the selected data!";
        $output->e = (($_POST['users']) ?? false) ? false : "Please check the selected users!";
    }
    if(!$output->e){
        $main_user = $_POST['mainUser'];
        $users = $_POST['users'];
        if(isset($main_user)){
            $userManager = new usermanager();
            // Update Main User
            if($users) {
                $users = implode(",", $users);
                $update['user_id'] = $main_user;
                $where = "user_id IN ($users)";
                $output->UpdateEmails = $db->updateAny('email_log', $update, $where);
            }
        }
    }
    echo json_encode($output);
}

// Move Logs
function moveLogs() {
    global $db;
    $output = new stdClass();
    // Check if not login
    if ($_SESSION["loggedin"]) $output->e = 'You need logout first!';
    // Check Captcha
    if (Broker['captcha'] && (strtoupper($_POST['captcha']) != $_SESSION['captcha']['code'])) {
        $_SESSION['captcha_length']++;
        $output->e = "You have entered the wrong captcha !";
    } else {
        $output->e = (($_POST['mainUser']) ?? false) ? false : "Please check the selected data!";
        $output->e = (($_POST['users']) ?? false) ? false : "Please check the selected users!";
    }
    if(!$output->e){
        $main_user = $_POST['mainUser'];
        $users = $_POST['users'];
        if(isset($main_user)){
            $userManager = new usermanager();
            // Update Main User
            if($users) {
                $users = implode(",", $users);
                $update['user_id'] = $main_user;
                $where = "user_id IN ($users)";
                $output->UpdateLogs = $db->updateAny('act_log_user', $update, $where);
            }
        }
    }
    echo json_encode($output);
}

// Move Sessions archive
function sessionsArchive() {
    global $db;
    $output = new stdClass();
    // Check if not login
    if ($_SESSION["loggedin"]) $output->e = 'You need logout first!';
    // Check Captcha
    if (Broker['captcha'] && (strtoupper($_POST['captcha']) != $_SESSION['captcha']['code'])) {
        $_SESSION['captcha_length']++;
        $output->e = "You have entered the wrong captcha !";
    } else {
        $output->e = (($_POST['mainUser']) ?? false) ? false : "Please check the selected data!";
        $output->e = (($_POST['users']) ?? false) ? false : "Please check the selected users!";
    }
    if(!$output->e){
        $main_user = $_POST['mainUser'];
        $users = $_POST['users'];
        if(isset($main_user)){
            $userManager = new usermanager();
            // Update Main User
            if($users) {
                $users = implode(",", $users);
                $update['user_id'] = $main_user;
                $where = "user_id IN ($users)";
                $output->UpdateSessionArchive = $db->updateAny('user_session', $update, $where);
            }
        }
    }
    echo json_encode($output);
}

// Remove user from merge list
function removeMerge() {
    $user_id = intval($_POST['id']);
    unset($_SESSION['M']['mergeUsers'][$user_id]);
    echo 1;
}

// Remove user from merge list
function removeMergeAll() {
    unset($_SESSION['M']['mergeUsers']);
    echo 1;
}

// Add user from merge list
function addMerge() {
    $user_id = intval($_POST['id']);
    $_SESSION['M']['mergeUsers'][$user_id]=$user_id;
    echo 1;
}

// Reset Password Request
function resetPasswordRequest() {
    global $db;
    $output = new stdClass();
    $output->e = (($_POST['user_id']) ?? false) ? false : "user id expected!";
    if(!$output->e){
        $up_token['token'] = bin2hex(random_bytes(50));
        $db->updateId('users', $_POST['user_id'], $up_token);
        // Send Email
        global $_Email_M;
        $receivers[] = array (
            'id'    =>  $_POST['user_id'],
            'email' =>  $_POST['email'],
            'data'  =>  array(
                'token' =>  $up_token['token']
            )
        );
        $subject = $theme = 'CRM_Reset_Password';
        $output->res = $_Email_M->send($receivers, $theme, $subject);
    }
    echo json_encode($output);
}

// Merge Duplicate users
function mergeDuplicates() {
    $output = new stdClass();
    $output->e = (($_POST['keepId']) ?? false) ? false : "keepId expected!";
    $output->e = (($_POST['target']) ?? false) ? false : "target expected!";
    $output->e = (($_POST['type']) ?? false) ? false : "type expected!";
    $output->e = (($_POST['users']) ?? false) ? false : "users expected!";
    if(!$output->e){
        global $db;
        global $userManager;
        $output->res = array();
        $users = explode(',',$_POST['users']);
        foreach($users as $user_id){
            if($user_id==$_POST['keepId']) continue;
            $update['user_id'] = $_POST['keepId'];
            $where =" user_id= $user_id ";
            $db->updateAny('notes',$update,$where);
            $userManager->delete($user_id);
            $output->res[] = "$user_id Merged.";
        }
    }
    echo json_encode($output);
}

// Temp Password
function tempPassword()
{
    global $db;
    $output = new stdClass();
    $output->e = (($_POST['id']) ?? false) ? false : "user id expected!";
    if (!$output->e) {
        $new_password = substr(hash('joaat', microtime()), 2);
        $password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $pa = GF::encodeAm($new_password);
        $update['password'] = $password_hashed;
        $update['pa'] = $pa;
        $update['fchange_pass'] = 1;
        $db->updateId('users', $_POST['id'], $update);
        $output->res = $new_password;
    }
    echo json_encode($output);
}
