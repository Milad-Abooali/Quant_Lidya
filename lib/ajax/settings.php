<?php

/**
 * Global Functions Class
 * 11:13 AM Wednesday, November 11, 2020 | M.Abooali
 */

// on null call
  function noF() {
      $output = new stdClass();
      $output->e = false;
      $output->res = $_POST;
      echo json_encode($output);
  }

// Revert Act
function revertAct() {
    $output = new stdClass();
    $output->e = false;

    $output->e = !(($_POST['id']) ?? false);
    $output->e = !(($_POST['type']) ?? false);
    $output->e = !(($_POST['json']) ?? false);
    if(!$output->e){
        if($_POST['type']==='Mass Assign'){
            foreach ($_POST['json'] as $item){
                $update=array();
                if( isset($item['new_conversion']) )
                    $update['conversion']= $item['old_conversion'];
                if( isset($item['new_retention']) )
                    $update['retention']= $item['old_retention'];
                global $db;
                $where = 'user_id='.$item['user'];
                $output->res[] = array(
                    'user'   => $item['user'],
                    'revert' => $db->updateAny('user_extra', $update, $where)
                );
            }
        }
    }
    echo json_encode($output);
    // Add actLog
    global $actLog; $actLog->add('Revert', $_POST['id'],1, json_encode($output->res));
}

// Update Job
function updateJob() {
    $output = new stdClass();
    $output->e = false;

    $output->e = !(($_POST['id']) ?? false);
    $output->e = !(($_POST['col']) ?? false);

    $update[$_POST['col']]= $_POST['status'];
    global $db;
    $output->res = $db->updateId('cronjobs', $_POST['id'], $update);
    // Add actLog
    global $actLog; $actLog->add('Jobs',null,1,json_encode($_POST));
    echo json_encode($output);
}

// Users Completion Save Form
  function userCompletionSave() {
      $output = new stdClass();
      $output->e = false;

      $update['value']= json_encode($_POST);
      $update['update_by']= $_SESSION["id"];

      $db = new iSQL(DB_admin);
      $table="sys_settings";
      $where="term='Users_Completion'";

      $output->res = $db->updateAny($table,$update,$where);
      // Add actLog
      global $actLog; $actLog->add('USER',null,1,'{"data":"Users Completion Rate Update"}');
      echo json_encode($output);
  }