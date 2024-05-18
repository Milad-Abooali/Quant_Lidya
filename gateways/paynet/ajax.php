<?php

require_once '../gateways/paynet/main.php';
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
    echo json_encode($output);
}

// Payment Link
function paymentLink($data) {
    $output = new stdClass();

    $paynet = new paynet();
    $request = $paynet->create_payment_link($data);

    if($request['code']=='0' && isset($request['url']))
    {
        $output->link = $request['url'];
    }
    else
    {
        $output->e = 'Ödeme linki üretilirken bir sorun oluştu';
    }

    echo json_encode($output);
}