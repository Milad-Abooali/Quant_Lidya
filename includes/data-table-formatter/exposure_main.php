<?php

// Buy
function c_1 ($data, $row, $col) {
    $result = number_format($data, 2, '.', '')." Lot";
    return $result;
}

// Buy AVG
function c_2 ($data, $row, $col) {
    $val = ($col[1] > 0) ? ($data / $col[1])  : '0';
    $result = number_format($val, 5, '.', '');
    return $result;
}

// Sell
function c_3 ($data, $row, $col) {
    $result = number_format($data, 2, '.', '')." Lot";
    return $result;
}

// Sell AVG
function c_4 ($data, $row, $col) {
    $val = ($col[3] > 0) ? ($data / $col[3]) : '0';
    $result = number_format($val, 5, '.', '');
    return $result;
}

// Net Volume
function c_5 ($data, $row, $col) {
    if($col[1]>$col[3]){
        $side = "Buy";
        $vol = $col[1]-$col[3];
        $volume = number_format($vol, 2, '.', '');
        $result = $volume." ".$side;
        
    } else if ($col[3]>$col[1]) {
        $side = "Sell";
        $vol = $col[3]-$col[1];
        $volume = number_format($vol, 2, '.', '');
        $result = $volume." ".$side;
    } else {
        $result = "Fully Hedged";
    }
    
    return $result;
}

// Profit
function c_6 ($data, $row, $col) {
    if($data > 0){
        $result = "<span class='text-success'>$".number_format($data, 2, '.', '')."</span>";
    } else if($data < 0){
        $result = "<span class='text-danger'>$".number_format($data, 2, '.', '')."</span>";
    } else {
        $result = "$".number_format($data, 2, '.', '');
    }
    return $result;
}

// Current Price
function c_7 ($data, $row, $col) {
    $result = number_format($data, 5, '.', '');
    return $result;
}