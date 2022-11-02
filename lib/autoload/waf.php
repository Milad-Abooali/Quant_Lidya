<?php

    /**
     * Class Web Application Firewall
     *
     * Mahan | WAF
     *
     * @package    -
     * @author     Milad Abooali <m.abooali@hotmail.com>
     * @copyright  -
     * @license    -
     * @version    1.0
     * 9:35 AM Tuesday, June 8, 2021
     */

    class waf
    {

        private $theme,$db,$table_users,$table_waf_m,$table_waf_ip,$table_waf_ip_login,$table_waf_ip_login_ex;
        public $UM=array();

        function __construct() {
            $this->db = new iSQL(DB_admin);
            $this->table_users = 'users';
            $this->table_waf_m = 'waf_m';
            $this->table_waf_ip = 'waf_ip';
            $this->table_waf_ip_login = 'waf_ip_login';
            $this->table_waf_ip_login_ex = 'waf_ip_login_ex';
        }

        /**
         * Get Module Status
         * @param string $m_name
         * @param int $status
         * @return bool
         */
        public function getModuleStatus($m_name)
        {
            $where = "m_name='$m_name'";
            return $this->db->selectRow($this->table_waf_m, $where)['status'];
        }

        /**
         * Get Module Settings
         * @param string $m_name
         * @param int $status
         * @return bool
         */
        public function getModuleSettings($m_name)
        {
            $where = "m_name='$m_name'";
            return json_decode($this->db->selectRow($this->table_waf_m, $where)['settings']);
        }

        /**
         * Update Module Status
         * @param string $m_name
         * @param int $status
         * @return bool
         */
        public function updateModuleStatus($m_name,$status)
        {
            $where = "m_name='$m_name'";
            $data['status'] = $status;
            $this->db->updateAny($this->table_waf_m, $data, $where);
            // Add actLog
            $act_detail = array(
                'Module'    =>  $m_name,
                'Status'    =>  $status
            );
            global $actLog; $actLog->add('WAF',null,1,json_encode($act_detail));
            return true;
        }

        /**
         * Update Module Settings
         * @param string $m_name
         * @param string|null $settings
         * @return bool
         */
        public function updateModuleSettings($m_name, $settings=null)
        {
            $where = "m_name='$m_name'";
            $data['settings'] = $settings;
            $this->db->updateAny($this->table_waf_m, $data, $where);
            // Add actLog
            $act_detail = array(
                'Module'    =>  $m_name,
                'Settings'    =>  $settings
            );
            global $actLog; $actLog->add('WAF',null,1,json_encode($act_detail));
            return true;
        }

        /**
         * Get IP
         * @return bool|int
         */
        public function getIP($ip) {
            $where = "ip='$ip'";
            return $this->db->selectRow($this->table_waf_ip,$where);
        }

        /**
         * Is Valid IPv4 / IPv6
         * @param $ip
         * @return bool
         */
        public function isValidIP() :bool {
            $ip = GF::getIP();
            if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) return true;
            if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) return true;
            $detail=array(
                'user_id'        =>  $_SESSION["id"],
                'ip'             =>  $ip,
                'captcha_length' =>  $_SESSION['captcha_length'],
                'WAF'            =>  'Not Valid IP'
            );
            global $actLog; $actLog->add('WAF Block', 0,0, json_encode($detail));
            return false;
        }

        /**
         * Check IP Blacklist
         * @return bool|int
         */
        public function isBlacklistIP() {
            $ip = GF::getIP();
            $where = "ip='$ip' And status=2";
            return $this->db->exist($this->table_waf_ip,$where);
        }

        /**
         * Check IP Whitelist
         * @return bool|int
         */
        public function isWhitelistIP() {
            $ip = GF::getIP();
            $where = "ip='$ip' And status=1";
            return $this->db->exist($this->table_waf_ip,$where);
        }

        /**
         * Check IP
         * @return bool|int
         */
        public function checkIP() {
            $ip = GF::getIP();
            $where = "ip='$ip'";
            return $this->db->selectRow($this->table_waf_ip,$where);
        }

        /**
         * Add IP
         * @param string $ip
         * @param string $info
         * @param int $status
         * @return bool
         */
        public function addIP($ip, $info, $status) {
            $data['ip'] = $ip;
            $data['info'] = $info;
            $data['status'] = $status;
            $where = "ip='$ip'";
            $exist = $this->db->selectRow($this->table_waf_ip, $where);
            if ($exist) {
                $inserted = $exist['id'];
                if($exist['status']!=1) $this->db->updateId($this->table_waf_ip, $inserted, $data);
            } else {
                $inserted = $this->db->insert($this->table_waf_ip, $data);
            }
            // Add actLog
            global $actLog; $actLog->add('WAF',$inserted,1,json_encode($data));
            return true;
        }

        /**
         * Delete IP
         * @param $id
         * @return bool
         */
        public function deleteIP($id)
        {
            $this->db->deleteId($this->table_waf_ip, $id);
            // Add actLog
            global $actLog; $actLog->add('WAF',$id,1,'["Delete IP."]');
            return true;
        }

        /**
         * Update IP
         * @param $id
         * @param $status
         * @return bool
         */
        public function updateIP($id, $status)
        {
            $data['status'] = $status;
            $this->db->updateId($this->table_waf_ip, $id, $data);
            // Add actLog
            global $actLog; $actLog->add('WAF',$id,1,'["Update IP to: '.$status.'"]');
            return true;
        }

        /**
         * Add Filter
         * @param $col
         * @param $cond
         * @param $val
         * @param $status
         * @return bool
         */
        public function addFilter($col, $cond, $val, $status)
        {
            $data['filter'] = $col . ' ' . $cond . ' ' . $val;
            $data['status'] = $status;
            $inserted = $this->db->insert($this->table_waf_ip_login, $data);
            // Add actLog
            global $actLog; $actLog->add('WAF',$inserted,1,"Add Filter: ".$data['filter']);
            return true;
        }

        /**
         * Update Filter
         * @param $id
         * @return bool
         */
        public function updateFilter($id,$status)
        {
            $data['status'] = $status;
            $this->db->updateId($this->table_waf_ip_login, $id, $data);
            // Add actLog
            global $actLog; $actLog->add('WAF',$id,1,'["Update Filter: '.$status.'"]');
            return true;
        }

        /**
         * Delete Filter
         * @param $id
         * @return bool
         */
        public function deleteFilter($id)
        {
            $this->db->deleteId($this->table_waf_ip_login, $id);
            // Add actLog
            global $actLog; $actLog->add('WAF',$id,1,'["Delete Filter."]');
            return true;
        }


        /**
         * List All Filters
         * @return array|bool
         */
        public function listFilters()
        {
            $where = "status = 1";
            return $this->db->select($this->table_waf_ip_login,$where);
        }

        /**
         * Check Filter
         * @param $user_id
         * @return bool
         */
        public function checkFilter($user_id)
        {
            $list = $this->listFilters();
            $is_hit = false;
            if ($list) foreach ($list as $item) {
                $where = $item['filter']." AND id=$user_id";
                $is_hit = $this->db->exist($this->table_users, $where);
                if ($is_hit) break;
            }
            return $is_hit;
        }

        /**
         * Check Datetime
         * @return bool
         */
        public function checkDatetime()
        {

            /*
            $today =  date('l');
            if ($today=='saturday' || $today=='sunday') return false;
            */

            $start  =  $this->getModuleSettings('waf_login_ip')->start;
            $end  =  $this->getModuleSettings('waf_login_ip')->end;

            $times['now']     = date('H:i A');
            $times['start']   = date('H:i A', strtotime($start));
            $times['end']     = date('H:i A', strtotime($end));
            if($times['now'] <= $times['start'] || $times['now'] >= $times['end']) return false;
            return true;
        }

        /**
         * Add Exception
         * @param string $col
         * @param string $cond
         * @param string $val
         * @param string $expire
         * @return bool
         */
        public function addException($col, $cond, $val, $expire)
        {
            $data['filter'] = $col . ' ' . $cond . ' ' . $val;
            $data['expire_date'] = $expire;
            $inserted = $this->db->insert($this->table_waf_ip_login_ex, $data);
            // Add actLog
            global $actLog; $actLog->add('WAF',$inserted,1,'["Add Exception: '.$data['filter'].'"]');
            return true;
        }

        /**
         * List All Exceptions
         * @return array|bool
         */
        public function listExceptions()
        {
            $where = "expire_date > NOW()";
            return $this->db->select($this->table_waf_ip_login_ex,$where);
        }

        /**
         * Check Exception
         * @param $user_id
         * @return bool
         */
        public function checkException($user_id)
        {
            $list = $this->listExceptions();
            $is_hit = false;
            if ($list) foreach ($list as $item) {
                $where = $item['filter']." AND id=$user_id";
                $is_hit =  $this->db->exist($this->table_users, $where);
                if ($is_hit) break;
            }
            return $is_hit;
        }

        /**
         * End Exception
         * @param $id
         * @return bool
         */
        public function endException($id)
        {
            $data['expire_date'] = $this->db->DATE;
            $this->db->updateId($this->table_waf_ip_login_ex, $id, $data);
            // Add actLog
            global $actLog; $actLog->add('WAF',$id,1,'["Exception Ended."]');
            return true;
        }

        /**
         * End Session
         * @param int $sessid
         * @return bool
         */
        public function endSess($sessid)
        {
            $data['status'] = 0;
            $this->db->updateId('user_session', $sessid, $data);
            $sid = $this->db->selectId('user_session', $sessid,'session')['session'];
            $sessionfile = session_save_path() . "/sess_" . $sid;
            if (file_exists($sessionfile)) unlink($sessionfile);
            // Add actLog
            global $actLog; $actLog->add('WAF',$sid,1,'["Session Ended."]');
            return true;
        }

        /**
         * End SEN
         * @param int $sessid
         * @return bool
         */
        public function endSEN($sen)
        {
            $sessionfile = session_save_path() . "/sess_" . $sen;
            if (file_exists($sessionfile)) unlink($sessionfile);
            // Add actLog
            global $actLog; $actLog->add('WAF',$sen,1,'["Session Ended."]');
            return true;
        }

        /**
         * End All Session
         * @return bool
         */
        public function endAllSess()
        {
            $where = 'id !='.$_SESSION['sess_id'];
            $drop_list = $this->db->select('user_session', $where);
            $data['status'] = 0;
            $i=0;
            if ($drop_list) foreach ($drop_list as $drop) {
                $i++;
                $this->db->updateId('user_session', $drop['id'], $data);
                $session_file = session_save_path() . "/sess_" . $drop['session'];
                if (file_exists($session_file)) unlink($session_file);
                global $actLog; $actLog->add('WAF',$drop['session'],1,'["Session Ended."]');
            }
            return $i;
        }


    }