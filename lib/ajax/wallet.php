<?php

/**
 * wallet Functions Class
 * 4:14 PM Tuesday, November 2, 2021 | Milad
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Add new Type
function newType() {
    $output = new stdClass();
    if( !isset($_POST['title']) || !isset($_POST['broker']) || !isset($_POST['unit']) ) $output->e = 'Check your form!';
    global $db;
    if (!isset($output->e)){
        $insert['title']  = $_POST['title'];
        $insert['rate']   = $_POST['rate'];
        $insert['sym']    = $_POST['symbol'];
        $insert['broker_id']  = $_POST['broker'];
        $insert['unit_id']    = $_POST['unit'];
        $insert['created_by'] = $_SESSION['id'];
        $insert['created_at'] = $db->DATE;

        $output->res = $db->insert('wallet_types', $insert);
        if(!$output->res) $output->e = 'Wallet exist on broker.';
    }
    echo json_encode($output);
}

// Update Type
function updateType() {
    $output = new stdClass();
    if( !isset($_POST['id']) || !isset($_POST['title']) || !isset($_POST['broker']) || !isset($_POST['unit']) ) $output->e = 'Check your form!';
    global $db;
    if (!isset($output->e)){
        $update['title']  = $_POST['title'];
        $update['rate']   = $_POST['rate'];
        $update['sym']    = $_POST['symbol'];
        $update['broker_id']  = $_POST['broker'];
        $update['unit_id']    = $_POST['unit'];
        $update['updated_by'] = $_SESSION['id'];
        $update['updated_at'] = $db->DATE;

        $output->res = $db->updateId('wallet_types', $_POST['id'], $update);
        if(!$output->res) $output->e = 'Can not update wallet type!';
    }
    echo json_encode($output);
}

// Get Type
function getTypeById()
{
    global $db;
    $type = $db->selectId('wallet_types', $_POST['type_id']);
    echo json_encode($type);
}

// Delete Type
function deleteType()
{
    $output = new stdClass();
    global $db;
    $where = "type_id=".$_POST['id'];
    $wallets = $db->exist('user_wallets',$where);
    if($wallets){
        $output->e = "There are some active wallet of this wallet type exist!";
    } else {
        $output->res = $db->deleteId('wallet_types', $_POST['id']);
        $output->DB = $db->log();
    }
    echo json_encode($output);
}

// Enable Wallet
function enableWallet()
{
    $output = new stdClass();
    if( !isset($_POST['type_id']) || !isset($_POST['user_id']) ) $output->e = 'Check your form!';
    global $db;
    if (!isset($output->e)){
        $insert['user_id']  = $_POST['user_id'];
        $insert['type_id']   = $_POST['type_id'];
        $insert['created_by'] = $_SESSION['id'];
        $insert['created_at'] = $db->DATE;
        $output->res = $db->insert('user_wallets', $insert);
    }
    echo json_encode($output);
}

// Wallet Delete
function deleteWallet()
{
    $output = new stdClass();
    if( !isset($_POST['id']) ) $output->e = 'Check your form!';
    global $db;
    if(!isset($output->e)) {
        $wallet = new wallet();
        $the_wallet = $wallet->getWalletById($_POST['id']);
        $transaction['action_type'] = 'close';
        $transaction['volume']      = $the_wallet['balance'];
        $transaction['source']      = $_POST['id'];
        $transaction['s_type']      = 'Wallet';
        $transaction['s_user_id']   = $the_wallet['user_id'];
        $transaction['s_balance']   = 0;
        $transaction['destination'] = '';
        $transaction['d_type'] = '';
        $transaction['d_user_id'] = '';
        $transaction['d_balance'] = '';
        $transaction['reference'] = '';
        $transaction['ex_rate'] = '';
        $transaction['commission'] = '';
        $transaction['status'] = 1;
        $transaction['created_by'] = $_SESSION['id'];
        $transaction['created_at'] = $db->DATE;
        if($wallet->addTransaction($transaction)) {
            $update['status']  = -1;
            $update['balance']  = 0;
            $output->res = $db->updateId('user_wallets', $_POST['id'], $update);
            if(!$output->res) $output->e = 'Can not delete wallet!';
        } else {
            $output->e = 'Can not set wallet balance to zero!';
        }
    }
    echo json_encode($output);
}

// Wallet Block
function blockWallet()
{
    $output = new stdClass();
    if( !isset($_POST['id']) ) $output->e = 'Check your form!';
    global $db;
    if(!isset($output->e)) {
        $update['status']  = 1;
        $output->res = $db->updateId('user_wallets', $_POST['id'], $update);
        if(!$output->res) $output->e = 'Can not block wallet!';
    }
    echo json_encode($output);
}

// Wallet Active
function activeWallet()
{
    $output = new stdClass();
    if( !isset($_POST['id']) ) $output->e = 'Check your form!';
    global $db;
    if(!isset($output->e)) {
        $update['status']  = 0;
        $output->res = $db->updateId('user_wallets', $_POST['id'], $update);
        if(!$output->res) $output->e = 'Can not active wallet!';
    }
    echo json_encode($output);
}

// Get wallet
function getWallet()
{
    global $db;
    $wallet = $db->selectId('user_wallets', $_POST['id']);
    echo json_encode($wallet);
}

// Wallet Update Balance
function updateWalletBalance()
{
    $output = new stdClass();
    if( !isset($_POST['id']) || !isset($_POST['balance']) ) $output->e = 'Check your form!';
    global $db;
    if(!isset($output->e)) {
        $update['balance']  = $_POST['balance'];
        $output->res = $db->updateId('user_wallets', $_POST['id'], $update);
        if(!$output->res) $output->e = 'Can not update wallet balance!';
    }
    echo json_encode($output);
}

// Deposit to Wallet Request
function walletDepositRequest() {

    global $db;

    // Escape All
    GF::escapeReq();

    $output = new stdClass();
    $output->e = !(($_POST['user_id']) ?? false);
    $output->e = !(($_POST['wallet_id']) ?? false);
    $output->e = !(($_POST['gateway']) ?? false);
    $output->e = !(($_POST['user_id']) ?? false);
    $output->e = !(($_POST['sym']) ?? false);
    $output->e = !(($_POST['amountWallet']) ?? false);
    $output->e = !(($_POST['amountMain']) ?? false);

    /**
     * Security Bug
     */
    // $request['user_id'] = $_POST['user_id'];
    $request['user_id'] = $_SESSION['id'];
    $request['req_type'] = 'deposit';

    $user_wallet = $db->selectId('user_wallets', $_POST['wallet_id']);

    $request['wallet_type'] = $user_wallet['type_id'];
    $request['wallet_id'] = $_POST['wallet_id'];
    $request['otherside_type'] = 'gateway';
    $request['otherside_ref'] = $_POST['gateway'];
    if ($_POST['payment'] ?? false) {
        $request['status'] = 'payment';
    } else {
        $request['status'] = 'pending';
    }
    $request['created_by'] = $request['updated_by'] = $_SESSION['id'];
    $request['created_at'] = $request['update_at'] = $db->DATE;
    $request['amountWallet'] = $_POST['amountWallet'];
    $request['amountMain'] = $_POST['amountMain'];

    $wallet = new wallet();

    $request_id = $wallet->addRequest($request);
    if($request_id) {
        if ($_FILES['doc']) {
            $count_files = count($_FILES['doc']['name']);
            for($i=0;$i<$count_files;$i++) {
                $filename = $request_id.'__'.rand(1,999).'__'.strtolower($_FILES['doc']['name'][$i]);
                $location = "../media/wallet_req/".$filename;
                $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
                $valid_extensions = array("jpg","jpeg","png");
                if (in_array(strtolower($imageFileType),$valid_extensions)) if (move_uploaded_file($_FILES['doc']['tmp_name'][$i],$location)) $wallet->addDoc($request_id, $filename);
            }
        }
        if($_POST['comment']) {
            $wallet->addComment($request_id, $_POST['comment']);
        }
        $output->res = $request_id;
    } else {
        $output->e = 'Error on adding your request!';
    }
    echo json_encode($output);
}

