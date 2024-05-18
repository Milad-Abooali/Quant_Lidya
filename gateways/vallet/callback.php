<?php

    require_once  '../../config.php';
    global $sess;
    if(isset($_GET['u'])) $sess->relogin($_GET['u'] ?? false);

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    global $db;

    $errors=array();

    function logFile($log, $name)
    {
        $file = fopen("log_".$name.".json", "w") or die("Unable to open file!");
        fwrite($file, json_encode($log));
        fclose($file);
    }
    $side = $_GET['s'] ?? 'nos';

    logFile($_REQUEST, $side);

    if(isset($_GET['s'])){
        echo '<br><br><button onclick="self.close()">Close</button><br><br>';
        if($_GET['s']==='ok'){
            die('Payment has been done, you can close the tab.');
        }
        else {
            die('Payment is failed, you can close this tab.');
        }
    }
    else {
        $post_required = array(
            'status',
            'paymentStatus',
            'hash',
            'paymentCurrency',
            'paymentAmount',
            'paymentType',
            'paymentTime',
            'orderId',
            'shopCode',
            'orderPrice',
            'productsTotalPrice',
        );
        foreach($post_required as $key){
            if(empty($_POST[$key])) $errors[] = $key.' expected.';
        }
        if($errors) {
            logFile($errors, 'error');
            die('Error:'.PHP_EOL.end($errors));
        }

        require_once  './main.php';
        $vallet = new vallet();

        $hash_string = $_POST['orderId'].$_POST['paymentCurrency'].$_POST['orderPrice'].$_POST['productsTotalPrice'].$_POST['productType'].$vallet->config["shopCode"].$vallet->config["hash"];
        $MY_HASH = base64_encode(pack('H*',sha1($hash_string)));
        if($MY_HASH!==$_POST['hash']) {
            $errors[]= 'INCORRECT HASH SIGNATURE';
            logFile($errors, 'error');
            die('Error:'.PHP_EOL.end($errors));
        }

        $order = $db->selectId('payment_orders', $_POST['orderId']);
        $o_data = json_decode($order['data']);

        if($_POST['paymentStatus']!=='paymentOk') {
            $errors[]= $_POST['paymentStatus'];
            $errors[]= 'paymentStatus';
            logFile($errors, 'error');
            die('Error:'.PHP_EOL.end($errors));
        }

        if($_POST['orderId']!==$order['id']) {
            $errors[]= $_POST['orderId'];
            $errors[]= $order['id'];
            $errors[]= 'Order id is not correct';
            logFile($errors, 'error');
            die('Error:'.PHP_EOL.end($errors));
        }
        if(floatval($_POST['paymentAmount']) !== floatval($order['amount'])) {
            $errors[]= $_POST['paymentAmount'];
            $errors[]= $order['amount'];
            $errors[]= 'Amount is not correct';
            logFile($errors, 'error');
            die('Error:'.PHP_EOL.end($errors));
        }
        $result = array();

        $data['status'] = 1;
        $result['payment_orders'] = $db->updateId('payment_orders', $order['id'], $data);

        $data['status'] = 'Pending';
        $result['transactions'] = $db->updateId('transactions', $order['transactions_id'], $data);

        include_once '../../lib/autoload/transaction.php';
        $crm_transaction = new Transaction();
        $result['addComment'] = $crm_transaction->addComment($order['transactions_id'],
            'CC Payment - Halkbank <br>Bank TID: '.$_POST['valletOrderNumber'].
            '<br>Order ID: '.$_POST['orderId'].
            '<br>USD/TRY: '.$o_data->USDTRY);

        logFile($result, 'done');
        die('Done.');
    }
