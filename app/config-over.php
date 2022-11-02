<?php
/**
 * APP - Main Page
 * By Milad [m.abooali@hotmail.com]
 */

// DevMod
define('APP_Dev_Mod', (bool) $_REQUEST['dev']);
// Origin Session
define('Origin_Session_Id', (string) session_id());

define('APP_PATH', __DIR__);

// eFunc
require_once(__DIR__.'/lib/eFun.php');

// Screens Permissions
require_once(__DIR__.'/lib/permits.php');

// Jumper Keywords
require_once(__DIR__.'/lib/keywords.php');

// HTML Blocks
require_once(__DIR__.'/lib/blocks.php');

// Session
require_once(__DIR__.'/lib/appSession.php');
appSession::checkRole();

// Main Class
require_once(__DIR__.'/lib/main.php');
$APP = new main();

// Escape User Input Values POST & GET
GF::escapeReq();
