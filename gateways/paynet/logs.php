<?php

$query['db']        = 'DB_admin';
$query['table']     = 'payment_orders';
$query['where']     = 'gateway_id='.$gw_id;
$query['table_html']     = 'paynet_payment_orders';
$query['key']       = 'id';
$query['columns']   = array(
    array(
        'db' => 'id',
        'th' => '#',
        'dt' => 0
    ),
    array(
        'db' => 'transactions_id',
        'th' => 'Transactions Id',
        'dt' => 1
    ),
    array(
        'db' => '(SELECT username FROM users WHERE users.id = user_id)',
        'th' => 'Email',
        'dt' => 2
    ),
    array(
        'db' => 'amount',
        'th' => 'Amount',
        'dt' => 3,
        'formatter' => true
    ),
    array(
        'db' => 'status',
        'th' => 'Status',
        'dt' => 4,
        'formatter' => true
    ),
    array(
        'db' => 'timestamp',
        'th' => 'Time',
        'dt' => 5,
        'formatter' => true
    ),
    array(
        'db' => 'id',
        'th' => 'Manage',
        'dt' => 6,
        'formatter' => true
    ),
    array(
        'db' => 'data',
        'th' => 'Data',
        'dt' => 7
    )
);
$option = '
          		"responsive": true,
                "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
        		"order": [ 0, "desc" ],
                "columnDefs": [
                    {
                        "targets": [ 7 ],
                        "visible": false,
                        "searchable": false
                    }
                ]
    ';
$table_paynet_logs = $factory::dataTableSimple(10, $query, $option);

?>
<section class="<?= $href ?>">

    <h6 class="text-center">Payment Orders</h6>
    <div>
        <?php echo $table_paynet_logs; ?>
    </div>

</section>

<?php GF::loadJS('f','gateways/'.$gateway.'/script.js'); ?>