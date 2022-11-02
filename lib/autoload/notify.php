<?php

  /**
   * notify Class
   * 5:27 PM Tuesday, November 3, 2020 | M.Abooali
   */
   
  class notify {
    
    private $db;
     
    function __construct() {
      global $DB_admin;
      $this->db = $DB_admin;
    }

    public function add($source,$type_id,$notify_data,$receiver,$rel_id=null){
      $sql = "INSERT INTO `notify` (
                                    rel_id,
                                    source,
                                    receiver,
                                    notify_type,
                                    notify_data,
                                    create_time
                                    )
                             VALUES (
                                    '$rel_id',
                                    '$source',
                                    '$receiver',
                                    '$type_id',
                                    '$notify_data',
                                    NOW()
                                    )";
      return ($this->db->query($sql)) ? mysqli_insert_id($this->db) : false;
    }


    public function addMulti($source,$type_id,$notify_data,$receivers){
      $receivers = explode(',', $receivers);
      $rel_id = $this->add($source,$type_id,$notify_data,$receivers[0]);
      unset($receivers[0]);
      if ($receivers) foreach($receivers as $receiver) if($receiver) $this->add($source,$type_id,$notify_data,$receiver,$rel_id);
      return ($rel_id) ?? false;
    }    
     
    public function remove($ids){
      $sql = "DELETE FROM`notify` WHERE id IN ($ids)";
      $res = $this->db->query($sql);
      return ($res) ? true : false;
    }

    public function update($ids,$col,$data){
      $sql = "update `notify` SET $col='$data' WHERE id IN ($ids)";
      $res = $this->db->query($sql);
      return ($res) ? true : false;
    }
    
    public function seen($ids){
      $sql = "update `notify` SET seen_time=NOW() WHERE id IN ($ids)";
      $res = $this->db->query($sql);
      return ($res) ? true : false;
    }
    
    public function action($id,$act){
      $sql = "SELECT * FROM `notify` WHERE id=$id";
      $res = $this->db->query($sql);
      $notify = ($res) ? mysqli_fetch_array($res) : NULL;
      $sql = "SELECT * FROM `notify_type` WHERE id=".$notify['notify_type'];
      $res = $this->db->query($sql);
      $notify_type = ($res) ? mysqli_fetch_array($res) : NULL;
      if ($act=='postpon') {
        $reaction = 1;
        $run_php = null;
      } elseif ($act=='done') {
        $reaction = 2;
        $run_php = $notify_type['on_done'];
      } elseif ($act=='dismiss') {
        $reaction = 3;
        $run_php = $notify_type['on_dismiss'];
      }
      eval($run_php);
      $sql = "update `notify` SET reaction=$reaction, react_time=NOW() WHERE id=$id";
      $res = $this->db->query($sql);
      return ($res) ? true : false;
    }


    // sample method for testing
    public function maker($id){
      $sql = "SELECT * FROM `notify` WHERE id=$id";
      $res = $this->db->query($sql);
      $notify = ($res) ? mysqli_fetch_array($res) : NULL;
      
      $sql = "SELECT * FROM `notify_type` WHERE id=".$notify['notify_type'];
      $res = $this->db->query($sql);
      $notify_type = ($res) ? mysqli_fetch_array($res) : NULL;
      
      $sql = "SELECT * FROM `users` WHERE id=".$notify['notify_data'];
      $res = $this->db->query($sql);
      $user = ($res) ? mysqli_fetch_array($res) : NULL;

      return sprintf($notify_type['notify_text'],$user['id'],$user['username']);
    }
    
    // Get All Notify
    public function getAllNotify($user_id){
        $sql = "SELECT n.`id`,n.`source`,n.`notify_data`,n.`seen_time`,n.`reaction`,n.`react_time`,n.`create_time`,t.`name` as type,t.`cat`,t.`notify_text`";
        $sql .= "FROM `notify` as `n` LEFT JOIN `notify_type` as `t` ON `n`.`notify_type`=`t`.`id` WHERE `n`.`receiver`=$user_id";
        $res = $this->db->query($sql);
        return ($res) ?? false;
    }

  }

###### TEST PAD

  //require_once "../config.php";
  //$notify = new notify();
  
  //$notify->add('Mila','1','u10',0');
  //$notify->addMulti('Mila','1','u10','0,55,78');
  //$notify->remove('4,5');
  //$notify->update(2,'source','system');
  //$notify->seen(6);
  //$notify->action(6,'done');
  //echo $notify->maker(1);
