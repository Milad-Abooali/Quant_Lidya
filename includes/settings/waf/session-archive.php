<?php

    $query['db']        = 'DB_admin';
    $query['table']     = 'user_session as u_sess';
    $query['table_html']     = 'waf_session_archive';
    $query['key']       = 'id';
    $query['columns']   = array(
                                array(
                                    'db' => 'id',
                                    'th' => '#',
                                    'dt' => 0
                                ),
                                array(
                                    'db' => 'user_id',
                                    'th' => 'User',
                                    'dt' => 1,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'session',
                                    'th' => 'Sess',
                                    'dt' => 2,
                                    'formatter' => true

                                ),
                                array(
                                    'db' => 'ip',
                                    'th' => 'IP',
                                    'dt' => 3,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'agent',
                                    'th' => 'Agent',
                                    'dt' => 4,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'status',
                                    'th' => 'Status',
                                    'dt' => 5,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'time',
                                    'th' => 'Start (Login)',
                                    'dt' => 6,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'id',
                                    'th' => 'Last Activity',
                                    'dt' => 7,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'id',
                                    'th' => 'Visited Pages',
                                    'dt' => 8,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'id',
                                    'th' => 'Actions',
                                    'dt' => 9,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'id',
                                    'dt' => 10,
                                    'field' => 'id',
                                    'th' => '<input type="checkbox" name="select_all" value="1" id="select-all">'
                                )
                            );
$option = "
       'columnDefs': [
            {
                'targets': 10,
                'searchable':false,
                'orderable':false,
                'className': 'dt-body-center',
                'render': function (data, type, full, meta){
                return '<input type=\"checkbox\" name=\"id[]\" value=\"'+ data + '\">';
                }
            },
         { 'width': '250px', 'targets': 4 }
       ],
       'order': [[6, 'desc']]
    ";
    $table_waf_session_archive = $factory::dataTableSimple(10, $query,$option,false,'table-sm');

?>
<section class="<?= $href ?>">


    <div class="row text-right">
        <div class="col-md-4 text-left">
            <label for="refreshTime">Session Lock</label><br>
            <input name="sen-id" id="sen-id" placeholder="SEN">
            <button class="btn btn-warning btn-sm doA-senEnd">Unlock</button>
        </div>
        <div class="col-md-4 text-left">
            <label for="refreshTime">Mass Tools</label><br>
            <button class="btn btn-sm btn-outline-danger doA-endAll"><i class="fa fa-exclamation-triangle mr-2"></i> End All</button>
            <button class="mx-3 btn btn-outline-danger btn-sm doA-endSelected"><i class="fa fa-check mr-2"></i> End Selected</button>
        </div>
        <div class="col-md-4 text-right float-right">
            <label for="refreshTime">Reload</label><br>
            <small class="refPageSel">
                <i class="fas fa-sync" data-toggle="tooltip" data-placement="bottom" title="Table will not reload."></i>
                <select id="refreshTime" name="refreshTime" class="custom-select custom-select-sm col-md-2 mx-2" data-tableid='DT_waf_session_archive'>
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
            <i id="do-reload" data-tableid='DT_waf_session_archive' class="fa fa-spinner alert-success primary rounded-circle" data-toggle="tooltip" data-placement="bottom" title="Force reload."></i>
        </div>
    </div>
    <hr>

    <h6 class="text-center">Session Archive</h6>
    <div>
        <?php echo $table_waf_session_archive; ?>
    </div>

</section>