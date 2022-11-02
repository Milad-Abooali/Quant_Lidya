<?php
/**
 * Command REcover Password
 */


$page['title'] = 'Command Password';

$_AJAX_ON = true;
$_API_ON  = false;

$email = $_REQUEST['email'] ?? false;

if (!$email) $error = true;

$email = $db->escape($_POST['email']);
// Check if exist
$where = "email='$email' AND unit IN (".Broker['units'].")";
$user = $db->selectRow('users',$where);

if ($user) {
    $up_token['token'] = bin2hex(random_bytes(50));
    $db->updateId('users', $user['id'], $up_token);
    // Send Email
    global $_Email_M;
    $subject = 'Reset your password on '. Broker['title'];
    $receivers[] = array (
        'id'    =>  $user['id'],
        'email' =>  $user['email'],
        'data'  =>  array(
            'broker_title' =>  Broker['title'],
            'broker_crm' =>  Broker['crm_url'],
            'token' =>  $up_token['token']
        )
    );
    $theme = 'pass_recovery';
    $_Email_M->send($receivers, $theme, $subject);
    $_RUN->res = 1;
} else {
    $error = "Sorry, no user exists on our system with that email";
}

// Add actLog
global $actLog;
$actLog->add('Recover Pass (AI)', ($user['id'] ?? false), (!$error), json_encode(array($email)));

$_RUN->ERROR = $_RUN->e = $error;