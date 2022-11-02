<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";

// Check Captcha
if ( $_SESSION["captcha_force"] && (strtoupper($_POST['captcha']) != $_SESSION['captcha']['code'])) {
    $_SESSION['captcha_length']++;
    global $actLog; $actLog->add('Note',null,0, json_encode(array("user_id"=>$_POST['user_id'],"note_id"=>$_POST['note'])));
    echo json_encode(array("statusCode"=>201,"error"=>"You have entered the wrong captcha !"));
    exit();
} else {
    // Escape All
    GF::escapeReq();
    global $db;
    $date = date('Y-m-d H:i:s');
    $note['note'] = GF::charReplace('tr', $_POST['note']);
    $note['note_type']  = $_POST['note_type'];
    $note['user_id']    = $_POST['user_id'];
    $note['created_at'] = $date;
    $note['created_by'] = $_SESSION["id"];
    $note['updated_at'] = $date;
    $note['updated_by'] = $_SESSION["id"];
    $insert = $db->insert('notes',$note);
    if($insert) {
        $update['lastnotedate'] = $date;
        $where = "user_id =".$_POST['user_id'];
        $db->updateAny('user_extra', $update, $where);
        // Add actLog
        global $actLog; $actLog->add('Note',$insert,1, json_encode(array("user_id"=>$_POST['user_id'],"note_id"=>$insert)));
        echo json_encode(array("statusCode"=>200));
    } else {
        global $actLog; $actLog->add('Note',null,0, json_encode(array("user_id"=>$_POST['user_id'],"note_id"=>$_POST['note'])));
        echo json_encode(array("statusCode"=>201));
    }
}
