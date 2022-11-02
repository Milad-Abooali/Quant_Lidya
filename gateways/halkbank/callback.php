<?php

    require_once  '../../config.php';
    $sess->relogin($_GET['uid'] ?? false);

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    if ($_POST['mdErrorMsg'] == 'Success' && $_POST['oid'] > 0) {

        require_once  'main.php';

        $bank = new halkbank();

        global $db;

        $order = $db->selectId('payment_orders', $_POST['oid']);
        $request['amount'] =  $order['amount'];

        $o_data = json_decode($order['data']);
        $bank->USDTRY = $o_data->USDTRY;
        $bank->cc_id = $o_data->ccID;
        $cc =$db->selectId('payment_cc', $bank->cc_id);

        $request['card_num'] =  $cc['number'];
        $request['exp_mm'] =  $cc['exp_mm'];
        $request['exp_yy'] =  $cc['exp_yy'];
        $request['cvv'] =  $cc['cvv'];

        $bank->order_id = $order['id'];

        $response = $bank->authPay($request);
        if ($response) {

            include_once '../../lib/autoload/transaction.php';
            $crm_transaction = new Transaction();

            $crm_transaction->addComment($order['transactions_id'],
        'CC Payment - Halkbank <br>Bank TID: '.$response['TransId'].
                 '<br>Order ID: '.$order['id'].
                 '<br>USD/TRY: '.$o_data->USDTRY);

            die('Payment has been done. close the window!');
        } else {
            die('Error [CURL] on Payment, Try again !');
        }
    } else {
        die('Error [3D] on Payment, Try again !');
    }