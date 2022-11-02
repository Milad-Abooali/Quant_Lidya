<?php

/**
 * App Session
 */
class appSession extends sessionManager {

    /**
     * Sync CRM
     * Check the CRM session status
     */
    public static function checkRole(){
        if($_SESSION['id']) {
            global $db;
            $where = "type='avatar' AND user_id=".$_SESSION['id'];
            $media = $db->selectRow('media',$where);
            if( $media['media'] ) $_SESSION['app']['avatar'] = 'media/'.$media['media'];

            if($_SESSION['type']==='Admin')
                $_SESSION['app']['role'] = 'admin';
            else if(in_array($_SESSION['type'],
                [
                'Backoffice',
                'ManagerCountry ',
                'Manager',
                'Sales Agent',
                'Retention Agent',
                'Sales Manager',
                'Retention Manager',
                'Lawyer'
                ]))
                $_SESSION['app']['role'] = 'agent';
            else if(in_array($_SESSION['type'],
                [
                'Leads',
                'Trader',
                'IB'
                ]))
                $_SESSION['app']['role'] = 'user';
            else
                $_SESSION['app']['role'] = 'guest';
        }
        else
            $_SESSION['app']['role'] = 'guest';
        return $_SESSION['app']['role'];
    }

}