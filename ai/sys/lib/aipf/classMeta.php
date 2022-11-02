<?php

/**
 * Meta
 *
 * @package    AI
 * @category   Lib
 * @author     Milad Abooali <m.abooali@hotmail.com>
 * @copyright  2012 - 2021 Codebox
 * @license    https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 * @version    1.0.0
 */

namespace AI;

class classMeta
{
    public static array $Blocks = [
        'tpData' =>
            [
            'api_path' =>   '/api/user/account/get_batch',
            'params' => null,
            'items' =>
            [
                'Login' => [ ],
                'CurrencyDigits' => [ ],
                'Balance' => [
                    'title' => 'Balance',
                    'tags' => [
                        'balance',
                        'account balance',
                    ]
                ],
                'Credit' => [
                    'title' => 'Credit',
                    'tags' => [
                        'credit',
                        'account credit'
                    ]
                ],
                'Margin' => [
                    'title' => 'Margin',
                    'tags' => [
                        'margin',
                        'account margin',
                        'used margin'
                    ]
                ],
                'MarginFree' => [
                    'title' => 'Free Margin',
                    'tags' => [
                        'freemargin',
                        'account freemargin',
                        'free margin',
                        'account free margin'
                    ]
                ],
                'MarginLevel' => [
                    'title' => 'Margin Level',
                    'tags' => [
                        'marginlevel',
                        'margin level',
                        'account margin level',
                        'account marginlevel'
                    ]
                ],
                'MarginLeverage' => [ ],
                'Profit' => [ ],
                'Storage' => [
                    'title' => 'Swap',
                    'tags' => [
                        'swap',
                        'account swap',
                        'storage',
                        'account storage'
                    ]
                ],
                'Commission' => [ ],
                'Floating' => [ ],
                'Equity' => [
                    'title' => 'Equity',
                    'tags' => [
                        'equity',
                        'account equity'
                    ]
                ],
                'SOActivation' => [ ],
                'SOTime' => [ ],
                'SOLevel' => [ ],
                'SOEquity' => [ ],
                'SOMargin' => [ ],
                'Assets' => [ ],
                'Liabilities' => [ ],
                'BlockedCommission' => [ ],
                'BlockedProfit' => [ ],
                'MarginInitial' => [ ],
                'MarginMaintenance' => [ ]
            ]
        ]
    ];

    function __construct()
    {
        self::$Blocks['tpData']['params'] =
                [
                    'login' =>  $_REQUEST['seltpaccount']
                ];
    }

    public static function getCustom($string)
    {
        $final_result = array();
        foreach(self::$Blocks as $block_name => $block) {
            $loop_result = array();
            foreach($block['items'] as $item_key => $item) {
                if($item['tags']) if(in_array($string, $item['tags'])) {
                    $loop_result['section'] = 'MT5';
                    $loop_result['block'] = $block_name;
                    $loop_result['item'] = $item_key;
                    $loop_result['title'] = $item['title'];
                    $loop_result['selectable'] = $item['selectable'];

                    $mt5api = new \mt5API();
                    $mt5api->get($block['api_path'], $block['params']);
                    if(!$mt5api->error) {
                        $loop_result['res'] = $mt5api->Response->answer[0]->$item_key;
                    } else {
                        $loop_result['res'] =  $mt5api->error;
                    }
                }
            }
            if($loop_result) $final_result[] = $loop_result;
        }
        return $final_result ?? false;
    }


}