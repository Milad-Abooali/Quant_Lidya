<?php

/**
 * Group Functions Class
 * 11:13 AM Wednesday, November 18, 2020 | M.Abooali
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

function creat() {
    $output = new stdClass();
    $output->e = !(($_POST['name']) ?? false);
    $Email_Theme = new Email_Theme();
    if (!$output->e) $output->res = $Email_Theme->creat($_POST['name'],$_POST['cat']);
    echo json_encode($output);
}

function update() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $Email_Theme = new Email_Theme();
    if (!$output->e) $output->res = $Email_Theme->update($_POST['id'],$_POST['name'],$_POST['cat'],$_POST['content']);
    echo json_encode($output);
}


function loadContent() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    global $db;
    if (!$output->e) {
        $output->res = $db->selectId('email_log', $_POST['id'],'content, subject');
        $output->res['content'] = base64_decode($output->res['content']);
    }
    echo json_encode($output);
}


function resend() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    global $db;
    if (!$output->e) $old_mail = $db->selectId('email_log', $_POST['id']);

    $mail = new Email_M();
    $receivers[] = array (
        'id' =>  $old_mail['user_id'],
        'email' =>  $old_mail['email'],
        'data' =>  null
    );

    $res = $mail->send($receivers, 0, $old_mail['subject'], base64_decode($old_mail['content']));

    $output->res = $res;
    echo json_encode($output);
}
