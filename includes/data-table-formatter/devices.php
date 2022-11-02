<?php

// Type(2) & Manufacturer(3)
function c_2($data, $row, $col){
    return '<span data-toggle="tooltip" data-placement="top" data-title="'.$data.'"><img class="fa-fw" src="/assets/icons/devices/'.str_replace(' ','-',$data).'.png"> '.$col[3].'</span>';
}

// Model(4) & Serial Number(5)
function c_4($data, $row, $col){
    return '<span data-toggle="tooltip" data-placement="left" data-title="Model">'
        .$data.'</span><br><span data-toggle="tooltip" data-placement="left" data-title="Manufacturer">'.$col[5].'</span>';
}

// CPU(6) & Memory (7) && Storage Size (8)
function c_6($data, $row, $col){
    return '<span data-toggle="tooltip" data-placement="left" data-title="CPU">'
        .$data.'</span><br><span data-toggle="tooltip" data-placement="left" data-title="Memory">'
        .$col[10].'</span><br><span data-toggle="tooltip" data-placement="left" data-title="Storage Size">'
        .$col[11].'</span>';
}


// OS(9) & OS License (10) && OS Version (11)
function c_9($data, $row, $col){
    return '<span data-toggle="tooltip" data-placement="left" data-title="OS">'
        .$data.'</span><br><span data-toggle="tooltip" data-placement="left" data-title="OS License">'
        .$col[10].'</span><br><span data-toggle="tooltip" data-placement="left" data-title="OS Version">'
        .$col[11].'</span>';
}

// Created By(15) & OS Created At (16)
function c_14($data, $row, $col){
    return '<span data-toggle="tooltip" data-placement="left" data-title="Created By">'
        .$data.'</span><br><span data-toggle="tooltip" data-placement="left" data-title="Created At">'
        .$col[15].'</span>';
}

// Updated By(16) & OS Updated At (17)
function c_16($data, $row, $col){
    return '<span data-toggle="tooltip" data-placement="left" data-title="Updated By">'
        .$data.'</span><br><span data-toggle="tooltip" data-placement="left" data-title="Updated At">'
        .$col[17].'</span>';
}

// Type(19)
function c_19($data, $row, $col){
    $html = '<button data-id="'.$data.'"  class="doA-edit mx-1 btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-fw fa-edit"></i></button>';
    $html .= '<button data-id="'.$data.'" class="doA-delete mx-1 btn btn-sm btn-outline-danger" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-fw fa-times"></i></button>';
    return $html;
}

