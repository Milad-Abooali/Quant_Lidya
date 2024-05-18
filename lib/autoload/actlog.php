<?php

/**
 * actLog Class
 * 4:14 PM Wednesday, November 4, 2020 | M.Abooali
 */

class actLog {

    private $db, $user_id;

    function __construct() {
        global $db;
        $this->db = $db;
        $this->user_id = $_SESSION["id"] ?? false;
    }

    public function add($act_type,$rel_id=null,$status=null,$detail=null,$user_id_f=null) {
        if ($user_id_f) $this->user_id = $user_id_f;
        $table = ($this->user_id) ? 'act_log_user' : 'act_log_guest';
        $user_id = ($this->user_id) ? $this->user_id : GF::getIP();
        $rel_path = (basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING'])) ?? 'index';
        $data['act_type'] = $act_type;
        $data['user_id'] = $user_id;
        $data['rel_path'] = $rel_path;
        $data['rel_id'] = $rel_id;
        $data['detail'] = $detail;
        $data['status'] = $status;
        $data['sess_id'] = $_SESSION['sess_id'] ?? session_id();
        if (!isset($_SESSION["id"])) $data['referer'] = $_SERVER['HTTP_REFERER'] ?? 'self';
        return $this->db->insert($table,$data);
    }

    public function lastPageVisited(){
        $res = $this->db->select('act_log_user', "user_id=$this->user_id", '*', $limit=5, 'timestamp DESC')[1];
        return ($res) ? $res['rel_path'] : FALSE;
    }

}

###### TEST PAD
