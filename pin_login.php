<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    // Escape All
    GF::escapeReq();

    $pincode = ($_POST["p"]) ?? (($_GET["p"]) ?? false);
    if($pincode) {
        $userid = ($_SESSION["id"]) ?? (($_POST["i"]) ?? $_GET["i"]);
        if($sess->pinLogin($userid, $pincode)){
            $_SESSION["locksess"] = false;

            // Add actLog
            global $actLog; $actLog->add('PIN',$userid,1,'{"user_id":"'.$userid.'","pincode":"'.$pincode.'"}');

            echo 1;
        }
    }