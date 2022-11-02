<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";
if(($_SESSION["id"]) ?? false) {
    if($_SESSION["type"] == "Admin"){
        header("location: welcome2.php");
        exit;
    } else {
        header("location: welcome.php");
        exit;
    }
}