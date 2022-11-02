<?php

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Delete Device
function deleteDevice() {
    $output = new stdClass();
    if( !isset($_POST['id']) ) {
        $output->e = 'Check your request!';
    } else {
        $id = $_POST['id'];
        global $db;
        $output->res = $db->deleteId('devices', $id);
        // Add actLog
        global $actLog; $actLog->add('Device', $id, $output->res, 'Deleted');
    }
    echo json_encode($output);
}

// Get Device
function getDevice() {
    $output = new stdClass();
    if( !isset($_POST['id']) ) {
        $output->e = 'Check your request!';
    } else {
        $id = $_POST['id'];
        global $db;
        $res = $db->selectId('devices', $id);
        global $userManager;
        $res['use_by']    = $userManager->get($res['use_by'])['username'];
        $res['manage_by'] = $userManager->get($res['manage_by'])['username'];
        $output->res = $res;
    }
    echo json_encode($output);
}

// Update Device
function updateDevice() {
    $output = new stdClass();
    if( !isset($_POST['id']) ) {
        $output->e = 'Check your request!';
    } else {
        $id = $_POST['id'];
        unset($_POST['id']);
        global $userManager;
        $_POST['use_by']    = $userManager->getUsername($_POST['use_by'])['id'];
        $_POST['manage_by'] = $userManager->getUsername($_POST['manage_by'])['id'];
        global $db;
        $output->res = $db->updateId('devices', $id, $_POST);
        global $actLog; $actLog->add('Device', $id, $output->res, 'Updated');
    }
    echo json_encode($output);
}

// Add Device
function addDevice() {
    $output = new stdClass();
    if( !isset($_POST['id']) ) {
        $output->e = 'Check your request!';
    } else {
        global $userManager;
        $_POST['use_by']    = $userManager->getUsername($_POST['use_by'])['id'];
        $_POST['manage_by'] = $userManager->getUsername($_POST['manage_by'])['id'];
        global $db;
        $output->res = $db->insert('devices', $_POST);
        global $actLog; $actLog->add('Device', $output->res, $output->res, 'Added');
    }
    echo json_encode($output);
}
