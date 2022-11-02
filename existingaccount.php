<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

	$type = $_POST['type'];
    $platform = $_POST['platform'];
    $tp = $_POST['tp'];
    $password = $_POST['password'];
    $userid = $_POST['userId'];
	
	$sql4 = "INSERT INTO tp (group_id,password,server,login,user_id,created_by) VALUES ('$type','$password','$platform','$tp','$userid','".$_SESSION["id"]."')";
    
//	echo $sql4;
	if (mysqli_query($DB_admin, $sql4)) {
	    echo json_encode(array("statusCode"=>200));

        // Add actLog
        global $actLog; $actLog->add('TP', $userid, 1, json_encode($_POST));

	} else {
	    echo mysqli_error($DB_admin);

        // Add actLog
        global $actLog; $actLog->add('TP', $userid, 0, json_encode($_POST));

	}
	mysqli_close($DB_admin);
?>