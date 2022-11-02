<?php

    global $db;
    global $_L;

    $where = "status=1";
    $gateways = $db->select('payment_gateways',$where, '*',null,'id ASC');

    $wallet = new wallet();

    $wallet_types = $wallet->getWalletTypes($_SESSION['unitn']);
    $wallets_data = array();
    if($wallet_types) foreach ($wallet_types as $wallet_type) {
        $wallets_data[$wallet_type['id']] = $wallet_type;
    }
    $user_wallets = $wallet->getUserWallet($_SESSION['id']);
    if($user_wallets){
        foreach ($user_wallets as $user_wallet) {
            $wallets_data[$user_wallet['type_id']]['user'] = $user_wallet;
        }
        $user_wallets_sets = implode(',',array_column($user_wallets,'id'));
    }
    $where = "user_id=".$_SESSION['id']." AND group_id=2";
    $tp_accounts = $db->select('tp', $where);

?>

<div class=" ">

        <input type="hidden" class="form-control filterDT mb-2" placeholder="id" data-tableid="DT_user_wallets_transactions" data-col="0">

        <?php


        $query['db']        = 'DB_admin';
        $query['table']     = 'wallet_transactions';
        $query['where']     = "((s_type='Wallet' AND source IN ($user_wallets_sets)) OR ( d_type='Wallet' AND destination IN ($user_wallets_sets)))";
        $query['table_html']     = 'user_wallets_transactions';
        $query['key']            = 'id';
        $query['columns']        = array(
            array(
                'db' => 'id',
                'th' => $_L->T('Num_Sign','general'),
                'dt' => 0
            ),
            array(
                'db' => '(SELECT username FROM users WHERE id=wallet_transactions.s_user_id)',
                'th' => $_L->T('Username','general'),
                'dt' => 1
            ),
            array(
                'db' => '(SELECT username FROM users WHERE id=wallet_transactions.d_user_id)',
                'th' => ' ',
                'dt' => 2
            ),
            array(
                'db' => 'action_type',
                'th' => $_L->T('Action','trade'),
                'dt' => 3
            ),
            array(
                'db' => 'volume',
                'th' => 'Action',
                'dt' => 4,
                'formatter' => true
            ),
            array(
                'db' => 'source',
                'th' => $_L->T('Source','marketing'),
                'dt' => 5,
                'formatter' => true
            ),
            array(
                'db' => 'destination',
                'th' => $_L->T('Destination','general'),
                'dt' => 6,
                'formatter' => true
            ),
            array(
                'db' => 'reference',
                'th' => $_L->T('Ref_Id','transactions'),
                'dt' => 7,
                'formatter' => true
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
                'dt' => 10
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
                'th' => $_L->T('Time','general'),
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
                                'targets': 1,
                                'orderable':false,
                                'visible': false
                            },
                            {
                                'targets': 2,
                                'orderable':false,
                                'visible': false
                            },
                            {
                                'targets': 3,
                                'orderable':false,
                                'visible': false
                            },
                            {
                                'targets': 8,
                                'orderable':false,
                                'visible': false
                            },
                            {
                                'targets': 9,
                                'orderable':false,
                                'visible': false
                            },
                            {
                                'targets': 10,
                                'orderable':false,
                                'visible': false
                            },
                            {
                                'targets': 11,
                                'orderable':false,
                                'visible': false
                            },
                            {
                                'targets': 12,
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
                    'order': [[0, 'desc']]
                ";
        $table_user_wallets_transactions = factory::dataTableSimple(25, $query,$option,false,'table-sm');
        ?>
        <?= $table_user_wallets_transactions ?>
        <?= factory::footer() ?>
    </div>

<script>


</script>