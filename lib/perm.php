<?php

/**
 * Permission Controller
 * @package    -
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  -
 * @license    -
 * @version    1.0.17
 * @update     Created by M.Abooali on 2021-01-15T16:30:08.521Z
 */

class perm
{

    /**
     * @var iSQL $db database iSQL object
     * @var string $table_users users table name
     * @var string $table_groups group table name
     * @var string $table_paths path table name
     * @var string $table_g_perm group permission table name
     * @var string $table_u_perm user permission table name
     */
    private $db;
    private $table_users, $table_groups, $table_paths, $table_g_perm, $table_u_perm;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->db = new iSQL(DB_admin);
        $this->table_users = 'users';
        $this->table_groups = 'user_groups';
        $this->table_paths = 'perm_paths';
        $this->table_g_perm = 'perm_groups';
        $this->table_u_perm = 'perm_users';
    }

    /**
     * Get Path Permissions
     * @param int $id path id
     * @return array|bool
     */
    public function getPathPerm ($id) {
        $res = $this->db->selectId($this->table_paths,$id);
        return $res ?? false;
    }

    /**
     * Get Group Permissions
     * @param int $id group id
     * @param int $path_id path id
     * @return mixed
     */
    public function getGroupPerm ($id, $path_id) {
        $res = array();
        $where = "group_id=$id AND path_id=$path_id";
        $perm = $this->db->selectRow($this->table_g_perm, $where);
        if ($perm) {
            $res = $this->db->selectId($this->table_groups, $id);
            $res['view']   = $perm['view'];
            $res['new']    = $perm['new'];
            $res['edit']   = $perm['edit'];
            $res['del']    = $perm['del'];
        }
        return ($res) ?? false;
    }

    /**
     * Get User Permissions
     * @param int $id user id
     * @param null|string $path path
     * @return array|bool
     */
    public function getUserPerm ($id,$path=null) {
        $where = "user_id=$id";
        if ($path) $where .= " AND path_id='$path'";
        $res = $this->db->select($this->table_u_perm, $where);
        return $res ?? false;
    }

    /**
     * List User Permissions on Path
     * @param int $user_id user id
     * @param string $path path
     * @return mixed
     */
    public function listUserPermOnPath ($user_id, $path) {
        $list = array();
        $where = "path='$path'";
        $path_row = $this->db->selectRow($this->table_paths, $where);
        if($path_row) {
            $path_id = $path_row['id'];
            // check def path
            $path_perm = $this->getPathPerm($path_id);
            if ($path_perm){
                $list['D']['view']    = $path_perm['view'];
                $list['D']['new']     = $path_perm['new'];
                $list['D']['edit']    = $path_perm['edit'];
                $list['D']['del']     = $path_perm['del'];
                // list perm for each group of user
                $user_groups = $this->db->selectId($this->table_users, $user_id,'groups')['groups'];
                if ($user_groups) {
                    $groups = explode(",",$user_groups);
                    foreach ($groups as $group) {
                        $g_perm = $this->getGroupPerm($group, $path_id);
                        if($g_perm) {
                            $list['G'][$g_perm['priority']]['view']    = $g_perm['view'];
                            $list['G'][$g_perm['priority']]['new']     = $g_perm['new'];
                            $list['G'][$g_perm['priority']]['edit']    = $g_perm['edit'];
                            $list['G'][$g_perm['priority']]['del']     = $g_perm['del'];
                        }
                    }
                   if($list['G']) ksort($list['G']);
                }
                // list user perm exception
                $user_perm = $this->getUserPerm($user_id,$path_id)[0];
                if ($user_perm) {
                    $list['U']['view']    = $user_perm['view'];
                    $list['U']['new']     = $user_perm['new'];
                    $list['U']['edit']    = $user_perm['edit'];
                    $list['U']['del']     = $user_perm['del'];
                }
            }
        }
        return ($list) ?? false;
    }

    /**
     * Get User Permissions on Path
     * @param int $user_id user id
     * @param string $path path
     * @return mixed
     */
    public function getUserPermOnPath ($user_id, $path) {
        $permit = array(
            'view'  =>  0,
            'new'   =>  0,
            'edit'  =>  0,
            'del'   =>  0
        );
        $perm_list = $this->listUserPermOnPath($user_id, $path);
        // Is exception
        if ($perm_list['U']) {
            $permit = $perm_list['U'];
        } else {
            // Is group perm - So mix the groups perms
            if ($perm_list['G']) {
                $permit = array_values($perm_list['G'])[0];
            } else {
                // Is default perm
                if ($perm_list['D']) {
                    $permit = $perm_list['D'];
                }
            }
        }
        return $permit;
    }

}