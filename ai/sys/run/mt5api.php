<?php
/**
 * MT5 API
 */

$_AJAX_ON = true;
$_API_ON  = false;

if($_SESSION['id']) {
    $action = $_REQUEST['act'] ?? false;
    if($action) $mt5api = new mt5API();
    switch ($action) {
        case 'tpData':
            $where = "user_id=".$_SESSION['id'];
            $tps =  $db->select('tp', $where);
            unset($db_aifx, $db, $db_admin);
            if($tps) foreach($tps as $tp){
                $params['login'] = $tp['login'];
                $mt5api->get('/api/user/account/get_batch', $params);
                $_RUN->e = $mt5api->error;
                $_RUN->res[$tp['login']]['api'] = $mt5api->Response;
                $_RUN->res[$tp['login']]['tp'] = $tp;
            }
            break;
        case 'getSymbol':
            $params['symbol'] = 'EURUSD';
            $params['trans_id'] = 0;
            $mt5api->get('/api/tick/last', $params);
            $_RUN->e = $mt5api->error;
            $_RUN->res = $mt5api->Response;
            break;
        case 'test':
            $genTime->start('test');
            $params = [
                'group' => '*',
            ];
            $mt5api->get('/api/user/get_batch', $params);
            $_RUN->e = $mt5api->error;
            $_RUN->res = $mt5api->Response;
            $genTime->end('test');
            $_RUN->e = $genTime->get('test');
            break;
        default:
            $_RUN->e = 'No Action !';
    }
} else {
    $_RUN->e = 'You are not logged in!';
}