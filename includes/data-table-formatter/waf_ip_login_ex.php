<?php


// Expire Button
function c_4 ($data, $row, $col) {
    $now = time();
    $exception = strtotime($col[2]);
    if ($now > $exception) {
        return '<button class="btn btn-secondary btn-sm disable" data-id="'.$data.'">Expired</button>';
    } else {
        return '<button class="btn btn-outline-danger btn-sm doExpire" data-id="'.$data.'">End Now</button>';
    }
}