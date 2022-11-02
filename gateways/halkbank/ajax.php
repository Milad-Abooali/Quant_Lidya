<?php

require_once '../gateways/halkbank/main.php';
require_once 'autoload/transaction.php';

// on null call
function test($data) {
    $output = new stdClass();
    $output->e = false;
    $output->res = $data;
    echo json_encode($output);
}

// Refund
function refund($data) {
    $output = new stdClass();
    $output->e = !(($_POST['DATA']['tid']) ?? false);
    $output->e = !(($_POST['DATA']['id']) ?? false);
    $bank = new halkbank('../gateways/halkbank/');
    if (!$output->e) $output->res = $bank->refundTransaction($_POST['DATA']['tid'], $_POST['DATA']['id']);
    echo json_encode($output);
}