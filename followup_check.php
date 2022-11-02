<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    $date = date('Y-m-d H:i:s');
    $date2 = date('Y-m-d H:i:s', strtotime("+5 min"));
    
    $sqlFU = "SELECT * FROM `user_extra` WHERE user_extra.followup BETWEEN '".$date."' AND '".$date2."'";
    echo $sqlFU;
    $resultFU = $DB_admin->query($sqlFU);
    while ($rowFU = mysqli_fetch_array($resultFU)) {

    }
	mysqli_close($DB_admin);
?>