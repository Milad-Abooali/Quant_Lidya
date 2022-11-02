<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

  require_once "config.php";

    // Add actLog
    global $actLog; $actLog->add('Logout',null,1);

  $sess->logout();

// Redirect to login page
header("location: login.php");
exit;
?>