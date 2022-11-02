<?php


$page['title'] = 'Load Message';

$_AJAX_ON = true;
$_API_ON  = false;

global $_RUN;
global $db_aifx;

if (!$_SESSION['id']) {
    $where = "session_id='".SESSION_ID."'";
    $table = 'messages_archive';
} else {
    $where = 'user_id='.$_SESSION['id'];
    $table = 'messages_archive';
}
$messages = $db_aifx->select($table, $where,'id,input_text,time,favorite',0,'id desc');

if ($messages) {
    $_RUN->res = $messages;
} else {
    $_RUN->e = end($db_aifx->log());
}