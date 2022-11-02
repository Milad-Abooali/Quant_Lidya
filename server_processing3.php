<?php

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
$columns = array(
	array( 'db' => 'MT4_TRADES.TICKET', 'dt' => 0, 'field' => 'TICKET', 'as' => 'TICKET' ),
	array( 'db' => 'MT4_USERS.LOGIN', 'dt' => 1, 'field' => 'LOGIN', 'as' => 'LOGIN' ),
	array( 'db' => 'MT4_USERS.NAME',  'dt' => 2, 'field' => 'NAME', 'as' => 'NAME' ),
	array( 'db' => 'MT4_TRADES.CMD',  'dt' => 3, 'field' => 'CMD', 'formatter' => function( $d, $row ){ 
	    if($d == 0){
	        $type = "Buy"; 
	    } else if($d == 1) { 
	        $type = "Sell"; 
	    } else if($d == 2) { 
	        $type = "Buy Limit"; 
	    } else if($d == 3) { 
	        $type = "Sell Limit"; 
	    } else if($d == 4) { 
	        $type = "Buy Stop"; 
	    } else if($d == 5) { 
	        $type = "Sell Stop"; 
	    } 
	    
	    return $type;
	    
	},'as' => 'CMD' ),
	array( 'db' => 'MT4_TRADES.VOLUME',  'dt' => 4, 'field' => 'VOLUME', 'formatter' => function( $d, $row ){ 
	    $volume = number_format($d / 100,2);
	    return $volume;
	    
	}, 'as' => 'VOLUME' ),
	array( 'db' => 'MT4_TRADES.SYMBOL',     'dt' => 5, 'field' => 'SYMBOL', 'as' => 'SYMBOL' ),
	//array( 'db' => 'MT4_TRADES.SL', 'dt' => 6, 'field' => 'SL', 'as' => 'SL' ),
	//array( 'db' => 'MT4_TRADES.TP',   'dt' => 7, 'field' => 'TP', 'as' => 'TP' ),
	array( 'db' => 'MT4_TRADES.OPEN_PRICE',   'dt' => 6, 'field' => 'OPEN_PRICE', 'as' => 'OPEN_PRICE' ),
	array( 'db' => 'MT4_TRADES.OPEN_TIME', 'dt' => 7, 'field' => 'OPEN_TIME', 'as' => 'OPEN_TIME' ),
	//array( 'db' => 'MT4_TRADES.CLOSE_TIME', 'dt' => 9, 'field' => 'CLOSE_TIME', 'as' => 'CLOSE_TIME' ),
	array( 'db' => 'MT4_TRADES.SWAPS', 'dt' => 8, 'field' => 'SWAPS', 'formatter' => function( $d, $row ){ 
	    $swap = number_format($d,2);
	    return $swap;
	    
	}, 'as' => 'SWAPS' ),
	array( 'db' => 'MT4_TRADES.PROFIT', 'dt' => 9, 'field' => 'PROFIT', 'formatter' => function( $d, $row ){ 
	    $profit = number_format($d,2);
	    return $profit;
	    
	}, 'as' => 'PROFIT' ),
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

$unit = $_GET['unit'];
$startTime = $_GET['startTime'];
$endTime = $_GET['endTime'];

    if ($unit == "Turkish"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "1"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
    } else if($unit == "Farsi"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "3"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
    } else if($unit == "Arabic"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "4"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
    } else if($unit == "English") {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "5"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }

        $array = str_replace(",", '","', $unitn);
    } else if($unit == "STPL") {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "6"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
    } else if($unit == "All") {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
    } else {
        
    }

//$in = '("' . implode('","', $groups) .'")';
//$notin = '("' . implode('","', $exclude) .'")';

$extraWhere = 'MT4_USERS.AGENT_ACCOUNT <> "1" AND CMD != 6 AND MT4_TRADES.CLOSE_TIME = "1970-01-01 00:00:00" AND MT4_USERS.GROUP IN ("'.$array.'")';

$joinQuery = 'FROM MT4_TRADES AS MT4_TRADES LEFT JOIN MT4_USERS AS MT4_USERS ON (MT4_TRADES.LOGIN = MT4_USERS.LOGIN)';

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);

?>
