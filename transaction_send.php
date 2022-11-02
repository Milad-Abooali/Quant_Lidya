<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();
    
    $type = $_POST["type"]; // 1: Deposit  - 2: Withdraw
    $amount = $_POST["amount"];
    $bank = $_POST["bank"];
    $login = $_POST["login"];
    $userid = $_POST["userid"];
    $date = date('Y-m-d H:i:s');
    
    if($type < 2) {
        /* Getting file name */
        $filename = strtolower($_FILES['file']['name']);
        
        /* Location */
        $location = "media/".$filename;
        $uploadOk = 1;
        $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
        
        /* Valid Extensions */
        $valid_extensions = array("jpg","jpeg","png");
        /* Check file extension */
        if( !in_array(strtolower($imageFileType),$valid_extensions) ) {
            $uploadOk = 0;
        }
        
        if($uploadOk == 0) {
            echo 0;
        }else{
           /* Upload file */
            if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
                $mysql_insert = 'INSERT INTO transactions (doc, user_id, t_type_id, t_status_id, amount, comment, tp_id, created_at, created_by, updated_at, updated_by) VALUES("'.$filename.'", "'.$userid.'", "'.$type.'", "1", "'.$amount.'", "Deposit Wire Transfer", "'.$login.'", "'.$date.'", "'.$_SESSION["id"].'", "'.$date.'", "'.$_SESSION["id"].'")';
                $is_insert = mysqli_query($DB_admin, $mysql_insert);

                $inserted_id = mysqli_insert_id($lDB_admin);

                // Add actLog
                global $actLog; $actLog->add('Transaction', $inserted_id,1, json_encode($_POST));
                echo 1;

            }else{
                echo 0;
            }
        }
    } else if ($type > 1) {
        $mysql_insert = 'INSERT INTO transactions (user_id, t_type_id, t_status_id, amount, comment, tp_id, created_at, created_by, updated_at, updated_by) VALUES("'.$userid.'", "'.$type.'", "1", "'.$amount.'", "Withdrawal Wire Transfer", "'.$login.'", "'.$date.'", "'.$_SESSION["id"].'", "'.$date.'", "'.$_SESSION["id"].'")';
        $is_insert = mysqli_query($DB_admin, $mysql_insert);

        $inserted_id = mysqli_insert_id($DB_admin);

        // Add actLog
        global $actLog; $actLog->add('Transaction', $inserted_id,1, json_encode($_POST));

    }
    if (!$is_insert) die("database error:". mysqli_error($DB_admin));
    $insert_id  = $DB_admin->insert_id;
    $db = new iSQL(DB_admin);
    $table = 'users';
    $user_unit = $db->selectId($table, $userid, $column='unit')['unit'];
    $where = "unit ='$user_unit' AND type='Manager'";
    $agents = $db->select($table, $where, 'id');
    foreach ($agents as $agent) $ids[] = $agent['id'];
    $receivers = implode(",",$ids);
    $notify->addMulti('User '.$_SESSION["id"],2,$insert_id,$receivers);
?>