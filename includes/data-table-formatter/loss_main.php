<?php

// Losing
function c_2 ($data, $row, $col) {
    $result = "%".number_format($data, 2, '.', ',');
    return $result;
}

// Total Orders
function c_3 ($data, $row, $col) {
    $result = number_format($data, 2, '.', ',');
    return $result;
}

// PNL
function c_4 ($data, $row, $col) {
    $result = "$".number_format($data, 2, '.', ',');
    return $result;
}

// Balance
function c_5 ($data, $row, $col) {
    $result = "$".number_format($data, 2, '.', ',');
    return $result;
}

// Equity
function c_6 ($data, $row, $col) {
    $result = "$".number_format($data, 2, '.', ',');
    return $result;
}

// Open Positions
function c_7 ($data, $row, $col) {
    $result = "$".number_format($data, 2, '.', ',');
    return $result;
}