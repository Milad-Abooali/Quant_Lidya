<?php

/**
 * Functions
 * @package    -
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  -
 * @license    -
 * @version    1.0.0
 * @update     Created by M.Abooali 09-2-2021
 */

class Func {

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
     * Console Log
     * @param string $string
     */
    public static function c($string, $line=true) {
        if($line) $string = str_replace("\r\n", "", $string);
        echo "<script>console.log('$string')</script>";
    }

    /**
     * Get clinte IP
     * @return string|false return clint IP or false
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
     * @param string $start start point
     * @param string|null $end end point
     * @return string
     */
    public static function timeAgo($start, $end=null) {
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
     * Check if Os is ?
     * Pass null to return PHP_OS_FAMILY
     * @param string|null $os 'Windows', 'BSD', 'Darwin', 'Solaris', 'Linux' or 'Unknown'.
     * @return bool
     */
    public static function isEnvOS($os=null) {
        if ($os) return (strtolower(PHP_OS_FAMILY) === strtolower($os));
        return PHP_OS_FAMILY;
    }



}

