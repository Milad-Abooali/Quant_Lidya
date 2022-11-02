<?php

    global $db;

    $date_start = $_POST[2]['start'];
    $date_end   = $_POST[2]['end'];
    $unit_name  = $_POST[2]['unit_name'];
    $unit_id    = $_POST[2]['unit_id'];

    $results=array();

    /**
     * MT4
     */
    $sql="
            SELECT 
                Sum(`withdrawal`) withdrawal,
                Sum(`deposit`) deposit,
                Sum(`zeroing`) zeroing,
                Sum(`profit`) profit,
                Sum(`swap`) swap,
                Sum(`trades_count`) trades_count,
                Sum(`bonus_in`) bonus_in,
                Sum(`bonus_in_count`) bonus_in_count,
                Sum(`bonus_out`) bonus_out,
                Sum(`bonus_out_count`) bonus_out_count,
                Sum(`withdrawal`) withdrawal,
                Sum(`withdrawal_count`) withdrawal_count,
                Sum(`deposit`) deposit,
                Sum(`deposit_count`) deposit_count,
                Sum(`zeroing`) zeroing,
                Sum(`zeroing_count`) zeroing_count,
                Sum(`correction`) correction,
                Sum(`correction_count`) correction_count,
                Sum(`ret_amount`) ret_amount,
                Sum(`ftd_amount`) ftd_amount,
                SUM(IF(`ftd_amount`>0, 1, 0)) ftd_count
            FROM tp_report_mt4 WHERE
                (`day` BETWEEN '$date_start' AND '$date_end')
            AND unit='$unit_name'
        ";
    $_db_mt4 = new iSQL(DB_admin);
    $_db_mt4->LINK->query($sql, MYSQLI_ASYNC);

    /**
     * MT5
     */
    $sql="
            SELECT 
                Sum(`withdrawal`) withdrawal,
                Sum(`deposit`) deposit,
                Sum(`zeroing`) zeroing,
                Sum(`profit`) profit,
                Sum(`swap`) swap,
                Sum(`trades_count`) trades_count,
                Sum(`bonus_in`) bonus_in,
                Sum(`bonus_in_count`) bonus_in_count,
                Sum(`bonus_out`) bonus_out,
                Sum(`bonus_out_count`) bonus_out_count,
                Sum(`withdrawal`) withdrawal,
                Sum(`withdrawal_count`) withdrawal_count,
                Sum(`deposit`) deposit,
                Sum(`deposit_count`) deposit_count,
                Sum(`zeroing`) zeroing,
                Sum(`zeroing_count`) zeroing_count,
                Sum(`correction`) correction,
                Sum(`correction_count`) correction_count,
                Sum(`ret_amount`) ret_amount,
                Sum(`ftd_amount`) ftd_amount,
                SUM(IF(`ftd_amount`>0, 1, 0)) ftd_count
            FROM tp_report_mt5 WHERE
                (`day` BETWEEN '$date_start' AND '$date_end')
            AND unit='$unit_name'
            ";
    $_db_mt5 = new iSQL(DB_admin);
    $_db_mt5->LINK->query($sql, MYSQLI_ASYNC);

    /**
     * Query Results
     */
    # MT4
    $_db_mt4_res  = $_db_mt4->LINK->reap_async_query();
    if($_db_mt4_res) while ($row = $_db_mt4_res->fetch_assoc()) $results['mt4'] = $row;
    # MT5
    $_db_mt5_res  = $_db_mt5->LINK->reap_async_query();
    if($_db_mt5_res) while ($row = $_db_mt5_res->fetch_assoc()) $results['mt5'] = $row;
    # All
    if($results['mt4'] && $results['mt5']) {
        foreach ($results['mt4'] as $k=>$v) $results['all'][$k] = $v + $results['mt5'][$k];
    } else if(!$results['mt5']) {
        $results['all'] = $results['mt4'];
    } else {
        $results['all'] = $results['mt5'];
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
<hr>
<span class="badge badge-info mt-2 float-right"><small>Date Range: <?= $date_range ?> Day</small></span>
<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#<?= $unit_name ?>-all" role="tab" aria-controls="<?= $unit_name ?>-all" aria-selected="true">All</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#<?= $unit_name ?>-mt4" role="tab" aria-controls="<?= $unit_name ?>-mt4" aria-selected="false">MT4</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#<?= $unit_name ?>-mt5" role="tab" aria-controls="<?= $unit_name ?>-mt5" aria-selected="false">MT5</a>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <!------------------------------------------------------------ALL-------------------------------------------------------------------------->
    <div class="tab-pane fade show active cube" id="<?= $unit_name ?>-all" role="tabpanel" aria-labelledby="<?= $unit_name ?>-all-tab">
        <div class="custom-control custom-switch card-switch">
            <input type="checkbox" class="custom-control-input card-switch-input" id="customSwitch<?= $unit_name ?>">
            <label class="custom-control-label" for="customSwitch<?= $unit_name ?>"></label>
        </div>
        <div class="row pl-3 pr-3 default-state">
            <div class="col-md-12 col-xs-12 border-bottom">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>FTD</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['ftd_amount'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['ftd_count'], 0, '.', ',')  ?> Clients</div>
                </div>
                <hr class="row">
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Retention</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['ret_amount'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['deposit_count']-$results['all']['ftd_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>P/S</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['profit'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['swap'], 2, '.', ',') ?> </div>
                </div>
                <hr class="row">
                <div class="row">
                    <div class="col-md-4"><strong>Correction</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['correction'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= $results['all']['correction_count']; ?> Times</div>
                </div>
                <hr class="row">
                <div class="row pb-3">
                    <div class="col-md-4"><strong>P&L</strong></div>
                    <div class="col-md-8"><?= '$'.number_format(($results['all']['profit']+$results['all']['swap']+$results['all']['bonus_in']+$results['all']['bonus_out']+$results['all']['correction'])+$results['all']['zeroing'], 2, '.', ',') ?></div>
                </div>
            </div>
        </div>
        <div class="row pl-3 pr-3 active-state">
            <div class="col-md-12 col-xs-12 border-bottom">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>Bonus In</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['bonus_in'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['bonus_in_count'], 0, '.', ',') ?> Times</div>
                </div>
                <hr class="row">
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Bonus Out</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['bonus_out'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['bonus_out_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>Bonus Net</strong></div>
                    <div class="col-md-8"><?= '$'.number_format($results['all']['bonus_in']+$results['all']['bonus_out'], 2, '.', ',') ?></div>
                </div>
                <hr class="row">
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Zeroing</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['zeroing'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['zeroing_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
        </div>
        <button data-meta-v="all" data-unit-id="<?= $unit_id ?>" data-unit-name="<?= $unit_name ?>" class="doA-load-detail btn btn-sm btn-outline-info btn-block" type="button"><i class="fa fa-outdent"></i> Detail</button>
    </div>
    <!------------------------------------------------------------MT4-------------------------------------------------------------------------->
    <div class="tab-pane fade" id="<?= $unit_name ?>-mt4" role="tabpanel" aria-labelledby="<?= $unit_name ?>-mt4-tab">

        <button data-meta-v="mt4" data-unit-id="<?= $unit_id ?>" data-unit-name="<?= $unit_name ?>" class="doA-load-detail btn btn-sm btn-outline-info" type="button"><i class="fa fa-outdent"></i> Detail</button>

        <div class="row">
            <div class="col-4">FTD</div>
            <div class="col-4"><?= '$'.number_format($results['mt4']['ftd_amount'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt4']['ftd_count'], 0, '.', ',')  ?> Clients</div>
        </div>
        <div class="row">
            <div class="col-4">Retention</div>
            <div class="col-4"><?= '$'.number_format($results['mt4']['ret_amount'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt4']['deposit_count']-$results['mt4']['ftd_count'], 0, '.', ',') ?> Times</div>
        </div>
        <div class="row">
            <div class="col-4">Bonus In</div>
            <div class="col-4"><?= '$'.number_format($results['mt4']['bonus_in'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt4']['bonus_in_count'], 0, '.', ',') ?> Times</div>
        </div>
        <div class="row">
            <div class="col-4">Bonus Out</div>
            <div class="col-4"><?= '$'.number_format($results['mt4']['bonus_out'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt4']['bonus_out_count'], 0, '.', ',') ?> Times</div>
        </div>
        <div class="row">
            <div class="col-6">Bonus Net</div>
            <div class="col-6"><?= '$'.number_format($results['mt4']['bonus_in']+$results['mt4']['bonus_out'], 2, '.', ',') ?></div>
        </div>
        <div class="row">
            <div class="col-4">Zeroing</div>
            <div class="col-4"><?= '$'.number_format($results['mt4']['zeroing'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt4']['zeroing_count'], 0, '.', ',') ?> Times</div>
        </div>
        <div class="row">
            <div class="col-4">Profit / Swap</div>
            <div class="col-4"><?= '$'.number_format($results['mt4']['profit'], 2, '.', ',') ?></div>
            <div class="col-4"><?= '$'.number_format($results['mt4']['swap'], 2, '.', ',') ?> </div>
        </div>
        <div class="row">
            <div class="col-6">P/L</div>
            <div class="col-6"><?= '$'.number_format(($results['mt4']['profit']+$results['mt4']['swap']+$results['mt4']['bonus_in']+$results['mt4']['bonus_out'])+$results['mt4']['zeroing'], 2, '.', ',') ?></div>
        </div>

    </div>
    <!------------------------------------------------------------MT5-------------------------------------------------------------------------->
    <div class="tab-pane fade" id="<?= $unit_name ?>-mt5" role="tabpanel" aria-labelledby="<?= $unit_name ?>-mt5-tab">

        <button data-meta-v="mt5" data-unit-id="<?= $unit_id ?>" data-unit-name="<?= $unit_name ?>" class="doA-load-detail btn btn-sm btn-outline-info" type="button"><i class="fa fa-outdent"></i> Detail</button>

        <div class="row">
            <div class="col-4">FTD</div>
            <div class="col-4"><?= '$'.number_format($results['mt5']['ftd_amount'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt5']['ftd_count'], 0, '.', ',')  ?> Clients</div>
        </div>
        <div class="row">
            <div class="col-4">Retention</div>
            <div class="col-4"><?= '$'.number_format($results['mt5']['ret_amount'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt5']['deposit_count']-$results['mt5']['ftd_count'], 0, '.', ',') ?> Times</div>
        </div>
        <div class="row">
            <div class="col-4">Bonus In</div>
            <div class="col-4"><?= '$'.number_format($results['mt5']['bonus_in'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt5']['bonus_in_count'], 0, '.', ',') ?> Times</div>
        </div>
        <div class="row">
            <div class="col-4">Bonus Out</div>
            <div class="col-4"><?= '$'.number_format($results['mt5']['bonus_out'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt5']['bonus_out_count'], 0, '.', ',') ?> Times</div>
        </div>
        <div class="row">
            <div class="col-6">Bonus Net</div>
            <div class="col-6"><?= '$'.number_format($results['mt5']['bonus_in']+$results['mt5']['bonus_out'], 2, '.', ',') ?></div>
        </div>
        <div class="row">
            <div class="col-4">Zeroing</div>
            <div class="col-4"><?= '$'.number_format($results['mt5']['zeroing'], 2, '.', ',') ?></div>
            <div class="col-4"><?= number_format($results['mt5']['zeroing_count'], 0, '.', ',') ?> Times</div>
        </div>
        <div class="row">
            <div class="col-4">Profit / Swap</div>
            <div class="col-4"><?= '$'.number_format($results['mt5']['profit'], 2, '.', ',') ?></div>
            <div class="col-4"><?= '$'.number_format($results['mt5']['swap'], 2, '.', ',') ?> </div>
        </div>
        <div class="row">
            <div class="col-6">P/L</div>
            <div class="col-6"><?= '$'.number_format(($results['mt5']['profit']+$results['mt5']['swap']+$results['mt5']['bonus_in']+$results['mt5']['bonus_out'])+$results['mt5']['zeroing'], 2, '.', ',') ?>mt5</div>
        </div>

    </div>
</div>