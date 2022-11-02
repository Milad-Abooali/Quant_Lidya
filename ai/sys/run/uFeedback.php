<?php
    /*
     * Socket Safe
     */

    $_AJAX_ON = true;
    $_API_ON = false;

    global $_RUN;
    global $db_aifx;

    $msg_id     = $_REQUEST['i'] ?? false;
    $true_res   = $_REQUEST['t'] ?? false;

    if ($msg_id) {
        $table = 'messages_archive';
        $update['feedback'] = $true_res;
        $db_aifx->updateId($table, $msg_id, $update);
        $_RUN->res = $_REQUEST;
    } else {
        $_RUN->e = "Who are you!";
    }