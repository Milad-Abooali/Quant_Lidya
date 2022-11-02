<?php

/**
 * Gateway Functions Class
 * 1:47 PM Monday, January 4, 2021 | M.Abooali
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Get Rates
function rates() {
    $output = new stdClass();

    $request = new CMT5Request();
    // Authenticate on the server using the Auth command
    if($request->Init('mt5.tradeclan.co.uk:443') && $request->Auth(1000,"@Sra7689227",1950,"WebManager"))
    {
        // USER GET State
    	$result = $request->Get('/api/tick/last_group?symbol='.$_POST['symbols'].'&group=real\\PERSVIP&trans_id=0');
    	if($result!=false)
    	{	
    		$json=json_decode($result);
    		//var_dump($json);
    		$total = count($json->answer);
    		for ($i = 0; $i < $total; $i++){
    		    if($json->answer[$i]->Symbol == "XAUUSD"){
    		        $Contract = 100;
    		    } else {
    		        $Contract = 100000;
    		    }
        		$output->res [$i]['symbol'] = $json->answer[$i]->Symbol;
        		$output->res [$i]['bid'] = $json->answer[$i]->Bid;
        		$output->res [$i]['ask'] = $json->answer[$i]->Ask;
        		$output->res [$i]['spread'] = round(($json->answer[$i]->Ask - $json->answer[$i]->Bid) * $Contract);
    		}
    	} else {
    	    $output->e = "API call didn't work";
    	}
    } else {
        $output->e = "Auth Error"; 
    }
    
    $request->Shutdown();
    
    echo json_encode($output);
}

function exchangeRate() {
    echo json_encode(GF::exchangeRate($_POST['s_sym'], $_POST['d_sym']));
}

