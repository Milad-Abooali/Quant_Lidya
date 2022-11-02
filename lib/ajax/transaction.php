<?php

/**
 * Transaction Functions Class
 * 1:47 PM Monday, January 4, 2021 | M.Abooali
 */
require_once "autoload/transaction.php";

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Add new Transaction
function add() {
    $output = new stdClass();
    $output->e = !(($_POST['type']) ?? false);
    $output->e = !(($_POST['amount']) ?? false);
    $output->e = !(($_POST['tp']) ?? false);
    $output->e = !(($_POST['user_id']) ?? false);
    $Transaction = new Transaction();
    $pending_transaction = $Transaction->checkTransactionWaiting($_POST['user_id']);
    $output->e = $pending_transaction;
    if (!$output->e) {

        // Escape All
        GF::escapeReq();

        if ($_POST['type']=='deposit') {
            $source = $_POST['gateway'];
            $destination = $_POST['tp'];
        } else if ($_POST['type']=='withdraw') {
            $source = $_POST['tp'];
            $destination = $_POST['bankAccount'];
        }
        $transaction_id = $Transaction->add($_POST['type'], $_POST['amount'], $source, $destination, $_POST['user_id'], $_POST['comment']);
        if ($transaction_id && $_FILES['doc']) {
            $count_files = count($_FILES['doc']['name']);
            for($i=0;$i<$count_files;$i++) {
                $filename = $transaction_id.'__'.rand(1,999).'__'.strtolower($_FILES['doc']['name'][$i]);
                $location = "../media/transaction/".$filename;
                $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
                $valid_extensions = array("jpg","jpeg","png");
                if (in_array(strtolower($imageFileType),$valid_extensions)) if (move_uploaded_file($_FILES['doc']['tmp_name'][$i],$location)) $Transaction->addDoc($transaction_id, $filename);
            }
        }
        $output->res = $transaction_id;
    }
    // Desk Manager
    $db = new iSQL(DB_admin);
    $user_unit = $db->selectId('users', $_POST['user_id'], 'unit')['unit'];
    $where = "unit ='$user_unit' AND type='Manager'";
    $agents = $db->select('users', $where, 'id');
    foreach ($agents as $agent) $ids[] = $agent['id'];
    $receivers = implode(",",$ids);
    global $notify; $notify->addMulti('User '.$_SESSION["id"],2,$transaction_id, $receivers);
    global $actLog; $actLog->add('Transaction',(($transaction_id) ?? null),(($transaction_id) ? 1 : 0), json_encode($_POST));
    echo json_encode($output);
}

// Add Comment/Doc to Transaction
function update() {
    $output = new stdClass();
    $output->e = !(($_POST['transaction_id']) ?? false);
    $Transaction = new Transaction();
    if (!$output->e) {
        if ($_POST['comment']) $Transaction->addComment($_POST['transaction_id'], $_POST['comment']);
        if ($_FILES['doc']) {
            $count_files = count($_FILES['doc']['name']);
            for($i=0;$i<$count_files;$i++) {
                $filename = $_POST['transaction_id'].'__'.rand(1,999).'__'.strtolower($_FILES['doc']['name'][$i]);
                $location = "../media/transaction/".$filename;
                $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
                $valid_extensions = array("jpg","jpeg","png");
                if (in_array(strtolower($imageFileType),$valid_extensions)) if (move_uploaded_file($_FILES['doc']['tmp_name'][$i],$location)) $Transaction->addDoc($_POST['transaction_id'], $filename);
                $output->res = $_POST['transaction_id'];
            }
        }
    }
    global $actLog; $actLog->add('Transaction',(($_POST['transaction_id']) ?? null),(($_POST['transaction_id']) ? 1 : 0), json_encode($_POST));
    echo json_encode($output);
}

// Cancel Transaction by User
function cancel() {
    $output = new stdClass();
    $output->e = !(($_POST['transaction_id']) ?? false);
    $Transaction = new Transaction();
    if (!$output->e) {
        $Transaction->cancel($_POST['transaction_id']);
    }
    global $actLog; $actLog->add('Transaction',(($_POST['transaction_id']) ?? null),(($_POST['transaction_id']) ? 1 : 0), json_encode($_POST));
    echo json_encode($output);
}

