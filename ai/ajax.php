<?php
    header('Content-Type: text/json');
    $_RUN = (object)[];

    $lite = array(
        'resfile',
        'hiMsg',
        'aiml',
        'bridge-status',
        'session'
    );
    $get = $_GET['get'] ?? false;
    if(in_array($get, $lite)){
        (file_exists('sys/l-config.php')) ? require_once  'sys/l-config.php' : exit();
    }
    else {
        (file_exists('sys/config.php')) ? require_once  'sys/config.php' : exit();
    }

    $run_page = 'sys/run/'.$_GET['get'].'.php';
    if (file_exists($run_page)) 
        include_once $run_page;
    else 
        echo $run_page;
    if ($_RUN && $_AJAX_ON) echo json_encode($_RUN);