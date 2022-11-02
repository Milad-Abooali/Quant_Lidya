<?php

// Username
function c_1 ($data) {
    $user = new usermanager();
    return $user->getCustom($data, 'username')['username'];
}

//Media
function c_3 ($data) {
    return "<a href='javascript:;' class='pop'>
            <img class='rounded-circle' src='media/$data' style='width: 20px; height: 20px'>
            </a>";
}

//Action
function c_4 ($data,$row,$col)
{
    $output = '<a href="javascript:;" data-id="'.$data.'" data-status="0" class="mverify">';
    if ($col[5] == 1) {
        $output .= '<i class="fas fa-check-circle text-success"></i>';
    } else {
        $output .= '<i class="far fa-times-circle text-danger"></i>';
    }
    $output .= '</a>';
    return $output;
}
