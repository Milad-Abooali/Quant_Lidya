<?php

// FTD
function c_5 ($data, $row, $col) {
    global $db;
    $where = 'Action = 2 AND Login = '.$col[0].' AND Time = "'.$data.'"';
    $sum = $db->sum('lidyapar_mt5.mt5_deals','Profit', $where) ?? 0;

    global $sum_t_ftd;
    $sum_t_ftd +=$sum;

    global $sum_c_ftd;
    if ($sum>0) $sum_c_ftd++;

    return $sum;
}

// Ret
function c_6 ($data, $row, $col) {
    global $db;
    $where = 'Action = 2 AND Login = '.$col[0].' AND Profit > "0" AND Comment != "Zeroing" AND Time BETWEEN "' . $_POST['startTime'] . '" AND "' . $_POST['endTime'] . '" AND Time != "'.$data.'"';
  //  $where = 'Action = 2 AND Login = '.$col[0].' AND Profit > "0" AND Comment Like "%Deposit%" AND Time != "'.$data.'"';
    $sum =  $db->sum('lidyapar_mt5.mt5_deals','Profit', $where) ?? 0;

    global $sum_t_ret;
    $sum_t_ret +=$sum;

    global $sum_c_ret;
    if ($sum>0) $sum_c_ret++;

    return $sum;
}