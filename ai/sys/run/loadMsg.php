<?php

    $page['title'] = 'Load Message';

    $_AJAX_ON = true;
    $_API_ON  = false;

    global $_RUN;
    global $db_aifx;

    $sessionId  = $_REQUEST['sessionId'];
    $userId     = $_REQUEST['userId'];
    $socketId   = $_REQUEST['socketId'];
    $date       = $_REQUEST['date'];


    if ($sessionId) {
        $table = 'messages_archive';

        $where  = "(session_id='$sessionId' OR socket_id='$socketId' ";
        if($userId) {
            $where .= "OR user_id='$userId'";
        } else {
            $where .= ") AND (user_id=0";
        }
        $where .= ") AND DATE(time)='".$date."'";
        $messages = $db_aifx->select($table, $where,'*',0,'id desc');

        $where  = "(session_id='$sessionId' OR socket_id='$socketId' ";
        if($userId) {
            $where .= "OR user_id='$userId'";
        } else {
            $where .= ") AND (user_id=0";
        }        $where .= ") AND DATE(time)<'".$date."'";
        $date_old = $db_aifx->selectRow($table, $where,'time desc')['time'];

        if ($messages) {
            $_RUN->res = [
                'date_old' => substr($date_old, 0, 10),
                'messages' => $messages
            ];
        } else {
            // No Message
            $_RUN->e = end($db_aifx->log());
        }
    }
    else {
        $_RUN->e = "Who are you!";
    }