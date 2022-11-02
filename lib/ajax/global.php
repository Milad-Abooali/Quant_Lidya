<?php

/**
 * Global Functions Class
 * 11:13 AM Wednesday, November 11, 2020 | M.Abooali
 */

// on null call
  function noF() {
      $output = new stdClass();
      $output->e = false;
      $output->res = $_POST;
      echo json_encode($output);
  }

// Captcha
function captcha() {
    include_once 'captcha/captcha.php';
    unset($_SESSION['captcha']);
    $_SESSION['captcha_length']++;
    $_SESSION['captcha'] = simple_php_captcha();
    echo ($_SESSION['captcha']['image_src']);
}


// Load Broker
function getBroker() {
    $error = !(($_POST['id']) ?? false);
    if (!$error) include_once "raw/broker.php";
    return (!$error);
}

// Update Broker Maintenance Mod
function setBrokerMaintenance() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    if (!$output->e) {
        $update['maintenance'] = ($_POST['maintenance']) ?? 0;
        global $db;
        $output->res = $db->updateId('brokers', $_POST['id'], $update);
    }
    global $actLog; $actLog->add('Broker',(($_POST['id']) ?? null),(($_POST['id']) ? 1 : 0), json_encode($_POST));
    echo json_encode($output);
}

// Delete Broker
function deleteBroker() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    if (!$output->e) {
        global $db;
        $deleted = $db->selectId('brokers', $_POST['id']);
        $output->res = $db->deleteId('brokers', $_POST['id']);
    }
    global $actLog; $actLog->add('Broker',(($_POST['id']) ?? null),(($_POST['id']) ? 1 : 0), json_encode($deleted));
    echo json_encode($output);
}

// Delete Unit
function deleteUnit() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    if (!$output->e) {
        global $db;
        $deleted = $db->selectId('units', $_POST['id']);
        $output->res = $db->deleteId('units', $_POST['id']);
    }
    global $actLog; $actLog->add('Broker',(($_POST['id']) ?? null),(($_POST['id']) ? 1 : 0), json_encode($deleted));
    echo json_encode($output);
}

// Add Unit
function newUnit() {
    $output = new stdClass();
    $output->e = !(($_POST['name']) ?? false);
    $output->e = !(($_POST['broker_id']) ?? false);
    $insert = array();
    if (!$output->e) {
        $insert = $_POST;
        global $db;
        $output->res = $db->insert('units', $insert);
    }
    global $actLog; $actLog->add('Broker',(($output->res) ?? null),(($output->res) ? 1 : 0), json_encode($insert));
    echo json_encode($output);
}

// Add Broker
function newBroker() {
    $output = new stdClass();
    $output->e = !(($_POST['title']) ?? false);
    $insert = array();
    if (!$output->e) {
        $insert = $_POST;
        if ($_FILES) {
            foreach($_FILES as $key => $file) {
                $filename = strtolower($_FILES[$key]['name']);
                $location = "../media/broker/".$filename;
                $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
                $valid_extensions = array("jpg","jpeg","png","gif","ico");
                if (in_array(strtolower($imageFileType),$valid_extensions)) if (move_uploaded_file($_FILES[$key]['tmp_name'],$location)) $insert[$key] = $filename;
            }
        }
        global $db;
        $output->res = $db->insert('brokers', $insert);
    }
    global $actLog; $actLog->add('Broker',(($output->res) ?? null),(($output->res) ? 1 : 0), json_encode($insert));
    echo json_encode($output);
}

// Update Broker
function updateBroker() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    if (!$output->e) {
        $update = $_POST;
        $update['captcha']     = ($_POST['captcha']) ?? 0;
        $update['pin_lock']    = ($_POST['pin_lock']) ?? 0;
        $update['maintenance'] = ($_POST['maintenance']) ?? 0;
        $update['edit_email']  = ($_POST['edit_email']) ? implode(',', $_POST['edit_email']) : 'Admin';
        $update['upload_docs'] = ($_POST['upload_docs']) ? implode(',', $_POST['upload_docs']) : 'Admin';
        if ($_FILES) {
            foreach($_FILES as $key => $file) {
                $filename = strtolower($_FILES[$key]['name']);
                $location = "../media/broker/".$filename;
                $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
                $valid_extensions = array("jpg","jpeg","png","gif","ico");
                if (in_array(strtolower($imageFileType),$valid_extensions)) if (move_uploaded_file($_FILES[$key]['tmp_name'],$location)) $update[$key] = $filename;
            }
        }
        global $db;
        $output->res = $db->updateId('brokers', $_POST['id'], $update);
    }
    global $actLog; $actLog->add('Broker',(($_POST['id']) ?? null),(($_POST['id']) ? 1 : 0), json_encode($_POST));
    echo json_encode($output);
}

// Widget
function widget() {
    $error = !(($_POST[1]) ?? false);
    if (!$error) include_once "widgets/".$_POST[1];
    return (!$error);
}

// Lock the screen
  function lockScreenOn() {
      $output = new stdClass();
      $output->e = false;
      $_SESSION["locksess"] = true;
      $output->res = $_SESSION["locksess"];
      echo json_encode($output);
  }

// Open the screen
  function lockScreenOff() {
      $output = new stdClass();
      $output->e = false;
      $_SESSION["locksess"] = false;
      $output->res = $_SESSION["locksess"];
      echo json_encode($output);
  }

// Agree with terms
function agreeTerms() {
    $output = new stdClass();
    $output->e = false;
    global $db;
    $_SESSION["date_approve"] = $data['date_approve'] = $db->DATE;
    $where = 'user_id='.$_SESSION['id'];
    $output->res = $db->updateAny('user_extra',$data,$where);

    echo json_encode($output);
}


