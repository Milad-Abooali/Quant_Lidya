<?php

    // Constants - AI
    require_once "constants.php";

    // Functions - AI
    require_once "func.php";

    // Constants - CRM
    require_once "../lib/gconst.php";

    // Functions - CRM
    require_once "../lib/gfunc.php";

    // Matching Phrases - AI
    include_once 'lib/matchingPhrases.php';

/*
    // MySql - AI
    require_once "lib/isql.php";
    $db_aifx = new iSQL(DB_aifx);
    $db = $db_admin = new iSQL(DB_admin);

    // User Manager - CRM
    require_once "../lib/autoload/usermanager.php";

    // Action Log - CRM
    require_once "../lib/autoload/actlog.php";
    $actLog = new actLog();

    // Email Manager - CRM
    require_once "../lib/autoload/email_theme.php";
    require_once "../lib/autoload/email_m.php";
    $_Email_M = new Email_M();

    // MT5 Class - CRM
    require_once "../lib/mt5.php";
    require_once "../lib/autoload/mt5API.php";
*/
    // Gen Time - CRM
    require_once "../lib/genTime.php";
    $genTime = new genTime('Page');

    /**
     * Construct App For Broker - AI/CRM
     */
    $_broker['title'] = "DevMod";
    $_broker['email'] = "info@example.com";
    $_broker['web_url'] = $_SERVER['HTTP_HOST'];
    $_broker['crm_url'] = $_SERVER['HTTP_HOST'];
    $session_path_tmp = $_SERVER['DOCUMENT_ROOT'].((IS_LAB) ? "/_sessions" : "/lidyacrm/_sessions");
    $_broker['session_path'] = str_replace('lidyacrm/lidyacrm', '/lidyacrm', $session_path_tmp);
    $_broker['terms_file'] = "includes/tos/d.html";
    $_broker['logo'] = "d.png";
    $_broker['dark_logo'] = "d-dark.png";
    $_broker['mini_logo'] = "d-sm.png";
    $_broker['favicon'] = "d.ico";
    $_broker['def_language'] = "english";
    $_broker['def_unit'] = 1;
    $_broker['captcha'] = true;
    $_broker['pin_lock'] = true;
    $_broker['maintenance'] = (IS_LAB) ? false : true;
    define("Broker", $_broker);

    // Seession Manager - AI
    require_once '../lib/session.php';
    $sess = new sessionManager();
    /*
    if($_SESSION['id']) {
        $user = new userManager();
        $_SESSION['user'] = $user->get($_SESSION['id']);
        $where = "type='avatar' AND user_id=".$_SESSION['id'];
        $media = $db->selectRow('media',$where);
        if ($media['media']) $_SESSION['avatar'] = '../../media/'.$media['media'];
    }
    */
    if(session_status() == PHP_SESSION_NONE && !headers_sent()) {
        setcookie("PHPSESSID", "", 1);
        unset($_COOKIE['PHPSESSID']);
        if (session_save_path() != "../_sessions") session_save_path("../_sessions");
        ini_set('session.gc_probability', 1);
        session_name("SID");
        session_start();
    }
    define('SESSION_ID',session_id());

/*
    // Web Application Firewall - CRM
    require_once "../lib/autoload/waf.php";
    $_waf = new waf();
    $_waf_isWhitelistIP = $_waf->isWhitelistIP();
    define("WAF_UM", $_waf->UM);
    // GF::wafCheck();
*/
    $_AJAX_ON = true;
    $_API_ON  = true;
    $_RUN_ON  = true;