<?php

    set_time_limit(30);

     require_once 'mt5.php';
     require_once 'mt5API.php';

    $mt5api = new mt5API();
    $api_symbol['symbol'] = 'EURUSD';
    $mt5api->get('/api/tick/last', $api_symbol);

    if(is_array($mt5api->Error)){
        $e = $mt5api->Error;
        var_dump($e);
    } else {
        echo 'No Error . . .!';
    }
    echo '<hr>';
    if(is_object($mt5api->Response)){
        $res = $mt5api->Response->answer;
        var_dump($res);
    } else {
        echo '<br>No Response . . !';
    }
