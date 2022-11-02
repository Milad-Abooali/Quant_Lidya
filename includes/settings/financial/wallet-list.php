<?php

    global $db;

    $query['db']        = 'DB_admin';
    $query['table']     = 'user_wallets';
    $query['table_html']     = 'user_wallets';
    $query['key']       = 'id';

    $query['columns']   = array(
        array(
            'db' => 'id',
            'th' => '#',
            'dt' => 0
        ),
        array(
            'db' => '(SELECT username FROM users WHERE id=user_wallets.user_id)',
            'th' => 'Username',
            'dt' => 1
        ),
        array(
            'db' => 'type_id',
            'th' => 'Wallet Type',
            'dt' => 2,
            'formatter' => true
        ),
        array(
            'db' => 'type_id',
            'th' => 'Wallet Currency',
            'dt' => 3,
            'formatter' => true
        ),
        array(
            'db' => 'balance',
            'th' => 'Balance',
            'dt' => 4,
            'formatter' => true
        ),
        array(
            'db' => 'updated_at',
            'th' => 'Last Update',
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
            'db' => 'id',
            'th' => 'Manage',
            'dt' => 7,
            'formatter' => true
        )
    );
    $table_user_wallets = $factory::dataTableSimple(10, $query,null,false,'table-sm');
?>

<section class="<?= $href ?>">

    <h6 class="text-center">Wallet Types</h6>
    <div>
        <div class="row">
            <div class="col-md-12">
                <?= $table_user_wallets ?>
            </div>
        </div>
    </div>

</section>

<style>
    .da-delDoc{display: none;}
</style>