// Cancel Transaction by time - Expire
function expire() {
    $output = new stdClass();
    $output->e = !(($_POST['transaction_id']) ?? false);
    $Transaction = new Transaction();
    if (!$output->e) {
        $Transaction->cancel($_POST['transaction_id'],'Credit cart payment is timeout.');
    }
    global $actLog; $actLog->add('Transaction',(($_POST['transaction_id']) ?? null),(($_POST['transaction_id']) ? 1 : 0), json_encode($_POST));
    echo json_encode($output);
}

// Load Transaction
function load() {
    $output = new stdClass();
    $output->e = !(($_POST['transaction_id']) ?? false);
    $Transaction = new Transaction();
    if (!$output->e) {
        $output->res = $Transaction->loadTransactionByID($_POST['transaction_id']);
    }
    echo json_encode($output);
}


// Timeline Transaction
function timeline() {
    $error = !(($_POST['transaction_id']) ?? false);
    $Transaction = new Transaction();
    if (!$error) include_once "raw/transaction-timeline.php";
    return (!$error);
}


// API Request
function api() {
    include_once "raw/api-balanceaction.php";
}

// API Bonus Request
function api_bonus() {
    include_once "raw/api-balanceaction.php";
}

// Desk Verify
function deskVerify() {
    $output = new stdClass();
    $output->e = !(($_POST['transaction_id']) ?? false);
    $output->e = !(($_POST['user_id']) ?? false);
    $Transaction = new Transaction();
    if (!$output->e) {
        $output->res = $Transaction->verify($_POST['transaction_id'], $_POST['user_id'], 'desk_verify');
        $t = $Transaction->loadTransactionByID($_POST['transaction_id']);
        if ($t['type']=='Deposit') {

            // Finance
            $usermanager = new usermanager();
            $name = $usermanager->getCustom($t['user_id'],'fname,lname');
            $full_name = $name['extra']['fname'].' '.$name['extra']['lname'];
            $text = "Type: <b>Deposit</b>\nName: <b>$full_name</b>\nAmount: <b>USD ".$t['amount']."</b>\nLink: https://".Broker['crm_url']."/finance_verify.php?k=".(($t['id']*7)+4)."&id=".$t['id'];
            $url = 'https://api.telegram.org/bot1453786628:AAFvUx8bzZqxOxA8n5cF2ot_zaDrF7eXC0s/sendMessage?chat_id=-1001478321844&parse_mode=html&text='.urlencode($text);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output_t = curl_exec($ch);
            curl_close($ch);

        } else if ($t['type']=='Withdraw') {

            // Treasury
            $db = new iSQL(DB_admin);
            $where = "type IN ('Backoffice','Admin')";
            $agents = $db->select('users', $where, 'id');
            foreach ($agents as $agent) $ids[] = $agent['id'];
            $receivers = implode(",",$ids);
            global $notify; $notify->addMulti('User '.$_SESSION["id"],3, $_POST['transaction_id'], $receivers);

        }
        global $actLog; $actLog->add('Transaction',(($_POST['transaction_id']) ?? null),(($output->res) ? 1 : 0));

    }

    echo json_encode($output);
}

