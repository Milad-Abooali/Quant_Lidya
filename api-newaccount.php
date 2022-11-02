<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";
$date = date('Y-m-d\TH:i:s\Z');
        
//echo $date;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

	$userId = $_POST['userId'];
	$type = $_POST['type'];
	$platform = $_POST['platform'];
	$group = $_POST['group'];
	$currency = $_POST['currency'];
	$name = $_POST['name'];
	$uname = $_POST['uname'];
	$usname = $_POST['usname'];
	$email = $_POST['email'];
	$amount = $_POST['amount'];
	
	if($platform == "MT5"){
    	// Example of use
        $request = new CMT5Request();
        // Authenticate on the server using the Auth command
        if($request->Init('mt5.tradeclan.co.uk:443') && $request->Auth(1000,"@Sra7689227",1950,"WebManager"))
        {
            function rand_string( $length ) {
            $chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789";
            return substr(str_shuffle($chars),0,$length);
            
            }
            $main_pass = rand_string(8);
            $investor_pass = rand_string(8);
            if($type == "2"){
                $prefixgroup = "real\\";
            } else {
                $prefixgroup = "demo\\";
            }
            // USER GET State
            $code = '/user_add?pass_main='.$main_pass.'&pass_investor='.$investor_pass.'&group='.$prefixgroup.''.$group.'&name=test&email='.$email.'&leverage=200';
            echo $code;
        	$result=$request->Get($code);
        	if($result!=false)
        	{	
        	    echo $result;
        		$json=json_decode($result);
        		$login_5 = $json->answer->Login;
        		if($type == "1"){
            	    $result2=$request->Get('/trade_balance?login='.$login_5.'&type=2&balance='.$amount.'&comment=Deposit');
            	}
        	}
        }
        $request->Shutdown();
	} else {
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
            
    	if($type == "1"){
            $curl2 = curl_init();
    
            curl_setopt_array($curl2, array(
              CURLOPT_URL => "https://mywebapi.com/api/MT4/5f601116-2a15-448c-afea-66e7b7d7c6c5/UserRecordGet/".$userId,
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
              CURLOPT_URL => "https://mywebapi.com/api/MT4/5f601116-2a15-448c-afea-66e7b7d7c6c5/UserRecordNew",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>'{"enable": 1,"leverage": 200,"group":"'.$group.'","name": "'.$name.'","country": "'.$country.'","email": "'.$email.'"}',
              CURLOPT_HTTPHEADER => array(
                "Api-Version: 1.0",
                "Content-Type: application/json",
                "Authorization: Bearer ".$data->access_token.""
              ),
            ));
            
            $response3 = curl_exec($curl3);
            
            $tp = json_decode($response3);
            
            curl_close($curl3);
            
            echo $response3;
    	} else if ($type == "2") {
            $curl2 = curl_init();
    
            curl_setopt_array($curl2, array(
              CURLOPT_URL => "https://mywebapi.com/api/MT4/895a45cf-8c17-4c16-80ad-79958f133358/UserRecordGet/".$userId,
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
              CURLOPT_URL => "https://mywebapi.com/api/MT4/895a45cf-8c17-4c16-80ad-79958f133358/UserRecordNew",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>'{"enable": 1,"leverage": 200,"group":"'.$group.'","name": "'.$name.'","country": "'.$country.'","email": "'.$email.'"}',
              CURLOPT_HTTPHEADER => array(
                "Api-Version: 1.0",
                "Content-Type: application/json",
                "Authorization: Bearer ".$data->access_token.""
              ),
            ));
            
            $response3 = curl_exec($curl3);
            
            $tp = json_decode($response3);
            
            curl_close($curl3);
            
            echo $response3;
    	}
	}
	

    $date = date('Y-m-d H:i:s');
    if($platform == "MT5"){
        $sqlPass = "INSERT INTO tp (user_id,login,password,group_id,server,created_at,created_by,updated_at,updated_by) VALUES ('$userId','$login_5','$main_pass','$type','$platform','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";

        $request1 = new CMT5Request();
        if($request1->Init('mt5.tradeclan.co.uk:443') && $request1->Auth(1000,"@Sra7689227",1950,"WebManager"))
        {
            $result1=$request1->Get('/user_get?login='.$login_5);
        	if($result1!=false)
        	{	
        		$json1=json_decode($result1);
        		if((int)$json1->retcode==0)
        		{
        			$user1=$json1->answer;
        			//--- Changing The Details
        			$user1->FirstName=$uname;
        			$user1->LastName=$usname;
        			$result1=$request1->Post('/user_update',json_encode($user1));
        		}
        	}
        	$request1->Shutdown();
        }
        
        mysqli_query($DB_admin, $sqlPass);

        $inserted_id = mysqli_insert_id($DB_admin);
        // Add actLog
        global $actLog; $actLog->add('MyWebAPI',$userId,1,'{"action":"New TP","user_id":"'.$userId.'","TP ID":"'.$inserted_id.'"}');

        // Send Email
        global $db;
        global $_Email_M;
        $where = "email='$email' AND unit IN (".Broker['units'].")";
        $receivers[] = array (
            'id'    =>  $db->selectRow('users',$where)['id'],
            'email' =>  $email,
            'data'  =>  array(
                'fname' =>  $uname,
                'lname' =>  $usname,
                'login' =>  $login_5,
                'pass' =>  $main_pass,
                'ipass' =>  $investor_pass
            )
        );
        $subject = $theme = 'TP_New_Account';
        $_Email_M->send($receivers, $theme, $subject);

    } else {
        $sqlPass = "INSERT INTO tp (user_id,login,password,group_id,server,created_at,created_by,updated_at,updated_by) VALUES ('$userId','$tp->login','$tp->password','$type','$platform','$date','".$_SESSION["id"]."','$date','".$_SESSION["id"]."')";
        mysqli_query($DB_admin, $sqlPass);

        $inserted_id = mysqli_insert_id($DB_admin);
        // Add actLog
        global $actLog; $actLog->add('MyWebAPI',$userId,1,'{"action":"New TP","user_id":"'.$userId.'","TP ID":"'.$inserted_id.'"}');

        // Send Email
        global $db;
        global $_Email_M;
        $where = "email='$email' AND unit IN (".Broker['units'].")";
        $receivers[] = array (
            'id'    =>  $db->selectRow('users',$where)['id'],
            'email' =>  $email,
            'data'  =>  array(
                'fname' =>  $uname,
                'lname' =>  $usname,
                'login' =>  $tp->login,
                'pass' =>  $tp->password,
                'ipass' =>  ''
            )
        );
        $subject = $theme = 'TP_New_Account';
        $_Email_M->send($receivers, $theme, $subject);
    }

    // Add actLog
    global $actLog; $actLog->add('MyWebAPI',$userId,1, json_encode($_POST));

    mysqli_close($DB_admin);
}
?>