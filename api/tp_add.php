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
    l -> Login,
    p -> password (Optional),
    g -> group_id,
    s -> server,
    email -> email
*/

if(isset($_REQUEST['l']) && isset($_REQUEST['g']) && isset($_REQUEST['s']) && isset($_REQUEST['email'])){
    global $db;
    $user_id = GF::getUserIdByEmail($_REQUEST['email']);
    if($user_id>0){
        $insert['login']      = $_REQUEST['l'];
        $insert['password']   = $_REQUEST['p'] ?? '';
        $insert['group_id']   = $_REQUEST['g'];
        $insert['server']     = $_REQUEST['s'];
        $insert['user_id']    = $user_id;

        if($db->insert('tp',$insert)){
            $output->res = 'true';
        } else {
            $output->e[] = 'ERROR:  DB Insert';
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