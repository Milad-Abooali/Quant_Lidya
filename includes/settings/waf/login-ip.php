<?php

    $Module_Name = 'waf_login_ip';
    $Module_Status = $_waf->getModuleStatus($Module_Name);
    $Module_Settings = $_waf->getModuleSettings($Module_Name);

    $a_code = (date("j")+date("m"))*7;

    $filter_cols = array(
        'id',
        'username',
        'email',
        'unit',
        'type',
        'created_at',
        'groups'
    );

    $filter_cond = array(
        '=',
        '>',
        '<',
        '!=',
        'IN'
    );

    $query['db']        = 'DB_admin';
    $query['table']     = 'waf_ip_login';
    $query['table_html']     = 'waf_ip_login';
    $query['key']       = 'id';
    $query['columns']   = array(
        array(
            'db' => 'id',
            'th' => '#',
            'dt' => 0
        ),
        array(
            'db' => 'filter',
            'th' => 'Filter',
            'dt' => 1
        ),
        array(
            'db' => 'status',
            'th' => 'Status',
            'dt' => 2,
            'formatter' => true

        ),
        array(
            'db' => 'id',
            'th' => 'Delete',
            'dt' => 3,
            'formatter' => true

        )
    );
    $table_waf_ip_login = $factory::dataTableSimple(10, $query,null,false,'table-sm');

    $query['db']        = 'DB_admin';
    $query['table']     = 'waf_ip_login_ex';
    $query['table_html']     = 'waf_ip_login_ex';
    $query['key']       = 'id';
    $query['columns']   = array(
        array(
            'db' => 'id',
            'th' => '#',
            'dt' => 0
        ),
        array(
            'db' => 'filter',
            'th' => 'Filter',
            'dt' => 1
        ),
        array(
            'db' => 'expire_date',
            'th' => 'Expire',
            'dt' => 2
        ),
        array(
            'db' => 'creat_date',
            'th' => 'Created',
            'dt' => 3
        ),
        array(
            'db' => 'id',
            'th' => 'Manage',
            'dt' => 4,
            'formatter' => true
        )
    );
    $table_waf_ip_login_ex = $factory::dataTableSimple(10, $query,null,false,'table-sm');


?>

<section class="<?= $href ?>">

    <div class="row">
        <div class="col-md-6 py-3">
            <h6>Login IP Rules</h6>
            <form id="addFilter" autocomplete="off" class="form-inline mb-3 small">
                WHERE
                <select class="form-control mx-2" id="col" name="col" required>
                    <option value="0" disabled selected>Select Column</option>
                    <?php if($filter_cols) foreach ($filter_cols as $key => $val) { ?>
                        <option value="<?= $val ?>"><?= $val ?></option>
                    <?php } ?>
                </select>
                <select class="form-control mx-2" id="cond" name="cond" required>
                    <option value="0" disabled selected>-</option>
                    <?php if($filter_cond) foreach ($filter_cond as $key => $val) { ?>
                        <option value="<?= $val ?>"><?= $val ?></option>
                    <?php } ?>
                </select>
                <input class="form-control mx-2" id="val" name="val">
                <input class="form-check-input" type="checkbox" id="rstatus" name="status" value="1">
                <label class="form-check-label" for="rstatus">Active</label>
                <button class="btn btn-success ml-2" type="submit">Add Rule</button>
            </form>
            <hr>
            <?= $table_waf_ip_login ?>
        </div>
        <div class="col-md-6 py-3">
            <h6>Setting</h6>
            <div class="row">
                <div class="col-md-8">
                    <small class="mx-auto">Offical Time Range</small>
                    <form id="timeRange" autocomplete="off" class="form-inline pl-2 border-left">
                        <input type="hidden" name="module" value="<?= $Module_Name ?>">
                        <input class="form-control mr-2" type="time" id="start" name="start" value="<?= $Module_Settings->start ?? null ?>">
                        <input class="form-control mr-2" type="time"  id="end" name="end" value="<?= $Module_Settings->end ?? null ?>">
                        <button class="btn btn-success btn-block form-control px-3" type="submit">Set Time</button>
                    </form>
                </div>
                <div class="col-md-4 text-center">
                    <div class="custom-control custom-switch alert alert-warning">
                        <input id="waf_m_status" type="checkbox" class="custom-control-input doActive" <?= ($Module_Status) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="waf_m_status">WAF Login IP</label>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6>
                        <small>Today:</small>
                        <span class="alert alert-info text-primary"><?= date('l - H:i A') ?></span>
                    </h6>

                </div>
                <div class="col-md-6">
                    <h6 class="text-center"> <small>Access Code:</small>
                        <span class="alert alert-success text-primary"><?= $a_code ?></span>
                    </h6>
                </div>
            </div>
            <hr>
            <h6>Add Exception</h6>
            <form id="addException" autocomplete="off" class="pl-2 border-left">
                <select class="form-control my-2" id="col" name="col" required>
                    <option value="0" disabled selected>Select Column</option>
                    <?php if($filter_cols) foreach ($filter_cols as $key => $val) { ?>
                        <option value="<?= $val ?>"><?= $val ?></option>
                    <?php } ?>
                </select>
                <select class="form-control my-2" id="cond" name="cond" required>
                    <option value="0" disabled selected>-</option>
                    <?php if($filter_cond) foreach ($filter_cond as $key => $val) { ?>
                        <option value="<?= $val ?>"><?= $val ?></option>
                    <?php } ?>
                </select>
                <input class="form-control my-2" id="val" name="val">
                <input class="form-control my-2" type="datetime-local" id="expire" name="expire">

                <button class="btn btn-success btn-block px-3" type="submit">Add Rule</button>
            </form>
            <hr>
            <?= $table_waf_ip_login_ex ?>
        </div>
    </div>

</section>
