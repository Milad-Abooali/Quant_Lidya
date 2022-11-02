<?php

/**
 * User
 *
 * @package    AI
 * @category   Lib
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  2012 - 2021 Codebox
 * @license    https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 * @version    1.0.0
 */

namespace AI;

class classUser
{
    public static array $Blocks = [
        'users' =>
            [
                'password' => [
                    'title' => 'Password',
                    'tags'=>[
                        'password'
                    ]
                ],
                'email' => [
                    'title' => 'Email',
                    'tags'=>[
                        'username',
                        'email',
                        'email address',
                        'mail',
                        'mail address'
                    ]
                ],
                'pincode' => [
                    'title' => 'PinCode',
                    'editable' => 1,
                    'tags'=>[
                        'pincode',
                        'pin',
                        'access pin',
                    ]
                ],
                'created_at' => [
                    'title' => 'Created at',
                    'tags'=>[
                        'registration date',
                        'signup date',
                        'start date'
                    ]
                ],
                'unit' => [
                    'title' => 'Unit',
                    'tags'=>[
                        'unit',
                        'desk',
                        'section',
                        'table'
                    ]
                ],
                'type' => [
                    'title' => 'Type',
                    'tags'=>[
                        'type',
                        'role',
                        'account type'
                    ]
                ]
            ],
        'tp' =>
            [
                'login' => [
                    'title' => 'Login',
                    'selectable' => 1,
                    'tags'=>[
                        'login',
                        'logins',
                        'loggin',
                        'loggins',
                        'laging',
                        'lagings',
                        'lagging',
                        'laggings',
                        'tps',
                        'tp'
                    ]
                ],
                'password' => [
                    'title' => 'Password',
                    'editable' => 1,
                    'tags'=>[
                        'password',
                    ]
                ],
                'group_id' => [ ],
                'ftd' => [
                    'title' => 'FTD Time',
                    'tags'=>[
                        'first deposit time',
                        'first time deposit',
                        'ftd time',
                    ]
                ],
                'ftd_amount' => [
                    'title' => 'FTD Amount',
                    'tags'=>[
                        'first deposit amount',
                        'first time deposit amount',
                        'ftd amount',
                    ]
                ],
                'retention' => [ ],
                'conversion' => [ ],
                'server' => [ ]
            ],
        'extra' =>
            [
                'fname' => [
                    'title' => 'First Name',
                    'editable' => 1,
                    'form_type' => null,
                    'val_html_type' => 'text',
                    'tags'=>[
                        'first name',
                        'firstname',
                        'forename name',
                        'middle name',
                        'personal name',
                        'name'
                    ]
                ],
                'lname' => [
                    'title' => 'Last Name',
                    'tags'=>[
                        'last name',
                        'lastname',
                        'surname',
                        'family name',
                        'family',
                        'lastname'
                    ]
                ],
                'phone' => [
                    'title' => 'First Name',
                    'tags'=>[
                        'phone',
                        'phone number'
                    ]
                ],
                'country' => [
                    'title' => 'First Name',
                    'tags'=>[
                        'country'
                    ]
                ],
                'city' => [
                    'title' => 'First Name',
                    'tags'=>[
                        'city'
                    ]
                ],
                'address' => [
                    'title' => 'First Name',
                    'tags'=>[
                        'address'
                    ]
                ],
                'language' => []
            ]
    ];


    public static function getCustom($string) {

        $final_result=array();
        foreach(self::$Blocks as $block_name => $block) {
            $loop_result = array();
            foreach($block as $item_key => $item) {
                if($item['tags']) if(in_array($string, $item['tags'])) {

                    $loop_result['section'] = 'CRM';
                    $loop_result['block'] = $block_name;
                    $loop_result['item'] = $item_key;
                    $loop_result['title'] = $item['title'];
                    $loop_result['selectable'] = $item['selectable'];

                    $userManager = new \userManager();

                    if($block_name!='users') {
                        $result =  $userManager->getCustom($_SESSION['id'], $item_key)[$block_name];
                        if($block_name=='tp') {
                            $loop_result['section'] = 'MT5';
                            if($item_key=='login') {
                                $loop_result['res'] = (count($result)==1) ? $result[0][$item_key] : $result;
                            } else {
                                if($_REQUEST['seltpaccount']) {
                                    global $db_admin;
                                    $where = "login=".$_REQUEST['seltpaccount'];
                                    $loop_result['res'] = $db_admin->selectRow('tp',$where)[$item_key];
                                } else {
                                    $loop_result['res'] = (count($result)==1) ? $result[0][$item_key] : $result;
                                }
                            }
                        } else {
                            $loop_result['res'] = (is_array($result) && count($result)==1) ? $result[$item_key] : $result;
                        }
                    } else {
                        $loop_result['res'] = $userManager->getCustom($_SESSION['id'], $item_key)[$item_key];
                    }
                }
            }
            if($loop_result) $final_result[] = $loop_result;
        }
        return $final_result ?? false;
    }


}
