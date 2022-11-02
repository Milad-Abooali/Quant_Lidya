<?php

function c_1 ($data, $row, $cols)
{
    if ($data==$cols[2])
        $output = $data;
    else
        $output = 'S: '.$data . '<br>D: '.$cols[2];
    return $output;
}
function c_10 ($data, $row, $cols)
{
    return 'S: '.$data . '<br>D: '.$cols[11];
}
function c_4 ($data, $row, $cols)
{
    if ($cols[14])
        $output = '['.$cols[14].']<br>'.$data;
    else
        $output = $data;
    return $output;
}
function c_5 ($data, $row,$cols)
{
    if ($cols[15])
        $output = '['.$cols[15].']<br>'.$data;
    else
        $output = $data;
    return $output;
}
function c_13 ($data)
{
    return '<span class="mt-2 alert d-block" data-toggle="tooltip" data-placement="top" title="'.$data.'">'.ucwords(GF::timeAgo($data)).'</span>';
}