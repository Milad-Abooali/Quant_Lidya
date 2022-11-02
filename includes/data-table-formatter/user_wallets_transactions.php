<?php

// Balance
function c_4 ($data, $row, $cols)
{
    $colors['deposit']  = 'text-success';
    $colors['transfer'] = 'text-primary';
    $colors['withdraw'] = 'text-danger';
    $colors['move']     = 'text-info';
    $colors['close']     = 'text-warning';

    return
        '<span class="'.$colors[$cols[3]].'">'.ucfirst($cols[3]).'</span><br><small>Volume:</small> <strong class="text-dark">'.GF::nf($data).'</strong>'
        .'<br><small>Fee:</small> <span class="text-muted">'.GF::nf($cols[9]).'</span>';
}

// Source
function c_5 ($data, $row, $cols)
{
    global $db;
    if ($cols[14]=='Wallet' && $data>0){
        $wallet_type_id =  $db->selectId('user_wallets', $data)['type_id'];
        $wallet_type_name =  $db->selectId('wallet_types', $wallet_type_id)['title'];
        $output = $wallet_type_name;
    } else {
        $output = $data;
    }
    return $cols[1].'<br>'.$cols[14].'<br>'.$output;
}

// Destination
function c_6 ($data, $row,$cols)
{
    global $db;
    if ($cols[15]=='Wallet' && $data>0){
        $wallet_type_id =  $db->selectId('user_wallets', $data)['type_id'];
        $wallet_type_name =  $db->selectId('wallet_types', $wallet_type_id)['title'];
        $output = $wallet_type_name;
    } else {
        $output = $data;
    }
    return $cols[2].'<br>'.$cols[15].'<br>'.$output;
}

// Ref ID
function c_7 ($data, $row,$cols)
{
    global $db;
    $where = 'transaction_id='.$cols[0];
    $docs = $db->select('wallet_transaction_docs',$where);
    $comments = $db->select('wallet_transaction_comment',$where);
    $extra = null;
    if($docs) foreach ($docs as $doc) {
        $extra .= '<a href="media/wallet_transaction/'.$doc['filename'].'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="'.$doc['filename'].'" data-id="'.$doc['id'].'" class="btn btn-outline-light text-primary btn-sm fa fa-file-alt m-1"></a>';
    }
    if(!empty($extra)) $extra .= "<br>";
    if ($comments) foreach ($comments as $comment){
        $extra .= '<a 
           data-toggle="popover"
           data-content="'.$comment['comment'].'" 
           tabindex="0" 
           role="button" 
           data-trigger="focus"
           data-html="true"   
           data-id="'.$comment['id'].'" class="btn btn-outline-light text-warning btn-sm fa fa-comment m-1 popupover"></a>';
    }
    return  $data.'<br>'.$extra;
}

//  Time
function c_13 ($data)
{
    return '<span class="mt-2 alert d-block" data-toggle="tooltip" data-placement="top" title="'.$data.'">'.ucwords(GF::timeAgo($data)).'</span>';
}