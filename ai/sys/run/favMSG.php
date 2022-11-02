<?php
    $page['title'] = 'Fav Message';

    $_AJAX_ON = true;
    $_API_ON  = false;

    global $_RUN;
    global $db_aifx;

    $id = $_REQUEST['id'] ?? false;
    $data['favorite'] = $_REQUEST['favorite'] ?? false;

    if ($id) {
        $_RUN->res = $db_aifx->updateId('messages_archive',$id, $data);
    } else {
        $_RUN->e = true;
    }
