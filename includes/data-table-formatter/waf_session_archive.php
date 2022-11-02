<?php


// User ID
function c_1 ($data, $row, $col) {
    global $db;
    $user = $db->selectId('users',$data)['username'];
    $output =  "<span data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"\" data-original-title=\"User ID: ".$data."\">$user</span>";
    $output .= "<br><a class='text-info small' href='sys_settings.php?section=waf_session-user&search=$data'>List User Sessions</a>";
    return $output;

}

// Agent
function c_2 ($data, $row, $col) {
    return "<a target='_blank' class='small' href='sys_settings.php?section=system_actlog&dt={\"table\":\"DT_actlog_user\",\"regex\":\"1\",\"cols\":{\"8\":\"".$col[0]."\"}}'>$data</a>";
}

// IP
function c_3 ($data, $row, $col) {
    $output =  "<a href='https://ipapi.co/$data/' target='_blank'>$data</a>";
    global $_waf;
    $waf_ip = $_waf->getIP($data);
    if ($waf_ip) {
        if ($waf_ip['status']==0) $output =  "<a class='text-warning' href='https://ipapi.co/$data/' target='_blank'>$data</a><br>".$waf_ip['info'];
        if ($waf_ip['status']==1) $output =  "<a class='text-success' href='https://ipapi.co/$data/' target='_blank'>$data</a><br>".$waf_ip['info'];
    }
    $output .= "<br><a class='text-info small' href='sys_settings.php?section=waf_session-ip&search=$data'>List IP Sessions</a>";
    return $output;
}

// Agent
function c_4 ($data, $row, $col) {
    return "<small>$data</small>";
}

// Status
function c_5 ($data, $row, $col) {
    if ($data) {
        if ($col[0] == $_SESSION['sess_id']) {
            return '<span class="btn btn-success btn-sm disabled" >Current Session</span>';
        } else {
            return '<button class="btn btn-outline-danger btn-sm doA-endSess" data-id="'.$col[0].'">End Now</button>';
        }
    } else {
        return '<span class="btn btn-light btn-sm">Ended</span>';
    }
}

// Timestamp
function c_6 ($data, $row, $col) {
    return '<span data-toggle="tooltip" data-placement="right" title="'.$data.'">'.GF::timeAgo($data).'</span>';
}

// Timestamp Last Activity
function c_7 ($data, $row, $col) {
    global $db;
    $where = 'sess_id ='.$data;
    $last = $db->selectRow('act_log_user',$where,'timestamp DESC')['timestamp'];
    return '<span data-toggle="tooltip" data-placement="right" title="'.$last.'">'.GF::timeAgo($last).'</span>';
}

// Visited
function c_8 ($data, $row, $col) {
    global $db;
    $where = 'sess_id ='.$data.' AND act_type="View"';
    return $db->count('act_log_user',$where);
}

// Actions
function c_9 ($data, $row, $col) {
    global $db;
    $where = 'sess_id ='.$data.' AND act_type!="View"';
    return $db->count('act_log_user',$where);
}