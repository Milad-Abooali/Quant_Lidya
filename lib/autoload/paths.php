<?php

/**
 * System Paths Class
 * 17:01 AM wednesday, November 19, 2020 | M.Abooali
 */

class paths
{

    private $db,$table;

    function __construct()
    {
        $this->db = new iSQL(DB_admin);
        $this->table = 'perm_paths';
    }

    /**
     * List Paths
     * @return array|bool
     */
    public function list() {
        $res = $this->db->selectAll($this->table);
        return ($res) ?? false;
    }

    /**
     * Add New Path
     * @param $path
     * @param $view
     * @param $new
     * @param $edit
     * @param $delete
     * @return bool|int|mysqli_result|string
     */
    public function add($path,$view,$new,$edit,$delete) {
        $data['path']   = $path;
        $data['view']   = $view;
        $data['new']    = $new;
        $data['edit']   = $edit;
        $data['del']    = $delete;
        $insert = $this->db->insert($this->table,$data);
        // Add actLog
        global $actLog; $actLog->add('Path',$insert,1,'["Add New Path"]');
        return ($insert) ?? false;
    }

    /**
     * Update path
     * @param $path_id
     * @param $path
     * @param $view
     * @param $new
     * @param $edit
     * @param $delete
     * @return bool|int|mysqli_result|string
     */
    public function update($path_id,$path,$view,$new,$edit,$delete) {
        $data['path']   = $path;
        $data['view']   = $view;
        $data['new']    = $new;
        $data['edit']   = $edit;
        $data['del']    = $delete;
        $res = $this->db->updateId($this->table,$path_id,$data);
        // Add actLog
        global $actLog; $actLog->add('Path',$path_id,1,'["Update path"]');
        return ($res) ?? false;
    }

    /**
     * Delete Path
     * @param $path_id
     * @return bool
     */
    public function delete($path_id) {
        $this->db->deleteId($this->table,$path_id);
        $where = 'path_id='.$path_id;
        $this->db->deleteAny('perm_groups',$where);
        $this->db->deleteAny('perm_users',$where);
        // Add actLog
        global $actLog; $actLog->add('Path',$path_id,1,'["Delete Path."]');
        return true;
    }

}