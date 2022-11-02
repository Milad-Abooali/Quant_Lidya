<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

	$note_id = $_POST['note_id'];
	$sql5 = 'DELETE FROM notes WHERE id ="'.$note_id.'"';
	//echo $sql5;
	if (mysqli_query($DB_admin, $sql5)) {
		echo json_encode(array("statusCode"=>200));

        // Add actLog
        global $actLog; $actLog->add('Note',$note_id,1,"Deleted");

    }
	else {
	    //echo mysqli_error($DB_admin);
		echo json_encode(array("statusCode"=>201));
	}
	mysqli_close($DB_admin);
?>