<?php

// Wallet Title
function c_2 ($data)
{
    global $db;
    $wallet = $db->selectId('wallet_types',$data);
    return $wallet['title'];
}

function c_4 ($data,$row,$col){
    $balance = '<div class="form-group"><input id="balance-'.$col[0].'" class="border-0" name="rate" type="number" step="0.01" value="'.$data.'" required>';
    $balance .= '<button data-id="'.$col[0].'" class="btn btn-sm btn-outline-dark btn-xs doA-balance mr-2" data-id="1">Update</button></div>';
    return ($col[6]!=-1) ? $balance : 0;
}
function c_3 ($data)
{
    global $db;
    $wallet = $db->selectId('wallet_types',$data);
    return $wallet['sym'];
}

function c_5 ($data)
{
    return '<span class="mt-2 alert d-block" data-toggle="tooltip" data-placement="top" title="'.$data.'">'.ucwords(GF::timeAgo($data)).'</span>';
}

function c_6 ($data)
{
    if ($data==-1) {
        return '<i class="text-danger fa fa-times-circle"></i> Removed';
    } else if ($data==1) {
        return '<i class="text-warning fa fa-ban"></i> Blocked';
    } else if ($data==0) {
        return '<i class="text-success fa fa-check-circle"></i> Active';
    }
    return "Undefined";
}

function c_7 ($data,$row,$col)
{
    $manage = '<button data-id="'.$data.'" class="float-left btn btn-sm btn-info btn-xs doM-history mx-2" data-id="1">History</button>';
    if($col[6]!=-1)
        $manage .= '<button data-toggle="tooltip" data-placement="top" title="Set Remove" data-id="'.$data.'" class="float-right btn btn-sm btn-danger mr-2 doA-delete"><i class="fa fa-times-circle"></i></button>';
    if($col[6]!=1 && $col[6]!=-1)
        $manage .= '<button data-toggle="tooltip" data-placement="top" title="Set Block" data-id="'.$data.'" class="float-right btn btn-sm btn-warning mr-2 doA-block"><i class="fa fa-ban"></i></button>';
    if($col[6]!=0 && $col[6]!=-1)
        $manage .= '<button data-toggle="tooltip" data-placement="top" title="Set Active" data-id="'.$data.'" class="float-right btn btn-sm btn-success mr-2 doA-active"><i class="fa fa-check-circle"></i></button>';
    return $manage;
}