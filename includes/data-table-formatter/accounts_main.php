<?php

// Balance
function c_2 ($data, $row, $col) {
    $result = "$".number_format($data, 2, '.', ',');
    return $result;
}

// Equity
function c_3 ($data, $row, $col) {
    $result = "$".number_format($data, 2, '.', ',');
    return $result;
}

// Margin Level
function c_4 ($data, $row, $col) {
    $result = "%".number_format($data, 2, '.', ',');
    return $result;
}

// Profit
function c_5 ($data, $row, $col) {
    $result = "$".number_format($data, 2, '.', ',');
    return $result;
}

// Margin Used
function c_6 ($data, $row, $col) {
    $result = "$".number_format($data, 2, '.', ',');
    return $result;
}

// Free Margin
function c_7 ($data, $row, $col) {
    $result = "$".number_format($data, 2, '.', ',');
    return $result;
}