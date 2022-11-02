<?php

// Action
function c_1 ($data, $row, $col) {
    $color['View']   = 'light';
    $color['New']    = 'success';
    $color['Edit']   = 'warning';
    $color['Delete'] = 'danger';
    return '<small class="px-2 rounded alert-'.(($color[$data]) ?? 'light').'">'.$data."</small>";
}

// Referer
function c_4 ($data, $row, $col) {
    $domain = parse_url($data, PHP_URL_HOST);
    return ($data) ? "<a target='_blank' class='small' data-toggle='tooltip' data-placement='top' title='$data' href='$data'>$domain</a>" : null;
}

// Sessıon
function c_8 ($data, $row, $col) {
    return ($col[9]) ? "<a target='_blank' class='small' data-toggle='tooltip' data-placement='top' title='View user actions' href='sys_settings.php?section=system_actlog&dt={\"table\":\"DT_actlog_user\",\"regex\":\"1\",\"cols\":{\"8\":\"$col[9]\"}}'>$data</a>" : $data;
}

// Sessıon
function c_9 ($data, $row, $col) {
    return "<a target='_blank' class='small' data-toggle='tooltip' data-placement='top' title='List other sessions of this user' href='sys_settings.php?section=waf_session-user&search=$data'>$data</a>";
}

// Detail
function c_5 ($data, $row, $col) {
    $type = $col[1];
    $data = str_replace("'","`", $data);
    if ($data) $output = "<button class='btn btn-xs btn-primary do-loadDetail'  data-type='$type' data-json='$data'>Show</button>";
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
