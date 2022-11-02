<?php

// result
function c_2 ($data, $row, $col) {
    $data = str_replace("'","`", $data);
    if ($data) $output = "<button class='btn btn-xs btn-primary do-loadDetail' data-json='$data'>Show</button>";
    return ($output) ?? null;
}

// start_time
function c_3 ($data, $row, $col) {
    return ($data) ? '<span data-toggle="tooltip" data-placement="right" title="'.$data.'">'.GF::timeAgo($data).'</span>' : null;
}

// end_time
function c_4 ($data, $row, $col) {
    return ($data) ? '<span data-toggle="tooltip" data-placement="right" title="'.$data.'">'.GF::timeAgo($data).'</span>' : null;
}


// status
function c_5 ($data, $row, $col) {
    return ($data) ? '<i class="fa fa-check-circle text-info"></i>' : '<i class="fa fa-ban text-danger"></i>';
}