<?php

    $query['db']        = 'DB_admin';
    $query['table']     = 'cronjobs';
    $query['table_html']     = 'jobs';
    $query['key']       = 'id';
    $query['columns']   = array(
        array(
            'db' => 'id',
            'th' => '#',
            'dt' => 0
        ),
        array(
            'db' => 'name',
            'th' => 'Job',
            'dt' => 1
        ),
        array(
            'db' => 'cycle',
            'th' => 'Cycle (m)',
            'dt' => 2
        ),
        array(
            'db' => 'last_log_id',
            'th' => 'Logs',
            'dt' => 3,
            'formatter' => true
        ),array(
            'db' => 'status',
            'th' => 'Status',
            'dt' => 4
        ),array(
            'db' => 'avoid',
            'th' => 'Avoid',
            'dt' => 5,
            'formatter' => true
        ),array(
            'db' => 'force_run',
            'th' => 'Force',
            'dt' => 6,
            'formatter' => true
        ),array(
            'db' => 'last_run',
            'th' => 'Last Run',
            'dt' => 7
        )
    );
    $table_jobs = $factory::dataTableSimple(25, $query,null,false,'table-sm');

?>

<section class="<?= $href ?>">

    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4"></div>
        <div class="col-md-4 text-right float-right">
            <label for="refreshTime">Reload</label><br>
            <small class="refPageSel">
                <i class="fas fa-sync" data-toggle="tooltip" data-placement="bottom" title="Table will not reload."></i>
                <select id="refreshTime" name="refreshTime" class="custom-select custom-select-sm col-md-2 mx-2" data-tableid='DT_jobs'>
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
            <i id="do-reload" data-tableid='DT_jobs' class="fa fa-spinner alert-success primary rounded-circle" data-toggle="tooltip" data-placement="bottom" title="Force reload."></i>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 py-3">
            <h6 class="text-center">Cron Jobs Manager</h6>

            <?= $table_jobs ?>
        </div>

    </div>

</section>