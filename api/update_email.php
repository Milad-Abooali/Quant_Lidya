<?php
/**
 * Tradeclan_CRM_bot
 *
 * http://t.me/Tradeclan_CRM_bot
 *
 * HTTP API:
 * 5919798210:AAG2qOR7G2CDb9Tn23OV7gV5RqC3gTnp4zc
 *
 *
 * https://api.telegram.org/bot5919798210:AAG2qOR7G2CDb9Tn23OV7gV5RqC3gTnp4zc/setwebhook?url=https://clientzone.tradeclan.by/api/telegram.php
 */

require_once "../config.php";

/**
 * Escape User Input Values POST & GET
 */
GF::escapeReq();
$output = new stdClass();

if($_REQUEST['api_key'] !== "7689227"){
    $output->e = 'ERROR: Api Key';
    exit( json_encode($output));
}

/*
    old -> old_email,
    new -> new_email
*/

if(isset($_REQUEST['old']) && isset($_REQUEST['new'])){
    global $db;
    $user_id = GF::getUserIdByEmail($_REQUEST['old']);
    $user_id_over = GF::getUserIdByEmail($_REQUEST['new']);
    if($user_id_over>0){
        $output->e[] = 'ERROR:  '.$_REQUEST['new'].' is exist!';
    }
    else if ($user_id>0){
        $update['email'] = $_REQUEST['new'];
        if($db->updateId('users', $user_id, $update)){
            $output->res = 1;
        } else {
            $output->e[] = 'ERROR:  DB Update';
            $output->e[] = end($db->log() );
        }
    } else {
        $output->e[] = 'ERROR:  User not found';
    }
} else {
    $output->e = 'ERROR: Inputs';
}
echo json_encode($output);
exit();