<?php

    $page['title'] = 'Session Loader';

    $_AJAX_ON = true;
    $_API_ON  = false;

    $key = $_REQUEST['key'] ?? false;

    if($_REQUEST['clear'] ?? false){
        if($key) unset($_SESSION[$key]);
        else $_SESSION=array();
    }

    if ($_AJAX_ON) {
        $_RUN->sessionId = session_id();
        if($key) $_RUN->$key = ($_SESSION[$key]) ?? null;
        if(!$key) $_RUN->session = ($_SESSION) ?? null;
    }