<?php

    $query['db']        = 'DB_admin';
    $query['table']     = 'email_log';
    $query['table_html']     = 'email_log';
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
                                    'dt' => 1
                                ),
                                array(
                                    'db' => 'email',
                                    'th' => 'Email',
                                    'dt' => 2
                                ),
                                array(
                                    'db' => 'subject',
                                    'th' => 'Subject',
                                    'dt' => 3
                                ),
                                array(
                                    'db' => 'send_date',
                                    'th' => 'Date',
                                    'dt' => 4
                                ),
                                array(
                                    'db' => 'id',
                                    'th' => 'Content',
                                    'dt' => 5,
                                    'formatter' => true
                                )
                            );
    $option = '
          		"responsive": true,
                "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
        		"order": [ 0, "desc" ]
    ';
    $table_email_logs = $factory::dataTableSimple(10, $query,$option);

?>
<section class="<?= $href ?>">

    <h6 class="text-center">Sent Mails Logs</h6>
    <div>
        <?php echo $table_email_logs; ?>
    </div>

</section>

