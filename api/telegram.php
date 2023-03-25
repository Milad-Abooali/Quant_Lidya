<?php
/**
 * LidyaFX_CRM
 *
 * https://t.me/LidyaFX_CRM_bot
 *
 * HTTP API:
 * 6077092870:AAHIW2ozmfTrpqSraC0vo2PuG4dbd5-aGHI
 *
 *
 * https://api.telegram.org/bot6077092870:AAHIW2ozmfTrpqSraC0vo2PuG4dbd5-aGHI/setwebhook?url=https://clientzone2.lidyaportal.com/api/telegram.php
 */

require_once "../config.php";
global $db;
global $userManager;

$bot_token = "6077092870:AAHIW2ozmfTrpqSraC0vo2PuG4dbd5-aGHI";

$path = "https://api.telegram.org/bot$bot_token";

$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$user_text = $update["message"]["text"];
$first_name = $update["message"]["from"]["first_name"];
$last_name = $update["message"]["from"]["last_name"];
$telegram_username = $update["message"]["from"]["username"];

$input = explode(' ',$user_text);
$output = "output is not handled!";
$encodedMarkup = '{}';
$input_field_placeholder = 'What do you want to do?';
switch($input[0])
{
    case "/test":
        $output = json_encode($update);
        break;
    case "/getAny":
        $where = "telegram='$telegram_username'";
        $result = $db->selectRow('user_gi',$where);
        if($result){
            $crm_user = $userManager->get($result['user_id']);
            $output = "Yor CRM detail is:\n".json_encode($crm_user);
        }
        else{
            $output = "You are not registered on our CRM with this Telegram username (@$telegram_username)!";
        }
        break;
    case "/myUsername":
        $where = "telegram='$telegram_username'";
        $result = $db->selectRow('user_gi',$where);
        if($result){
            $crm_user = $userManager->get($result['user_id']);
            $output = "Yor CRM username is <b>".$crm_user['username']."</b> and linked to your telegram username (@$telegram_username)";
        }
        else{
            $output = "You are not registered on our CRM with this Telegram username (@$telegram_username)!";
        }
        break;
    case "/myPhone":
        $where = "telegram='$telegram_username'";
        $result = $db->selectRow('user_gi',$where);
        if($result){
            $crm_user = $userManager->get($result['user_id']);
            $output = "Yor CRM phone number is <b>".$crm_user['user_extra']['phone']."</b>";
        }
        else{
            $output = "You are not registered on our CRM with this Telegram username (@$telegram_username)!";
        }
        break;

    case "/resetPass":
        global $DB_admin;
        $where = "telegram='$telegram_username'";
        $result = $db->selectRow('user_gi',$where);
        if($result){
            $crm_user = $userManager->get($result['user_id']);
            $e=false;
            if(!isset($input[1]) || !isset($input[2])){
                $e = "Please set your new password 2 time with one space like this: \n /resetPass 123456 123456";
            }

            // Validate new password
            if(empty(trim($input[1]))){
                $e = "Please set your new password 2 time with one space like this: \n /resetPass 123456 123456";
            } elseif(strlen(trim($input[1])) < 6){
                $e = "Password must have at least 6 characters.";
            } else{
                $new_password = trim($input[1]);
            }

            if($e){
                $output = $e;
            }
            else {

            $sql = "UPDATE users SET password = ?, pa = ? WHERE email = ?";
            if($stmt = mysqli_prepare($DB_admin, $sql)){
                mysqli_stmt_bind_param($stmt, "sss", $param_password, $param_pa, $param_email );
                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
                $param_email = $crm_user['username'];
                $param_pa = GF::encodeAm($new_password);
                if(mysqli_stmt_execute($stmt)){
                    $db->updateId('users', $result['user_id'], array('token'=>null));
                    global $_Email_M;
                    $receivers[] = array (
                        'id'    =>  $result['user_id'],
                        'email' =>  $crm_user['email'],
                        'data'  =>  array(
                            'email'         =>  $crm_user['email'],
                            'new_password'  =>  $new_password
                        )
                    );
                    $subject = $theme = 'CRM_Password_Changed';
                    $_Email_M->send($receivers, $theme, $subject);
                    global $actLog; $actLog->add('Change Pass', $result['user_id'],1,'{"new_password":"'.$new_password.'","email":"'.$crm_user['email'].'"}');

                    $output = "Your password has been updated.";
                } else {
                    $output = 'Oops! Something went wrong. Please try again later.';
                }
            }

                $output = "Your password has been updated.";

            }
        }
        else{
            $output = "You are not registered on our CRM with this Telegram username (@$telegram_username)!";
        }
        break;
    case "/tgInfo":
        $output = json_encode($update["message"]);
        break;
    case "/start":
    default:
        $output = "hello <b>$first_name</b>, please select your desired command:
                \n /start
                \n /myUsername | Check The CRM Username.
                \n /myPhone | Check your phone number in the CRM.
                \n /resetPass | Update your CRM account password.
                ";
}

$keyboard = array(
    'keyboard' => array(
        array(
            "/start",
            "/myUsername",
            "/resetPass",
            "/myPhone",
            //"/getAny",
        )
    ),
    "resize_keyboard"=>true,
    "input_field_placeholder"=> $input_field_placeholder
);
$encodedKeyboard = json_encode($keyboard, true);
$text= urlencode($output);
file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&parse_mode=html&text=$text&reply_markup=$encodedKeyboard");

$inline_keyboard = array(
    "inline_keyboard" => array(
        array(
            array("text" => "CRM", "url" => "https://clientzone2.lidyaportal.com"),
            array("text" => "Website", "url" => "https://lidyafx.com"),
        )
    )
);
$encodedInline_keyboard = json_encode($inline_keyboard, true);
$text = urlencode('🌐 Links');
file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&parse_mode=html&text=$text&reply_markup=$encodedInline_keyboard");
