<?php

// Type (2), O_Type (3)
function c_2 ($data, $row, $col) {
    $html = $data;
    if ($col['3']) $html .= ': '.$col['3'];
    return $html;
}

// created_at (7), id (0)
function c_7 ($data, $row, $col) {
    if($_SESSION['type']!="Admin")
        return $data;
    return '<a href="javascript:;" data-id="'.$col[0].'" class="remove-expense float-right"><i class="fas fa-times-circle"></i></a>'.$data;
}

