<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

// Send email to user with the token in a link they can click on

    $subject = "Welcome to ".Broker['title'];
    $msg = $_POST['body'];
    $email = $db->escape($_POST['email']);

    $mail = new Email_M();
    $where = "email='$email' AND unit IN (".Broker['units'].")";
    $receivers[] = array (
        'id' =>  $db->selectRow('users',$where)['id'],
        'email' =>  $email,
        'data' =>  null
    );
    $mail->send($receivers, 0, $subject, $msg);
