<?php
/**
 * Command Login
 */


$page['title'] = 'Command Register';

$_AJAX_ON = true;
$_API_ON  = false;

$timeoffset = 180;

if(!$sess->IS_LOGIN) {
    $register = $sess->register();
    if ($register->res>0) $sess->login($timeoffset, $_POST['email'], $register->pass, false, false);
}

if($sess->IS_LOGIN) {
    $user = new userManager();
    $_RUN->user = $user->get($_SESSION['id']);
    $where = "type='avatar' AND user_id=".$_SESSION['id'];
    $media = $db->selectRow('media',$where);
    if ($media['media']) $_RUN->avatar = '../../media/'.$media['media'];
}
$_RUN->e = false;
$_RUN->registeredId = $register->res;
$_RUN->IS_LOGIN = $sess->IS_LOGIN;
$_RUN->ERROR = $register->e ?? ($sess->ERROR ?? null) ;