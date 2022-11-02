<?php

    $query['db']        = 'DB_admin';
    $query['table']     = 'waf_ip';
    $query['table_html']     = 'waf_ip';
    $query['key']       = 'id';
    $query['columns']   = array(
        array(
            'db' => 'id',
            'th' => '#',
            'dt' => 0
        ),
        array(
            'db' => 'ip',
            'th' => 'IP',
            'dt' => 1,
            'formatter' => true
        ),
        array(
            'db' => 'status',
            'th' => 'Status',
            'dt' => 2,
            'formatter' => true
        ),
        array(
            'db' => 'info',
            'th' => 'Info',
            'dt' => 3
        ),array(
            'db' => 'id',
            'th' => 'Manage',
            'dt' => 4,
            'formatter' => true
        )
    );
    $table_waf_ip = $factory::dataTableSimple(25, $query,null,false,'table-sm');

?>

<section class="<?= $href ?>">

    <div class="row">
        <div class="col-md-12 py-3">
            <h6>Add IP to WAF Database</h6>


            <form id="addIP" autocomplete="off" class="form-inline">
                <input class="form-control mr-2" id="ip" minlength="7" maxlength="15" size="15" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$" name="ip" placeholder="ex: 255.255.255.255">
                <input class="form-control mr-2" id="ip_info" name="ip_info" placeholder="IP Info">

                <div class="btn-group" data-toggle="buttons">
                    <label class="btn alert-secondary form-check-label active">
                        <input class="form-check-input" type="radio" name="status" id="status1" value="0" checked> Null
                    </label>
                    <label class="btn alert-success form-check-label">
                        <input class="form-check-input" type="radio" name="status" id="status2" value="1"> Whitelist
                    </label>
                    <label class="btn alert-danger form-check-label">
                        <input class="form-check-input" type="radio" name="status" id="status3" value="2"> Blacklist
                    </label>

                </div>

                <button class="btn btn-success ml-2" type="submit">Add Rule</button>
            </form>
            <hr>
            <?= $table_waf_ip ?>
        </div>

    </div>

</section>