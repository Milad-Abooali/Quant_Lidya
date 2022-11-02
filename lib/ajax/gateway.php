<?php

/**
 * Gateway Functions Class
 * 1:47 PM Monday, January 4, 2021 | M.Abooali
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Call Gateway function
function gatewayDo() {
    $output = new stdClass();
    $output->e = !(($_POST['GW']) ?? false);
    $output->e = !(($_POST['FUNC']) ?? false);
    $output->e = !(($_POST['DATA']) ?? false);
    if (!$output->e) {
        $ajax_file =  "../gateways/".$_POST['GW']."/ajax.php";
        if (file_exists($ajax_file)) include($ajax_file);
        call_user_func($_POST['FUNC'], $_POST['DATA']);
    }
}

// Get Rate USD TRY
function rateUSDTRY() {
    $output = new stdClass();
    global $db;
    $where ="Symbol='USDTRY'";
    $output->res = $db->selectRow('lidyapar_mt5.mt5_prices',$where)['AskLast'];
    echo json_encode($output);
}

// Load Gateway Deposit
function load() {
    $error = !(($_POST['path']) ?? false);
    if (!$error) include_once "raw/gateway/".$_POST['path'].".php";
    return (!$error);
}


// Load payment information
function paymentInfo() {
    $error = !(($_POST['lang']) ?? false);
    if (!$error) include_once "raw/paymentInfo_".$_POST['lang'].".php";
    return (!$error);
}