<?php
require_once "config.php";

require_once "config.php";

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
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
$table = 'user_extra';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'users.id', 'dt' => 0, 'field' => 'id', 'as' => 'id' ),
    array( 'db' => 'user_extra.fname', 'dt' => 1, 'field' => 'fname', 'as' => 'fname' ),
    array( 'db' => 'user_extra.lname', 'dt' => 2, 'field' => 'lname', 'as' => 'lname' ),
    array( 'db' => 'users.username', 'dt' => 3, 'field' => 'username', 'as' => 'username' ),
    array( 'db' => 'users.email', 'dt' => 4, 'field' => 'email', 'as' => 'email' ),
    array( 'db' => 'user_extra.phone', 'dt' => 5, 'field' => 'phone', 'as' => 'phone' ),
    array( 'db' => 'DATE_FORMAT(user_extra.created_at, "%y/%m/%d %H:%i:%s")', 'dt' => 6, 'field' => 'created_at', 'as' => 'created_at' ),
    array( 'db' => '(SELECT username FROM users WHERE user_extra.conversion = users.id)', 'dt' => 7, 'field' => 'conversion', 'as' => 'conversion' ),
    array( 'db' => 'status.status', 'dt' => 8, 'field' => 'status', 'as' => 'status' ),
    array( 'db' => 'DATE_FORMAT(user_extra.lastnotedate, "%y/%m/%d %H:%i:%s")', 'dt' => 9, 'field' => 'lastnotedate', 'as' => 'lastnotedate' ),
    array( 'db' => 'user_marketing.lead_src', 'dt' => 10, 'field' => 'source', 'as' => 'source' ),
    array( 'db' => 'users.unit', 'dt' => 11, 'field' => 'source', 'as' => 'unit' ),
    array( 'db' => 'DATE_FORMAT(user_extra.updated_at, "%y/%m/%d %H:%i:%s")', 'dt' => 12, 'field' => 'created_at', 'as' => 'updated_at' ),
    array( 'db' => 'users.id', 'dt' => 13, 'field' => 'id', 'as' => 'action' ),
    array( 'db' => 'user_extra.country', 'dt' => 14, 'field' => 'country', 'as' => 'country' )
);
 
// SQL server connection information
$sql_details = array(
	'user' => DB_admin['username'],
	'pass' => DB_admin['password'],
	'db'   => DB_admin['name'],
	'host' => DB_admin['hostname']
);
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class3.php' );
if($_SESSION["type"] == "Admin") {
    $joinQuery = 'FROM users AS users LEFT JOIN user_extra AS user_extra ON users.id = user_extra.user_id LEFT JOIN status AS status ON user_extra.status = status.id LEFT JOIN user_marketing ON user_marketing.user_id = user_extra.user_id';
    $extraWhere = 'user_extra.type = 1';
} else if ($_SESSION["type"] == "Manager") {
    $joinQuery = 'FROM users AS users LEFT JOIN user_extra AS user_extra ON users.id = user_extra.user_id LEFT JOIN status AS status ON user_extra.status = status.id LEFT JOIN user_marketing ON user_marketing.user_id = user_extra.user_id';
    $extraWhere = 'user_extra.type = 1 AND user_extra.unit = "'.$_SESSION["unitn"].'"';
} else if ($_SESSION["type"] == "Sales Agent") {
    $joinQuery = 'FROM users AS users LEFT JOIN user_extra AS user_extra ON users.id = user_extra.user_id LEFT JOIN status AS status ON user_extra.status = status.id LEFT JOIN user_marketing ON user_marketing.user_id = user_extra.user_id';
    $extraWhere = 'user_extra.type = 1 AND user_extra.unit = "'.$_SESSION["unitn"].'" AND user_extra.conversion = "'.$_SESSION["id"].'"';
}
//$joinQuery = 'FROM users AS users LEFT JOIN user_extra AS user_extra ON users.id = user_extra.user_id';
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);

//
//    if($_SESSION["type"] == "Admin") {
//        $joinQuery = 'users AS users LEFT JOIN user_extra AS user_extra ON users.id = user_extra.user_id LEFT JOIN status AS status ON user_extra.status = status.id LEFT JOIN user_marketing ON user_marketing.user_id = user_extra.user_id';
//        $extraWhere = ' AND user_extra.type = 1';
//    } else if ($_SESSION["type"] == "Manager") {
//        $joinQuery = 'users AS users LEFT JOIN user_extra AS user_extra ON users.id = user_extra.user_id LEFT JOIN status AS status ON user_extra.status = status.id LEFT JOIN user_marketing ON user_marketing.user_id = user_extra.user_id';
//        $extraWhere = ' AND user_extra.type = 1 AND user_extra.unit = "'.$_SESSION["unitn"].'"';
//    } else if ($_SESSION["type"] == "Sales Agent") {
//        $joinQuery = 'users AS users LEFT JOIN user_extra AS user_extra ON users.id = user_extra.user_id LEFT JOIN status AS status ON user_extra.status = status.id LEFT JOIN user_marketing ON user_marketing.user_id = user_extra.user_id';
//        $extraWhere = ' AND user_extra.type = 1 AND user_extra.unit = "'.$_SESSION["unitn"].'" AND user_extra.conversion = "'.$_SESSION["id"].'"';
//    }
//    global $db;
//    include 'lib/qsp.php';
//    $qsp = new qsp($db);
//    $query = $joinQuery.$extraWhere;
//    echo json_encode($qsp->Complex($_GET, $query, $columns, $primaryKey));