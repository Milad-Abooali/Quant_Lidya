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

}