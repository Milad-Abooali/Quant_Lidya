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

$user_id = $_POST['userID'] ?? false;
$retention = $_POST['retention'] ?? 0;
$conversion = $_POST['conversion'] ?? 0;
$affiliate = $_POST['affiliate'] ?? 0;

GF::updateFtd($user_id, $retention, $conversion, null, null, $affiliate);