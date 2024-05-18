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

            require_once  './main.php';
            $paynet = new paynet();

            $order = $db->selectId('payment_orders', $_GET['o']);
            $o_data = json_decode($order['data']);

            if($_REQUEST['reference_no']!=md5($order['id'])) {
                $errors[]= $_REQUEST['reference_no'];
                $errors[]= $order['id'];
                $errors[]= md5($order['id']);
                $errors[]= 'Order id is not correct';
                logFile($errors, 'error');
                die('Error:'.PHP_EOL.end($errors));
            }
            $result = array();
            unset($data);
            $data['status'] = 1;
            $result['payment_orders'] = $db->updateId('payment_orders', $order['id'], $data);
            $data['status'] = 'Pending';
            $result['transactions'] = $db->updateId('transactions', $order['transactions_id'], $data);

            include_once '../../lib/autoload/transaction.php';
            $crm_transaction = new Transaction();
            $result['addComment'] = $crm_transaction->addComment($order['transactions_id'],
                'CC Payment - Paynet <br>TID: '.$_REQUEST['xact_id'].
                '<br>Order ID: '.$order['id'].
                '<br>Referance: '.$_REQUEST['reference_no']);
            logFile($result, 'done');
            die('Payment has been done, you can close the tab.');
        }
        else {
            die('Payment is failed, you can close this tab.');
        }
    }
    else {
        $post_required = array(
            'referance_no',
            'xact_id',
            'card_holder',
            'card_number',
            'amount',
            'currency',
            'order_id',
            'is_succeed',
        );
        foreach($post_required as $key){
            if(empty($_POST[$key])) $errors[] = $key.' expected.';
        }
        $get_required = array(
            'o',
            'u'
        );
        foreach($get_required as $key){
            if(empty($_GET[$key])) $errors[] = $key.' expected.';
        }
        if($errors) {
            logFile($errors, 'error');
            die('Error:'.PHP_EOL.end($errors));
        }

        require_once  './main.php';
        $paynet = new paynet();

        $order = $db->selectId('payment_orders', $_GET['o']);
        $o_data = json_decode($order['data']);

        if(!$_POST['is_succeed']) {
            $errors[]= $_POST['is_succeed'];
            $errors[]= 'is_succeed';
            logFile($errors, 'error');
            die('Error:'.PHP_EOL.end($errors));
        }

        if($_GET['o']!==$order['id']) {
            $errors[]= $_GET['o'];
            $errors[]= $order['id'];
            $errors[]= 'Order id is not correct';
            logFile($errors, 'error');
            die('Error:'.PHP_EOL.end($errors));
        }
        if(floatval($_POST['amount']) !== floatval($order['amount'])) {
            $errors[]= $_POST['amount'];
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
            'CC Payment - Paynet <br>TID: '.$_POST['order_id'].
            '<br>Order ID: '.$_POST['referance_no'].
            '<br>Card Number: '.$_POST['card_number'].
            '<br>Currency: '.$_POST['currency']);

        logFile($result, 'done');
        die('Done.');
    }