// Withdrawal from Wallet Request
function walletWithdrawalRequest() {

    global $db;

    // Escape All
    GF::escapeReq();

    $output = new stdClass();
    $output->e = !(($_POST['user_id']) ?? false);
    $output->e = !(($_POST['wallet_id']) ?? false);
    $output->e = !(($_POST['bankAccount']) ?? false);
    $output->e = !(($_POST['user_id']) ?? false);
    $output->e = !(($_POST['sym']) ?? false);
    $output->e = !(($_POST['amountWallet']) ?? false);
    $output->e = !(($_POST['amountMain']) ?? false);

    /**
     * Security Bug
     */
    // $request['user_id'] = $_POST['user_id'];
    $request['user_id'] = $_SESSION['id'];
    $request['req_type'] = 'withdrawal';

    $user_wallet = $db->selectId('user_wallets', $_POST['wallet_id']);

    $request['wallet_type'] = $user_wallet['type_id'];
    $request['wallet_id'] = $_POST['wallet_id'];
    $request['otherside_type'] = 'Bank Account';
    $request['otherside_ref'] = $_POST['bankAccount'];
    $request['status'] = 'pending';
    $request['created_by'] = $request['updated_by']= $_SESSION['id'];
    $request['created_at'] = $request['update_at'] = $db->DATE;
    $request['amountWallet'] = $_POST['amountWallet'];
    $request['amountMain'] = $_POST['amountMain'];

    $wallet = new wallet();

    $request_id = $wallet->addRequest($request);
    if($request_id) {
        if ($_FILES['doc']) {
            $count_files = count($_FILES['doc']['name']);
            for($i=0;$i<$count_files;$i++) {
                $filename = $request_id.'__'.rand(1,999).'__'.strtolower($_FILES['doc']['name'][$i]);
                $location = "../media/wallet_req/".$filename;
                $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
                $valid_extensions = array("jpg","jpeg","png");
                if (in_array(strtolower($imageFileType),$valid_extensions)) if (move_uploaded_file($_FILES['doc']['tmp_name'][$i],$location)) $wallet->addDoc($request_id, $filename);
            }
        }
        if($_POST['comment']) {
            $wallet->addComment($request_id, $_POST['comment']);
        }
        $output->res = $request_id;
    } else {
        $output->e = 'Error on adding your request!';
    }
    echo json_encode($output);
}

