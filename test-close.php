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
    // Authenticate on the server using the Auth command
    if($request->Init('mt5.tradeclan.co.uk:443') && $request->Auth(1000,"@Sra7689227",1950,"WebManager"))
    {
        // USER GET State
        //request->Login(10003);
        //request->SourceLogin(1000);
        //request->Action(IMTRequest::TA_DEALER_POS_EXECUTE);
        //request->Type(IMTOrder::OP_BUY);
        //request->Volume(SMTMath::VolumeToInt(1.0));
        //request->Symbol(L"GBPUSD");
        //request->PriceOrder(1.9999);
        //request->Position(95910); //ticket of SELL 1.00 lot GBPUSD
        //res=manager->DealerSend(request,&sink,id);
        
        $code = '/api/dealer/send_request';
        $body = '{"Action" : "200","Login" : "100001","Symbol" : "EURUSD","Volume" : "100","TypeFill" : "1","Type" : "1","PriceOrder" : "1.12970","Position" : "1741389","Digits" : "5"}';
    	$result = $request->Post($code,$body);
    	
    	if($result!=false)
    	{	
    	    echo $result;
    		$json=json_decode($result);

    	    echo "<script>console.log(".json_encode($trades_rows).");</script>";
    	}
    }
    $request->Shutdown();
//}
?>