<?php

    /**
    * Global Constant
    * 9:36 AM Tuesday, June 8, 2021 | M.Abooali
    */

    // Session
    define('DEF_TIME',60*30); // 30 Minutes
    define('REMEMBER_TIME',60*60*24*30); // 1 Month

    // Check is Lab
    define('IS_LAB',($_SERVER['HTTP_HOST'] == "crmlab"));

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
            "name"      => "tradeclanby_admin",
            "prefix"    => '',
            "username"  => "tradeclanby_all",
            "password"  => "nu=bnS5DlPYh"
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
    define("DOWNLOAD_LINK", [
        "MT5"               => 'https://download.mql5.com/cdn/web/21689/mt5/tradeclanintltd5setup.exe',
        "MT4"               => '',
        "Forex_E-Book"      => ''
    ]);