// Cancel Request
function cancelRequest(){
    $output = new stdClass();
    global $db;
    $request = $db->selectId('wallet_requests', $_POST['req_id']);
    if($request) {
        if( in_array($request['status'],['accepted','processing']) ) {
            $output->e = 'Request is under process and can not cancel.';
        } else {
            $update['status'] = 'cancelled';
            $output->res = $db->updateId('wallet_requests', $_POST['req_id'],$update);

            $wallet = new wallet();
            $wallet->addComment($_POST['req_id'], 'Request is cancelled. Payment time is expired.');
        }
    } else {
        $output->e = 'Request not found.';
    }
    echo json_encode($output);
}

// To wallet
function toWallet()
{
    global $db;
    $output = new stdClass();


    $output->e = ( (($_POST['s_wallet']) ?? false)>0 ) ? false : 'Source wallet is required!';
    if (!$output->e)
        $output->e = ( (($_POST['d_wallet']) ?? false)>0 ) ? false : 'Receiver wallet is required!';
    if (!$output->e)
        $output->e = ( (($_POST['amountTransfer']) ?? false)>0 ) ? false : 'Amount can not be zero!';

    if(!$output->e) {
        $s_wallet = $db->selectId('user_wallets', $_POST['s_wallet']);
        $d_wallet = $db->selectId('user_wallets', $_POST['d_wallet']);

        if($s_wallet['type_id'] != $d_wallet['type_id']) {
            $output->e = 'Receiver wallet currency and sender wallet currency are not same!';
        }
        if($s_wallet['balance'] < $_POST['amountTransfer']) {
            $output->e = 'Insufficient balance!';
        }
        if($s_wallet['status'] != 0) {
            $output->e = 'Sender wallet is blocked!';
        }
        if($d_wallet['status'] != 0) {
            $output->e = 'Receiver wallet is blocked!';
        }
    }
    if(!$output->e){
        $wallet = new wallet();

        $wallet->updateBalance($_POST['s_wallet'],-$_POST['amountTransfer']);
        $wallet->updateBalance($_POST['d_wallet'],$_POST['amountTransfer']);

        $transaction['action_type'] = 'transfer';
        $transaction['volume']      = $_POST['amountTransfer'];
        $transaction['source']      = $_POST['s_wallet'];
        $transaction['s_type']      = 'Wallet';
        $transaction['s_user_id']   = $s_wallet['user_id'];
        $transaction['s_balance']   = $wallet->getBalance($_POST['s_wallet']);
        $transaction['destination'] = $_POST['d_wallet'];
        $transaction['d_type']      = 'Wallet';
        $transaction['d_user_id']   = $d_wallet['user_id'];
        $transaction['d_balance']   = $wallet->getBalance($_POST['d_wallet']);
        $transaction['reference']   = '';
        $transaction['ex_rate']     = 1;
        $transaction['commission']  = 0;
        $transaction['status']      = 1;
        $transaction['created_by']  = $_SESSION['id'];
        $transaction['created_at']  = $db->DATE;
        $output->res = $wallet->addTransaction($transaction);

    }
    echo json_encode($output);

}

