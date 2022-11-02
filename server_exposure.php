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
//SELECT MIN(SYMBOL), SUM(CMD), SUM(VOLUME), SUM(IF (CMD = 0, VOLUME, 0)) AS BUY, SUM(IF (CMD = 1, VOLUME, 0)) AS SELL FROM `MT4_TRADES` WHERE CLOSE_TIME = "1970-01-01 00:00:00" AND CMD < 2 GROUP BY SYMBOL

$columns = array(
	array( 'db' => 'SUBSTRING_INDEX(`c`.`SYMBOL`,".",1)', 'dt' => 0, 'field' => 'TEST', 'as' => 'TEST' ),
	//array( 'db' => 'MIN(MT4_TRADES.LOGIN)', 'dt' => 1, 'field' => 'LOGIN', 'as' => 'LOGIN' ),
	array( 'db' => 'SUM(`c`.`VOLUME`)',  'dt' => 1, 'field' => 'VOLUME', 'formatter' => function( $d, $row ){ 
	    $volume = number_format($d / 100,2);
	    return $volume;
	    
	}, 'as' => 'VOLUME' ),
	array( 'db' => 'SUM(`c`.`PROFIT`)',  'dt' => 2, 'field' => 'PROFIT', 'formatter' => function( $d, $row ){ 
	    $profit = number_format($d,2);
	    return $profit;
	}, 'as' => 'PROFIT' ),
	array( 'db' => 'SUM(IF (`c`.`CMD` = 0, `c`.`VOLUME`, 0))', 'dt' => 3, 'field' => 'BUY', 'formatter' => function( $d, $row ){ 
	    $buy = number_format($d / 100,2);
	    return $buy;
	}, 'as' => 'BUY' ),
	array( 'db' => 'SUM(IF (`c`.`CMD` = 1, `c`.`VOLUME`, 0))', 'dt' => 4, 'field' => 'SELL', 'formatter' => function( $d, $row ){ 
	    $sell = number_format($d / 100,2);
	    return $sell;
	}, 'as' => 'SELL' ),
	array( 'db' => 'AVG(IF (`c`.`CMD` = 0, `c`.`OPEN_PRICE`, `c`.`OPEN_PRICE`))',  'dt' => 5, 'field' => 'AVG_BUY','as' => 'AVG_BUY' ),
	array( 'db' => 'SUM(`c`.`OPEN_PRICE` * (FORMAT(`c`.`VOLUME` / 100,2)))',  'dt' => 6, 'field' => 'AVG_KAM', 'formatter' => function( $d, $row ){ 
	    $price = $d;
	    $volume2 = number_format($row['VOLUME'] / 100,2);
	    $total = $price / $volume2;
	    return $total;
	}, 'as' => 'AVG_KAM' ),
	array( 'db' => 'SUM(`c`.`PROFIT`)', 'dt' => 7, 'field' => 'PROFIT2', 'formatter' => function( $d, $row ){ 
	    $profit2 = $d;
	    $balance = $row['BALANCE'];
	    $total2 = $balance - $profit2;
	    return $total2 *= -1 ;
	    
	}, 'as' => 'PROFIT2' ),
	array( 'db' => 'SUM(`u`.`BALANCE`)',  'dt' => 8, 'field' => 'BALANCE','as' => 'BALANCE' ),
	array( 'db' => 'SUM(`c`.`CMD`)',  'dt' => 9, 'field' => 'CMD','as' => 'CMD' ),
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

require( 'ssp.class2.php' );

$extraWhere = '`u`.`LOGIN` <> "2402" AND `u`.`LOGIN` <> "1310087188" AND `u`.`LOGIN` <> "1300377707" AND `u`.`LOGIN` <> "461988" AND `u`.`LOGIN` <> "35" AND `u`.`LOGIN` <> "555" AND CLOSE_TIME = "1970-01-01 00:00:00" AND CMD < 2';
$joinQuery = "FROM `MT4_TRADES` AS `c` JOIN `MT4_USERS` AS `u` ON (`u`.`LOGIN` = `c`.`LOGIN`)";
$groupBy = "TEST";


echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy )
);

?>
