<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

/**
 * Config
 * 11:04 AM Thursday, November 5, 2020 | M.Abooali
 */

    ini_set('memory_limit', '2048M');
    date_default_timezone_set('Europe/Athens');

    //  error_reporting(E_ALL);
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('ignore_repeated_errors', false);
    ini_set('display_errors', true);
    ini_set('log_errors', true);

    // Constants
    require_once "lib/gconst.php";

// Composer
require_once 'vendor/autoload.php';

    // global Functions
    require_once "lib/gfunc.php";

    // iSQL
    require_once "lib/genTime.php";
    $genTime = new genTime('Page');

    // iSQL
    require_once "lib/isql.php";
    $db = new iSQL(DB_admin);

// CRM ROOT Path
define('CRM_ROOT', __DIR__ . DIRECTORY_SEPARATOR);

/**
 * Construct App For Broker
 */
    $where  = 'crm_url="'.$_SERVER['HTTP_HOST'].'"';
    $_broker = $db->selectRow('brokers', $where);
    if(!$_broker) {
        $_broker['id'] = 1;
        $_broker['title'] = "DevMod";
        $_broker['email'] = "info@example.com";
        $_broker['web_url'] = $_SERVER['HTTP_HOST'];
        $_broker['crm_url'] = $_SERVER['HTTP_HOST'];
        $_broker['session_path'] = $_SERVER['DOCUMENT_ROOT'] . "/_sessions";
        $_broker['terms_file'] = "includes/tos/d.html";
        $_broker['logo'] = "d.png";
        $_broker['dark_logo'] = "d-dark.png";
        $_broker['mini_logo'] = "d-sm.png";
        $_broker['favicon'] = "d.ico";
        $_broker['def_language'] = "english";
        $_broker['def_unit'] = 1;
        $q_units = $db->selectAll('units');
        if($q_units) foreach($q_units as $unit) $list_units[] = $unit['name'];
        if($list_units ?? false) $_broker['units'] = "'".implode("','",$list_units)."'";
        $_broker['captcha'] = true;
        $_broker['pin_lock'] = false;
        $_broker['maintenance'] = (IS_LAB) ? false : true;
        $_broker['edit_email'] = 'Admin';
        $_broker['upload_docs'] = 'Admin';
    } else {
        $_broker['session_path'] = $_SERVER['DOCUMENT_ROOT'].$_broker['session_path'];
    }
    $_broker['edit_email'] = explode(',',$_broker['edit_email']);
    $_broker['upload_docs'] = explode(',',$_broker['upload_docs']);
    define("Broker", $_broker);


// Session
    if (!($is_cron ?? false)) {
        require_once "lib/session.php";
        $sess = new sessionManager();

        /**
         * Lock Screen
         */
        $lock_screen = ($_SESSION["locksess"]) ?? false;

        /**
         * Check Login
         * Force guest to login
         */
        $requested_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
        $guest_permited_pages = array(
            'lib/ajax.php',
            'ajax.php',
            'login.php',
            'login-ip.php',
            'wp-login.php',
            'register.php',
            'callback.php',
            '/halkbank/callback.php',
            '/gateways/halkbank/callback.php',
            '/webpay/callback.php',
            '/gateways/webpay/callback.php',
            '/gateways/streampay/callback.php',
            '/streampay/callback.php',
            'forget-password.php',
            'reset-password.php',
            'api/lead_add.php',
            'api/telegram.php',
            'telegram.php',
            'lead_add.php',
            'balance.php',
            'app.php',
            'webapp.php'
        );
        if (!$sess->IS_LOGIN && !in_array($requested_page, $guest_permited_pages)) {
            header("Location: login.php");
            exit();
        } else if ($sess->IS_LOGIN && $sess->forceChangePasswod($_SESSION['id']) && $requested_page != 'reset-password.php') {
            header("Location: reset-password.php");
            exit();
        }
    }

    //DevMod
    if (isset($_GET['DevMod'])) $_SESSION['M']['DevMod'] = $_GET['DevMod'];
