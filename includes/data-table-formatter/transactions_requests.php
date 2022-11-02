<?php

// Fullname
function c_0 ($data) {
    $user = new usermanager();
    if ($data) $user_data =  $user->getCustom($data, "fname,lname");
    $full_name = $user_data['extra']['fname'].' '.$user_data['extra']['lname'];
    return $full_name;
}


// Source
function c_5 ($data, $row, $col) {
    if ($col[1] == 'Deposit') {
        global $db;
        $data = $db->selectId('payment_gateways',$data,'name')['name'];
    }
    return $data;
}


// Verification
function c_8 ($data, $row, $col) {

    if ($col[1] == 'Deposit' && $col[7]=='Pending') {
        $desk_act     = 'pointer desk';
        $finance_act  = ($col[8]) ? 'pointer finance' : '';
        $treasury_act = ($col[8] && $col[11]) ? 'pointer treasury' : '';
    } else if  ($col[1] == 'Withdraw' && $col[7]=='Pending') {
        $desk_act     = 'pointer desk';
        $treasury_act  = ($col[8]) ? 'pointer treasury' : '';
        $finance_act = ($col[8] && $col[12]) ? 'pointer finance' : '';
    }

    if($data) {
        $desk = "text-success";
        $start = $col[15];
        $end = $col[8];
        $time = ucwords(GF::timeAgo($start,$end)).'<br>S: '.$start.'<br>E: '.$end;
        $output = "<span data-toggle='tooltip' data-placement='top' data-html='true' title='' data-original-title='$time' class='verify-status ".$desk."'>D</span> ";
    } else {
        $desk = "text-danger";
        $output = "<span class='$desk_act verify-status ".$desk."' data-user='".$col[0]."' data-id='".$col[13]."'>D</span> ";
    }

    if($col[11]){
        $finance = "text-success";
        $start = ($col[1] == 'Deposit') ? $col[8] : $col[12] ;
        $end = $col[11];
        $time = ucwords(GF::timeAgo($start,$end)).'<br>S: '.$start.'<br>E: '.$end;
        $output .= "<span data-toggle='tooltip' data-placement='top' data-html='true' title='' data-original-title='$time' class='verify-status ".$finance."'>F</span> ";
    } else {
        $finance = "text-danger";
        $output .=  "<span class='$finance_act verify-status ".$finance."' data-user='".$col[0]."' data-id='".$col[13]."'>F</span> ";
    }

    if($col[12]){
        $treasury = "text-success";
        $start = ($col[1] == 'Deposit') ? $col[11] : $col[8] ;
        $end = $col[12];
        $time = ucwords(GF::timeAgo($start,$end)).'<br>S: '.$start.'<br>E: '.$end;
        $output .=  "<span data-toggle='tooltip' data-placement='top' data-html='true' title='' data-original-title='$time' class='verify-status ".$treasury."'>T</span>";
    } else {
        $treasury = "text-danger";
        $tp = ($col[1]=='Deposit') ? $col[6] : $col[5];
        $output .=  "<span class='$treasury_act verify-status ".$treasury."' data-user='".$col[0]."'  data-unit='".$col[14]."' data-tp='".$tp."' data-id='".$col[13]."' data-type='".$col[1]."' data-amount='".$col[2]."'>T</span>";
    }

    $output = ($col[7]!='Pending') ? '<span style="opacity:0.2">'.$output.'</span>' : $output;

    $start = $col[15];
    $end = $col[10];
    if($start == $end) {
        $time = 'No action.';
    } else {
        $time = ucwords(GF::timeAgo($start,$end));
    }
    $time_e = 'S: '.$start.'<br>E: '.$end;

    $output .= "<hr><span data-toggle='tooltip' data-placement='top' data-html='true' title='' data-original-title='$time_e'>$time</span>";
    return $output;
}

// Updated By
function c_9 ($data) {
    $user = new usermanager();
    return $user->getCustom($data, 'username')['username'];
}

// Updated At
function c_10 ($data, $row, $col) {
    if ($col[7]=='Pending') {
    $diff =  strtotime("now") - strtotime($data);
    $diff = ($diff > 86400) ? 9 : intval($diff/300);
    } else {
        $diff = 0;
    }
    return '<span class="mt-2 alert d-block" style="background:rgba(255,150,0,0.'.$diff.')" data-toggle="tooltip" data-placement="right" title="'.$data.'">'.ucwords(GF::timeAgo($data)).'</span>';
}

// Timeline
function c_13 ($data, $row, $col) {
    $output = '<button data-tid="'.$data.'" class="doM-timeline btn btn-primary">View</button>';
    if (in_array($_SESSION['type'],array('Admin')) && !in_array($col[7],array('Done', 'Canceled')) && $col[7]!='Deposit') {
        $output .= '<button data-tid="'.$data.'" class="doA-cancel mx-2 btn btn-danger">Cancel</button>';
    }
    return $output;
}