// Agree Set From Admin
function agreeSet() {
    $output = new stdClass();
    $output->e = false;
    global $db;
    $data['date_approve'] = ($_POST['status']) ? $db->DATE : null;
    $where = 'user_id='.$_POST['id'];
    $output->res = $db->updateAny('user_extra',$data,$where);

    echo json_encode($output);
}

// Agree E-Book From Admin
function allowSet() {
    $output = new stdClass();
    $output->e = false;
    global $db;
    $data['cid'] = ($_POST['status']) ? $db->DATE : null;
    $output->res = $db->updateId('users',$_POST['id'],$data);

    echo json_encode($output);
}

// Leader Board
function leaderBoardFTD() {
    $output = new stdClass();
    $output->e = false;
    if ($_SESSION["type"] == "Admin") {
        $sql = "SELECT conversion agent_id,(SELECT CONCAT(fname,' ',lname) FROM `user_extra` WHERE user_id = tp.conversion) agent_name, SUM(ftd_amount) amount,COUNT(ftd_amount) count FROM `tp` tp
            WHERE MONTH(ftd) = MONTH(now()) AND YEAR(ftd) = YEAR(now())
            GROUP BY conversion ORDER BY SUM(ftd_amount) DESC, COUNT(ftd_amount) DESC, conversion DESC";
    } else if ($_SESSION["type"] !== "Leads" && $_SESSION["type"] !== "IB" && $_SESSION["type"] !== "Trader") {
        $sql = "SELECT tp.conversion agent_id,(SELECT CONCAT(fname,' ',lname) FROM `user_extra` WHERE user_id = tp.conversion) agent_name, SUM(tp.ftd_amount) amount,COUNT(tp.ftd_amount) count FROM `tp` tp LEFT JOIN user_extra user_extra USING(user_id)
            WHERE MONTH(ftd) = MONTH(now()) AND YEAR(ftd) = YEAR(now()) AND user_extra.unit = '".$_SESSION["unitn"]."'
            GROUP BY tp.conversion ORDER BY SUM(tp.ftd_amount) DESC, COUNT(tp.ftd_amount) DESC, tp.conversion DESC";
    }
    global $DB_admin;
    $result = $DB_admin->query($sql);
    $i=1;
    if($result) while ($row = mysqli_fetch_array($result)) {
        $output->res[$row['agent_id']] = array(
            'agent_id' => $row['agent_id'],
            'rank' => $i++,
            'agent_name' => $row['agent_name'],
            'amount' => $row['amount'],
            'count' => $row['count']
        );
    }
    // No FTD Agents
    if ($_SESSION["type"] == "Admin") {
        $sql = "SELECT CONCAT(fname,' ',lname) agent_name, user_id agent_id FROM `user_extra` WHERE type IN (9,6,8) AND status != 9";
    } else if ($_SESSION["type"] !== "Leads" && $_SESSION["type"] !== "IB" && $_SESSION["type"] !== "Trader") {
        $sql = "SELECT CONCAT(fname,' ',lname) agent_name, user_id agent_id FROM `user_extra` WHERE type IN (9,6,8) AND unit = '".$_SESSION["unitn"]."' AND status != 9";
    }
    $agent_list = $DB_admin->query($sql);
    if($agent_list) while ($row = mysqli_fetch_array($agent_list)) {
        if($output->res[$row['agent_id']]) continue;
        $output->res[$row['agent_id']] = array(
            'agent_id' => $row['agent_id'],
            'rank' => $i++,
            'agent_name' => $row['agent_name'],
            'amount' => 0,
            'count' => 0
        );
    }
    echo json_encode($output);
}

// Return Staff Selector
function selectorStaff(){
    global $db;
    $unit   = $_POST['unit'] ?? null;
    $type   = $_POST['type'] ?? null;
    $status = $_POST['status'] ?? null;
    $where='';
    if($unit) $where    .= ' unit="'.$unit.'" ';
    if($type) $where    .= ' type="'.$type.'" ';
    if($status) $where  .= ' status="'.$status.'" ';
    $list_staff=array();
    $units = $db->select('units', $where);
    foreach($units as $unit){
        $where = "unit=".$unit['id'];
        $list_staff[$unit['name']] = $db->select('staff_list', $where);
    }
    $output['res']='';
    if($list_staff) foreach ($list_staff as $unit=>$staffs){
        $output['res'] .= '<optgroup label="'.$unit.'">';
        if($staffs) foreach($staffs as $staff) $output['res'] .= '<option value="'.$staff['id'].'">'.$staff['email'].'</option>';
        $output['res'] .= '</optgroup>';
    }
    echo json_encode($output);
}

// Return IB Selector
function selectorByType(){
    global $db;
    $where = 'broker_id='.Broker['id'];
    $units = $db->select('units', $where);
    $type   = $_POST['type'] ?? null;
    $list_user=array();
    foreach($units as $unit){
        $where  = "unit='".$unit['name']."' ";
        $where  .= 'AND type="'.$type.'" ';
        $list_user[$unit['name']] = $db->select('users', $where);
        $output['where'][] = $where;
    }
    $output['res']='';
    if($list_user) foreach ($list_user as $unit=>$users){
        $output['res'] .= '<optgroup label="'.$unit.'">';
        if($users) foreach($users as $user) $output['res'] .= '<option value="'.$user['id'].'">'.$user['email'].'</option>';
        $output['res'] .= '</optgroup>';
    }
    echo json_encode($output);
}
