<?php

    $_AJAX_ON = false;
    $_API_ON  = false;

    if (!in_array($_SESSION['type'],['Admin'])) {
        $path = '../../login.php';
        header("Location: $path");
        die();
    } else {
        $user = new userManager();
        $_RUN->user = $user->get($_SESSION['id']);
        $where = "type='avatar' AND user_id=".$_SESSION['id'];
        $media = $db->selectRow('media',$where);
        if ($media['media']) $_RUN->user['avatar'] = '../../media/'.$media['media'];
    }

