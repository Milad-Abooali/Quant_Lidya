<?php

require_once '../gateways/vallet/main.php';
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

    $vallet = new vallet();
    $request = $vallet->create_payment_link($data);

    if($request['status']=='success' && isset($request['payment_page_url']))
    {
        $output->link = $request['payment_page_url'];
    }
    else
    {
        $output->e = 'Ödeme linki üretilirken bir sorun oluştu';
    }

    echo json_encode($output);
}