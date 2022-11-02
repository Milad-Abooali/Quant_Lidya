<?php

// Action
function c_1 ($data, $row, $col) {
    $color['View']   = 'light';
    $color['New']    = 'success';
    $color['Edit']   = 'warning';
    $color['Delete'] = 'danger';
    return '<small class="px-2 rounded alert-'.(($color[$data]) ?? 'light').'">'.$data."</small>";
}

// Detail
function c_5 ($data, $row, $col) {
    $id = $col[0];
    $type = $col[1];
    $source = $col[4];
    $status = $col[6];
    $data = str_replace("'","`", $data);
    $output = '';
    if($data)
        $output .= "<button class='btn btn-sm btn-outline-primary do-loadDetail' data-type='$type' data-json='$data'><i class='fa fa-eye'></i></button>";
    if($type==='Mass Assign' && $status)
        $output .= "<button class='ml-1 btn btn-sm btn-outline-danger do-revert' data-id='$id' data-type='$type' data-json='$data'><i class='fa fa-reply-all'></i></button>";
    if($type==='Revert'){
        $filter = '{"table":"DT_actlog_user","regex":"1","cols":{"0":"'.$source.'"}}';
        $output .= "<a class='ml-1 btn btn-sm btn-outline-danger load-revert-source' href='sys_settings.php?section=system_actlog&dt=$filter'><i class='fa fa-book'></i></a>";

    }
    return ($output) ?? null;
}

// Status
function c_6 ($data, $row, $col) {
    return ($data) ? '<i class="fa fa-check-circle text-info"></i>' : '<i class="fa fa-ban text-danger"></i>';
}

// Timestamp
function c_7 ($data, $row, $col) {
    return ($data) ? '<span data-toggle="tooltip" data-placement="right" title="'.$data.'">'.GF::timeAgo($data).'</span>' : null;
}
