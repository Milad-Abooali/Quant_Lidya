<?php

    // Them
    define("APP", [
        "name"      => 'AI FX',
        "root"      => "/ai",
        "url"      => "https://".$_SERVER['HTTP_HOST']."/ai"
    ]);

    // Them
    define("UI", [
        "id"      => 1,
        "path"      => __DIR__."/../ui/default",
    ]);


    // Check is Lab
    define('IS_LAB',($_SERVER['HTTP_HOST'] == "crmlab"));

    // Database - Def: 3306
    if (IS_LAB) {
        // Lab
        define("DB_aifx", [
            "hostname"  => "127.0.0.1",
            "port"      => 3306,
            "name"      => "lidyapar_aifx",
            "prefix"    => '',
            "username"  => "root",
            "password"  => ""
        ]);
        define("DB_admin", [
            "hostname"  => "127.0.0.1",
            "port"      => 3306,
            "name"      => "lidyapar_admin",
            "prefix"    => '',
            "username"  => "root",
            "password"  => ""
        ]);
        define("DB_mt4", [
            "hostname"  => "127.0.0.1",
            "port"      => 3306,
            "name"      => "lidyapar_mt4",
            "prefix"    => '',
            "username"  => "root",
            "password"  => ""
        ]);
        define("DB_mt4_demo", [
            "hostname"  => "127.0.0.1",
            "port"      => 3306,
            "name"      => "lidyapar_mt4_demo",
            "prefix"    => '',
            "username"  => "root",
            "password"  => ""
        ]);
        define("DB_mt5", [
            "hostname"  => "127.0.0.1",
            "port"      => 3306,
            "name"      => "lidyapar_mt5",
            "prefix"    => '',
            "username"  => "root",
            "password"  => ""
        ]);
    } else {
        // Online
        define("DB_aifx", [
            "hostname"  => "136.243.109.195",
            "port"      => 3306,
            "name"      => "lidyapar_aifx",
            "prefix"    => '',
            "username"  => "lidyapar_admin",
            "password"  => "@Sra7689227"
        ]);
    }

    date_default_timezone_set('Europe/Athens');

    error_reporting(0);
    header("X-Powered-By: ");

    ini_set('memory_limit', '2048M');
    date_default_timezone_set('Europe/Athens');

    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('ignore_repeated_errors', !IS_LAB);
    ini_set('display_errors', IS_LAB);
    ini_set('log_errors', IS_LAB);

    // UTF-8
    mb_internal_encoding('utf-8');
    mb_http_output('utf-8');
    mb_http_input('utf-8');
    mb_language('uni');
    mb_regex_encoding('utf-8');

    /* Python Path */
    define('PY_PATH', 'C:\\ProgramData\\Anaconda3\\python.exe');
    define('PY_SCRIPT_PATH', 'C:\\wamp\\www\\crmlab\\ai\\brain\\bridge\\engine.py');

    /* HI Engine */
    define('HI_E', 1);