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

// Get Stats
function stats() {
    $output = new stdClass();

    $request = new CMT5Request();
    // Authenticate on the server using the Auth command
    if($request->Init('mt5.tradeclan.co.uk:443') && $request->Auth(1000,"@Sra7689227",1950,"WebManager"))
    {
        // USER GET State
    	$result = $request->Get('/api/user/account/get_batch?login='.$_POST['logins']);
    	//$output->result = $result;
    	if($result!=false)
    	{	
    		$json=json_decode($result);
    		//var_dump($json);

            $total = (is_array($json->answer)) ? count($json->answer) : 0;

    		for ($i = 0; $i < $total; $i++){
    		    $result2 = $request->Get('/api/user/get?login='.$json->answer[$i]->Login);
            	//$output->result = $result;
            	if($result2!=false)
            	{	
            		$json2=json_decode($result2);
            		$output->res [$i]['Name'] = $json2->answer->Name;
            	}
        		$output->res [$i]['Login'] = $json->answer[$i]->Login;
        		$output->res [$i]['Balance'] = $json->answer[$i]->Balance;
        		$output->res [$i]['Credit'] = $json->answer[$i]->Credit;
        		$output->res [$i]['Margin_Used'] = $json->answer[$i]->Margin;
        		$output->res [$i]['Margin_Free'] = $json->answer[$i]->MarginFree;
        		$output->res [$i]['Margin_Level'] = $json->answer[$i]->MarginLevel;
        		$output->res [$i]['Profit'] = $json->answer[$i]->Profit;
        		$output->res [$i]['Equity'] = $json->answer[$i]->Equity;
        		$output->res [$i]['Swap'] = $json->answer[$i]->Storage;
        		$output->res [$i]['Commission'] = $json->answer[$i]->Commission;
        		$output->res [$i]['Floating'] = $json->answer[$i]->Floating;
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

// Get openPositions
function openPositions() {
    $output = new stdClass();

    $request = new CMT5Request();
    // Authenticate on the server using the Auth command
    if($request->Init('mt5.tradeclan.co.uk:443') && $request->Auth(1000,"@Sra7689227",1950,"WebManager"))
    {
        // USER GET State
    	$result = $request->Get('/api/position/get_page?login='.$_POST['logins'].'&offset=0&total=100');
    	//$output->result = $result;
    	if($result!=false)
    	{	
    		$json=json_decode($result);
    		//var_dump($json);
            $total = (is_array($json->answer)) ? count($json->answer) : 0;
    		for ($i = 0; $i < $total; $i++){

        		$output->res [$i]['Login'] = $json->answer[$i]->Login;
        		$output->res [$i]['Position'] = $json->answer[$i]->Position;
        		$output->res [$i]['Symbol'] = $json->answer[$i]->Symbol;
        		$output->res [$i]['Action'] = $json->answer[$i]->Action;
        		$output->res [$i]['TimeCreate'] = date('Y-m-d H:i:s', strtotime("@".$json->answer[$i]->TimeCreate." -2 hours"));
        		$output->res [$i]['Volume'] = $json->answer[$i]->Volume;
        		$output->res [$i]['PriceOpen'] = $json->answer[$i]->PriceOpen;
        		$output->res [$i]['PriceSL'] = $json->answer[$i]->PriceSL;
        		$output->res [$i]['PriceTP'] = $json->answer[$i]->PriceTP;
        		$output->res [$i]['PriceCurrent'] = $json->answer[$i]->PriceCurrent;
        		$output->res [$i]['Storage'] = $json->answer[$i]->Storage;
        		$output->res [$i]['Profit'] = $json->answer[$i]->Profit;
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

// Post closePosition
function closePosition() {
    $output = new stdClass();

    $request = new CMT5Request();
    // Authenticate on the server using the Auth command
    if($request->Init('mt5.tradeclan.co.uk:443') && $request->Auth(1000,"@Sra7689227",1950,"WebManager"))
    {
        $result = $request->Get('/api/position/get_batch?ticket='.$_POST['position']);
    	//$output->result = $result;
    	if($result!=false)
    	{	
    		$json=json_decode($result);
    		$output->res = $json;
    		if($json->answer[0]->Login == $_POST['logins']){
    		    if($json->answer[0]->Action == 1){
    		        $type = 0;
    		    } else if($json->answer[0]->Action == 0){
    		        $type = 1;
    		    }
        		// Close Position
            	$path = '/api/dealer/send_request';
                $body = '{"Action" : "200","Login" : "'.$json->answer[0]->Login.'","Symbol" : "'.$json->answer[0]->Symbol.'","Volume" : "'.$json->answer[0]->Volume.'","TypeFill" : "1","Type" : "'.$type.'","PriceOrder" : "'.$json->answer[0]->PriceCurrent.'","Position" : "'.$json->answer[0]->Position.'","Digits" : "'.$json->answer[0]->Digits.'"}';
            	$result2 = $request->Post($path,$body);
            	//$output->result = $result;
            	if($result2!=false)
            	{	
            		$json2=json_decode($result2);
            		$output->res = $json2;
            		
            	} else {
            	    $output->e = "API call didn't work";
            	}
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

?>