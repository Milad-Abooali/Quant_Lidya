<?php

/**
 * Group Functions Class
 * 11:13 AM Wednesday, November 18, 2020 | M.Abooali
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

function reportMonth() {
    $output = new stdClass();
    $output->e = false;
    global $db;
    $src = ($_POST['s']) ?? false;
    $month = (count((array) $_POST['m']) ?? false) ? ('("'.implode('","',$_POST['m']).'")') : '(MONTH(CURRENT_DATE()))';
    $year = ($_POST['y']>0) ? $_POST['y']: 'YEAR(CURRENT_DATE())';
    $results=array();
    if ($src) {
        foreach ($src as $this_src) {
            $where  ="MONTH(`date`) IN $month AND YEAR(`date`) = $year AND src ='$this_src' ";
            $results[] = $db->select('marketing_report',$where,'*, DAY(`date`) as day');
        }
    } else {
        $where  ="MONTH(`date`) = $month AND YEAR(`date`) = $year ";
        $results[] = $db->select('marketing_report',$where,'*, DAY(`date`) as day');
    }
    $ftd_list=$ret_list=$ret_c_list=$ftd_c_list=$ret_c_t_list=array();
    if ($results) foreach($results as $result) {
        if ($result) foreach($result as $row) {
            $ftd_list[$row['day']] += $row['ftd'];
            $ret_list[$row['day']] += $row['ret'];
            $ret_c_t_list[$row['day']] += $row['ret_c_t'];
            $ftd_c_list[$row['day']] += $row['ftd_c'];
        }
    }
    for($i=1;$i<40;$i++) {
        $output->ftd[] = ($ftd_list[$i]) ? $ftd_list[$i] : '0';
        $output->ret[] = ($ret_list[$i]) ? $ret_list[$i] : '0';
        $output->ftd_c[] = ($ftd_c_list[$i]) ? $ftd_c_list[$i] : '0';
        $output->ret_c_t[] = ($ret_c_t_list[$i]) ? $ret_c_t_list[$i] : '0';
    }
    $output->total['ftd'] = ($output->ftd) ? array_sum($output->ftd) : 0;
    $output->total['ret'] = ($output->ret) ? array_sum($output->ret) : 0;
    $output->total['ftd_c'] = ($output->ftd_c) ? array_sum($output->ftd_c) : 0;
    $output->total['ret_c_t'] = ($output->ret_c_t) ? array_sum($output->ret_c_t) : 0;
    echo json_encode($output);
}

function reportYear() {
    $output = new stdClass();
    $output->e = false;
    global $db;
    $src = ($_POST['s']) ?? false;
    $year = (count((array) $_POST['y']) ?? false) ? ('("'.implode('","',$_POST['y']).'")') : '(YEAR(CURRENT_DATE()))';
    $results=array();
    if ($src) {
        foreach ($src as $this_src) {
            $where  ="YEAR(`date`) IN $year AND src ='$this_src' ";
            $results[] = $db->select('marketing_report',$where,'*, MONTH(`date`) as month');
        }
    } else {
        $where  ="YEAR(`date`) = $year ";
        $results[] = $db->select('marketing_report',$where,'*, MONTH(`date`) as month');
    }
    $ftd_list=$ret_list=$ret_c_list=$ftd_c_list=$ret_c_t_list=array();
    if ($results) foreach($results as $result) {
        if ($result) foreach($result as $row) {
            $ftd_list[$row['month']] += $row['ftd'];
            $ret_list[$row['month']] += $row['ret'];
            $ret_c_t_list[$row['month']] += $row['ret_c_t'];
            $ftd_c_list[$row['month']] += $row['ftd_c'];
        }
    }
    for($i=1;$i<13;$i++) {
        $output->ftd[] = ($ftd_list[$i]) ? $ftd_list[$i] : '0';
        $output->ret[] = ($ret_list[$i]) ? $ret_list[$i] : '0';
        $output->ftd_c[] = ($ftd_c_list[$i]) ? $ftd_c_list[$i] : '0';
        $output->ret_c_t[] = ($ret_c_t_list[$i]) ? $ret_c_t_list[$i] : '0';
    }
    $output->total['ftd'] = ($output->ftd) ? array_sum($output->ftd) : 0;
    $output->total['ret'] = ($output->ret) ? array_sum($output->ret) : 0;
    $output->total['ftd_c'] = ($output->ftd_c) ? array_sum($output->ftd_c) : 0;
    $output->total['ret_c_t'] = ($output->ret_c_t) ? array_sum($output->ret_c_t) : 0;
    echo json_encode($output);
}

function leadsDeposit() {
    $output = new stdClass();
    $output->e = false;

    // check if post

    $start = $_POST['startTime'].' 00:00:00';
    $end   = $_POST['endTime'].' 23:59:59';

    $units        = ($_POST['inc_units'] == 'true') ? $_POST['units'] : ['**'];
    $source       = ($_POST['inc_source'] == 'true') ? $_POST['sources'] : ['**'];
    $campaigns    = ($_POST['inc_camp'] == 'true') ? $_POST['campaigns'] : ['**'];
    $ex_campaigns = ($_POST['inc_excamp'] == 'true') ? explode(',',$_POST['ex_campaigns'] ?? '') : ['**'];

    if(($_POST['inc_source']==$_POST['inc_camp']) && ($_POST['inc_camp']==$_POST['inc_excamp']) && ($_POST['inc_excamp']==$_POST['inc_units']) && $_POST['inc_source']=='false')
        $units = $source = $campaigns = $ex_campaigns = [];
    global $db;
    $res=$mt_list=$ftd=$ret=$ret_c=$sum=$helper=array();
    $sql = 'SELECT mt5_deals.login, mt5_deals.profit, mt5_deals.time FROM lidyapar_mt5.mt5_deals WHERE mt5_deals.Comment != "Zeroing" AND mt5_deals.Action = "2" AND mt5_deals.Profit >0 AND mt5_deals.Time BETWEEN "'.$start.'" AND "'.$end.'"';
    
    $result = $db->run($sql);
    if(is_object($result)) while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) $mt_list[] = $row;
    $userManager = new userManager();
    if($mt_list ?? false) foreach ($mt_list as $k => $mt) {
        $where = 'login='.$mt['login'];
        $tp = $db->selectRow('tp',$where);
        if ($tp ?? false) {
            if($tp['group_id']==2) {
                $email = $userManager->getCustom($tp['user_id'],'email')['email'];
                $lead_unit = $userManager->getCustom($tp['user_id'],'unit')['unit'];
                $lead_src = $userManager->getCustom($tp['user_id'],'lead_src')['marketing']['lead_src'];
                $lead_camp =  $userManager->getCustom($tp['user_id'],'lead_camp')['marketing']['lead_camp'];
                $lead_ex_camp =  $userManager->getCustom($tp['user_id'],'campaign_extra')['marketing']['campaign_extra'];

                $units_eq = ($units[0]=='**') ? true : in_array($lead_unit, $units);
                $source_eq = ($source[0]=='**') ? true : in_array(strtolower($lead_src), $source);
                $campaigns_eq = ($campaigns[0]=='**') ? true : in_array(strtolower($lead_camp), $campaigns);

                $ex_campaigns_in = false;
                if($ex_campaigns) foreach($ex_campaigns as $ex_campaign_item) {
                    $ex_campaigns_in += (strpos($lead_ex_camp, $ex_campaign_item)!== false) ? 1 : 0;
                    $output->test[$email][$ex_campaign_item] = $ex_campaigns_in;
                }
                $ex_campaigns_eq = ($ex_campaigns[0]=='**') ? true : $ex_campaigns_in;

                if ($units_eq && $source_eq && $campaigns_eq && $ex_campaigns_eq) {
                    if($tp['ftd'] == $mt['time']) {
                        $ftd[$tp['login']] += $mt['profit'];
                        $sum['ftd'] += $mt['profit'];
                        $sum['ftd_c']++;
                    } else {
                        $ret[$tp['login']] += $mt['profit'];
                        $ret_c[$tp['login']]++;
                        $sum['ret_c_t']++;
                        $sum['ret_c'] = count($ret_c);
                        $sum['ret'] += $mt['profit'];
                    }
                    $res[$tp['login']] = array(
                        $tp['login'],
                        $email,
                        $lead_unit,
                        $lead_src,
                        $lead_camp,
                        $lead_ex_camp,
                        $tp['ftd'],
                        $ftd[$tp['login']] ?? 0,
                        $ret[$tp['login']]?? 0,
                        $ret_c[$tp['login']]?? 0
                    );
                }


            }
        }
    }
    foreach($res as $row) $output->list[] = $row;
    $output->sum = $sum;
    echo json_encode($output);
}

function comparisionMixed() {
    $output = new stdClass();
    $output->e = false;
    global $db;
    $itemvs = ($_POST['itemvs']) ?? false;
    // Left side
    $L_src = ($_POST['source_leads']) ?? false;
    $L_start = ($_POST['startTime']) ?? false;
    $L_end = ($_POST['endTime']) ?? false;
    $L_start_d = new DateTime($L_start);
    $L_end_d = new DateTime($L_end);
    $L_results=$L=array();
    if ($L_src) {
        foreach ($L_src as $this_src) {
            $where  ="date BETWEEN '$L_start 00:00:00' AND '$L_end 23:59:59' AND src ='$this_src' ";
            $L_results[] = $db->select('marketing_report',$where,'*,DATE(date) as day');
        }
    } else {
        $where  ="date BETWEEN '$L_start 00:00:00' AND '$L_end 23:59:59'";
        $L_results[] = $db->select('marketing_report',$where,'*,DATE(date) as day');
    }
    if ($L_results) foreach($L_results as $result) if ($result) foreach($result as $day) $L[$day['day']] += $day[$itemvs];
    $output->total['L'] = ($L) ? array_sum($L) : 0;

    // Right side
    $R_src = ($_POST['source_leadsR']) ?? false;
    $R_start = ($_POST['startTimeR']) ?? false;
    $R_end = ($_POST['endTimeR']) ?? false;
    $R_start_d = new DateTime($R_start);
    $R_end_d = new DateTime($R_end);
    $R_results=$R=array();
    if ($R_src) {
        foreach ($R_src as $this_src) {
            $where  ="date BETWEEN '$R_start 00:00:00' AND '$R_end 23:59:59' AND src ='$this_src' ";
            $R_results[] = $db->select('marketing_report',$where,'*,DATE(date) as day');
        }
    } else {
        $where  ="date BETWEEN '$R_start 00:00:00' AND '$R_end 23:59:59'";
        $R_results[] = $db->select('marketing_report',$where,'*,DATE(date) as day');
    }
    if ($R_results) foreach($R_results as $result) if ($result) foreach($result as $day) $R[$day['day']] += $day[$itemvs];
    $output->total['R'] = ($R) ? array_sum($R) : 0;

    // Date Range
    $start = ($L_start_d>$R_start_d) ? $R_start : $L_start;
    $end  = ($L_end_d>$R_end_d) ? $L_end : $R_end;
    while (strtotime($start) <= strtotime($end)) {
        $output->range[] = $start;
        $start = date ("Y-m-d", strtotime("+1 day", strtotime($start)));
    }
    foreach($output->range as $day) {
        $output->L[] = ($L[$day]) ?? 0;
        $output->R[] = ($R[$day]) ?? 0;
    }
    echo json_encode($output);
}

function comparisionFixed() {
    $output = new stdClass();
    $output->e = false;
    global $db;
    $range = ($_POST['range']) ?? false;

    // Left side
    $L_src = ($_POST['source_leads']) ?? false;
    $L_start = ($_POST['startTime']) ?? false;
    $L_end = date('Y-m-d', strtotime($L_start . " + $range day"));
    $L_results=$L=array();
    if ($L_src) {
        foreach ($L_src as $this_src) {
            $where  ="date BETWEEN '$L_start 00:00:00' AND '$L_end 23:59:59' AND src ='$this_src' ";
            $L_results[] = $db->select('marketing_report',$where,'*,DATE(date) as day');
        }
    } else {
        $where  ="date BETWEEN '$L_start 00:00:00' AND '$L_end 23:59:59'";
        $L_results[] = $db->select('marketing_report',$where,'*,DATE(date) as day');
    }
    if ($L_results) foreach($L_results as $result) if ($result) foreach($result as $day) {
        $L['ftd'][$day['day']] += $day['ftd'];
        $L['ret'][$day['day']] += $day['ret'];
        $L['ftd_c'][$day['day']] += $day['ftd_c'];
        $L['ret_c_t'][$day['day']] += $day['ret_c_t'];
    }
    $output->total['L']['ftd'] = ($L['ftd']) ? array_sum($L['ftd']) : 0;
    $output->total['L']['ret'] = ($L['ret']) ? array_sum($L['ret']) : 0;
    $output->total['L']['ftd_c'] = ($L['ftd_c']) ? array_sum($L['ftd_c']) : 0;
    $output->total['L']['ret_c_t'] = ($L['ret_c_t']) ? array_sum($L['ret_c_t']) : 0;


    // Right side
    $R_src = ($_POST['source_leadsR']) ?? false;
    $R_start = ($_POST['startTimeR']) ?? false;
    $R_end = date('Y-m-d', strtotime($R_start . " + $range day"));
    $R_results=$R=array();
    if ($R_src) {
        foreach ($R_src as $this_src) {
            $where  ="date BETWEEN '$R_start 00:00:00' AND '$R_end 23:59:59' AND src ='$this_src' ";
            $R_results[] = $db->select('marketing_report',$where,'*,DATE(date) as day');
        }
    } else {
        $where  ="date BETWEEN '$R_start 00:00:00' AND '$R_end 23:59:59'";
        $R_results[] = $db->select('marketing_report',$where,'*,DATE(date) as day');
    }
    if ($R_results) foreach($R_results as $result) if ($result) foreach($result as $day) {
        $R['ftd'][$day['day']] += $day['ftd'];
        $R['ret'][$day['day']] += $day['ret'];
        $R['ftd_c'][$day['day']] += $day['ftd_c'];
        $R['ret_c_t'][$day['day']] += $day['ret_c_t'];
    }
    $output->total['R']['ftd'] = ($R['ftd']) ? array_sum($R['ftd']) : 0;
    $output->total['R']['ret'] = ($R['ret']) ? array_sum($R['ret']) : 0;
    $output->total['R']['ftd_c'] = ($R['ftd_c']) ? array_sum($R['ftd_c']) : 0;
    $output->total['R']['ret_c_t'] = ($R['ret_c_t']) ? array_sum($R['ret_c_t']) : 0;


    $output->endr = $R_end;
    $output->endl = $L_end;

    // Date Range
    while (strtotime($L_start) <= strtotime($L_end)) {
        $output->range[] = $L_start;
        $L_start = date ("Y-m-d", strtotime("+1 day", strtotime($L_start)));
    }
    foreach($output->range as $day) {
        $output->L['ftd'][] = ($L['ftd'][$day]) ?? 0;
        $output->L['ret'][] = ($L['ret'][$day]) ?? 0;
        $output->L['ftd_c'][] = ($L['ftd_c'][$day]) ?? 0;
        $output->L['ret_c_t'][] = ($L['ret_c_t'][$day]) ?? 0;
    }

    $output->range=array();
    while (strtotime($R_start) <= strtotime($R_end)) {
        $output->range[] = $R_start;
        $R_start = date ("Y-m-d", strtotime("+1 day", strtotime($R_start)));
    }
    foreach($output->range as $day) {
        $output->R['ftd'][] = ($R['ftd'][$day]) ?? 0;
        $output->R['ret'][] = ($R['ret'][$day]) ?? 0;
        $output->R['ftd_c'][] = ($R['ftd_c'][$day]) ?? 0;
        $output->R['ret_c_t'][] = ($R['ret_c_t'][$day]) ?? 0;
    }

    $output->range=array();
    for ($i=1;$i<=$range;$i++) $output->range[] = $i;
    echo json_encode($output);
}
