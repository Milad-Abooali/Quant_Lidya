<?php

    /**
    * Global Constant
    * 9:36 AM Tuesday, June 8, 2021 | M.Abooali
    */

    // Session
    define('DEF_TIME',60*30); // 30 Minutes
    define('REMEMBER_TIME',60*60*24*30); // 1 Month

    // MT5 Server AUTH
    define("MT5_AUTH", [
        'url'      => 'mt5.fx-technology.com',
        'port'     => '443',
        'login'    => '1000',
        'password' => '@Sra7689227',
        'build'    => '1950',
        'agent'    => 'WebManager'
    ]);

    // Check is Lab
    define('IS_LAB',($_SERVER['SERVER_ADDR'] == "::1"));

    // Databse - Def: 3306 - Neor: 4040
    if (IS_LAB){
        // Lab
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
        define("DB_admin", [
            "hostname"  => "136.243.109.195",
            "port"      => 3306,
            "name"      => "fourinfx_admin",
            "prefix"    => '',
            "username"  => "fourinfx_all",
            "password"  => "@Sra7689227"
        ]);
        define("DB_mt4", [
            "hostname"  => "136.243.109.195",
            "port"      => 3306,
            "name"      => "lidyapar_mt4",
            "prefix"    => '',
            "username"  => "lidyapar_mt4",
            "password"  => "@Sra7689227"
        ]);
        define("DB_mt4_demo", [
            "hostname"  => "136.243.109.195",
            "port"      => 3306,
            "name"      => "lidyapar_mt4_demo",
            "prefix"    => '',
            "username"  => "lidyapar_mt4_demo",
            "password"  => "@Sra7689227"
        ]);
        define("DB_mt5", [
            "hostname"  => "136.243.109.195",
            "port"      => 3306,
            "name"      => "lidyapar_mt5",
            "prefix"    => '',
            "username"  => "lidyapar_mt5",
            "password"  => "@Sra7689227"
        ]);
    }

    // Define Download links
    const DOWNLOAD_LINK = [
        "MT5" => "https://download.mql5.com/cdn/web/16644/mt5/lidyatrade5setup.exe",
        "MT4" => '',
        "Forex_E-Book" => 'assets/e-book/LidyaFX_Forex_E-Book_v1.2.pdf'
    ];

    const REDIRECT_TO = [
        "web_trader"    =>"/webapp",
        "webapp"        =>"/webapp",
        "app"           =>"/app"
    ];