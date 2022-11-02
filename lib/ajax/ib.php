<?php

/**
 * IB Functions Class
 * 9:57 AM Friday, August 13, 2021 | Milad
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Add new group
function newGroup() {
    $output = new stdClass();
    if( !isset($_POST['group_name']) || !isset($_POST['broker'])) $output->e = 'Check your form!';
    global $db;
    if (!isset($output->e)){
        $insert['name'] = $_POST['group_name'];
        $insert['broker_id'] = $_POST['broker'];
        $rebates = array();

        // MT4 rebate rate
        if($_POST['mt4_s'] ?? false) foreach ($_POST['mt4_s'] as $k => $symbol) {
            $rebates['mt4'][] = [
                $symbol,
                $_POST['mt4_v'][$k]
            ];
        }
        // MT5 rebate rate
        if($_POST['mt5_s'] ?? false) foreach ($_POST['mt5_s'] as $k => $symbol) {
            $rebates['mt5'][] = [
                $symbol,
                $_POST['mt5_v'][$k]
            ];
        }
        if($rebates) $insert['rebates'] = json_encode($rebates);
        $output->res = $db->insert('ib_groups', $insert);
        if(!$output->res) $output->e = 'Group exist on broker.';
    }
    echo json_encode($output);
}

// Edit group
function editGroup() {
    $output = new stdClass();
    if( !isset($_POST['group_id']) || !isset($_POST['broker']) || !isset($_POST['group_name'])) {
        $output->e = 'Check your form!';
    } else {
        $group_id = $_POST['group_id'];
        global $db;
        $rebates = array();

        $update['name'] = $_POST['group_name'];
        $update['broker_id'] = $_POST['broker'];

        // MT4 rebate rate
        if($_POST['mt4_s'] ?? false) foreach ($_POST['mt4_s'] as $k => $symbol) {
            $rebates['mt4'][] = [
                $symbol,
                $_POST['mt4_v'][$k]
            ];
        }
        // MT5 rebate rate
        if($_POST['mt5_s'] ?? false) foreach ($_POST['mt5_s'] as $k => $symbol) {
            $rebates['mt5'][] = [
                $symbol,
                $_POST['mt5_v'][$k]
            ];
        }
        if($rebates) $update['rebates'] = json_encode($rebates);
        $output->res = $db->updateId('ib_groups', $group_id, $update);

    }
    echo json_encode($output);
}

// Delete group
function deleteGroup() {
    $output = new stdClass();
    if( !isset($_POST['groupId']) ) {
        $output->e = 'Check your form!';
    } else {
        $group_id = $_POST['groupId'];
        global $db;
        $where = "group_id=$group_id";
        $ibs = $db->exist('ibs_list',$where);
        if($ibs) {
            $output->e = 'Can not delete group, there are some members in this group!';
        } else {
            $output->res = $db->deleteId('ib_groups', $group_id);
        }
    }
    echo json_encode($output);
}

// Add Member
function addMember() {
    $output = new stdClass();
    if( !isset($_POST['group_id']) || !isset($_POST['ib_id']) ) {
        $output->e = 'Check your form!';
    } else {
        $insert['id'] = $_POST['ib_id'];
        $insert['group_id'] = $_POST['group_id'];
        global $db;
        $db->insert('ibs_list',$insert, true);
        $output->res = true;
    }
    echo json_encode($output);
}

// Remove Member
function removeMember() {
    $output = new stdClass();
    if( !isset($_POST['group_id']) || !isset($_POST['ib_id']) ) {
        $output->e = 'Check your form!';
    } else {
        $id = $_POST['ib_id'];
        $update['group_id'] = null;
        global $db;
        $db->updateId('ibs_list', $id, $update);
        $output->res = true;
    }
    echo json_encode($output);
}

// Load Contract Modal
function myContracts(){
    include_once "raw/contracts.php";
}

// Add new Contract
function newContract(){
    $output = new stdClass();
    global  $db;
    if(!isset($_POST['uid'])) $output->e = 'uid';
    if (!$output->e && $_FILES['contract']) {
        $filename = date('dmYhis').'_'.strtolower($_FILES['contract']['name']);
        $location = "../media/contracts/".$filename;
        $fileType = pathinfo($location,PATHINFO_EXTENSION);
        $valid_extensions = array("pdf","doc","docx","png","jpeg","jpg");
        if (in_array(strtolower($fileType),$valid_extensions)) {
            if (move_uploaded_file($_FILES['contract']['tmp_name'],$location)){
                $insert['user_id'] = $_POST['uid'];
                $insert['filename'] = $filename;
                $insert['has_signed'] = ($_POST['has_signed']) ?? 0;
                $insert['comment'] = ($_POST['comment']) ?? null;
                $insert['status'] = ($_POST['status']) ?? 'pending';
                $insert['created_by'] = $_SESSION['id'];
                $output->res = $db->insert('ib_contracts', $insert);
                global $actLog; $actLog->add('Contracts', $output->res, 1, json_encode($_POST));
            }
        } else {
            $output->e = 'File Extensions';
        }
    }
    echo json_encode($output);
}

// Remove Contract
function deleteContract() {
    $output = new stdClass();
    global  $db;
    if( !isset($_POST['id']) ) {
        $output->e = 'Check your form!';
    } else {
        $id = $_POST['id'];
        $db->deleteId('ib_contracts', $id);
        global $actLog; $actLog->add('Contracts', $id, 1, '{"action":"deleted"');
        $output->res = true;
    }
    echo json_encode($output);
}

// Update Contract
function updateContract() {
    $output = new stdClass();
    global  $db;
    if( !isset($_POST['id']) || !isset($_POST['status']) ) {
        $output->e = 'Check your form!';
    } else {
        $update['updated_by'] = $_SESSION['id'];
        $update['status'] = $_POST['status'];
        $id = $_POST['id'];
        $db->updateId('ib_contracts', $id, $update);
        global $actLog; $actLog->add('Contracts', $id, 1, json_encode($_POST));
        $output->res = true;
    }
    echo json_encode($output);
}