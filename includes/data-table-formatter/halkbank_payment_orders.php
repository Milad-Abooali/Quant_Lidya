<?php

// Amount
function c_3 ($data) {
    return '₺ '.$data;
}

// Status
function c_4 ($data) {
    return ($data) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-clock text-secondary"></i>';
}

// Time
function c_5 ($data) {
    return '<span data-toggle="tooltip" data-placement="right" title="'.$data.'">'.GF::timeAgo($data).'</span>';
}

// Button
function c_6 ($data, $row, $col) {
    if($col[7]) {
        $output = "<button data-resp='".$col[7]."' class='doM-response btn btn-sm btn-primary mr-2'>Show Responses</button>";
        if($col[4]) {
            $TransId = json_decode($col[7])->TransId;
            $output .= '<button data-tid="'.$TransId.'" data-id="'.$data.'" class="doA-refund btn btn-sm  btn-danger mr-2">Refund</button>';
        }
    }
    return $output;
}