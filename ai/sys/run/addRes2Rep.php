<?php

    $_AJAX_ON = true;
    $_API_ON = false;

    global $_RUN;
    global $db_aifx;

    $insert['submit_by'] = $_REQUEST['user-id'];
    $insert['q']         = $_REQUEST['question'];
    $insert['a']         = $_REQUEST['answer'];
    $insert['msg_id']    = $_REQUEST['msgid'];
    $insert_id           = $db_aifx->insert('res_rep', $insert);
    if($insert_id>0) {
        $_RUN->res = $insert_id;
    } else {
        $_RUN->e = end($db_aifx->log());
    }