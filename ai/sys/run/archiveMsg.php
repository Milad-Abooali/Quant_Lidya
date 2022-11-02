<?php

    $page['title'] = 'Archive Message';

    $_AJAX_ON = true;
    $_API_ON  = false;

    global $_RUN;
    global $db_aifx;

    // Session Change
    $old_session_id = session_id();
    session_write_close();
    session_id($_REQUEST['sessionId']);
    session_start();

    $msg_id = $_REQUEST['msg_id'];
    $msg    = $_REQUEST['msg'];
    $res    = $_REQUEST['res'];

    if ($msg_id) {
        $update = [
            'msg'    => $msg,
            'res'    => $res
        ];
        $result = $db_aifx->updateId('messages_archive', $msg_id, $update);
        $_RUN->res = $result;
    }
    else {
        $_RUN->e = "Who are you!";
    }

    // Session Change revers
    session_write_close();
    session_id($old_session_id);
    session_start();