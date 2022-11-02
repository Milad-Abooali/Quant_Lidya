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

// Get current user Notify
function getNotify() {
    $output = new stdClass();
    $output->e = false;
    $db = new iSQL(DB_admin);

    $where = 'receiver='.$_SESSION["id"]." AND seen_time is NULL";
    $notification = $db->select('notify',$where);
    $notify_types = $db->select('notify_type');
    if ($notification) foreach ($notification as $key => $notify) {
        $notify_type = $notify['notify_type'] -1;
        $notification[$key]['type'] = $notify_types[$notify_type]['name'];
        $notification[$key]['cat'] = $notify_types[$notify_type]['cat'];
        $notification[$key]['n_text'] = $notify_types[$notify_type]['notify_text'];
        
        // Transaction Details Start
        if(in_array($notify_type,array('2','3','4'))){
            $notification[$key]['details'] = $db->selectId('transactions',$notify['notify_data']);
            $userManager = new userManager();
            $notification[$key]['user'] = $userManager->getCustom($notification[$key]['details']['user_id'],'email')['email'];
            //$notification[$key]['user'] = $db->selectId('users',$notification[$key]['details']['user_id']);
        }
        // Transaction Details End
    }
    $output->res = $notification;
    echo json_encode($output);
}

// New Leads Added
    function addLeads() {
        $units = ($_POST) ?? false;
        $output = new stdClass();
        $output->e = ($units) ? false : true;
        if (!$output->e) {
            $notify = new notify();
            $db = new iSQL(DB_admin);
            if ($units) foreach ($units as $unit => $count) {
                $table = "user_extra";
                $res = $db->select($table,"unit=$unit AND type=6",'user_id');
                $list=array();
                if ($res) foreach($res as $man) $list[]= $man['user_id'];
                if ($list) $receivers = implode(",", $list);
                $output->res[] = $notify->addMulti('excelA','5',$count,$receivers);
            }
        }
        echo json_encode($output);
    }


// New Leads Register
function register() {
    $output = new stdClass();
    $output->e = !(($_POST['unit_id']) ?? false);
    if (!$output->e) {
        $notify = new notify();
        global $db;
        $unit = $db->escape($_POST[0]);
        $res = $db->select('user_extra',"unit=$unit AND type=6",'user_id');
        $list=array();
        if ($res) foreach($res as $man) $list[]= $man['user_id'];
        if ($list) $receivers = implode(",", $list);
        $output->res[] = $notify->addMulti('Register','5',1, $receivers);
    }
    echo json_encode($output);
}

// New Leads Register
    function regLeads() {
        $unit = ($_POST['$unit']) ?? false;
        $notify = new notify();
        $output = new stdClass();
        $output->e = ($units) ? false : true;
        $db = new iSQL(DB_admin);
        $table = "user_extra";
        $res = $db->select($table,"unit=$unit AND type=6",'user_id');
        $list=array();
        if ($res) foreach($res as $man) $list[]= $man['user_id'];
        if ($list) $receivers = implode(",", $list);
        $output->res = $notify->addMulti('Register','5',1,$receivers);
        echo json_encode($output);
    }

// Seen
    function seen() {
        $output = new stdClass();
        $output->e = false;
        global  $notify;
        $id = ($_POST['id']) ?? false;
        $output->res = $notify->seen($id);
        echo json_encode($output);
    }