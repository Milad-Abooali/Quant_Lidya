<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'MT4_TRADES';

// Table's primary key
$primaryKey = 'LOGIN';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = 
    array(
    	array( 'db' => 'MT4_TRADES.TICKET', 'dt' => 0, 'field' => 'TICKET', 'as' => 'TICKET' ),
    	array( 'db' => 'MT4_USERS.LOGIN', 'dt' => 1, 'field' => 'LOGIN', 'as' => 'LOGIN' ),
    	array( 'db' => 'MT4_USERS.NAME',  'dt' => 2, 'field' => 'NAME', 'as' => 'NAME' ),
    	array( 'db' => 'MT4_TRADES.PROFIT',  'dt' => 3, 'formatter' => function( $d, $row ){ 
    	    if($d >= 0){
    	        $type = "Deposit"; 
    	        
    	    } else if($d <= 0) { 
    	        $type = "Withdraw"; 
    	        
    	    }
    	    return $type;
    	    
    	},'field' => 'CMD', 'as' => 'CMD' ),
    	array( 'db' => 'MT4_TRADES.PROFIT',     'dt' => 4, 'field' => 'PROFIT', 'formatter' => function( $d, $row ){ 
    	    $profit = number_format($d,2,'.','');
    	    return $profit;
    	    
    	}, 'as' => 'PROFIT' ),
    	array( 'db' => 'MT4_TRADES.COMMENT', 'dt' => 5, 'field' => 'COMMENT', 'as' => 'COMMENT' ),
    	array( 'db' => 'MT4_TRADES.OPEN_TIME',   'dt' => 6, 'field' => 'OPEN_TIME', 'as' => 'OPEN_TIME' ),
    	//array( 'db' => 'CRM.ConversionOwnerName', 'dt' => 7, 'field' => 'ConversionOwnerName', 'as' => 'ConversionOwnerName' ),
        array( 'db' => 'IF(MT2.FTD != MT4_TRADES.OPEN_TIME AND CRM.TransactionOwner IS NOT NULL, CRM.TransactionOwner,IF(MT2.FTD = MT4_TRADES.OPEN_TIME AND CRM.TransactionOwner IS NOT NULL, CRM.TransactionOwner, "MT4 Direct"))', 'dt' => 7, 'field' => 'OWNER', 'as' => 'OWNER' ),
    	array( 'db' => 'IF(MT4_TRADES.COMMENT != "Deposit Wire Transfer" AND MT4_TRADES.COMMENT != "Deposit Credit Card" AND MT4_TRADES.COMMENT != "Withdrawal Wire Transfer" AND MT4_TRADES.COMMENT != "Deposit" AND MT4_TRADES.COMMENT != "Withdrawal Credit Card" AND MT4_TRADES.COMMENT != "Withdrawal" AND MT4_TRADES.COMMENT != "zeroutbonus" AND MT4_TRADES.COMMENT != "zerooutbonus" AND MT4_TRADES.COMMENT != "Zerout", "BONUS", 
IF((MT4_TRADES.COMMENT = "Deposit Wire Transfer" OR MT4_TRADES.COMMENT = "Deposit Credit Card" OR MT4_TRADES.COMMENT = "Deposit") AND MT2.FTD != MT4_TRADES.OPEN_TIME , "RED" ,
IF((MT4_TRADES.COMMENT = "Deposit Wire Transfer" OR MT4_TRADES.COMMENT = "Deposit Credit Card" OR MT4_TRADES.COMMENT = "Deposit") AND MT2.FTD = MT4_TRADES.OPEN_TIME , "FTD" ,
IF((MT4_TRADES.COMMENT = "zeroutbonus" OR MT4_TRADES.COMMENT = "Zeroutbonus" OR MT4_TRADES.COMMENT = "Zerout" OR MT4_TRADES.COMMENT = "zerooutbonus") AND MT2.FTD != MT4_TRADES.OPEN_TIME , "ZERO" ,
IF((MT4_TRADES.COMMENT = "Withdrawal Wire Transfer" OR MT4_TRADES.COMMENT = "Withdrawal Credit Card" OR MT4_TRADES.COMMENT = "Withdrawal") AND MT2.FTD != MT4_TRADES.OPEN_TIME, "R-WD" ,
IF((MT4_TRADES.COMMENT = "Withdrawal Wire Transfer" OR MT4_TRADES.COMMENT = "Withdrawal Credit Card" OR MT4_TRADES.COMMENT = "Withdrawal") AND MT2.FTD = MT4_TRADES.OPEN_TIME, "FTD-WD" , "BONUS"))))))', 'dt' => 8, 'field' => 'FTD', 'as' => 'FTD' ),
        //array( 'db' => 'IF((MT4_TRADES.COMMENT = "Deposit Wire Transfer" OR MT4_TRADES.COMMENT = "Deposit Credit Card" OR MT4_TRADES.COMMENT = "Deposit") AND MT2.FTD != MT4_TRADES.OPEN_TIME , "RET" ,"No RET")', 'dt' => 9, 'field' => 'RET', 'as' => 'RET' ),
        array( 'db' => 'CRMRet.RetentionOwnerName', 'dt' => 9, 'field' => 'RetentionOwnerName', 'as' => 'RetentionOwnerName' ),
    );

// SQL server connection information
$sql_details = array(
	'user' => DB_mt4['username'],
	'pass' => DB_mt4['password'],
	'db'   => DB_mt4['name'],
	'host' => DB_mt4['hostname']
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );
if($_SESSION["type"] == "Retention Agent") {
    $tp = $_POST['tp'];
    $tp = str_replace(",", '","', $tp);
} else if($_SESSION["type"] == "Sales Agent") {
    $tp = $_POST['tp'];
    $tp = str_replace(",", '","', $tp);
} else if($_SESSION["type"] == "IB") {
    $tp = $_POST['tp'];
    $tp = str_replace(",", '","', $tp);
} else {
    $email = $_POST['email'];
}
$startTime = $_POST['startTime'];
$endTime = $_POST['endTime'];
$servername = "localhost";

if($_SESSION["type"] == "Retention Agent") {
    $extraWhere = 'CMD = 6 AND MT4_USERS.LOGIN IN ("'.$tp.'") AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
} else if($_SESSION["type"] == "Sales Agent") {
    $extraWhere = 'CMD = 6 AND MT4_USERS.LOGIN IN ("'.$tp.'") AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
} else if($_SESSION["type"] == "IB") {
    $extraWhere = 'CMD = 6 AND MT4_USERS.LOGIN IN ("'.$tp.'") AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
} else {
    $extraWhere = 'CMD = 6 AND MT4_USERS.EMAIL = "'.$email.'" AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
}   
$joinQuery = 'FROM MT4_TRADES AS MT4_TRADES LEFT JOIN MT4_USERS AS MT4_USERS ON (MT4_TRADES.LOGIN = MT4_USERS.LOGIN) LEFT JOIN CRM AS CRMRet ON (MT4_TRADES.LOGIN = CRMRet.AccountNumber) LEFT JOIN (SELECT Ticket AS TICKET2, Login AS LOGIN3, TransactionOwner, DepositMethod FROM `CRMTR`) AS CRM ON (MT4_TRADES.TICKET = CRM.TICKET2) LEFT JOIN (SELECT MIN(OPEN_TIME) AS FTD, MIN(LOGIN) AS LOGIN2 FROM `MT4_TRADES` WHERE CMD = 6 GROUP BY LOGIN) AS MT2 ON (MT4_TRADES.LOGIN = MT2.LOGIN2)';


echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);

?>