// Finance Verify
function financeVerify() {
    $output = new stdClass();
    $output->e = !(($_POST['transaction_id']) ?? false);
    $output->e = !(($_POST['user_id']) ?? false);
    $Transaction = new Transaction();

    if (!$output->e) {
        $output->res = $Transaction->verify($_POST['transaction_id'], $_POST['user_id'], 'finance_verify');
        $t = $Transaction->loadTransactionByID($_POST['transaction_id']);

        $usermanager = new usermanager();
        $userDone = $usermanager->getCustom($_SESSION['id'],'username');
        $name = $usermanager->getCustom($t['user_id'],'fname,lname');
        $full_name = $name['extra']['fname'].' '.$name['extra']['lname'];

        if ($t['type']=='Deposit') {
            $text = "USD ".$t['amount']." from ".$full_name." has been received by ".$userDone;

            // Treasury
            $db = new iSQL(DB_admin);
            $where = "type IN ('Backoffice','Admin')";
            $agents = $db->select('users', $where, 'id');
            foreach ($agents as $agent) $ids[] = $agent['id'];
            $receivers = implode(",",$ids);
            global $notify; $notify->addMulti('User '.$_SESSION["id"],3, $_POST['transaction_id'], $receivers);

        } else if ($t['type']=='Withdraw') {
            $text = "USD ".$t['amount']." from ".$full_name." has been sent by ".$userDone;

            // Desk Manager
            $db = new iSQL(DB_admin);
            $user_unit = $db->selectId('users', $t['user_id'], 'unit')['unit'];
            $where = "unit ='$user_unit' AND type='Manager'";
            $agents = $db->select('users', $where, 'id');
            foreach ($agents as $agent) $ids[] = $agent['id'];
            $receivers = implode(",",$ids);
            global $notify; $notify->addMulti('User '.$_POST['user_id'],4,$_POST['transaction_id'], $receivers);
            // User
            $notify->add('User '.$_POST['user_id'],4,$_POST['transaction_id'], $t['user_id']);
            $Transaction->done($t['id']);
        }

        $url = 'https://api.telegram.org/bot1453786628:AAFvUx8bzZqxOxA8n5cF2ot_zaDrF7eXC0s/sendMessage?chat_id=-1001478321844&parse_mode=html&text='.urlencode($text);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output_t = curl_exec($ch);
        curl_close($ch);

        global $actLog; $actLog->add('Transaction',(($_POST['transaction_id']) ?? null),(($output->res) ? 1 : 0));

    }
    echo json_encode($output);
}


// Treasury Verify
function treasuryVerify() {
    $output = new stdClass();
    $output->e = !(($_POST['transaction_id']) ?? false);
    $output->e = !(($_POST['user_id']) ?? false);
    $Transaction = new Transaction();
    if (!$output->e) {
        $output->res = $Transaction->verify($_POST['transaction_id'], $_POST['user_id'], 'treasury_verify');
        $t = $Transaction->loadTransactionByID($_POST['transaction_id']);

        $userManager = new userManager();
        $name = $userManager->getCustom($t['user_id'],'fname,lname');
        $full_name = $name['extra']['fname'].' '.$name['extra']['lname'];


        if ($t['type']=='Deposit') {

            // Desk Manager
            $db = new iSQL(DB_admin);
            $user_unit = $db->selectId('users', $t['user_id'], 'unit')['unit'];
            $where = "unit ='$user_unit' AND type='Manager'";
            $agents = $db->select('users', $where, 'id');
            foreach ($agents as $agent) $ids[] = $agent['id'];
            $receivers = implode(",",$ids);
            global $notify; $notify->addMulti('User '.$_POST['user_id'],4,$_POST['transaction_id'], $receivers);
            // User
            $notify->add('User '.$_POST['user_id'],4,$_POST['transaction_id'], $t['user_id']);
            $Transaction->done($t['id']);

        } else if ($t['type']=='Withdraw') {
            $text = "Type: <b>Withdrawal</b>\nName: <b>$full_name</b>\nAmount: <b>USD ".$t['amount']."</b>\nLink: https://".Broker['crm_url']."/finance_verify.php?k=".(($t['id']*7)+4)."&id=".$t['id'];

            $url = 'https://api.telegram.org/bot1453786628:AAFvUx8bzZqxOxA8n5cF2ot_zaDrF7eXC0s/sendMessage?chat_id=-1001478321844&parse_mode=html&text='.urlencode($text);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output_t = curl_exec($ch);
            curl_close($ch);

        }

        global $actLog; $actLog->add('Transaction',(($_POST['transaction_id']) ?? null),(($output->res) ? 1 : 0));

    }
    echo json_encode($output);
}

