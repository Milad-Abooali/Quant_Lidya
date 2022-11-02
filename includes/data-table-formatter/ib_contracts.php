<?php

// User_id
function c_1 ($data, $row, $col) {
    return ($data===$_SESSION['email']) ? 'You' : $data;
}

// File
function c_2 ($data, $row, $col) {
    return '<a target="_blank" href="media/contracts/'.$data.'" class="btn btn-light btn-sm""><i class="fas fa-file-download"></i></a>';
}

// Signed
function c_4 ($data, $row, $col) {
    $col_3 = ($col[3]) ? '<i data-toggle="tooltip"  data-placement="right" title="Signed" class=" mx-auto fa fa-check-circle text-green"></i> Signed<br>' : '';
    return $col_3.$data;
}

// created
function c_6 ($data, $row, $col) {
    $col_6 = ($data) ? '<span data-toggle="tooltip" data-placement="right" title="'.$data.'">'.GF::timeAgo($data).'</span>' : '-';
    $col_7 = ($col[7]===$_SESSION['email']) ? 'You' : $col[7];
    return $col_7.'<br>'.$col_6;
}

// updated
function c_8 ($data, $row, $col) {
    $col_8 = ($data) ? '<span data-toggle="tooltip" data-placement="right" title="'.$data.'">'.GF::timeAgo($data).'</span>' : '-';
    $col_9 = ($col[9]===$_SESSION['email']) ? 'You' : $col[9];
    return $col_9.'<br>'.$col_8;
}

// Manage
function c_10 ($data, $row, $col) {
    if( in_array($_SESSION['type'],['Admin', 'Backoffice', 'Manager', 'Sales Agent'])){
        $html='';
        if($_SESSION['type']==='Admin')
            $html .= '<button class="bt btn-danger doA-delete-contract" data-id="'.$data.'"><i class="fa fa-times-circle"></i></button>';
        $html .= '
                  <select class="" id="status-contract" data-id="'.$data.'">
                    <option value="0" selected>Change Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                    <option value="expired">Expired</option>
                    <option value="Unknown">unknown</option>
                  </select>
                ';
        return $html;
    } else {
        return '-';
    }
}

