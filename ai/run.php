<?php
    $_RUN = (object)[];
    header('Content-Type: text/html');
    (file_exists('sys/config.php')) ? require_once  'sys/config.php' : exit();
    $run_page = 'sys/run/'.$_GET['get'].'.php';
    if (file_exists($run_page)) include_once $run_page;
    $them_page = UI['path'].'/'.$_GET['get'].'.htm';

    (file_exists($them_page) && $_RUN_ON) ? include_once $them_page : include_once UI['path'].'/404.htm';;
