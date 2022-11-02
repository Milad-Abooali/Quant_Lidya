<?php

/**
 * User Groups Class
 * 17:01 AM wednesday, November 18, 2020 | M.Abooali
 */

class groups
{

    private $db,$table_g,$table_u;

    function __construct()
    {
        $this->db = new iSQL(DB_admin);
        $this->table_g = 'user_groups';
        $this->table_u = 'users';
    }

    /**
     * List Groups
     * @return bool|int|mysqli_result|string
     */
    public function list()
    {
        $res = $this->db->selectAll($this->table_g);
        return ($res) ?? false;
    }

    /**
     * List Group Users
     * @return bool|int|mysqli_result|string
     */
    public function users($id)
    {
        $where = "FIND_IN_SET('$id', groups)";
        $res = $this->db->select($this->table_u,$where);
        return ($res) ?? false;
    }

    /**
     * Add Users TO Groups
     * @param $id_set
     * @param $group_set
     * @return bool|int|mysqli_result|string
     */
    public function addUsers($id_set,$group_set)
    {
        $ids = explode(",", $id_set);
        $groups = ','.$group_set;

        $where = "id IN ($id_set)";
        $res = $this->db->append($this->table_u,'groups',$where, $groups);

        if ($res) {
            $count = (count($ids) ?? 1);
            $where = "id IN ($group_set)";
            $res = $this->db->increase($this->table_g,'users',$where,$count);
        }
        // Add actLog
        $act_detail = array(
            'users' => $id_set,
            'groups'=> $groups
        );
        global $actLog; $actLog->add('Groups',null,1,json_encode($act_detail));
        return ($res) ?? false;
    }

    /**
     * Remove Users From Groups
     * @param $id_set
     * @param $group_set
     * @return bool|int|mysqli_result|string
     */
    public function removeUsers($id_set,$group_set)
    {
        $ids = explode(",", $id_set);
        $groups = explode(",", $group_set);
        foreach ($groups as $group) {
            $sql = "UPDATE ".$this->table_u."
                    SET groups = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', groups, ','), ',$group,', ','))
                    WHERE id IN ($id_set)";
            $this->db->run($sql);
        }
        $count = (count($ids) ?? 1);
        $where = "id IN ($group_set)";
        $res = $this->db->decrease($this->table_g,'users',$where,$count);
        // Add actLog
        $act_detail = array(
            'users' => $id_set,
            'groups'=> $groups
        );
        global $actLog; $actLog->add('Groups',null,1,json_encode($act_detail));
        return ($res) ?? false;
    }

    /**
     * Add new Group
     * @param string $name
     * @param int $priority
     * @return bool|int|mysqli_result|string
     */
    public function add($name,$priority)
    {
        $data['name'] = $name;
        $data['priority'] = $priority;
        $insert = $this->db->insert($this->table_g,$data);
        // Add actLog
        global $actLog; $actLog->add('Groups',$insert,1);
        return ($insert) ?? false;
    }

    /**
     * Copy Group
     * @param string $name
     * @param int $priority
     * @param int $from
     * @param bool $perm
     * @param bool $users
     * @return bool
     */
    public function copyFrom($name,$priority , $from, $perm, $users)
    {
        $new_group_id  = $this->add($name, $priority);
        if ($new_group_id) {
            if ($perm) {
                $where = 'group_id='.$from;
                $perms  = $this->db->select('perm_groups',$where);
                if ($perms) foreach ($perms as $perm) $this->addPerm($new_group_id,$perm['pid'],$perm['view'],$perm['new'],$perm['edit'],$perm['del']);
            }
            if ($users) {
                $where = "FIND_IN_SET($from, groups)";
                $users = $this->db->select('users',$where,'id');
                if ($users) {
                    foreach ($users as $user) $this->addUsers($user['id'],$new_group_id);

                }
            }
        }
        // Add actLog
        // Add actLog
        $act_detail = array(
            'New Group' => $new_group_id,
            'Source Group'=> $from
        );
        global $actLog; $actLog->add('Groups',$new_group_id,1,json_encode($act_detail));
        return ($new_group_id) ?? false;
    }

