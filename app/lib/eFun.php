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
     * Get Symbol
     */
    public static function getSymbol($symbol)
    {
        $mt5api = new mt5API();
        $api_symbol['symbol'] = $symbol;
        $mt5api->get('/api/symbol/get', $api_symbol);
        $e = $mt5api->Error;
        return $mt5api->Response->answer;
    }

    /**
     * Get Symbol By Group
     */
    public static function getSymbolByGroup($symbol, $group)
    {
        $mt5api = new mt5API();
        $api_symbol['symbol'] = $symbol;
        $api_symbol['group'] = $group;
        $mt5api->get('/api/symbol/get_group', $api_symbol);
        $e = $mt5api->Error;
        return $mt5api->Response->answer;
    }

    /**
     * Get Symbol By Login
     */
    public static function getSymbolByLogin($symbol, $login)
    {
        $group = self::getLoginGroup($login);
        return self::getSymbolByGroup($symbol, $group['name']);
    }

    /**
     * Get Price
     */
    public static function getPrice($symbol)
    {
        $mt5api = new mt5API();
        $api_symbol['symbol'] = $symbol;
        $mt5api->get('/api/tick/last', $api_symbol);
        $e = $mt5api->Error;
        return $mt5api->Response->answer;
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
    public static function isTradeOpenByGroup($symbol, $group)
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
    public static function isTradeOpenByLogin($symbol, $login)
    {
        $group = self::getLoginGroup($login);
        if($group['name']) return self::isTradeOpenByGroup($symbol, $group['name']);
        return $login;
    }

    /**
     * Epoc Time
     * @param $epoc
     * @param null $addition
     * @param string $format
     * @return false|string
     */
    public static  function eTime($epoc, $addition=null, $format='Y-m-d H:i:s'){
        return date($format, strtotime("@".$epoc." ".$addition));
    }

    /**
     * Check for Empty Val
     * @param $value
     * @param null $no_val
     * @param string $char
     * @param false $process
     * @return mixed|string
     */
    public static function vacancy($value, $no_val=null, $char='-', $process=false){
        $output = $value;
        if($no_val){
            if(is_array($no_val)) {
                foreach($no_val as $item)
                    if($value===$item)
                        $output=$char;
            }
            else {
                if($value===$no_val)
                    $output=$char;
            }
        }
        else {
            if(!($value>0))
                $output=$char;
        }
        if(!$process || $output==$char)
            return $output;
        else
            $process($output);
    }
}