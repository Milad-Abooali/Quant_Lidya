<?php

    $query['db']        = 'DB_admin';
    $query['table']     = 'brokers';
    $query['table_html']     = 'brokers_list';
    $query['key']       = 'id';
    $query['columns']   = array(
                                array(
                                    'db' => 'id',
                                    'th' => '#',
                                    'dt' => 0
                                ),
                                array(
                                    'db' => 'title',
                                    'th' => 'Title',
                                    'dt' => 1
                                ),
                                array(
                                    'db' => 'logo',
                                    'th' => 'Logo',
                                    'dt' => 2,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'maintenance',
                                    'th' => 'Maintenance',
                                    'dt' => 3,
                                    'formatter' => true
                                ),
                                array(
                                    'db' => 'id',
                                    'th' => 'Manage',
                                    'dt' => 4,
                                    'formatter' => true
                                )
                            );
    $option = '
          		"responsive": true,
                "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
        		"order": [ 0, "asc" ]
    ';
    $table_brokers_list = $factory::dataTableSimple(10, $query,$option);

?>
<section class="<?= $href ?>">

    <h6 class="text-center">Brokers List</h6>
    <div>
        <?php echo $table_brokers_list; ?>
    </div>

</section>

<style>
    .da-delDoc{display: none;}
</style>