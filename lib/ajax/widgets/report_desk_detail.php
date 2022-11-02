<?php

    global $db;

    $date_start = $_POST[2]['start'];
    $date_end   = $_POST[2]['end'];
    $unit_name  = $_POST[2]['unit_name'];
    $unit_id    = $_POST[2]['unit_id'];
    $meta_v     = $_POST[2]['meta_v'];

    $result = array();
    /**
     * Agent List
     */
    global $db;
    // $where ='unit='.$unit_id;
    $staff_list = $db->select('staff_list');

    /**
     * MT4
     */
    if($meta_v=='mt4' || $meta_v=='all') {
        $sql="
                SELECT 
                    *
                FROM tp_report_mt4 WHERE
                    (`day` BETWEEN '$date_start' AND '$date_end')
                AND unit='$unit_name'
                ";
        $_db_mt4 = new iSQL(DB_admin);
        $_db_mt4->LINK->query($sql, MYSQLI_ASYNC);
    }

    /**
     * MT5
     */
    if($meta_v=='mt5' || $meta_v=='all') {
        $sql="
                SELECT 
                    *
                FROM tp_report_mt5 WHERE
                    (`day` BETWEEN '$date_start' AND '$date_end')
                AND unit='$unit_name'
                ";
        $_db_mt5 = new iSQL(DB_admin);
        $_db_mt5->LINK->query($sql, MYSQLI_ASYNC);
    }

    $staffs=array();
    foreach ($staff_list as $staff){
        $staffs[$staff['id']] = $staff;
    }

    # Result MT4
    if($meta_v=='mt4' || $meta_v=='all') {
        $_db_mt4_res  = $_db_mt4->LINK->reap_async_query();
        if($_db_mt4_res) while ($row = $_db_mt4_res->fetch_assoc()){

            $conversion_id = $row['conversion_id'] ?? 0;
            $retention_id  = $retention_id ?? 0;

            # FTD
            $staffs[$conversion_id]['ftd_amount']       += $row['ftd_amount'];
            $staffs[$conversion_id]['ftd_count']        += ($row['ftd_amount']>0) ? 1 : 0;
            # Ret
            $staffs[$retention_id]['ret_amount']        += $row['ret_amount'];
            $staffs[$retention_id]['ret_count']         += $row['deposit_count'] - ( ($row['ftd_amount']>0) ? 1 : 0 );
            $staffs[$retention_id]['profit']            += $row['profit'];
            $staffs[$retention_id]['swap']              += $row['swap'];
            $staffs[$retention_id]['trades_count']      += $row['trades_count'];
            $staffs[$retention_id]['bonus_in']          += $row['bonus_in'];
            $staffs[$retention_id]['bonus_in_count']    += $row['bonus_in_count'];
            $staffs[$retention_id]['bonus_out']         += $row['bonus_out'];
            $staffs[$retention_id]['bonus_out_count']   += $row['bonus_out_count'];
            $staffs[$retention_id]['withdrawal']        += $row['withdrawal'];
            $staffs[$retention_id]['withdrawal_count']  += $row['withdrawal_count'];
            $staffs[$retention_id]['zeroing']           += $row['zeroing'];
            $staffs[$retention_id]['zeroing_count']     += $row['zeroing_count'];
        }
    }

    # Result MT5
    if($meta_v=='mt5' || $meta_v=='all') {
        $_db_mt5_res  = $_db_mt5->LINK->reap_async_query();
        if($_db_mt5_res) while ($row = $_db_mt5_res->fetch_assoc()){

            $conversion_id = $row['conversion_id'] ?? 0;
            $retention_id  = $row['retention_id'] ?? 0;

            # FTD
            $staffs[$conversion_id]['ftd_amount']      += $row['ftd_amount'];
            $staffs[$conversion_id]['ftd_count']       += ($row['ftd_amount']>0) ? 1 : 0;
            # Ret
            $staffs[$retention_id]['ret_amount']       += $row['ret_amount'];
            $staffs[$retention_id]['ret_count']        += $row['deposit_count'] - ( ($row['ftd_amount']>0) ? 1 : 0 );
            $staffs[$retention_id]['profit']           += $row['profit'];
            $staffs[$retention_id]['swap']             += $row['swap'];
            $staffs[$retention_id]['trades_count']     += $row['trades_count'];
            $staffs[$retention_id]['bonus_in']         += $row['bonus_in'];
            $staffs[$retention_id]['bonus_in_count']   += $row['bonus_in_count'];
            $staffs[$retention_id]['bonus_out']        += $row['bonus_out'];
            $staffs[$retention_id]['bonus_out_count']  += $row['bonus_out_count'];
            $staffs[$retention_id]['withdrawal']       += $row['withdrawal'];
            $staffs[$retention_id]['withdrawal_count'] += $row['withdrawal_count'];
            $staffs[$retention_id]['zeroing']          += $row['zeroing'];
            $staffs[$retention_id]['zeroing_count']    += $row['zeroing_count'];
        }
    }

    /**
     * Date Range
     */
    $date_1 = date_create($date_start);
    $date_2 = date_create($date_end);
    $date_interval = date_diff($date_1, $date_2);
    // '%R%a days'
    $date_range = $date_interval->format('%a');

