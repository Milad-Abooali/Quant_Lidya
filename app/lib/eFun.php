<?php

/**
 * eFun
 * Public Static Functions
 */
class eFun{

    /**
     * Include All Files
     * @param string $path
     * @param bool $once
     * @param string $ex
     * @param string $callback
     */
    public static function include_all(string $path, bool $once=true, string $ex='*.php', string $callback=''): void
    {
        foreach(glob("{$path}/{$ex}") as $f_name){
            ($once) ? include_once($f_name) : include($f_name);
            if(function_exists($callback))
                $callback($f_name);
        }
    }

    /**
     * Require All Files
     * @param string $path
     * @param bool $once
     * @param string $ex
     * @param string $callback
     */
    public static function require_all(string $path, bool $once=true, string $ex='*.php', string $callback=''): void
    {
        foreach(glob("{$path}/{$ex}") as $f_name){
            ($once) ? require_once($f_name) : require($f_name);
            if(function_exists($callback))
                $callback($f_name);
        }
    }

    /**
     * Add Include Path
     * @param string $path
     * @return string
     */
    public static function add_inc_path(string $path): string
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);
        return get_include_path();
    }

    /**
     * Session Jump
     * @param string $session_id
     * @return false|string
     */
    public static function sessionJump(string $session_id){
        session_write_close();
        session_id($session_id);
        session_start();
        appSession::checkRole();
        global $APP;
        $APP->updateRole();
    }

    /**
     * Session Jump Back
     */
    public static function sessionJumpBack(){
        session_write_close();
        session_id(Origin_Session_Id);
        session_start();
    }

    /**
     * Minify HTML
     */
    public static function htmlMinify(string $html){
        $html = str_replace(array("\r","\n","  "),'', $html);
        $html = str_replace(array("> <",">\t<"),'><', $html);
        $html = str_replace(array('" ',' "'),'"', $html);
        return $html;
    }

    /**
     * Epoch to DateTime
     */
    public static function epoch2date($item) {
        $item[0] = date("Y-m-d H:i:s",$item[0]);
        return $item;
    }

    /**
     * Get Login Group (Day & Time)
     * @return array
     */
    public static function getLoginGroup($login)
    {
        $mt5api = new mt5API();
        $api_group['login'] = $login;
        $mt5api->get('/api/user/group', $api_group);
        $group['error'] = $mt5api->Error;
        $api_group = $mt5api->Response;
        $group['name'] = $api_group->answer->group;
        $group['demo_groups'] = array('LidyaGOLD', 'LidyaSTD', 'LidyaVIP');
        $group['is_demo'] = $group['name'] != str_ireplace($group['demo_groups'],"XX",$group['name']);
        return $group;
    }

    /**
     * Is Trade Open by Group(Day & Time)
     * @return bool
     */
    public static function isTradeOpenGroup($symbol, $group)
    {
        $mt5api = new mt5API();
        $api_symbol['symbol'] = $symbol;
        $api_symbol['group'] = $group;

        $mt5api->get('/api/symbol/get_group', $api_symbol);
        $e = $mt5api->Error;
        $api_symbol = $mt5api->Response->answer->SessionsTrades;
        $is_open=false;
        $week_day= date('w',strtotime("today"));
        $time_in_min = ceil( (time()-strtotime("today"))/60 );
        $symbol_times = $api_symbol[$week_day];
        if($symbol_times) foreach ($symbol_times as $symbol_time) {
            if( ($symbol_time->Open <= $time_in_min) && ($time_in_min <= $symbol_time->Close) ) {
                $is_open = true;
                break;
            }
        }
        return $is_open;
    }

    /**
     * Is Trade Open by Login (Day & Time)
     * @return bool|array
     */
    public static function isTradeOpenLogin($symbol, $login)
    {
        $group = self::getLoginGroup($login);
        if($group['name']) return self::isTradeOpenGroup($symbol, $group['name']);
        return $login;
    }


}