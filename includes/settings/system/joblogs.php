<?php


    $query['db']        = 'DB_admin';
    $query['table']     = 'cronjobs_logs';
    $query['table_html']     = 'job_logs';
    $query['key']       = 'id';
    $query['columns']   = array(
                                array(
                                    'db' => 'id',
                                    'th' => '#',
                                    'dt' => 0
                                ),
                                array(
                                    'db' => 'func_name',
                                    'th' => 'Job',
                                    'dt' => 1
                                ),
                                array(
                                    'db' => 'result',
                                    'th' => 'Result',
                                    'dt' => 2,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'start_time',
                                    'th' => 'Started',
                                    'dt' => 3,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'end_time',
                                    'th' => 'Ended',
                                    'dt' => 4,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'status',
                                    'th' => 'Status',
                                    'dt' => 5,
                                    'formatter' => true
                                )
                            );
    $option = '
          		"responsive": true,
                "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
        		"order": [ 0, "desc" ]
    ';
    $table_job_logs = $factory::dataTableSimple(10, $query,$option);

?>
<section class="<?= $href ?>">

    <h6 class="text-center">Jobs Logs</h6>
    <div>
        <?php echo $table_job_logs; ?>
    </div>

</section>

