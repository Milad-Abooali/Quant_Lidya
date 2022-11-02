<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    $id = $_POST["id"];
    $status = $_POST["status"];
    $cat = $_POST["cat"];
    $type = $_POST["type"];

    // Update Verify Cat
    if (($_SESSION['type']=='Admin' || $_SESSION['type']=='Manager') && $cat =='desk')          $data['desk_verify'] = $status;
    if (($_SESSION['type']=='Admin' || $_SESSION['type']=='Backoffice') && $cat =='finance')    $data['finance_verify'] = $status;
    if (($_SESSION['type']=='Admin' || $_SESSION['type']=='Dealing') && $cat =='treasury')      $data['treasury_verify'] = $status;
    if ($data)  $update = $db->updateId('transactions',$id,$data);

    if ($update) {

        // Notify
        $steps = $db->selectId('transactions',$id,'desk_verify,finance_verify,treasury_verify,user_id');
        $user_unit = $db->selectId('users', $steps['user_id'], 'unit')['unit'];

        $transaction = $db->selectId('transactions',$id);

        $where = "user_id=".$steps['user_id'];
        $user_name = $db->select('user_extra', $where, 'fname,lname',1)[0];
        $full_name = $user_name['fname']." ".$user_name['lname'];

        if ($steps['desk_verify'] && !$steps['finance_verify'] && !$steps['treasury_verify']) $verify = 'desk';
        if ($steps['desk_verify'] && $steps['finance_verify'] && !$steps['treasury_verify'])  $verify = 'desk_finance';
        if ($steps['desk_verify'] && !$steps['finance_verify'] && $steps['treasury_verify'])  $verify = 'desk_treasury';
        if ($steps['desk_verify'] && $steps['finance_verify'] && $steps['treasury_verify'])   $verify = 'all';

        if($verify == 'desk')           $where = ($type == "1") ? "type='Backoffice'" : "type='Dealing' OR type='Admin";
        if($verify == 'desk_finance')   $where = "type='Dealing' OR type='Admin'";
        if($verify == 'desk_treasury')  $where = "type='Backoffice'";
        if($verify == 'all')            $where = "unit ='$user_unit' AND type='Manager'";

        if($where == "type='Backoffice'") {

            if ($type == "1") {  // From Desk
                $text = "Type: <b>Deposit</b>\nName: <b>$full_name</b>\nAmount: <b>USD ".$transaction['amount']."</b>\nLink: https://".Broker['crm_url']."/finance_verify.php?id=$id&t=d ";
            } else { // from treasury
                $text = "Type: <b>Withdrawal</b>\nName: <b>$full_name</b>\nAmount: <b>USD ".$transaction['amount']."</b>\nLink: https://".Broker['crm_url']."/finance_verify.php?id=$id&t=w ";
            }

            $url = 'https://api.telegram.org/bot1453786628:AAFvUx8bzZqxOxA8n5cF2ot_zaDrF7eXC0s/sendMessage?chat_id=-1001478321844&parse_mode=html&text='.urlencode($text);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            GF::P($text);
        }


        $receivers = $db->select('users', $where,'id');
        if($receivers) foreach ($receivers as $agent) $ids[] = $agent['id'];

        if($receivers) if($verify != 'all') {
            $receivers = implode(",", $ids);
            global $notify; $notify->addMulti('Transaction '.$_SESSION['id'],3, $id, $receivers);
        } else {
            $receivers = implode(",", $ids);
            global $notify; $notify->addMulti('Transaction '.$_SESSION['id'],4, $id, $receivers);
            global $notify; $notify->add('Transaction '.$_SESSION['id'],4, $id, $steps['user_id']);
        }

        // Add actLog
        global $actLog; $actLog->add('Verify',$id,1,json_encode($_POST));

        // header
        echo json_encode(array("statusCode"=>200));
    } else {

        // ActLog
        global $actLog; $actLog->add('Verify',$id,0,json_encode($_POST));

        // header
        echo json_encode(array("statusCode"=>201));
    }