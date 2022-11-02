<?php

  /**
   * User Manager Class
   * 9:35 AM Tuesday, June 8, 2021
   */
   
    class userManager {

        public $DB_LOG;
        private $db;
        private $t_users = array(
            '*',
            'id',
            'username',
            'password',
            'email',
            'unit',
            'platform',
            'platform_d',
            'type',
            'pa',
            'cid',
            'created_at',
            'token',
            'pincode',
            'profile_rate',
            'groups'
        );
        private $t_user_extra = array(
            '*',
            'id',
            'user_id',
            'fname',
            'lname',
            'phone',
            'country',
            'city',
            'address',
            'interests',
            'hobbies',
            'ip',
            'status',
            'unit',
            'retention',
            'conversion',
            'type',
            'followup',
            'lastnotedate',
            'assigned_date',
            'assigned_date_ret',
            'created_at',
            'created_by',
            'date_approve',
            'updated_at',
            'updated_by',
            'need_update'
        );
        private $t_user_fx = array(
            '*',
            'id',
            'user_id',
            'job_cat',
            'job_title',
            'exp_fx',
            'exp_fx_year',
            'exp_cfd',
            'exp_cfd_year',
            'income',
            'investment',
            'strategy',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by'
        );
        private $t_user_gi = array(
            '*',
            'id',
            'user_id',
            'bd',
            'whatsapp',
            'telegram',
            'facebook',
            'instagram',
            'twitter',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by'
        );
        private $t_user_marketing = array(
            '*',
            'id',
            'user_id',
            'lead_src',
            'lead_camp',
            'campaign_extra',
            'affiliate',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by'
        );
        private $t_tp = array(
            '*',
            'id',
            'login',
            'password',
            'group_id',
            'user_id',
            'server',
            'retention',
            'conversion',
            'ftd',
            'ftd_amount',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by'
        );


        /**
         * DataTable constructor.
         */
        function __construct() {

        }

        /**
         * Set
         * @param int $id
         * @param array $table_items
         * @return int
         * @example setCustom(1,
         * [
         *  'users' => [
         *          email => 'test@test.com',
         *          unit => 'Turkish'
         *      ],
         *  'user_extra' =>
         *      [
         *          fname => 'Jojo',
         *          unit => 4
         *      ]
         * ])
         */
        public function setCustom($id, $table_items) {
            $result=0;
            $old=array();
            $where = 'user_id='.$id;
            $_db = new iSQL(DB_admin);
            if($table_items['users']) {
                $old['users'] = $this->getCustom($id, implode(',', array_keys($table_items['users'])));
                $result += ($_db->updateId('users', $id, $table_items['users'])) ? 1 : 0;
            }
            if($table_items['user_marketing']) {
                $old['user_marketing'] = $this->getCustom($id, implode(',', array_keys($table_items['user_marketing'])))['marketing'];
                $result += ($_db->updateAny('user_marketing', $table_items['user_marketing'], $where)) ? 1 : 0;
            }
            if($table_items['user_gi']) {
                $old['user_gi'] = $this->getCustom($id, implode(',', array_keys($table_items['user_gi'])))['gi'];
                $result += ($_db->updateAny('user_gi', $table_items['user_gi'], $where)) ? 1 : 0;
            }
            if($table_items['user_fx']) {
                $old['user_fx'] = $this->getCustom($id, implode(',', array_keys($table_items['user_fx'])))['fx'];
                $result += ($_db->updateAny('user_fx', $table_items['user_fx'], $where)) ? 1 : 0;
            }
            if($table_items['user_extra']) {
                $old['user_extra'] = $this->getCustom($id, implode(',', array_keys($table_items['user_extra'])))[' '];
                $result += ($_db->updateAny('user_extra', $table_items['user_extra'], $where)) ? 1 : 0;
            }
            $this->DB_LOG = $_db->log();
            global $actLog;

            $actLog->add('User', $id, boolval($old), json_encode(array('Old'=>$old,'New'=>$table_items)));
            return $result;
        }

        /**
         * Get
         * @param $id
         * @param $costume - Sets (,)
         * @return array|bool
         */
        public function getCustom($id, $costume) {
            $costume = str_replace(' ','',$costume);
            $costume = explode(',',$costume);
            $data = array();
            $users_columns = $tp_columns = $user_extra_columns = $marketing_columns = $user_fx_columns = $user_gi_columns = null;
            $where = 'user_id='.$id;
            foreach ($costume as $item) {
                if (in_array($item, $this->t_users)) $users_columns[] = $item;
                if (in_array($item, $this->t_user_extra)) $user_extra_columns[] = $item;
                if (in_array($item, $this->t_user_marketing)) $marketing_columns[] = $item;
                if (in_array($item, $this->t_user_gi)) $user_gi_columns[] = $item;
                if (in_array($item, $this->t_user_fx)) $user_fx_columns[] = $item;
                if (in_array($item, $this->t_tp)) $tp_columns[] = $item;
            }
            if ($users_columns) {
                $columns = implode(',',$users_columns);
                $_db_user = new iSQL(DB_admin);
                $query = "SELECT $columns FROM `users` WHERE id=$id limit 1";
                $_db_user->LINK->query($query, MYSQLI_ASYNC);
            }

            if ($marketing_columns) {
                $columns = implode(',',$marketing_columns);
                $_db_marketing = new iSQL(DB_admin);
                $query = "SELECT $columns FROM `user_marketing` WHERE $where limit 1";
                $_db_marketing->LINK->query($query, MYSQLI_ASYNC);
            }

            if ($user_gi_columns) {
                $columns = implode(',',$user_gi_columns);
                $_db_gi = new iSQL(DB_admin);
                $query = "SELECT $columns FROM `user_gi` WHERE $where limit 1";
                $_db_gi->LINK->query($query, MYSQLI_ASYNC);
            }

            if ($user_fx_columns) {
                $columns = implode(',',$user_fx_columns);
                $_db_fx = new iSQL(DB_admin);
                $query = "SELECT $columns FROM `user_fx` WHERE $where limit 1";
                $_db_fx->LINK->query($query, MYSQLI_ASYNC);
            }

            if ($user_extra_columns) {
                $columns = implode(',',$user_extra_columns);
                $_db_extra = new iSQL(DB_admin);
                $query = "SELECT $columns FROM `user_extra` WHERE $where limit 1";
                $_db_extra->LINK->query($query, MYSQLI_ASYNC);
            }

            if ($tp_columns) {
                $columns = implode(',',$tp_columns);
                $_db_tp = new iSQL(DB_admin);
                $query = "SELECT $columns FROM `tp` WHERE $where";
                $_db_tp->LINK->query($query, MYSQLI_ASYNC);
            }

            if ($users_columns) {
                $_db_user = $_db_user->LINK->reap_async_query();
                if($_db_user) $data = mysqli_fetch_array($_db_user, MYSQLI_ASSOC);
            }
            if ($marketing_columns) {
                $_db_marketing = $_db_marketing->LINK->reap_async_query();
                if($_db_marketing) $data['marketing'] = mysqli_fetch_array($_db_marketing, MYSQLI_ASSOC);
            }
            if ($user_gi_columns) {
                $_db_gi = $_db_gi->LINK->reap_async_query();
                if($_db_gi)  $data['gi'] = mysqli_fetch_array($_db_gi, MYSQLI_ASSOC);
            }
            if ($user_fx_columns) {
                $_db_fx = $_db_fx->LINK->reap_async_query();
                if($_db_fx)   $data['fx'] = mysqli_fetch_array($_db_fx, MYSQLI_ASSOC);
            }
            if ($user_extra_columns) {
                $_db_extra = $_db_extra->LINK->reap_async_query();
                if($_db_extra)   $data['extra'] = mysqli_fetch_array($_db_extra, MYSQLI_ASSOC);
            }
            if ($tp_columns) {
                $_db_tp = $_db_tp->LINK->reap_async_query();
                if($_db_tp) while ($row=mysqli_fetch_array($_db_tp, MYSQLI_ASSOC)) {
                    $data['tp'][] = $row;
                }
            }

            return ($data) ?? false;
        }
        
        /**
         * Get
         * @param $id
         * @return bool|array
         */
        public function get($id) {
            $isql = new iSQL(DB_admin);
            $data = $isql->selectId('users',$id);
            $where = 'user_id='.$id;
            $data['marketing'] = $isql->selectRow('user_marketing',$where);
            $data['gi'] = $isql->selectRow('user_gi',$where);
            $data['fx'] = $isql->selectRow('user_fx',$where);
            $data['user_extra'] = $isql->selectRow('user_extra',$where);
            if($data['user_extra']) $data['user_extra']['need_update'] = json_decode($data['user_extra']['need_update'],1);
            $data['tp'] = $isql->select('tp',$where);
            unset($isql);
            return ($data) ?? false;
        }

        /**
         * Get
         * @param $username
         * @return bool|array
         */
        public function getUsername($username) {
            $isql = new iSQL(DB_admin);
            $where = "username='$username'";
            $data = $isql->selectRow('users',$where);
            $where = 'user_id='.$data['id'];
            $data['marketing'] = $isql->selectRow('user_marketing',$where);
            $data['gi'] = $isql->selectRow('user_gi',$where);
            $data['fx'] = $isql->selectRow('user_fx',$where);
            $data['user_extra'] = $isql->selectRow('user_extra',$where);
            if($data['user_extra']) $data['user_extra']['need_update'] = json_decode($data['user_extra']['need_update'],1);
            $data['tp'] = $isql->select('tp',$where);
            unset($isql);
            return ($data) ?? false;
        }

        /**
         * Delete
         * @param $id
         * @return bool
         */
        public function delete ($id) {
            $isql = new iSQL(DB_admin);
            $isql->deleteId('users',$id);
            $where = 'user_id='.$id;
            $act['data'] = $isql->selectRow('user_extra',$where);
            $isql->deleteAny('user_session',$where);
            $isql->deleteAny('user_marketing',$where);
            $isql->deleteAny('user_gi',$where);
            $isql->deleteAny('user_fx',$where);
            $isql->deleteAny('user_extra',$where);
            // Add actLog
            $act['act'] = "delete";
            global $actLog; $actLog->add('User',$id,1,json_encode($act));
            unset($isql);
            return true;
        }
        
        /**
         * Sync MT5
         * @param $id
         * @return bool
         */
        public function syncMT5 ($id,$params,$body) {
            
            $isql = new iSQL(DB_admin);
            $MT5API = new mt5API();
    
            $where = "user_id=$id";
            $logins = $isql->select('tp',$where,'login');
            
            if($logins) foreach($logins as $login) {
                $params['Login'] = $login['login'];
                $MT5API->post('/api/user/update',$params,$body);
            }
            
            return true;
        }
        
        /**
         * FTD Check
         * @param $id
         * @return bool
         */
        public function ftdCheck ($id) {
            $TPs_FTD = $this->getCustom($id, 'ftd,ftd_amount,login,server');
            if($TPs_FTD['tp']) foreach($TPs_FTD['tp'] as $TP_FTD) if($TP_FTD['ftd_amount'] AND $TP_FTD['ftd_amount'] != 0) return $TPs_FTD['tp'];
            return false;
        }

    }

###### TEST PAD
