<?php

// broker_id
function c_4 ($data)
{
    global $db;
    $broker = $db->selectId('brokers',$data);
    return $broker['title'];
}

// unit_id
function c_5 ($data)
{
    global $db;
    $broker = $db->selectId('units',$data);
    return $broker['name'];
}

// Creat Edit / delete Button
function c_6 ($data, $row,$cols) {

    $edit = '<button data-id="'.$data.'" class="float-right btn btn-sm btn-info btn-xs doM-edit mx-2" data-id="1">Edit</button>';
    $delete = '<button data-toggle="tooltip" data-placement="top" title="Delete Wallet Type" data-id="'.$data.'" class="float-right btn btn-sm btn-danger mr-2 doA-delete"><i class="fa fa-times-circle"></i></button>';

    return $delete.$edit;
}