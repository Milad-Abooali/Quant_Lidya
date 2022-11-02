<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";

$date = date('Y-m-d\TH:i:s\Z');
        
//echo $date;

if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        /**
         * Escape User Input Values POST & GET
         */
        GF::escapeReq();

    	$userId = $_POST['userId'];
    	$amount = $_POST['Amount'];
    	$comment = $_POST['Comment'];
    	$type = $_POST['Type'];
    	$userIdto = $_POST['userId'];
    	
    	if($type == "Withdraw"){
    	    $amount = -abs($amount);
    	} else {
    	    if ($amount < 0)
            {
            	$amount = abs($amount);
            }
    	}
    	
    	echo $amount;
    	
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
        
        //echo $response;
        
        $data = json_decode($response);
        
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
          CURLOPT_URL => "https://mywebapi.com/api/MT4/895a45cf-8c17-4c16-80ad-79958f133358/TradeTransaction",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>'{"expiration": "'.$date.'","tradeTransactionType": "BrBalance","tradeCommand":"Balance","orderBy":'.$userId.',"price":'.$amount.',"comment":"'.$comment.'"}',
          CURLOPT_HTTPHEADER => array(
            "Api-Version: 1.0",
            "Content-Type: application/json",
            "Authorization: Bearer ".$data->access_token.""
          ),
        ));
        
        $response3 = curl_exec($curl3);
        
        curl_close($curl3);

        // Add actLog
        global $actLog; $actLog->add('MyWebAPI',$userId,1,'{"Action":"Change Balance","Group":"'.$amount.'"}');
        echo $response3;
        
    	//$servername = "localhost";
        //$username = "lidyapar_admin";
        //$password2 = "@Sra7689227";
        //$dbname = "lidyapar_admin";
        
        // Create connection
        //$conn = new mysqli($servername, $username, $password2, $dbname);
        // Check connection
        //if ($conn->connect_error) {
        //    die("Connection failed: " . $conn->connect_error);
        //}
        
        //$date = date('Y-m-d H:i:s');
        
        //$sqlPass = "UPDATE tp SET password = '$password', updated_at = '$date', updated_by = '".$_SESSION["id"]."' WHERE login = '$userId'";
        //mysqli_query($conn, $sqlPass);
        //mysqli_close($conn);
    }
}
?>