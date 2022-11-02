<?php

    global $db;

    $date_start = $_POST[2]['start'];
    $date_end   = $_POST[2]['end'];
    $staff_id   = ($_POST[2]['staff_id']>0) ? $_POST[2]['staff_id'] : "''";
    $staff_name = $_POST[2]['staff_name'];
    $unit_name  = $_POST[2]['unit_name'];
    $meta_v     = $_POST[2]['meta_v'] ?? 'all';

    $result = array();

    /**
     * MT4
     */
    if($meta_v=='mt4' || $meta_v=='all') {
        $sql="
                SELECT 
                    *
                FROM tp_report_mt4 WHERE
                    (`day` BETWEEN '$date_start' AND '$date_end')
                AND (retention_id=$staff_id OR conversion_id=$staff_id)
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
                AND (retention_id=$staff_id OR conversion_id=$staff_id)
                AND unit='$unit_name'
                ";
        $_db_mt5 = new iSQL(DB_admin);
        $_db_mt5->LINK->query($sql, MYSQLI_ASYNC);
    }

    # Result MT4
    if($meta_v=='mt4' || $meta_v=='all') {
        $_db_mt4_res  = $_db_mt4->LINK->reap_async_query();
        if($_db_mt4_res) while ($row = $_db_mt4_res->fetch_assoc()){
            $result[] = $row;
        }
    }

    # Result MT5
    if($meta_v=='mt5' || $meta_v=='all') {
        $_db_mt5_res  = $_db_mt5->LINK->reap_async_query();
        if($_db_mt5_res) while ($row = $_db_mt5_res->fetch_assoc()){
            $result[] = $row;
        }
    }


    $logins=array();
    foreach ($result as $login_day){
        $logins[$login_day['login']][] = $login_day;
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
    <div id="table-detail-login-custom" class="col-12 mt-4 form-inline">

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
<div class="row" id="updateTP">
    <div class="col-12 mt-3">
        <form class="form-inline">
            <div class="form-group mb-2">
                <label class="mr-2" id="tpid"></label>
                <input class="form-control" type="hidden" id="inputUserID"/>
             </div>
            <div class="form-group mb-2 mr-2">
                <input class="form-control" type="number" placeholder="How Many Days" id="days" />
            </div>
            <a class="btn btn-primary mb-2 mr-2 do-a-UpdateTP" href="javascript:;">Update</a>
            <a class="btn btn-danger mb-2" id="closeUpdateTP" href="javascript:;"><i class="fas fa-times"></i></a>
        </form>
    </div>
</div>
<div id="table-detail-wrapper" class="mt-4">
    <table id="table-detail-login" class="display table table-sm table-hover" style="width:100%">
        <thead>
            <tr>
                <th class="text-center border-left border-right" colspan="3">Client</th>
                <th class="text-center text-success border-left border-right">FTD</th>
                <th class="text-center text-primary border-left border-right" colspan="2">Retention</th>
                <th class="text-center border-left border-right" colspan="4">Trades</th>
                <th class="text-center border-left border-right" colspan="2">Bonus In</th>
                <th class="text-center border-left border-right" colspan="2">Bonus Out</th>
                <th class="text-center text-danger border-left border-right" colspan="2">Withdrawal</th>
            </tr>
            <tr>
                <th>Date</th>
                <th>TP</th>
                <th>Email</th>
                <th class="text-success ftd-group amounts-group">$</th>
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
            </tr>
        </thead>
        <tbody>
        <?php
        if($result) foreach($result as $login) {
            //if( ($login['ftd_amount']??0)==0 && ($login['ret_amount']??0)==0 && ($login['withdrawal']??0)==0 && ($login['bonus_in']??0)==0 && ($login['bonus_out']??0)==0 ) continue;

            $login['ret_count'] = $login['deposit_count'] - ( ($login['ftd_amount']>0) ? 1 : 0 );
        ?>
            <tr>
                <td><?= $login['day'] ?></td>
                <td class="agent-cols tp-login" data-toggle="tooltip" data-placement="top" title="<?= $login['id'] ?>" data-userid="<?= $login['user_id'] ?>" data-tp="<?= $login['login'] ?>"><?= $login['login'] ?></td>
                <td class="agent-cols"><?= $login['email'] ?></td>
                <td class="ftd-cols" style="border-right:1px dashed #77e79c;"><?= ($login['ftd_amount']>0) ? '$'.number_format($login['ftd_amount'], 2, '.', ',') : null ?></td>
                <td class="ret-cols" style="border-right:1px dashed #d8bde8;"><?= ($login['ret_amount']>0) ? '$'.number_format($login['ret_amount'], 2, '.', ',') : null ?></td>
                <td class="ret-cols"><?= ($login['ret_count']>0) ? number_format($login['ret_count'], 0, '.', ',') : null ?></td>
                <td><?= ($login['trades_count']>0) ? '$'.number_format($login['profit'], 2, '.', ',') : null ?></td>
                <td><?= ($login['trades_count']>0) ? '$'.number_format($login['swap'], 2, '.', ',') : null ?></td>
                <td><?= ($login['trades_count']>0) ? number_format($login['trades_count'], 0, '.', ',') : null ?></td>
                <td><?= ($login['trades_count']>0) ? '$'.number_format((($login['profit'] ?? 0)-($login['swap'] ?? 0)), 2, '.', ',') : null ?></td>
                <td><?= ($login['bonus_in']>0) ? '$'.number_format($login['bonus_in'], 2, '.', ',') : null ?></td>
                <td><?= ($login['bonus_in_count']>0) ? number_format($login['bonus_in_count'], 0, '.', ',') : null ?></td>
                <td><?= ($login['bonus_out']<0) ? '$'.number_format($login['bonus_out'], 2, '.', ',') : null ?></td>
                <td><?= ($login['bonus_out_count']>0) ? number_format($login['bonus_out_count'], 0, '.', ',') : null ?></td>
                <td class="w-cols"style="border-right:1px dashed #f8d7da;"><?= ($login['withdrawal']<0) ? '$'.number_format($login['withdrawal'], 2, '.', ',') : null ?></td>
                <td class="w-cols"><?= ($login['withdrawal_count']>0) ? number_format($login['withdrawal_count'], 0, '.', ',') : null ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script>
    $( "#updateTP" ).hide();
    
    var tableDetailLogin = $('#table-detail-login').DataTable({
        'pageLength': 25,
        'order': [[0, 'desc']]
    });

    $('body').on('change','#table-detail-login-custom .table-custom-col', function() {
        let columnGroup = $(this).attr('data-columns');
        if($(this).prop("checked") == true) {
            tableDetailLogin.columns( '.'+columnGroup ).visible(true);
        } else if($(this).prop("checked") == false) {
            tableDetailLogin.columns( '.'+columnGroup ).visible(false);
        }
    });
    
    $('body').on('click','.tp-login', function() {
        let user_id = $(this).attr('data-userid');
        let userTP = $(this).attr('data-tp');
        $( "#updateTP" ).show( "slow", function() {
            $("#tpid").text("TP : "+userTP);
            $("#inputUserID").val(user_id);
            $("#days").val('0');
        });
    });
    
    $('body').on('click','#closeUpdateTP', function() {
        $( "#updateTP" ).hide("slow");
    });
    
    $('body').on('click','.do-a-UpdateTP', function(e) {
        let user_id = $('#inputUserID').val();
        let days = $('#days').val();
        if(days!=0){
            e.preventDefault(); 
            var url = "https://clientzone1.lidyaportal.com/_tools/tp_report.php?day="+days+"&uid="+user_id; 
            window.open(url, '_blank');
        }
    });

    $('#col-trades, #col-bonus, #col-zeroing').trigger('click');

</script>
<style>
    #table-detail-login .rotate{
        transform: rotate(-90deg);
        transform-origin: top left;
    }
    #table-detail-login th {
        text-align: center;
    }
    #table-detail-login td {
        border-left: 1px dotted #efefef;
        border-right: 1px dotted #efefef;
    }
    #table-detail-login td:first-child {
        border-left: none;
    }
    #table-detail-login td:last-child {
        border-right: none;
    }

    #table-detail-login tr:hover .agent-cols {
        color: blue;
    }
    #table-detail-login tr:hover .ftd-cols {
        color: darkgreen;
        background: #77e79c;
    }
    #table-detail-login tr:hover .ret-cols {
        color: darkmagenta;
        background: #d8bde8;
    }
    #table-detail-login tr:hover .w-cols {
        color: red;
        background: #f8d7da;
    }
</style>
