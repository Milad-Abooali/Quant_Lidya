<?php

    global $db;

    $query['db']        = 'DB_admin';
    $query['table']     = 'wallet_transactions';
    $query['table_html']     = 'wallet_transactions';
    $query['key']       = 'id';
    $query['columns']   = array(
        array(
            'db' => 'id',
            'th' => '#',
            'dt' => 0
        ),
        array(
            'db' => '(SELECT username FROM users WHERE id=wallet_transactions.s_user_id)',
            'th' => 'Username',
            'dt' => 1,
            'formatter' => true
        ),
        array(
            'db' => '(SELECT username FROM users WHERE id=wallet_transactions.d_user_id)',
            'th' => ' ',
            'dt' => 2
        ),
        array(
            'db' => 'volume',
            'th' => 'Volume',
            'dt' => 3
        ),
        array(
            'db' => 'source',
            'th' => 'Source',
            'dt' => 4,
            'formatter' => true
        ),
        array(
            'db' => 'destination',
            'th' => 'Dest',
            'dt' => 5,
            'formatter' => true
        ),
        array(
            'db' => 'action_type',
            'th' => 'Action',
            'dt' => 6
        ),
        array(
            'db' => 'reference',
            'th' => 'Ref Id',
            'dt' => 7
        ),
        array(
            'db' => 'ex_rate',
            'th' => 'Rate (e)',
            'dt' => 8
        ),
        array(
            'db' => 'commission',
            'th' => 'Fee',
            'dt' => 9
        ),
        array(
            'db' => 's_balance',
            'th' => 'Balance',
            'dt' => 10,
            'formatter' => true
        ),
        array(
            'db' => 'd_balance',
            'th' => 'Balance',
            'dt' => 11
        ),
        array(
            'db' => '(SELECT username FROM users WHERE id=wallet_transactions.created_by)',
            'th' => 'By',
            'dt' => 12
        ),
        array(
            'db' => 'created_at',
            'th' => 'Time',
            'dt' => 13,
            'formatter' => true
        ),
        array(
            'db' => 's_type',
            'th' => ' ',
            'dt' => 14
        ),
        array(
            'db' => 'd_type',
            'th' => ' ',
            'dt' => 15
        )
    );
$option = "
        		'columnDefs': [
        		        {
                            'targets': 2,
                            'orderable':false,
                            'visible': false
        		        },
        		        {
                            'targets': 11,
                            'orderable':false,
                            'visible': false
        		        },
        		        {
                            'targets': 14,
                            'orderable':false,
                            'visible': false
        		        },
        		        {
                            'targets': 15,
                            'orderable':false,
                            'visible': false
        		        }
                ],
                'lengthMenu': [ [5, 10, 25, 50, 100, 200, 300, 400, 500, -1], [5, 10, 25, 50, 100, 200, 300, 400, 500, 'All'] ],
                'order': [[13, 'desc']]
    ";
    $table_wallet_transactions = $factory::dataTableSimple(25, $query,$option,false,'table-sm');
?>

<section class="<?= $href ?>">

    <h6 class="text-center">Wallet Transaction</h6>
    <div>
        <div class="row">
            <div class="col-md-12">
                <?= $table_wallet_transactions ?>
            </div>
        </div>
    </div>

</section>

<style>
    .da-delDoc{display: none;}
</style>