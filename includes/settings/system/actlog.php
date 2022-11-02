<?php

    $columns = "count(*) as recodes, act_type";
    $types = $db->select('act_log_user', 0, $columns,0,0,'`act_type`');

    $query['db']            = 'DB_admin';
    $query['table']         = 'act_log_user as acts';
    $query['table_html']    = 'actlog_user';
    $query['key']           = 'id';
    $query['columns']       = array(
                                array(
                                    'db' => 'id',
                                    'th' => '#',
                                    'dt' => 0
                                ),
                                array(
                                    'db' => 'act_type',
                                    'th' => 'Action',
                                    'dt' => 1,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => '(SELECT username FROM users WHERE users.id = user_id)',
                                    'th' => 'User',
                                    'dt' => 2,
                                ),
                                array(
                                    'db' => 'rel_path',
                                    'th' => 'Path',
                                    'dt' => 3
                                ),
                                array(
                                    'db' => 'rel_id',
                                    'th' => 'Affected Item',
                                    'dt' => 4
                                ),
                                array(
                                    'db' => 'detail',
                                    'th' => 'Detail',
                                    'dt' => 5,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'status',
                                    'th' => 'Status',
                                    'dt' => 6,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'timestamp',
                                    'th' => 'Time',
                                    'dt' => 7,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'sess_id',
                                    'th' => 'Session ID',
                                    'dt' => 8,
                                    'formatter' => true
                                )
                            );

    $option = "
        'order': [[0, 'desc']]
    ";
    $table_actlog_user = $factory::dataTableSimple(10, $query,$option);
    GF::makeCSS('f','.ctd {max-width: 100px;}');

?>
<section class="<?= $href ?>">

    <h6 class="text-center">Action Logs</h6>

    <div>
        <div class="form-row border-bottom mb-3">
            <div class="form-group col-md-12">
                <?php if($types) foreach($types as $item) { ?>
                    <button class="filterDTX btn btn-sm btn-outline-info small mb-2" data-tableid="DT_actlog_user" data-col="1" data-filter="<?= $item['act_type'] ?>">&nbsp; <?= $item['act_type'] ?> (<?= $item['recodes'] ?>)</button>
                <?php  } ?><br>
                <button class="filterDTX btn btn-sm btn-secondary d-block col-5 mx-auto" data-tableid="DT_actlog_user" data-col="1" data-filter="">&nbsp; Remove Type Filter (Show All)</button>
                <hr>
            <div>
        <div>
        <div class="form-row border-bottom mb-3">

            <div class="form-group col">
                <label class="label">From</label>
                <input id="date_start" class="DT_CustomOperation_date form-control input-sm mb-2"  type="datetime-local">
                <label class="label">To</label>
                <input id="date_end" class="DT_CustomOperation_date form-control input-sm mb-2" type="datetime-local">
                <button class="clear-date btn btn-block btn-light mt-2">Clear</button>
                <div class="d-none">
                    <input id="DT_actlog_user_time" class="DT_actlog_user_CustomOperation" value="" type="text" readonly="">
                </div>
            </div>
            <div class="form-group col">
                <label class="label">User</label>
                <input type="search" class="form-control filterDT mb-2" placeholder="Username" data-tableid="DT_actlog_user" data-col="2" >
                <label>Session</label>
                <input type="search" class="form-control filterDT mb-2" placeholder="Sess ID" data-tableid="DT_actlog_user" data-col="8" >
            </div>
            <div class="form-group col">
                <label>Path</label>
                <input type="search" class="form-control filterDT mb-2" placeholder="Path" data-tableid="DT_actlog_user" data-col="3">
            </div>
            <div class="form-group col">

            </div>
            <div class="form-group col text-right float-right">
                <label for="refreshTime">Reload</label><br>
                <small class="refPageSel">
                    <i class="fas fa-sync" data-toggle="tooltip" data-placement="bottom" title="Table will not reload."></i>
                    <select id="refreshTime" name="refreshTime" class="custom-select custom-select-sm col-md-2 mx-2" data-tableid='DT_actlog_user'>
                        <option value="0" selected>No</option>
                        <option value="3">3 s</option>
                        <option value="15">15 s</option>
                        <option value="60">1 M</option>
                        <option value="180">3 M</option>
                        <option value="300">5 M</option>
                        <option value="600">10 M</option>
                        <option value="900">15 M</option>
                    </select>
                    <span></span>
                </small>
                <i id="do-reload" data-tableid='DT_actlog_user' class="fa fa-spinner alert-success primary rounded-circle" data-toggle="tooltip" data-placement="bottom" title="Force reload."></i>
            </div>

        </div>
    </div>
    <div>
        <?php echo $table_actlog_user; ?>
    </div>

</section>
