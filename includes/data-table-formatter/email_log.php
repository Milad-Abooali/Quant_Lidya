<?php

// subject
function c_3 ($data, $row, $col) {
    if(substr( $data, 0, 7 ) === "=?UTF-8"){
        $data = str_replace('=?UTF-8?B?','',$data);
        $data = str_replace('?=','',$data);
        $data = base64_decode($data);
    }
    return $data;
}

// Action
function c_5 ($data, $row, $col) {
    return '<button class="btn btn-outline-primary btn-sm doA-ShowEmail mr-2" data-id="'.$data.'">Show</button><button class="btn btn-success btn-sm doA-resend" data-id="'.$data.'">reSend</button>';
}