?>


<div class="row">
    <div class="col-12">
        <span class="badge badge-danger mt-2"><small>Platform: <?= $meta_v ?></small></span>
        <span class="badge badge-info mt-2"><small>Date Range: <?= $date_range ?> Day</small></span>
    </div>
    <div id="table-detail-custom" class="col-12 mt-4 form-inline">
        <div class="form-check">
            <input class="table-custom-col form-check-input" data-columns="ftd-group" type="checkbox" id="col-ftd" checked>
            <label class="form-check-label" for="col-ftd">
                FTD
            </label>
        </div>
        <div class="form-check ml-3">
            <input class="table-custom-col form-check-input" data-columns="ret-group" type="checkbox" id="col-ret" checked>
            <label class="form-check-label" for="col-ret">
                Retention
            </label>
        </div>
        <div class="form-check ml-3">
            <input class="table-custom-col form-check-input" data-columns="trades-group" type="checkbox" id="col-trades" checked>
            <label class="form-check-label" for="col-trades">
                Trades
            </label>
        </div>
        <div class="form-check ml-3">
            <input class="table-custom-col form-check-input" data-columns="bonus-group" type="checkbox" id="col-bonus" checked>
            <label class="form-check-label" for="col-bonus">
                Bonus
            </label>
        </div>
        <div class="form-check ml-3">
            <input class="table-custom-col form-check-input" data-columns="withdrawal-group" type="checkbox" id="col-withdrawal" checked>
            <label class="form-check-label" for="col-withdrawal">
                Withdrawal
            </label>
        </div>
        <div class="form-check ml-3">
            <input class="table-custom-col form-check-input" data-columns="zeroing-group" type="checkbox" id="col-zeroing" checked>
            <label class="form-check-label" for="col-zeroing">
                Zeroing
            </label>
        </div>

        <div class="form-check ml-3">
            <input class="table-custom-col form-check-input" data-columns="amounts-group" type="checkbox" id="col-amounts" checked>
            <label class="form-check-label" for="col-amounts">
                All Amounts
            </label>
        </div>
        <div class="form-check ml-3">
            <input class="table-custom-col form-check-input" data-columns="counts-group" type="checkbox" id="col-counts" checked>
            <label class="form-check-label" for="col-counts">
                All Counts
            </label>
        </div>
    </div>
</div>

