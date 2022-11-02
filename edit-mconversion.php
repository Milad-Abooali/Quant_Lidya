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

    $usersCount = count($_POST["users"]);
    
    //echo $usersCount;
    
    $conversion = $_POST['conversion'];
    
	$date = date('Y-m-d H:i:s');
	
    $db = new iSQL(DB_admin);
	$table = 'user_extra';
	
	if($usersCount) {
        $act_detail = array();
        for($i=0;$i<$usersCount;$i++) {
            global $userManager;
            $old_conversion = $userManager->getCustom($_POST["users"][$i],'conversion')['extra']['conversion'];

            $where = 'user_id='.$_POST["users"][$i];
            $data['conversion'] = $_POST["conversion"];
            $data['updated_at'] = $date;
            $data['assigned_date'] = $date;
            $data['updated_by'] = $_SESSION["id"];
            $db->updateAny($table, $data, $where);

            //$sql = "UPDATE user_extra SET conversion = ".$_POST["conversion"].", assigned_date = '$date', updated_at = '$date', updated_by = '".$_SESSION["id"]."' WHERE user_id = '".$_POST["users"][$i]."'";
            if ($db->updateAny($table, $data, $where)) {
                echo json_encode(array("statusCode"=>200));



            } else {
    //    	    echo mysqli_error($db->log());
                echo json_encode(array("statusCode"=>201));
            }
            $act_detail[] = array(
                'user'          => $_POST["users"][$i],
                'old_conversion' => $old_conversion,
                'new_conversion' => $_POST['conversion']
            );
            //echo json_encode($db->log('sql'));
	    }
        // Add actLog
        global $actLog; $actLog->add('Mass Assign', null,1, json_encode($act_detail));
	}
	$notify->add('mconversion',6,$i,$_POST["conversion"]);
	mysqli_close($DB_admin);
?>