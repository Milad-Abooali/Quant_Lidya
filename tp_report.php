<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";
//$date = date('Y-m-d\TH:i:s\Z');

/**
 * Escape User Input Values POST & GET
 */
GF::escapeReq();

$date = new DateTime();
        
//echo $date;

//if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$tp_id = $_GET['login'];
	
	$request = new CMT5Request();
    $req_Init = $request->Init(MT5_AUTH['url'].':'.MT5_AUTH['port']);
    $req_auth = $request->Auth(MT5_AUTH['login'],MT5_AUTH['password'],MT5_AUTH['build'],MT5_AUTH['agent']);
    if($req_Init && $req_auth)
    {
        // USER GET State
        $code = '/user_get?login='.$tp_id;
        //echo $code;
    	$result=$request->Get($code);
    	if($result!=false)
    	{	
    	    //echo $result;
    		$json=json_decode($result);
    		
    		echo "Name: ".$json->answer->Name;
    		echo "</br>";
    		echo "Login: ".$json->answer->Login;
    		echo "</br>";
    		echo "Group: ".$json->answer->Group;
    		echo "</br>";
    		echo "Registration: ".gmdate("Y/m/d H:i:s", $json->answer->Registration);
    		echo "</br>";
    		echo "Last Access: ".gmdate("Y/m/d H:i:s", $json->answer->LastAccess);
    		echo "</br>";
    		echo "Last IP: ".$json->answer->LastIP;
    		echo "</br>";
    		echo "Leverage: ".$json->answer->Leverage;
    		echo "</br>";
    		echo "Balance: ".$json->answer->Balance;
    		echo "</br>";
    		echo "Credit: ".$json->answer->Credit;
    		echo "</br>";
    		echo "Balance Prev Day: ".$json->answer->BalancePrevDay;
    		echo "</br>";
    		echo "Balance Prev Month: ".$json->answer->BalancePrevMonth;
    		echo "</br>";
    		echo "Equity Prev Day: ".$json->answer->EquityPrevDay;
    		echo "</br>";
    		echo "Equity Prev Month: ".$json->answer->EquityPrevMonth;
    		echo "</br>";
    		
    		$code1 = '/deal_get_total?login='.$tp_id.'&from=30793709&to='.$date->getTimestamp();
    	    $result1 = $request->Get($code1);
    	    if($result1!=false)
    	    {
    	        //echo $result1;
    		    $json1=json_decode($result1);
    		    $TotalDeals = $json1->answer->total;
    		    echo "Total Deals: ".$TotalDeals;
    		    echo "</br>";
    	    }
    	    $trades_rows = array();
    	    for($i = 0; $i <= $TotalDeals; $i = $i + 100){
        		$code2 = '/deal_get_page?login='.$tp_id.'&from=30793709&to='.$date->getTimestamp().'&offset='.$i.'&total=100';
        	    $result2 = $request->Get($code2);
        	    if($result2!=false)
        	    {
        	        $step_rows = json_decode($result2)->answer;
            	    echo count($step_rows).' | ';
        	        foreach ($step_rows as $row) array_push($trades_rows, $row);
        	    }
    	    }
    	    echo count($trades_rows);
    	    GF::P($trades_rows);
    	    echo "<script>console.log(".json_encode($trades_rows).");</script>";

    	}
    }
    $request->Shutdown();
//}
?>