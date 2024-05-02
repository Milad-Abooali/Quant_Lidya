<?php

if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager") {

        $date = date('Y-m-d\TH:i:s\Z');

        $is_bonus    = $_POST['is_bonus'] ?? false;

        $tp_id = $_POST['tp_id'] ?? false;
        if (!$tp_id) die('Please Send TP ID !');

        $type = $_POST['type'];
        if (!$type) die('Please Send Type !');

        $amount = $_POST['amount'] ?? false;
        if (!$amount) die('Please Send Amount !');
        if ($type=='Withdraw') $amount = -$amount;

        $comment = $_POST['comment'] ?? false;
        if (!$comment) die('Please Send Comment !');

        global $db;
        $where  = "`login`=".$tp_id;
        $server = $db->selectRow('tp',$where)['server'];

    	if ($server == "MT4") {
    	    $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://auth.cplugin.net/connect/token",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>"grant_type=client_credentials&scope=webapi&client_id=cd205448-0fb9-4dd8-9abf-9e0687b149ac&client_secret=7fa5cbbd-13c2-43a4-97b8-842fbc54f0f1",
              CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response);
            $curl2 = curl_init();
            curl_setopt_array($curl2, array(
              CURLOPT_URL => "https://mywebapi.com/api/MT4/895a45cf-8c17-4c16-80ad-79958f133358/UserRecordGet/".$tp_id,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(
                "api-version: 1.0",
                "Authorization: Bearer ".$data->access_token.""
              ),
            ));
            $response2 = curl_exec($curl2);
            curl_close($curl2);
            $user = json_decode($response2);
            $curl3 = curl_init();
            curl_setopt_array($curl3, array(
              CURLOPT_URL => "https://mywebapi.com/api/MT4/895a45cf-8c17-4c16-80ad-79958f133358/TradeTransaction",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>'{"expiration": "'.$date.'","tradeTransactionType": "BrBalance","tradeCommand":"Balance","orderBy":'.$tp_id.',"price":'.$amount.',"comment":"'.$comment.'"}',
              CURLOPT_HTTPHEADER => array(
                "Api-Version: 1.0",
                "Content-Type: application/json",
                "Authorization: Bearer ".$data->access_token.""
              ),
            ));
            $response3 = curl_exec($curl3);
            curl_close($curl3);
    	} else if ($server == "MT5") {
            $request = new CMT5Request();
            $req_Init = $request->Init(MT5_AUTH['url'].':'.MT5_AUTH['port']);
            $req_auth = $request->Auth(MT5_AUTH['login'],MT5_AUTH['password'],MT5_AUTH['build'],MT5_AUTH['agent']);
            if($req_Init && $req_auth)
            {
                $comment = str_replace(' ', '%20', $comment);
                $type_mt5 = ($is_bonus) ? 6 : 2; // 6: Bonus | 2: D || W
                $result = $request->Get('/trade_balance?login='.$tp_id.'&type='.$type_mt5.'&balance='.$amount.'&comment='.$comment.'&check_margin=0');
            }
            $request->Shutdown();
    	}
        GF::P($result);
}