    /**
     * Update Group
     * @param int $id
     * @param string $name
     * @param id $priority
     * @return bool|int|mysqli_result|string
     */
    public function update($id,$name, $priority)
    {
        $data['name'] = $name;
        $data['priority'] = $priority;
        $res = $this->db->updateId($this->table_g,$id,$data);
        // Add actLog
        global $actLog; $actLog->add('Groups',$id,1,'["Update Group"]');
        return ($res) ?? false;
    }

    /**
     * Drop Group Users
     * @param $id
     * @return bool|int|mysqli_result|string
     */
    public function drop($id)
    {
        $sql = "UPDATE `users`
                SET groups = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', groups, ','), ',$id,', ','))
                WHERE FIND_IN_SET($id, groups)";
        $res = $this->db->run($sql);
        if ($res) {
            $data['users'] = 0;
            $res = $this->db->updateId($this->table_g,$id,$data);
        }
        // Add actLog
        global $actLog; $actLog->add('Groups',$id,1,'["Drop Group Users"]');
        return ($res) ?? false;
    }

    /**
     * Delete Group
     * @param $id
     * @return bool|int|mysqli_result|string
     */
    public function delete($id)
    {
        $this->db->deleteId($this->table_g,$id);
        $where = 'group_id='.$id;
        $this->db->deleteAny('perm_groups',$where);
        // Add actLog
        global $actLog; $actLog->add('Groups',$id,1,'["Delete Group"]');
        return true;
    }

    /**
     * Add Perm
     * @param int $gid
     * @param int $pid
     * @param bool $view
     * @param bool $new
     * @param bool $edit
     * @param bool $del
     * @return bool|int|mysqli_result|string
     */
    public function addPerm($gid,$pid,$view,$new,$edit,$del)
    {
        $data['group_id']   = $gid;
        $data['path_id']    = $pid;
        $data['view']       = $view;
        $data['new']        = $new;
        $data['edit']       = $edit;
        $data['del']        = $del;

        $where = "group_id=$gid AND path_id=$pid";
        $exist = $this->db->exist('perm_groups',$where);

        if($exist) {
            $result = $this->db->updateAny('perm_groups',$data,$where);
        } else {
            $result = $this->db->insert('perm_groups',$data);
        }
        // Add actLog
        global $actLog; $actLog->add('Groups',$pid,1,'{"Group":"'.$gid.'","Perm":"'.$pid.'"}');
        return ($result) ?? false;
    }

    /**
     * Delete Group Perm
     * @param $id
     * @return bool|int|mysqli_result|string
     */
    public function delPerm($id)
    {
        $this->db->deleteId('perm_groups',$id);
        // Add actLog
        global $actLog; $actLog->add('Groups',$id,1,'["Delete Perm"]');
        return true;
    }

    /**
     * Add User Perm
     * @param int $uid
     * @param int $pid
     * @param bool $view
     * @param bool $new
     * @param bool $edit
     * @param bool $del
     * @return bool|int|mysqli_result|string
     */
    public function addUserPerm($uid,$pid,$view,$new,$edit,$del)
    {
        $data['user_id']    = $uid;
        $data['path_id']    = $pid;
        $data['view']       = $view;
        $data['new']        = $new;
        $data['edit']       = $edit;
        $data['del']        = $del;

        $where = "user_id=$uid AND path_id=$pid";
        $exist = $this->db->exist('perm_users',$where);

        if($exist) {
            $result = $this->db->updateAny('perm_users',$data,$where);
        } else {
            $result = $this->db->insert('perm_users',$data);
        }

        // Add actLog
        global $actLog; $actLog->add('Groups',$pid,1,'{"User":"'.$uid.'","Perm":"'.$pid.'"}');
        return ($result) ?? false;
    }


    /**
     * Delete User Perm
     * @param $id
     * @return bool|int|mysqli_result|string
     */
    public function delUserPerm($id)
    {
        $this->db->deleteId('perm_users',$id);

        // Add actLog
        global $actLog; $actLog->add('Groups',$id,1,'[Delete User Perm]');
        return true;
    }

}