<div id="table-detail-wrapper" class="mt-4">
    <table id="table-detail" class="display table table-sm table-hover" style="width:100%">
        <thead>
            <tr>
                <th class="text-center border-left border-right">Agents</th>
                <th class="text-center text-success border-left border-right" colspan="2">FTD</th>
                <th class="text-center text-primary border-left border-right" colspan="2">Retention</th>
                <th class="text-center border-left border-right" colspan="4">Trades</th>
                <th class="text-center border-left border-right" colspan="2">Bonus In</th>
                <th class="text-center border-left border-right" colspan="2">Bonus Out</th>
                <th class="text-center text-danger border-left border-right" colspan="2">Withdrawal</th>
                <th class="text-center border-left border-right" colspan="2">Zeroing</th>
            </tr>
            <tr>
                <th>Email</th>
                <th class="text-success ftd-group amounts-group">$</th>
                <th class="text-success ftd-group counts-group">#</th>
                <th class="text-primary ret-group amounts-group">$</th>
                <th class="text-primary ret-group counts-group">#</th>
                <th class="trades-group amounts-group">Profit</th>
                <th class="trades-group amounts-group">SWAP</th>
                <th class="trades-group counts-group">#</th>
                <th class="trades-group amounts-group">P/L</th>
                <th class="text-warning bonus-group amounts-group">$</th>
                <th class="text-warning bonus-group counts-group">#</th>
                <th class="text-secondary bonus-group amounts-group">$</th>
                <th class="text-secondary bonus-group counts-group">#</th>
                <th class="text-danger withdrawal-group amounts-group">$</th>
                <th class="text-danger withdrawal-group counts-group">#</th>
                <th class="zeroing-group amounts-group">$</th>
                <th class="zeroing-group counts-group">#</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if($staffs) foreach($staffs as $staff) {
            if( ($staff['ftd_amount']??0)==0 && ($staff['ret_amount']??0)==0 && ($staff['withdrawal']??0)==0 && ($staff['bonus_in']??0)==0 && ($staff['bonus_out']??0)==0 ) continue;
        ?>
            <tr>
                <td class="agent-cols" data-toggle="tooltip" data-placement="top" title="<?= $staff['full_name'] ?>">
                    <button data-unit-name="<?= $unit_name ?>" data-staff-name="<?= $staff['full_name'] ?>" data-staff-id="<?= $staff['id'] ?>" data-meta-v="<?= $meta_v ?>" class="doA-load-logins btn btn-sm btn-outline-info"> <i class="fa fa fa-outdent"></i></button>
                    <?php if ($staff['unit']!=$unit_id) { ?>
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    <?php } ?>
                    <?= $staff['email'] ?>
                </td>
                <td class="ftd-cols" style="border-right:1px dashed #77e79c;"><?= ($staff['ftd_amount']>0) ? '$'.number_format($staff['ftd_amount'], 2, '.', ',') : null ?></td>
                <td class="ftd-cols"><?= ($staff['ftd_count']>0) ? number_format($staff['ftd_count'], 0, '.', ',') : null ?></td>
                <td class="ret-cols" style="border-right:1px dashed #d8bde8;"><?= ($staff['ret_amount']>0) ? '$'.number_format($staff['ret_amount'], 2, '.', ',') : null ?></td>
                <td class="ret-cols"><?= ($staff['ret_count']>0) ? number_format($staff['ret_count'], 0, '.', ',') : null ?></td>
                <td><?= ($staff['trades_count']>0) ? '$'.number_format($staff['profit'], 2, '.', ',') : null ?></td>
                <td><?= ($staff['trades_count']>0) ? '$'.number_format($staff['swap'], 2, '.', ',') : null ?></td>
                <td><?= ($staff['trades_count']>0) ? number_format($staff['trades_count'], 0, '.', ',') : null ?></td>
                <td><?= ($staff['trades_count']>0) ? '$'.number_format((($staff['profit'] ?? 0)-($staff['swap'] ?? 0)), 2, '.', ',') : null ?></td>
                <td><?= ($staff['bonus_in']>0) ? '$'.number_format($staff['bonus_in'], 2, '.', ',') : null ?></td>
                <td><?= ($staff['bonus_in_count']>0) ? number_format($staff['bonus_in_count'], 0, '.', ',') : null ?></td>
                <td><?= ($staff['bonus_out']<0) ? '$'.number_format($staff['bonus_out'], 2, '.', ',') : null ?></td>
                <td><?= ($staff['bonus_out_count']>0) ? number_format($staff['bonus_out_count'], 0, '.', ',') : null ?></td>
                <td class="w-cols"style="border-right:1px dashed #f8d7da;"><?= ($staff['withdrawal']??false) ? '$'.number_format($staff['withdrawal'], 2, '.', ',') : null ?></td>
                <td class="w-cols"><?= ($staff['withdrawal_count']>0) ? number_format($staff['withdrawal_count'], 0, '.', ',') : null ?></td>
                <td><?= ($staff['zeroing']??false) ? '$'.number_format($staff['zeroing'], 2, '.', ',') : null ?></td>
                <td><?= ($staff['zeroing_count']>0) ? number_format($staff['zeroing_count'], 0, '.', ',') : null ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>
    var tableDetail = $('#table-detail').DataTable({
        'pageLength': 25,
        'order': [[2, 'desc']]
    });

    $('body').on('change','#table-detail-custom .table-custom-col', function() {
        let columnGroup = $(this).attr('data-columns');
        if($(this).prop("checked") == true) {
            tableDetail.columns( '.'+columnGroup ).visible(true);
        } else if($(this).prop("checked") == false) {
            tableDetail.columns( '.'+columnGroup ).visible(false);
        }
    });

    $('#col-trades, #col-bonus, #col-zeroing').trigger('click');

</script>
<style>
    #table-detail .rotate{
        transform: rotate(-90deg);
        transform-origin: top left;
    }
    #table-detail th {
        text-align: center;
    }
    #table-detail td {
        border-left: 1px dotted #efefef;
        border-right: 1px dotted #efefef;
    }
    #table-detail td:first-child {
        border-left: none;
    }
    #table-detail td:last-child {
        border-right: none;
    }

    #table-detail tr:hover .agent-cols {
        color: blue;
    }
    #table-detail tr:hover .ftd-cols {
        color: darkgreen;
        background: #77e79c;
    }
    #table-detail tr:hover .ret-cols {
        color: darkmagenta;
        background: #d8bde8;
    }
    #table-detail tr:hover .w-cols {
        color: red;
        background: #f8d7da;
    }
</style>
