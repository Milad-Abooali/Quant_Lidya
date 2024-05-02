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
    p -> password,
    email -> email
*/

if(isset($_REQUEST['p']) && isset($_REQUEST['email'])){
    global $db;
    $user_id = GF::getUserIdByEmail($_REQUEST['email']);
    if($user_id>0){

        $new_password = $_REQUEST['p'];
        $password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $pa = GF::encodeAm($new_password);
        $update['password'] = $password_hashed;
        $update['pa'] = $pa;

        if($db->updateId('users', $user_id, $update)){
            $output->res = $new_password;
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