// Get Bonus
function getBonus() {
    $error = !(($_POST['unit']) ?? false);
    $Transaction = new Transaction();
    if (!$error) {
        global $db;

        $where = "type='FTD' AND unit IN (".$_POST['unit'].",0)";
        $opt = $db->select('bonus',$where);
        echo '<optgroup label="Select"><option value="0" selected>No Bonus</option></optgroup><optgroup label="FTD">';
        foreach ($opt as $item) {
           echo '<option value="'.$item['value'].'">'.$item['title'].'</option>';
        }
        echo '</optgroup>';

        $where = "type='RET' AND unit IN (".$_POST['unit'].",0)";
        $opt = $db->select('bonus',$where);
        echo '<optgroup label="Retention">';
        foreach ($opt as $item) {
            echo '<option value="'.$item['value'].'">'.$item['title'].'</option>';
        }
        echo '</optgroup>';

        $where = "type='CAM' AND unit IN (".$_POST['unit'].",0)";
        $opt = $db->select('bonus',$where);
        echo '<optgroup label="Campaign">';
        foreach ($opt as $item) {
            echo '<option value="'.$item['value'].'">'.$item['title'].'</option>';
        }
        echo '</optgroup>';
    }
    return (!$error);
}

// Add Card
function ccAdd() {
    $output = new stdClass();
    $output->e = !(($_POST['holder']) ?? false);
    $output->e = !(($_POST['card']) ?? false);
    $output->e = !(($_POST['cvv']) ?? false);
    $output->e = !(($_POST['exp_mm']) ?? false);
    $output->e = !(($_POST['exp_yy']) ?? false);
    $output->e = !(($_POST['user_id']) ?? false);
    if (!$output->e) {
        $card['holder']  = $_POST['holder'];
        $card['number']  = $_POST['card'];
        $card['cvv']     = $_POST['cvv'];
        $card['exp_mm']  = $_POST['exp_mm'];
        $card['exp_yy']  = $_POST['exp_yy'];
        $card['user_id'] = $_POST['user_id'];
        $card['save']    = $_POST['save'];
        global $db;
        $output->res = $db->insert('payment_cc',$card);
    }
    echo json_encode($output);
}


// Add Order
function orderAdd() {
    $output = new stdClass();
    $output->e = !(($_POST['transactions_id']) ?? false);
    $output->e = !(($_POST['user_id']) ?? false);
    $output->e = !(($_POST['amount']) ?? false);
    $output->e = !(($_POST['gateway_id']) ?? false);
    if (!$output->e) {
        $order['transactions_id']  = $_POST['transactions_id'];
        $order['user_id']     = $_POST['user_id'];
        $order['amount']      = $_POST['amount'];
        $order['gateway_id']  = $_POST['gateway_id'];
        $data['ccID']   = $_POST['ccID'];
        $data['USDTRY'] = $_POST['USDTRY'];
        $order['data']  = json_encode($data);
        global $db;
        $output->res = $db->insert('payment_orders',$order);
    }
    echo json_encode($output);
}

// Order Check
function orderCheck() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    if (!$output->e) {
        global $db;
        $output->res = $db->selectId('payment_orders',$_POST['id'],'status')['status'];
    }
    echo json_encode($output);
}

// Load Saved CC
function loadCC() {
    $output = new stdClass();
    $output->e = !(($_POST['user_id']) ?? false);
    if (!$output->e) {
        global $db;
        $where = 'user_id='.$_POST['user_id'].' AND save=1';
        $output->res = $db->select('payment_cc', $where,'*',0,0,'number');
    }
    echo json_encode($output);
}

// Remove Saved CC
function removeCC() {
    $output = new stdClass();
    $output->e = !(($_POST['card_id']) ?? false);
    $output->e = !(($_POST['card_num']) ?? false);
    if (!$output->e) {
        global $db;
        $where = 'user_id='.$_SESSION['id'].' AND number='.$_POST['card_num'];
        $card['save'] = 0;
        $output->res = $db->updateAny('payment_cc', $card, $where);
    }
    echo json_encode($output);
}