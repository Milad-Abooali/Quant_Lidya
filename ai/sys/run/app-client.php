<?php

global $db;
global $db_aifx;

$page['title'] = 'APP Client';

$_AJAX_ON = true;
$_API_ON = false;

$action = $_POST['a'] ?? false;
if($action=='isUserExist'){
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $where = "username='$email'";
    $_RUN->isUserExist = $db->exist('users',$where);

    if($email){
        $guest['email'] = $email;
        $guest['session'] = SESSION_ID;
        $guest['is_user'] = $_RUN->isUserExist;
        $db_aifx->insert('guest_sessions', $guest);
    }

}