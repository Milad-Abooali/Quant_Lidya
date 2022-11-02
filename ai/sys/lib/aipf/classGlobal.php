<?php

/**
 * Global
 *
 * @package    AI
 * @category   Lib
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  2012 - 2021 Codebox
 * @license    https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 * @version    1.0.0
 */

namespace AI;

class classGlobal
{

    function __construct()
    {

    }

    public static function setCustom($string) {
        include_once 'classMeta.php';
        include_once 'classUser.php';

        $final_result=array();

        // User
        foreach(classMeta::$Blocks as $block_name => $block) {
            $loop_result = array();
            foreach($block['items'] as $item_key => $item) {
                if($item['tags'] && $item['editable']) if(in_array($string, $item['tags'])) {

                    $loop_result['class'] = 'classMeta';
                    $loop_result['section'] = 'MT5';
                    $loop_result['block'] = $block_name;
                    $loop_result['item'] = $item_key;
                    $loop_result['title'] = $item['title'];
                    $loop_result['selectable'] = $item['selectable'];
                    $loop_result['form_type'] = $item['form_type'];
                    $loop_result['val_html_type'] = $item['val_html_type'];

                    $loop_result['res'] = 1;

                }
            }
            if($loop_result) $final_result[] = $loop_result;
        }

        // Meta
        foreach(classUser::$Blocks as $block_name => $block) {
            $loop_result = array();
            foreach($block as $item_key => $item) {
                if($item['tags'] && $item['editable']) if(in_array($string, $item['tags'])) {

                    $loop_result['class'] = 'classUser';
                    $loop_result['section'] = 'CRM';
                    $loop_result['block'] = $block_name;
                    $loop_result['item'] = $item_key;
                    $loop_result['title'] = $item['title'];
                    $loop_result['selectable'] = $item['selectable'];
                    $loop_result['form_type'] = $item['form_type'];
                    $loop_result['val_html_type'] = $item['val_html_type'];

                    $loop_result['res'] = 1;

                }
            }
            if($loop_result) $final_result[] = $loop_result;
        }

        return $final_result ?? false;

    }


}