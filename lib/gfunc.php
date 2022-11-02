<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

/**
 * Global Functions Class
 * @package    -
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  -
 * @license    -
 * @version    1.0.17
 * @update     Created by M.Abooali on 9:36 AM Tuesday, June 8, 2021
 */

class GF {

    /**
     * Get clinte IP
     * @return string|fals return clint IP or false
     */
    public static function getIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return ($ip) ?? false;
    }

    /**
    * Print input in pre as code
    * @param string|array $input
    * @param bool $eol
    * @param string|bool $block
    */
    public static function p($input, $block=false, $eol=false) {
        echo ($block) ? "<$block>" : null;
        echo ($eol) ? PHP_EOL : null;
        echo '<pre><code>';
        if(is_array($input)) {
            print_r($input);
        } else {
            var_dump($input);
        }
        echo '</code></pre>';
        echo ($eol) ? PHP_EOL : null;
        echo ($block) ? "</$block>" : null;
    }

    /**
     * Profile Rate
     * @param int $id
     * @return bool|mixed
     */
    public static function profileRate($id) {
        $db = new iSQL(DB_admin);
        $res = $db->selectId('users',$id,'profile_rate')['profile_rate'];
        return ($res) ?? false;
    }

    /**
    * Profile Completion Rate Cal
    * @param string|array $input
    * @param null $_sys_Users_Completion
    * @return int
    */
    public static function profileRateCal($input, $_sys_Users_Completion=null) {
        if (!$_sys_Users_Completion) {
            global $_sys;
            $_sys_Users_Completion = json_decode($_sys['Users_Completion']);
        }
        $rate = 0;
        $need_update = array();
        (filter_var($input['username'], FILTER_VALIDATE_EMAIL)) ? $rate += $_sys_Users_Completion->email : $need_update[]= 'username';
        (strlen($input['fname']) > 3) ? $rate += $_sys_Users_Completion->fname : $need_update[]= 'fname';
        (strlen($input['lname']) > 3) ? $rate += $_sys_Users_Completion->lname : $need_update[]= 'lname';
        (strlen($input['phone']) > 7) ? $rate += $_sys_Users_Completion->phone : $need_update[]= 'phone';
        (strlen($input['country']) > 3) ? $rate += $_sys_Users_Completion->country : $need_update[]= 'country';
        (strlen($input['city']) > 3) ? $rate += $_sys_Users_Completion->city : $need_update[]= 'city';
        (strlen($input['address']) > 10) ? $rate += $_sys_Users_Completion->address : $need_update[]= 'address';
        (strlen($input['interests']) > 3 && strpos($input['interests'], ',') !== false) ? $rate += $_sys_Users_Completion->interests : $need_update[]= 'interests';
        (strlen($input['hobbies']) > 3 && strpos($input['hobbies'], ',') !== false) ? $rate += $_sys_Users_Completion->hobbies : $need_update[]= 'hobbies';
        (strlen($input['job_title']) > 3) ? $rate += $_sys_Users_Completion->jobtitle : $need_update[]= 'job_title';
        ($input['job_cat'] > 0) ? $rate += $_sys_Users_Completion->jobcat : $need_update[]= 'job_cat';
        ($input['income'] > 0) ? $rate += $_sys_Users_Completion->income : $need_update[]= 'income';
        ($input['investment'] > 0) ? $rate += $_sys_Users_Completion->planinvest : $need_update[]= 'investment';
        (strlen($input['strategy']) > 3) ? $rate += $_sys_Users_Completion->tstrategy : $need_update[]= 'strategy';
        (strlen($input['whatsapp']) > 4) ? $rate += $_sys_Users_Completion->WhatsApp : $need_update[]= 'whatsapp';
        (strlen($input['telegram']) > 4) ? $rate += $_sys_Users_Completion->Telegram : $need_update[]= 'telegram';
        (strlen($input['facebook']) > 4) ? $rate += $_sys_Users_Completion->Facebook : $need_update[]= 'facebook';
        (strlen($input['instagram']) > 4) ? $rate += $_sys_Users_Completion->Instagram : $need_update[]= 'instagram';
        (strlen($input['twitter']) > 4) ? $rate += $_sys_Users_Completion->Twitter : $need_update[]= 'twitter';
        ($input['exp_fx'] && $input['exp_fx_year'] > 0) ? $rate += $_sys_Users_Completion->exFX : $need_update[]= 'exp_fx_year';
        ($input['exp_cfd'] && $input['exp_cfd_year'] > 0) ? $rate += $_sys_Users_Completion->exCFD : $need_update[]= 'exp_cfd_year';
        $bd_y = substr($input['bd'], 0, 4);
        ($bd_y > 1920 && $bd_y < 2005) ? $rate += $_sys_Users_Completion->birthdate : $need_update[]= 'bd';
        (strlen($input['lead_src']) > 3) ? $rate += $_sys_Users_Completion->Source : $need_update[]= 'lead_src';
        (strlen($input['lead_camp']) > 3) ? $rate += $_sys_Users_Completion->Campaign : $need_update[]= 'lead_camp';
        $db = new iSQL(DB_admin);
        $update['profile_rate'] = $rate;
        $id = $input['id'];
        $db->updateId('users',$id,$update);
        $data['need_update'] = json_encode($need_update);
        $db->updateAny('user_extra',$data,'user_id='.$id);
        return true;
    }

    /**
     * Compare By Time Stamp
     * @param string $time1 first time
     * @param string $time2  time
     * @return int
     */
    public static function compareByTimeStamp($time1, $time2)
    {
        if (strtotime($time1) < strtotime($time2))
            return 1;
        else if (strtotime($time1) > strtotime($time2))
            return -1;
        else
            return 0;
    }

    /**
    * Get Profile of user by ID
    * @param $id user id
    * @return bool|array
    */
    public static function getUserProfile($id) {
        $sql = '
        SELECT 
            u.id,
            u.username,
            x.fname,
            x.lname,
            x.phone,
            x.country,
            x.city,
            x.address,
            x.interests,
            x.hobbies,
            x.job_cat,
            x.job_title,
            x.exp_fx,
            x.exp_fx_year,
            x.exp_cfd,
            x.exp_cfd_year,
            x.income,
            x.investment,
            x.strategy,
            x.bd,
            x.whatsapp,
            x.telegram,
            x.facebook,
            x.instagram,
            x.twitter,
            x.lead_src,
            x.lead_camp
        FROM users u 
        LEFT JOIN ( 
            SELECT 
                ex.user_id,
                ex.fname,
                ex.lname,
                ex.phone,
                ex.country,
                ex.city,
                ex.address,
                ex.interests,
                ex.hobbies,
                fx.job_cat,
                fx.job_title,
                fx.exp_fx,
                fx.exp_fx_year,
                fx.exp_cfd,
                fx.exp_cfd_year,
                fx.income,
                fx.investment,
                fx.strategy,
                gi.bd,
                gi.whatsapp,
                gi.telegram,
                gi.facebook,
                gi.instagram,
                gi.twitter,
                mk.lead_src,
                mk.lead_camp
            FROM user_extra ex 
            LEFT JOIN user_fx fx USING(user_id)
            LEFT JOIN user_gi gi USING(user_id)
            LEFT JOIN user_marketing mk USING(user_id)
        ) x on u.id=x.user_id 
        WHERE u.id='.$id;
        $db = new iSQL(DB_admin);
        $res = $db->run($sql);
        if ($res) while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) $output = $row;
        return $output ?? false;
    }


    /**
     * Get Average Rate for filtered users
     * @param null $where WHERE condition
     * @return bool|int
     */
    public static function profileRateAvg($where=null){
        $db = new iSQL(DB_admin);
        $sql = 'SELECT AVG(u.profile_rate) avg_rate FROM `users` u LEFT JOIN user_extra x on u.id= x.user_id WHERE '.$where;
        $res = $db->run($sql);
        if ($res) while ($row=mysqli_fetch_array($res)) $output =  $row['avg_rate'];
        return ($output) ?? false;
    }

    /**
     * @param string $pos position of placing code
     * @param string $path  path of the file
     * @param bool $defer if true defer added
     * @return bool
     */
    public static function loadJS($pos,$path,$defer=true){
        global $_sys_header,$_sys_footer;
        if (in_array($pos, array('h','head','header'))) $_sys_header .= '<script src="'.$path.'"'.(($defer) ? ' defer' : null) .'></script>';
        if (in_array($pos, array('f','foot','footer'))) $_sys_footer .= '<script src="'.$path.'"'.(($defer) ? ' defer' : null) .'></script>';
        return true;
    }

    /**
     * @param string $pos position of placing code
     * @param string $path  path of the file
     * @return bool
     */
    public static function loadCSS($pos,$path){
        global $_sys_header,$_sys_footer;
        if (in_array($pos, array('h','head','header'))) $_sys_header .= '<link href="'.$path.'" rel="stylesheet" type="text/css">';
        if (in_array($pos, array('f','foot','footer'))) $_sys_footer .= '<link href="'.$path.'" rel="stylesheet" type="text/css">';
        return true;
    }

    /**
     * @param string $pos position of placing code
     * @param string $content   codes
     * @param bool $onload  if true add in document ready
     * @return bool
     */
    public static function makeJS($pos,$content,$onload=true){
        global $_sys_header,$_sys_footer;
        if ($onload) $content = '$(document).ready(function(){'.$content.'});';
        if (in_array($pos, array('h','head','header'))) $_sys_header .= "<script>$content</script>";
        if (in_array($pos, array('f','foot','footer'))) $_sys_footer .= "<script>$content</script>";
        return true;
    }

    /**
     * @param string $pos position of placing code
     * @param string $content   codes
     * @return bool
     */
    public static function makeCSS($pos,$content){
        global $_sys_header,$_sys_footer;
        if (in_array($pos, array('h','head','header'))) $_sys_header .= "<style>$content</style>";
        if (in_array($pos, array('f','foot','footer'))) $_sys_footer .= "<style>$content</style>";
        return true;
    }

    /**
     * @param string $string input text
     * @param string $lang
     * @return string
     */
    public static function charReplace($lang, $string) {
        if ($lang=='tr') {
            $search = array("ı", "ğ", "ü", "ş", "ö", "ç", "Ğ", "İ", "Ş", "Ö", "Ü", "Ç");  //turkish letters
            $replace = array("i", "g", "u", "s", "o", "c", "G", "I", "S", "O", "U", "C"); //english coordinators letters
        }
        return str_replace($search, $replace, $string);
    }

    /**
     * @param time $start start point
     * @param time|null $end end point
     * @return string
     */
    public static function timeAgo ($start, $end=null) {
        $time_ago        = strtotime($start);
        $current_time    = ($end) ? strtotime($end) : time();
        $time_difference = $current_time - $time_ago;
        $seconds         = $time_difference;
        $minutes = round($seconds / 60); // value 60 is seconds
        $hours   = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec
        $days    = round($seconds / 86400); //86400 = 24 * 60 * 60;
        $weeks   = round($seconds / 604800); // 7*24*60*60;
        $months  = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60
        $years   = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60
        if ($seconds <= 60){
            return ($end) ? "one minute" : "Just Now";
        } else if ($minutes <= 60){
            if ($minutes == 1){
                $output =  "one minute";
            } else {
                $output =  "$minutes minutes";
            }
        } else if ($hours <= 24){
            if ($hours == 1){
                $output =  "an hour";
            } else {
                $output =  "$hours hrs";
            }
        } else if ($days <= 7){
            if ($days == 1){
                $output =  "1 day";
            } else {
                $output =  "$days days";
            }
        } else if ($weeks <= 4.3){
            if ($weeks == 1){
                $output =  "a week";
            } else {
                $output =  "$weeks weeks";
            }
        } else if ($months <= 12){
            if ($months == 1){
                $output =  "a month";
            } else {

                $output =  "$months months";
            }
        } else {
            if ($years == 1){
                $output =  "one year";
            } else {

                $output = "$years years";
            }
        }

        return ($end) ? $output : $output.' ago';
    }

    /**
     * @param int $length
     * @param bool $add_dashes
     * @param string $available_sets
     * @return string
     */
    public static function genPass($length = 8, $add_dashes = false, $available_sets = 'lud')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];

        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    public static function cLog($string, $json=false) {
        if($json){
            echo "<script>console.log(JSON.parse('".json_encode($string)."'))</script>";
        }else{
            echo "<script>console.log('$string')</script>";
        }
    }

    public static function encodeAm ($string, $key=null) {
        return bin2hex($string);
    }
    public static function decodeAm ($string, $key=null) {
        return hex2bin($string);
    }

    public static function escapeReq() {
        global $db;
        if ($_POST) foreach ($_POST as $k => $v) $post_escaped[$k] = $db->escape($v);
        $_POST = $post_escaped;
        if ($_GET) foreach ($_GET as $k => $v) $get_escaped[$k] = $db->escape($v);
        $_GET = $get_escaped;
        return true;
    }

    public static function between($x, $lim1, $lim2) {
        if ($lim1 < $lim2) {
            $lower = $lim1; $upper = $lim2;
        }
        else {
            $lower = $lim2; $upper = $lim1;
        }
        return (($x >= $lower) && ($x <= $upper));
    }

    public static function wafCheck() {
        $force_start = 10;
        $force_end = 30;
        $action_start = 30;
        $action_end = 55;

        global $_waf;
        global $_waf_isWhitelistIP;
        global $db;

        // Bypass for Whitelist IP
        if ($_waf->isWhitelistIP()){
            $_SESSION["captcha_force"] = false;
            return;
        } 
        
        if ($_waf->isBlacklistIP()) exit('🦊 . . . Hi!<br>Your IP hits on one or more of our security rules and blocked in our system!<br><br>IEN: ['.bin2hex(GF::getIP()).']');
        $where = "user_id=".$_SESSION["id"]." AND act_type NOT IN ('WAF','WAF Block','WAF Captcha','View') AND (`timestamp` BETWEEN (DATE_SUB(NOW(),INTERVAL 15 MINUTE)) AND NOW())";
        $act_last_5_min = $db->count('act_log_user',$where);

        if(self::between(($_SESSION['captcha_length'] ?? 0), $force_start, $force_end) ||  self::between($act_last_5_min, $action_start, $action_end)) {
            $detail=array(
                'user_id'        =>  $_SESSION["id"],
                'ip'             =>  GF::getIP(),
                'action_count'   =>  $act_last_5_min,
                'captcha_length' =>  $_SESSION['captcha_length'],
                'WAF'            =>  'Force Captcha'
            );
            if(Broker['captcha'] && !$_waf_isWhitelistIP) {
                global $actLog; $actLog->add('WAF Captcha',0,1, json_encode($detail));
                $_SESSION["captcha_force"] = true;
            } else {
                $_SESSION["captcha_force"] = false;
                if( !isset($_SESSION['log_WAF_captcha']) ) global $actLog; $actLog->add('WAF Captcha',0,0, json_encode($detail));
                $_SESSION['log_WAF_cap0tcha'] = true;
                return false;
            }
        } else if( (($_SESSION['captcha_length'] ?? 0)>$force_end) || ($act_last_5_min>$action_end) ) {
            $detail=array(
                'user_id'        =>  $_SESSION["id"],
                'ip'             =>  GF::getIP(),
                'action_count'   =>  $act_last_5_min,
                'captcha_length' =>  $_SESSION['captcha_length'],
                'WAF'            =>  'Block IP'
            );
            if($_waf_isWhitelistIP) {
                if( !isset($_SESSION['log_WAF_block']) ) {
                    global $actLog;
                    $actLog->add('WAF Block',0,0, json_encode($detail));
                }
                $_SESSION['log_WAF_block'] = true;
                return false;
            } else {
                global $actLog; $actLog->add('WAF Block',0,1, json_encode($detail));
                $_waf->addIP(GF::getIP(), 'User ID: '.$_SESSION["id"], 2);
                exit('🦊 . . . Hi!<br>Your session hits on one or more of our security rules and limited!<br><br>SEN: ['.bin2hex(session_id()).']<br>IEN:['.bin2hex(GF::getIP()).']');
            }
        }
    }
    
        /**
     * Format Numbers
     * @param int $num
     * @param int $dot
     * @return string
     */
    public static function nf ($num=0, $dot=0) {
        if ($dot==0) {
            $num = round ($num);
            $output = number_format ($num, 0, '.', ',');
        } else {
            $output = number_format ($num, $dot, '.', ',');
        }
        return $output;
    }

    /**
     * @param $array
     * @return mixed
     */
    public static function last_key($array){
        $keys = array_keys($array);
        return $keys[count($keys)-1];
    }
        
    // FTD UPDATE Update FUNC
    public static function updateFtd($user_id, $retention=null, $conversion=null, $ftd=null, $ftd_amount=null, $affiliate=null) {
        $result = (object)[];
        if ($user_id) {
            global $db;
            $mt4 = new iSQL(DB_mt4);
            $mt5 = new iSQL(DB_mt5);
            // Get User TP Ids
            $where = "user_id = $user_id AND group_id = 2";
            $tp_list = $db->select('tp', $where);
            if($tp_list) foreach($tp_list as $tp) {
                $update = array();
                if($ftd && $ftd_amount) {
                    $update['ftd'] = $ftd;
                    $update['ftd_amount'] = $ftd_amount;
                } else {
                    if ($tp['server']=='MT4')
                    {   # MT4 - Get FTD
                        $where = 'CMD=6 AND LOGIN ='.$tp['login'];
                        $mt4_res = $mt4->selectRow('MT4_TRADES', $where,0);
                        $update['ftd'] = $mt4_res['OPEN_TIME'];
                        $update['ftd_amount'] = $mt4_res['PROFIT'];
                    }
                    elseif ($tp['server']=='MT5')
                    {   # MT5 - Get FTD
                        $where = "Action=2 AND Login=".$tp['login']." ORDER BY Time ASC";
                        $mt5_res = $mt5->selectRow('mt5_deals', $where,0);
                        $arrayWord = array('Zeroing','Carried Balance From MT4');
                        if(!in_array($mt5_res['Comment'] , $arrayWord) ){
                            $update['ftd'] = $mt5_res['Time'];
                            $update['ftd_amount'] = $mt5_res['Profit'];
                        } else {
                            $update['ftd'] = "0000-00-00 00:00:00";
                            $update['ftd_amount'] = "0";
                        }
                    } 
                }
                
                if ($update['ftd'])
                {   # Update TP
                    if($retention) $update['retention'] = $retention;
                    if($conversion) $update['conversion'] = $conversion;
                    if($affiliate) $update['ib'] = $affiliate;
                    $where = 'login='.$tp['login'];
                    $result->res[$tp['login']] = $db->updateAny('tp', $update, $where);
                } else $result->res[$tp['login']] = false;
            }
        } else $result->e = 'Error - User ID';
        return $result;
    }

    /**
     * @param $string
     * @return bool
     */
    public static function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     *  Get Login Details - MT5
     */
    public static function getLoginDetails($login) {
        global $db;
        $sql = "SELECT * FROM 
                lidyapar_mt5.mt5_users Tu 
                LEFT JOIN 
                lidyapar_mt5.mt5_groups Tg ON Tu.Group = Tg.Group
                LEFT JOIN 
                lidyapar_mt5.mt5_accounts Ta ON Tu.Login = Ta.Login 
                WHERE Tu.Login=$login LIMIT 1";
        $result = $db->run($sql);
        $output=array();
        if(is_object($result)) {
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $output[] = $row;
            }
            mysqli_free_result($result);
        }
        return ($output) ? $output[0] : false;
    }

    /**
     * Exchange Rate - MT5
     */
    public static function exchangeRate($s_sym, $d_sym)
    {
        $output = array();
        $output['res']['inputs'] = $s_sym.$d_sym;
        $MT5API = new mt5API();
        $params['symbol'] = strtoupper($s_sym.$d_sym);
        $MT5API->get('/api/tick/last', $params);
        $output['e']   = $MT5API->error;
        if($MT5API->Response->trans_id > 0) {
            $output['res']['order'] = 'LR';
            $output['res']['rate'] = $MT5API->Response->answer[0]->Bid;
        } else {
            $params['symbol'] = strtoupper($d_sym.$s_sym);
            $MT5API->get('/api/tick/last', $params);
            $output['e']   = $MT5API->error;
            if($MT5API->Response->trans_id>0) {
                $output['res']['order'] = 'RL';
                $output['res']['rate'] = 1/$MT5API->Response->answer[0]->Bid;
            } else {
                $output['e']   = 'no exchange rate!';
                $output['res'] = null;
            }
        }
        unset($MT5API);
        return $output;
    }

}

