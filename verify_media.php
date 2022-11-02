<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();
    
    $id = $_POST["id"];
    $status = $_POST["status"];
    
    $mysql_update = 'UPDATE media SET verify="'.$status.'" WHERE id="'.$id.'"';
    
    if (mysqli_query($DB_admin, $mysql_update)) {
        //echo $mysql_update;
		echo json_encode(array("statusCode"=>200));

        // Add actLog
        global $actLog; $actLog->add('Verify',$id,1,json_encode($_POST));
	} 
	else {
	    echo mysqli_error($DB_admin);
		echo json_encode(array("statusCode"=>201));

        // Add actLog
        global $actLog; $actLog->add('Verify',$id,0,json_encode($_POST));
	}
?>