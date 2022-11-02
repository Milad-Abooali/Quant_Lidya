<?php
    if($_GET['get']  ?? false) {
        include_once 'sys/constants.php';
        $file_path = UI['path'].'/assets/'.$_GET['get'];
        clearstatcache();
        if (file_exists($file_path) && filesize($file_path)) $file = file_get_contents($file_path);
    }
    if ($file ?? false) {
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);
        if ($ext=='js' || $ext=='JS') {
            header('Content-Type: text/javascript');
        } else if($ext=='css' || $ext=='CSS') {
            header('Content-Type: text/css');
        } else {
            header('Content-Type: '.mime_content_type($file_path));
        }
        echo($file);
    } else {
        header("X-Powered-By: ");
        header("HTTP/1.0 404 Not Found");
    }