// TO MT5
function toMT5()
{
    global $db;
    $output = new stdClass();

    $output->e = ( (($_POST['s_wallet']) ?? false)>0 ) ? false : 'Source wallet is required!';
    if (!$output->e)
        $output->e = ( (($_POST['d_mt5']) ?? false)>0 ) ? false : 'Receiver MT5 account login is required!';
    if (!$output->e)
        $output->e = ( (($_POST['sAmount']) ?? false)>0 ) ? false : 'Amount can not be zero!';
    if (!$output->e)
        $output->e = ( (($_POST['dAmount']) ?? false)>0 ) ? false : 'Amount can not be zero!';

    if(!$output->e) {
        $s_wallet = $db->selectId('user_wallets', $_POST['s_wallet']);
        $s_wallet_type = $db->selectId('wallet_types', $s_wallet['type_id']);
        $d_mt5 = GF::getLoginDetails($_POST['d_mt5']);

        if($s_wallet_type['title'] != $d_mt5['Currency']) {
            $rate = GF::exchangeRate($s_wallet_type['title'], $d_mt5['Currency']);
            $output->e = $rate['e'];
            if($rate['res']['order']=="RL") {
                $_POST['dAmount'] = $_POST['sAmount']*$rate['res']['rate'];
            } else if($rate['res']['order']=="LR"){
                $_POST['dAmount'] = $_POST['sAmount']/$rate['res']['rate'];
            }
        } else {
            $_POST['dAmount'] = $_POST['sAmount'];
        }
        if($s_wallet['balance'] < $_POST['sAmount']) {
            $output->e = 'Insufficient balance - wallet!';
        }
        if($s_wallet['status'] != 0) {
            $output->e = 'Sender wallet is blocked!';
        }
        if(substr($d_mt5['Group'], 4) != "real" && $d_mt5['Group']!="TEST-PLT") {
            $output->e = 'Receiver MT5 account is not real!';
        }
    }
    if(!$output->e){
        $wallet = new wallet();
        $mt5API = new mt5API();

        $wallet->updateBalance($_POST['s_wallet'],-$_POST['sAmount']);
        $mt5API->updateBalance($_POST['d_mt5'], $_POST['dAmount'], "From CRM Wallet");

        $output->e = $mt5API->Error;

        unset($mt5API);

        $transaction['action_type'] = 'transfer';
        $transaction['volume']      = $_POST['sAmount'];
        $transaction['source']      = $_POST['s_wallet'];
        $transaction['s_type']      = 'Wallet';
        $transaction['s_user_id']   = $s_wallet['user_id'];
        $transaction['s_balance']   = $wallet->getBalance($_POST['s_wallet']);
        $transaction['destination'] = $_POST['d_mt5'];
        $transaction['d_type']      = 'MT5';
        $transaction['d_user_id']   = $s_wallet['user_id'];
        $transaction['d_balance']   = '';
        $transaction['reference']   = '';
        $transaction['ex_rate']     = $rate['res']['rate'];
        $transaction['commission']  = 0;
        $transaction['status']      = 1;
        $transaction['created_by']  = $_SESSION['id'];
        $transaction['created_at']  = $db->DATE;
        $output->res = $wallet->addTransaction($transaction);

    }
    echo json_encode($output);

}

