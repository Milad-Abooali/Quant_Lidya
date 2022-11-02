<?php

    global $db;
    $date_start = $_POST[2]['start'];
    $date_end = $_POST[2]['end'];

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
        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#all-all" role="tab" aria-controls="all-all" aria-selected="true">All</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#all-mt4" role="tab" aria-controls="all-mt4" aria-selected="false">MT4</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#all-mt5" role="tab" aria-controls="all-mt5" aria-selected="false">MT5</a>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <!------------------------------------------------------------ALL-------------------------------------------------------------------------->
    <div class="tab-pane fade show active" id="all-all" role="tabpanel" aria-labelledby="all-all-tab">

        <div class="row pl-3 pr-3">
            <div class="col-md-3 col-sm-12 rounded <?php if($results['all']['ftd_amount'] > 0 && $results['all']['ftd_amount'] < 20000){ echo 'bg-gradient-danger text-white'; } else if($results['all']['ftd_amount'] > 20000) { echo 'bg-gradient-primary text-white'; } else { echo 'bg-gradient-danger text-white'; } ?>">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>FTD</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['ftd_amount'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['ftd_count'], 0, '.', ',')  ?> Clients</div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4"><strong>Retention</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['ret_amount'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['deposit_count']-$results['all']['ftd_count'], 0, '.', ',') ?> Times</div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Net Deposit</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['ftd_amount']+$results['all']['ret_amount'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['deposit_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>Withdraw</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['withdrawal'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['withdrawal_count'], 0, '.', ',') ?> Times</div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4"><strong>Bonus In</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['bonus_in'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['bonus_in_count'], 0, '.', ',') ?> Times</div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Bonus Out</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['bonus_out'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['bonus_out_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                 <div class="row pt-3">
                    <div class="col-md-4"><strong>Net D/W</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['ftd_amount']+$results['all']['ret_amount']+$results['all']['withdrawal'], 2, '.', ',') ?></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4"><strong>Net Bonus</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['bonus_in']+$results['all']['bonus_out'], 2, '.', ',') ?></div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Zeroing</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['all']['zeroing'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['all']['zeroing_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 rounded <?php if(($results['all']['profit']+$results['all']['swap']+$results['all']['bonus_in']+$results['all']['bonus_out'])+$results['all']['zeroing'] < 0){ echo 'bg-gradient-info text-white'; } else { echo 'bg-gradient-danger text-white'; } ?>">
                <div class="row pt-3">
                    <div class="col-md-12 text-center"><h2 class="text-white"><?= '$'.number_format(($results['all']['profit']+$results['all']['swap']+$results['all']['bonus_in']+$results['all']['bonus_out']+$results['all']['correction'])+$results['all']['zeroing'], 2, '.', ',') ?></h2></div>
                </div>
                <hr class="mt-1 mb-3">
                <div class="row pb-3">
                    <div class="col-md-4 text-center font-weight-bold">Profit</div>
                    <div class="col-md-4 text-center font-weight-bold">Swap</div>
                    <div class="col-md-4 text-center font-weight-bold">Correction</div>
                    <div class="col-md-4 text-center"><?= '$'.number_format($results['all']['profit'], 2, '.', ',') ?></div>
                    <div class="col-md-4 text-center"><?= '$'.number_format($results['all']['swap'], 2, '.', ',') ?></div>
                    <div class="col-md-4 text-center"><?= '$'.number_format($results['all']['correction'], 2, '.', ',') ?></div>
                </div>
                <div class="text-center mb-2">
                    <small>NET P&L is equal to (Profit + Swap + Correction + Net Bonus + Zeroing)</small>
                </div>
            </div>
        </div>

    </div>
    <!------------------------------------------------------------MT4-------------------------------------------------------------------------->
    <div class="tab-pane fade" id="all-mt4" role="tabpanel" aria-labelledby="all-mt4-tab">

        <div class="row pl-3 pr-3">
            <div class="col-md-3 col-sm-12 rounded <?php if($results['mt4']['ftd_amount'] > 0 && $results['mt4']['ftd_amount'] < 20000){ echo 'bg-gradient-danger text-white'; } else if($results['mt4']['ftd_amount'] > 20000) { echo 'bg-gradient-primary text-white'; } else { echo 'bg-gradient-danger text-white'; } ?>">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>FTD</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt4']['ftd_amount'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt4']['ftd_count'], 0, '.', ',')  ?> Clients</div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Retention</strong></strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt4']['ret_amount'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt4']['deposit_count']-$results['mt4']['ftd_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>Bonus In</strong></strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt4']['bonus_in'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt4']['bonus_in_count'], 0, '.', ',') ?> Times</div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Bonus Out</strong></strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt4']['bonus_out'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt4']['bonus_out_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>Bonus Net</strong></strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt4']['bonus_in']+$results['mt4']['bonus_out'], 2, '.', ',') ?></div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Zeroing</strong></strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt4']['zeroing'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt4']['zeroing_count'], 0, '.', ',') ?> Times</div>
                    </div>
            </div>
            <div class="col-md-3 col-sm-12 rounded <?php if($results['mt4']['profit']+$results['mt4']['swap'] < 0){ echo 'bg-gradient-info text-white'; } else { echo 'bg-gradient-danger text-white'; } ?>">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>Profit / Swap</strong></strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt4']['profit'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt4']['swap'], 2, '.', ',') ?> </div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>P&L</strong></strong></div>
                    <div class="col-md-4"><?= '$'.number_format(($results['mt4']['profit']+$results['mt4']['swap']+$results['mt4']['bonus_in']+$results['mt4']['bonus_out'])+$results['mt4']['zeroing'], 2, '.', ',') ?></div>
                </div>
            </div>
        </div>

    </div>
    <!------------------------------------------------------------MT5-------------------------------------------------------------------------->
    <div class="tab-pane fade" id="all-mt5" role="tabpanel" aria-labelledby="all-mt5-tab">

        <div class="row pl-3 pr-3">
            <div class="col-md-3 col-sm-12 rounded <?php if($results['mt5']['ftd_amount'] > 0 && $results['mt5']['ftd_amount'] < 20000){ echo 'bg-gradient-danger text-white'; } else if($results['mt5']['ftd_amount'] > 20000) { echo 'bg-gradient-primary text-white'; } else { echo 'bg-gradient-danger text-white'; } ?>">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>FTD</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt5']['ftd_amount'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt5']['ftd_count'], 0, '.', ',')  ?> Clients</div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Retention</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt5']['ret_amount'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt5']['deposit_count']-$results['mt5']['ftd_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>Bonus In</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt5']['bonus_in'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt5']['bonus_in_count'], 0, '.', ',') ?> Times</div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Bonus Out</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt5']['bonus_out'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt5']['bonus_out_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>Bonus Net</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt5']['bonus_in']+$results['mt5']['bonus_out'], 2, '.', ',') ?></div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>Zeroing</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt5']['zeroing'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= number_format($results['mt5']['zeroing_count'], 0, '.', ',') ?> Times</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 rounded <?php if($results['mt5']['profit']+$results['mt5']['swap'] < 0){ echo 'bg-gradient-info text-white'; } else { echo 'bg-gradient-danger text-white'; } ?>">
                <div class="row pt-3">
                    <div class="col-md-4"><strong>Profit / Swap</strong></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt5']['profit'], 2, '.', ',') ?></div>
                    <div class="col-md-4"><?= '$'.number_format($results['mt5']['swap'], 2, '.', ',') ?> </div>
                </div>
                <hr>
                <div class="row pb-3">
                    <div class="col-md-4"><strong>P&L</strong></div>
                    <div class="col-md-4"><?= '$'.number_format(($results['mt5']['profit']+$results['mt5']['swap']+$results['mt5']['bonus_in']+$results['mt5']['bonus_out'])+$results['mt5']['zeroing'], 2, '.', ',') ?></div>
                </div>
            </div>
        </div>

    </div>
</div>