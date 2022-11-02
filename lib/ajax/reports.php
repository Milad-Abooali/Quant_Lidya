<?php

/**
 * Reports Functions
 * 11:13 AM Wednesday, November 18, 2020 | M.Abooali
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

function topTen() {
    $output = new stdClass();
    $output->e = false;

    global $db;
    $unit = ($_POST['u']) ?? false;
    $month = (count((array) $_POST['m']) ?? false) ? ('("'.implode('","',$_POST['m']).'")') : '(MONTH(CURRENT_DATE()))';
    $year = (count((array) $_POST['y']) ?? false) ? ('("'.implode('","',$_POST['y']).'")') : '(YEAR(CURRENT_DATE()))';

    $results=array();

    if ($unit) $unit = ('("'.implode('","',$unit).'")');
    $unit_where = ($unit) ? 'AND unit IN '.$unit : null;

    $sql="
            SELECT 
                (SELECT CONCAT(`unit`, ' | ', email) AS  email FROM users WHERE id=retention_id LIMIT 1) agent,
                Sum(`ret_amount`) amount,
                Sum(`deposit_count`) item
            FROM tp_report_mt5 WHERE
                MONTH(`day`) IN $month
                AND YEAR(`day`) IN $year
                $unit_where
                AND ret_amount>0
            GROUP BY retention_id
            ORDER BY amount DESC
            LIMIT 10
    ";
    $_db_retention = new iSQL(DB_admin);
    $_db_retention->LINK->query($sql, MYSQLI_ASYNC);

    $sql="
            SELECT 
                (SELECT CONCAT(`unit`, ' | ', email) AS  email  FROM users WHERE id=conversion_id LIMIT 1) agent,
                Sum(`ftd_amount`) amount,
                count(DISTINCT login) item
            FROM tp_report_mt5 WHERE
                MONTH(`day`) IN $month
                AND YEAR(`day`) IN $year
                $unit_where
                AND ftd_amount>0
            GROUP BY conversion_id
            ORDER BY amount DESC
            LIMIT 10
    ";
    $_db_conversion = new iSQL(DB_admin);
    $_db_conversion->LINK->query($sql, MYSQLI_ASYNC);

    $_db_retention_res  = $_db_retention->LINK->reap_async_query();
    $_db_conversion_res  = $_db_conversion->LINK->reap_async_query();

    for ($i=0; $i<10; $i++) {
        if($_db_retention_res) $r  = $_db_retention_res->fetch_row();
        if($_db_conversion_res) $c  = $_db_conversion_res->fetch_row();
        if($r){
            $results['r'][$i]['label']  = $r[0];
            $results['r'][$i]['y']  = $r[1];
            $results['r'][$i]['x']  = $r[2];
        }
        if($c) {
            $results['c'][$i]['label']  = $c[0];
            $results['c'][$i]['y']  = $c[1];
            $results['c'][$i]['x']  = $c[2];
        }
    }


    /*
    $where  ="MONTH(`day`) IN $month AND YEAR(`day`) = $year $unit_where AND ret_amount>0";
    $results['retentions'] = $db->select('tp_report',$where,'retention_id, Sum(`ret_amount`) ret_amount, Sum(`deposit_count`) count,count(DISTINCT `login`)',10, 'ret_amount DESC','retention_id');

    $where  ="MONTH(`day`) IN $month AND YEAR(`day`) = $year $unit_where AND ftd_amount>0";
    $results['conversions'] = $db->select('tp_report',$where,'conversion_id, Sum(`ftd_amount`) ftd_amount, count(`id`) count',10, 'ftd_amount DESC','conversion_id');

    $where  ="MONTH(`day`) IN $month AND YEAR(`day`) = $year $unit_where AND zeroing>0";
    $results['zeroing'] = $db->select('tp_report',$where,'retention_id, Sum(`zeroing`) zeroing, Sum(`zeroing_count`) count',10, 'zeroing DESC','retention_id');
    */


    $output->res = $results;
    echo json_encode($output);

}

