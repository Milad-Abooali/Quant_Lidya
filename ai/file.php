<?php
    header('Content-Type: text/json');
    $_RUN = (object)[];
    (file_exists('sys/l-config.php')) ? require_once  'sys/l-config.php' : exit();
    $run_page = 'sys/run/'.$_GET['get'].'.php';
    if (file_exists($run_page)) include_once $run_page;
    if ($_RUN && $_AJAX_ON) echo json_encode($_RUN);