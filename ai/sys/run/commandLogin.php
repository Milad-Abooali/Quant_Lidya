<?php
/**
 * Command Login
 */


$page['title'] = 'Command Login';

$_AJAX_ON = true;
$_API_ON  = false;

$username = $_REQUEST['username'] ?? false;
$password = $_REQUEST['password'] ?? false;

$timeoffset = 180;

if (!$username || !$password) $error = true;

if(!$sess->IS_LOGIN) $sess->login($timeoffset, $username, $password, false, false);

if($sess->IS_LOGIN) {
    $user = new userManager();
    $_RUN->user = $user->get($_SESSION['id']);
    $where = "type='avatar' AND user_id=".$_SESSION['id'];
    $media = $db->selectRow('media',$where);
    if ($media['media']) $_RUN->avatar = '../../media/'.$media['media'];
}
$_RUN->e = $error;
$_RUN->IS_LOGIN = $sess->IS_LOGIN;
$_RUN->ERROR = $sess->ERROR;