// From MT5
function fromMT5()
{
    global $db;
    $output = new stdClass();

    $output->e = ( (($_POST['s_wallet']) ?? false)>0 ) ? false : 'Receiver wallet is required!';
    if (!$output->e)
        $output->e = ( (($_POST['d_mt5']) ?? false)>0 ) ? false : 'Source MT5 account login is required!';
    if (!$output->e)
        $output->e = ( (($_POST['sAmount']) ?? false)>0 ) ? false : 'Amount can not be zero!';
    if (!$output->e)
        $output->e = ( (($_POST['dAmount']) ?? false)>0 ) ? false : 'Amount can not be zero!';

    if(!$output->e) {
        $s_wallet = $db->selectId('user_wallets', $_POST['s_wallet']);
        $s_wallet_type = $db->selectId('wallet_types', $s_wallet['type_id']);
        $d_mt5 = GF::getLoginDetails($_POST['d_mt5']);

        if($s_wallet_type['title'] != $d_mt5['Currency']) {
            $rate = GF::exchangeRate($s_wallet_type['title'], $d_mt5['Currency']);
            $output->e = $rate['e'];
            if($rate['res']['order']=="RL") {
                $_POST['sAmount'] = $_POST['dAmount']/$rate['res']['rate'];
            } else if($rate['res']['order']=="LR"){
                $_POST['sAmount'] = $_POST['dAmount']*$rate['res']['rate'];
            }
        } else {
            $_POST['dAmount'] = $_POST['sAmount'];
        }
        if($d_mt5['Equity'] < $_POST['dAmount']) {
            $output->e = 'Insufficient equity! - MT5';
        }
        if($s_wallet['status'] != 0) {
            $output->e = 'Receiver wallet is blocked!';
        }
        if(substr($d_mt5['Group'], 4) != "real" && $d_mt5['Group']!="TEST-PLT") {
            $output->e = 'Source MT5 account is not real!';
        }
    }
    if(!$output->e){
        $wallet = new wallet();
        $mt5API = new mt5API();

        $wallet->updateBalance($_POST['s_wallet'],$_POST['sAmount']);
        $mt5API->updateBalance($_POST['d_mt5'], -$_POST['dAmount'], "From CRM Wallet");

        $output->e = $mt5API->Error;

        unset($mt5API);

        $transaction['action_type'] = 'transfer';
        $transaction['volume']      = $_POST['dAmount'];
        $transaction['source']      = $_POST['d_mt5'];
        $transaction['s_type']      = 'MT5';
        $transaction['s_user_id']   = $s_wallet['user_id'];
        $transaction['s_balance']   = '';
        $transaction['destination'] = $_POST['s_wallet'];
        $transaction['d_type']      = 'Wallet';
        $transaction['d_user_id']   = $s_wallet['user_id'];
        $transaction['d_balance']   = $wallet->getBalance($_POST['s_wallet']);
        $transaction['reference']   = '';
        $transaction['ex_rate']     = $rate['res']['rate'];
        $transaction['commission']  = 0;
        $transaction['status']      = 1;
        $transaction['created_by']  = $_SESSION['id'];
        $transaction['created_at']  = $db->DATE;
        $output->res = $wallet->addTransaction($transaction);

    }
    echo json_encode($output);

}