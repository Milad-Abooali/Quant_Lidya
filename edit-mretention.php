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
    $retention = $_POST['retention'];
	$date = date('Y-m-d H:i:s');
	if ($usersCount) {
        $act_detail = array();
        for($i=0;$i<$usersCount;$i++) {
            global $userManager;
            $old_retention = $userManager->getCustom($_POST["users"][$i],'retention')['extra']['retention'];
            if (mysqli_query($DB_admin, "UPDATE user_extra SET retention = ".$_POST["retention"].", assigned_date_ret = '$date', updated_at = '$date', updated_by = '".$_SESSION["id"]."' WHERE user_id = '".$_POST["users"][$i]."'")) {
                echo json_encode(array("statusCode"=>200));
                $act_detail[] = array(
                    'user'          => $_POST["users"][$i],
                    'old_retention' => $old_retention,
                    'new_retention' => $retention
                );
            }
            else {
                echo mysqli_error($DB_admin);
                echo json_encode(array("statusCode"=>201));
            }
        }
        global $actLog; $actLog->add('Mass Assign', null,1, json_encode($act_detail));
    }
	mysqli_close($DB_admin);
?>