define("DevMod", (IS_LAB || isset($_SESSION['M']['DevMod'])));

    // Factory
    require_once "lib/factory.php";
    $factory = new factory();

/**
 * Global Setting
 */

    $sys_s = $db->select('sys_settings');
    if ($sys_s) foreach ($sys_s as $item) $_sys[$item['term']] = $item['value'];

    $_sys_header=$_sys_footer=null;

/**
 * Database (MySQL)
 */
    mb_internal_encoding('utf-8');
    mb_http_output('utf-8');
mb_http_input('I');
    mb_language('uni');
    mb_regex_encoding('utf-8');

    $DB_admin = mysqli_connect(DB_admin['hostname'], DB_admin['username'], DB_admin['password'], DB_admin['name'], DB_admin['port']);
    $DB_mt4 = mysqli_connect(DB_mt4['hostname'], DB_mt4['username'], DB_mt4['password'], DB_mt4['name'], DB_mt4['port']);
    $DB_mt4_demo = mysqli_connect(DB_mt4_demo['hostname'], DB_mt4_demo['username'], DB_mt4_demo['password'], DB_mt4_demo['name'], DB_mt4_demo['port']);
    $DB_mt5 = mysqli_connect(DB_mt5['hostname'], DB_mt5['username'], DB_mt5['password'], DB_mt5['name'], DB_mt5['port']);
    if (mysqli_connect_errno())	throw new RuntimeException("Connect failed: %s\n", mysqli_connect_error());
    mysqli_set_charset($DB_admin,'utf8');
    mysqli_set_charset($DB_mt4,'utf8');
    mysqli_set_charset($DB_mt4_demo,'utf8');
    mysqli_set_charset($DB_mt5,'utf8');
 
/**
 * Autoloader
 */

    // Language Manager   
    require_once "lib/lanman.php";
$_language = $_GET['language'] ?? $_SESSION['language'] ?? Broker['def_language'];
    define('LANGUAGE_NAME',$_language);
if (isset($_SESSION['id'])) {
        $where = 'user_id='.$_SESSION['id'];
        $data['language'] = $db->escape($_language);
        $db->updateAny('user_extra',$data, $where);
        $_SESSION['language'] = $_language;
    }
    $_L = new LangMan($_language, (Broker['maintenance'] ?? false));

//  $_L = new LangMan($_language, (Broker['maintenance'] ?? false));


    // Permission System
    require_once "lib/autoload/groups.php";
    require_once "lib/autoload/paths.php";

    // Notify
    require_once "lib/autoload/notify.php";
    $notify = new notify();

    // Action Log
    require_once "lib/autoload/actlog.php";
    $actLog = new actLog();

    require_once "lib/autoload/email_theme.php";
    require_once "lib/autoload/email_m.php";
    $_Email_M = new Email_M();

    if (!($is_cron ?? false)) {
        // Web Application Firewall
        require_once "lib/autoload/waf.php";
        $_waf = new waf();

        if(!$_waf->isValidIP()) {
            exit( '🦊 . . . Hi!<br> Your session hits on one or more of our security rules and limited!<br><br>Not Valid IP: '.GF::getIP() );
        }

        $_waf_isWhitelistIP = $_waf->isWhitelistIP();
        define("WAF_UM", $_waf->UM);
        GF::wafCheck();
    }
    if(!Broker['captcha']) {
        $_SESSION["captcha_force"]=false;
    }
    // User Manager
    require_once "lib/autoload/usermanager.php";
    $userManager = new userManager();

    // MT5 Class
    require_once "lib/mt5.php";
    
    // MT5 New Class
    require_once "lib/autoload/mt5API.php";
    
    // TaskManager Class
    require_once "lib/task_manager.php";
    $taskManager = new taskManager();

    // Wallet
    require_once "lib/wallet.php";