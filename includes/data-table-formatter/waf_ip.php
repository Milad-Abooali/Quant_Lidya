<?php

// IP whois link
function c_1 ($data, $row) {
    return "<a class='text-warning' href='https://ipapi.co/$data/' target='_blank'>$data</a>";
}

// Creat Status Icon
function c_2 ($data, $row) {
    if ($data == 1) {
        $output = '<i class="far fa-check-circle text-success"></i>';
    } elseif ($data == 2) {
        $output = '<i class="fa fa-ban text-danger"></i>';
    } else {
        $output = '<i class="fa fa-adjust text-muted"></i>';
    }
    return $output;
}

// Creat Edit Button
function c_4 ($data, $row,$cols) {

    $Whitelist = '<button data-status="1" data-id="'.$data.'" class="btn btn-sm btn-outline-success mr-2 doA-update"><i class="far fa-check-circle"></i> Whitelist</button>';
    $Blacklist = '<button data-status="2" data-id="'.$data.'" class="btn btn-sm btn-outline-danger mr-2 doA-update"><i class="fa fa-ban"></i> Blacklist</button>';
    $Null = '<button data-status="0" data-id="'.$data.'" class="btn btn-sm btn-outline-secondary mr-2 doA-update"><i class="fa fa-adjust"></i> Null</button>';

    $delete = '<button data-id="'.$data.'" class="float-right btn btn-sm btn-danger mr-2 doA-delete"><i class="fa fa-times-circle"></i></button>';

    if ($cols[2] == 1) {
        $output = $Null.$Blacklist;
    } elseif ($cols[2] == 2) {
        $output = $Null.$Whitelist;
    } else {
        $output = $Blacklist.$Whitelist;
    }
    return $output.$delete;
}