<?php

    global $db;

    $where = "status=1";
    $gateways = $db->select('payment_gateways',$where, '*',null,'id ASC');

    $wallet = new wallet();

    $wallet_types = $wallet->getWalletTypes($_SESSION['unitn']);
    $wallets_data = array();
    if($wallet_types) foreach ($wallet_types as $wallet_type) {
        $wallets_data[$wallet_type['id']] = $wallet_type;
    }
    $user_wallets = $wallet->getUserWallets($_SESSION['id']);
    if($user_wallets) foreach ($user_wallets as $user_wallet) {
        $wallets_data[$user_wallet['type_id']]['user'] = $user_wallet;
    }
    $user_wallets_sets = implode(',',array_column($user_wallets,'id'));

    $where = "user_id=".$_SESSION['id']." AND group_id=2";
    $tp_accounts = $db->select('tp', $where);

?>

<ul class="nav nav-tabs" id="wallet-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="wallets-tab" data-toggle="tab" href="#wallets" role="tab" aria-controls="wallets" aria-selected="true">My Wallet</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="transfer-tab" data-toggle="tab" href="#transfer" role="tab" aria-controls="transfer" aria-selected="false">Transfer</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="request-tab" data-toggle="tab" href="#request" role="tab" aria-controls="request" aria-selected="false">Requests</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="transaction-tab" data-toggle="tab" href="#transaction" role="tab" aria-controls="transaction" aria-selected="false">Transactions</a>
    </li>
</ul>
<div class="tab-content" id="wallet-tabs-content">
    <div class="tab-pane fade show active" id="wallets" role="tabpanel" aria-labelledby="wallets-tab">
        <div class="row">
            <?php
            if($wallets_data)
                foreach ($wallets_data as $wallet_data) {
            ?>
                <div class="col-md-3" data-id="<?= $wallet_data['id'] ?>">
                    <div class="table-responsive py-2">
                        <table class="table table-sm table-bordered ">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong class="float-left"><?= $wallet_data['title'] ?></strong>
                                        <span class="text-muted small float-right border rounded px-1" data-toggle="tooltip" title="Wallet Id"><?= $wallet_data['user']['id'] ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center" class="bg-success">
                                        <?php if ($wallet_data['user'] && $wallet_data['user']['status']==0) { ?>
                                            <strong class="display-6 text-dark d-block"><?= $wallet_data['sym'].' '.GF::nf($wallet_data['user']['balance']) ?></strong>
                                        <?php } else if ($wallet_data['user'] && $wallet_data['user']['status']==1) { ?>
                                            <strong class="display-6 text-muted d-block"><?= $wallet_data['sym'].' '.GF::nf($wallet_data['user']['balance']) ?></strong>
                                        <?php } else { ?>
                                            <button data-id="<?= $wallet_data['id'] ?>" class="my-1 btn btn-outline-primary doA-activeWallet">Active This</button>
                                        <?php } ?>
                                        <div class="btn-group mt-1">
                                            <?php if ($wallet_data['user'] && $wallet_data['user']['status']==0){ ?>
                                                <button data-id="<?= $wallet_data['user']['id'] ?>" data-rate="<?= $wallet_data['rate'] ?>" data-sym="<?= $wallet_data['sym'] ?>" class="btn btn-sm btn-success doM-deposit">Deposit</button>
                                                <button data-id="<?= $wallet_data['user']['id'] ?>" data-balance="<?= $wallet_data['user']['balance'] ?>" data-rate="<?= $wallet_data['rate'] ?>" data-sym="<?= $wallet_data['sym'] ?>" class="btn btn-sm btn-danger doM-withdrawal">Withdrawal</button>
                                            <?php } else if ($wallet_data['user'] && $wallet_data['user']['status']==1) { ?>
                                                <strong class="alert-danger btn-sm">Blocked</strong>
                                           <?php } else { ?>
                                                <button class="btn btn-sm btn-light disabled" disabled>Deposit</button>
                                                <button class="btn btn-sm btn-light disabled" disabled>Withdrawal</button>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php
                }
            ?>
        </div>
    </div>
    <div class="tab-pane fade" id="transfer" role="tabpanel" aria-labelledby="transfer-tab">

        <div class="col-sm-12 mt-4">
            <div class="row">
                <div class="col-3">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" data-toggle="pill" href="#to-wallet" role="tab">To other Wallet </a>
                        <a class="nav-link" data-toggle="pill" href="#to-mt5" role="tab">To MT5 <sup class="text-muted">Deposit</sup></a>
                        <a class="nav-link" data-toggle="pill" href="#from-mt5" role="tab">From MT5 <sup class="text-muted">Withdrawal</sup></a>
                    </div>
                </div>
                <div class="col-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="to-wallet" role="tabpanel" aria-labelledby="to-wallet">
                            <p>Transfer credits from your wallets to the other client wallets.</p>

                            <form id="toWallet" name="toWallet" method="post" enctype="multipart/form-data">
                                <div class="col-sm-12 mb-3">
                                    <label for="inputUnit"><span class="text-danger">*</span> Source /<small>Please select the source</small></label>
                                    <div class="row">
                                        <div class="mb-3 col">
                                            <select class="form-control" id="s_wallet" name="s_wallet" required>
                                                <option value="0" disabled selected>Select Walleet</option>

                                                <?php
                                                    if ($wallets_data) foreach ($wallets_data as $wallet) {
                                                        if($wallet['user']['status']==0 && $wallet['user']['balance']>0) {
                                                ?>
                                                    <option data-sym="<?= $wallet['sym'] ?>" value="<?= $wallet['user']['id'] ?>"><?= $wallet['sym'] ?> <?= $wallet['title'] ?></option>
                                                <?php
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="pt-2 col-2 text-center">
                                            To
                                        </div>
                                        <div class="mb-3 col">
                                            <input type="number" class="form-control" min="1" step="1" id="d_wallet" name="d_wallet" placeholder="Receiver Wallet Id" required>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <small>Receiver wallet currency and sender wallet currency must be the same</small>
                                        <hr>
                                    </div>
                                </div>
                                <div id="wraper-gw">
                                    <div class="input-group mb-3 col">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info text-white strong symTransfer" id="sym"></span>
                                        </div>
                                        <input type="number" class="form-control" min="0.01" max="9999999.00" step="0.01" id="amountTransfer" name="amountTransfer" placeholder="0.00" required>
                                    </div>
                                </div>
                               <hr>
                                <div class="col-sm-12">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <small class="float-right" style="line-height: 33px;">* Please make sure all the entered data are correct.</small>
                                    <div id="fRes" class="mt-3 alert" style="display: none;"></div>
                                </div>
                            </form>

                        </div>
                        <div class="tab-pane fade" id="to-mt5" role="tabpanel" aria-labelledby="to-mt5">
                            <p>Transfer credits from your wallets to the MT5 trading platform account.</p>
                            <?php if($tp_accounts) { ?>
                            <form id="toMT5" name="toMT5" method="post" enctype="multipart/form-data">
                                <div class="col-sm-12 mb-3">
                                    <label for="inputUnit"><span class="text-danger">*</span> Source /<small>Please select the source</small></label>
                                    <div class="row">
                                        <div class="mb-3 col">
                                            <select class="form-control" id="s_wallet" name="s_wallet" required>
                                                <option value="0" disabled selected>Select Walleet</option>
                                                <?php if ($wallets_data) foreach ($wallets_data as $wallet) {
                                                    if($wallet['user']['status']==0 && $wallet['user']['balance']>0) {
                                                        ?>
                                                        <option data-sym="<?= $wallet['title'] ?>" value="<?= $wallet['user']['id'] ?>"><?= $wallet['sym'] ?> <?= $wallet['title'] ?></option>
                                                <?php
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="pt-2 col-2 text-center">
                                            To
                                        </div>
                                        <div class="mb-3 col">
                                            <select class="form-control" id="d_mt5" name="d_mt5" required>
                                                <option value="0" disabled selected>Select MT5 Login</option>
                                                <?php if ($tp_accounts) foreach($tp_accounts as $account) { ?>
                                                    <option data-sym="<?= GF::getLoginDetails($account['login'])['Currency'] ?>" value="<?php echo $account['login']; ?>"><?php echo $account['login']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <small>Current Exchange Rate: <strong class="badge-info px-2" id="walletRate">1.00</strong></small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="input-group mb-3 col">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info text-white strong symTransfer" id="sym"></span>
                                        </div>
                                        <input type="number" class="form-control s-amount" min="0.01" max="9999999.99" step="0.01" id="sAmount" name="sAmount" placeholder="0.00" required="">
                                    </div>
                                    <div class="pt-2 col-1">
                                        =
                                    </div>
                                    <div class="input-group mb-3 col">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-primary text-white strong symMT5"></span>
                                        </div>
                                        <input type="number" class="form-control d-amount" min="0.01" max="9999999.99" step="0.01" id="dAmount" name="dAmount" placeholder="0.00" required="">
                                    </div>
                                </div>

                                <hr>
                                <div class="col-sm-12">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <small class="float-right" style="line-height: 33px;">* Please make sure all the entered data are correct.</small>
                                    <div id="fRes" class="mt-3 alert" style="display: none;"></div>
                                </div>
                            </form>
                            <?php } else { ?>
                                <p class="alert alert-danger">You dont have any active real MT5 account.</p>
                            <?php } ?>
                        </div>
                        <div class="tab-pane fade" id="from-mt5" role="tabpanel" aria-labelledby="from-mt5">
                            <p>Transfer credits from your MT5 trading platform account to your wallet.</p>
                            <?php if($tp_accounts) { ?>
                                <form id="fromMT5" name="fromMT5" method="post" enctype="multipart/form-data">
                                    <div class="col-sm-12 mb-3">
                                        <label for="inputUnit"><span class="text-danger">*</span> Source /<small>Please select the source</small></label>
                                        <div class="row">
                                            <div class="mb-3 col">
                                                <select class="form-control" id="d_mt5" name="d_mt5" required>
                                                    <option value="0" disabled selected>Select MT5 Login</option>
                                                    <?php if ($tp_accounts) foreach($tp_accounts as $account) { ?>
                                                        <option data-sym="<?= GF::getLoginDetails($account['login'])['Currency'] ?>" value="<?php echo $account['login']; ?>"><?php echo $account['login']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="pt-2 col-2 text-center">
                                                To
                                            </div>
                                            <div class="mb-3 col">
                                                <select class="form-control" id="s_wallet" name="s_wallet" required>
                                                    <option value="0" disabled selected>Select Walleet</option>
                                                    <?php if ($wallets_data) foreach ($wallets_data as $wallet) {
                                                        if($wallet['user']['status']==0) {
                                                            ?>
                                                            <option data-sym="<?= $wallet['title'] ?>" value="<?= $wallet['user']['id'] ?>"><?= $wallet['sym'] ?> <?= $wallet['title'] ?></option>
                                                            <?php
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <small>Current Exchange Rate: <strong class="badge-info px-2" id="walletRate">1.00</strong></small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="input-group mb-3 col">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-gradient-primary text-white strong symMT5"></span>
                                            </div>
                                            <input type="number" class="form-control d-amount" min="0.01" max="9999999.99" step="0.01" id="dAmount" name="dAmount" placeholder="0.00" required="">
                                        </div>
                                        <div class="pt-2 col-1">
                                            =
                                        </div>
                                        <div class="input-group mb-3 col">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-gradient-info text-white strong symTransfer" id="sym"></span>
                                            </div>
                                            <input type="number" class="form-control s-amount" min="0.01" max="9999999.99" step="0.01" id="sAmount" name="sAmount" placeholder="0.00" required="">
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="col-sm-12">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                        <small class="float-right" style="line-height: 33px;">* Please make sure all the entered data are correct.</small>
                                        <div id="fRes" class="mt-3 alert" style="display: none;"></div>
                                    </div>
                                </form>                            <?php } else { ?>
                            <p class="alert alert-danger">You dont have any active real MT5 account.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="tab-pane fade" id="request" role="tabpanel" aria-labelledby="request-tab">

        <div class="py-4">
            <?php
                $where = "user_id=".$_SESSION['id'];
                $requests = $db->select('wallet_requests',$where);
            ?>
            <table class="table table-sm table-striped dataTable">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Request</th>
                    <th>Amount</th>
                    <th>status</th>
                    <th>Manage</th>
                </tr>
                </thead>
                <tbody>
                <?php if($requests) foreach($requests as $req) { ?>
                    <tr>
                        <td><?= $req['id'] ?></td>
                        <td><?= ucfirst($req['req_type']) ?></td>
                        <td data-toggle="tooltip" data-placement="top" title="<?= $wallets_data[$req['wallet_type']]['title'] ?>"><?= $wallets_data[$req['wallet_type']]['sym'] ?> <?= GF::nf($req['amountWallet']) ?></td>
                        <td><?= $req['status'] ?></td>
                        <td>
                            <?php if($req['status']=='accepted') { ?>
                                <button data-rid="<?= $req['id'] ?>" data-tid="<?= $req['transaction_id'] ?>" class="doT-transaction float-right small btn btn-info"><i class="fa fa-newspaper mr-2"></i> Transaction</button>
                            <?php } else if ($req['req_type']=='deposit') { ?>
                                <?php if($req['otherside_ref']==3) { ?>
                                    <div class="btn-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-money-bill mr-2"></i> Credit Card</span>
                                        </div>
                                        <input type="button" data-tid="<?= $req['id'] ?>" data-now="1636549479" data-expire="1636549777" data-amount="1" data-gateway="3" class="doM-payNow btn btn-sm btn-outline-success px-3" value="Pay Now">
                                    </div>
                                <?php } else { ?>

                                <?php } ?>
                            <?php } else if ($req['req_type']=='withdrawal') { ?>

                            <?php } if(!in_array($req['status'], ['cancelled','accepted'])) { ?>
                                <button data-id="<?= $req['id'] ?>" class="doA-cancel float-right small btn btn-outline-danger"><i class="fa fa-minus-circle mr-2"></i> Cancel</button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>


    </div>
    <div class="tab-pane fade" id="transaction" role="tabpanel" aria-labelledby="transaction-tab">
        <div class="py-4">

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
                        'th' => '#',
                        'dt' => 0
                    ),
                    array(
                        'db' => '(SELECT username FROM users WHERE id=wallet_transactions.s_user_id)',
                        'th' => 'Username',
                        'dt' => 1
                    ),
                    array(
                        'db' => '(SELECT username FROM users WHERE id=wallet_transactions.d_user_id)',
                        'th' => ' ',
                        'dt' => 2
                    ),
                    array(
                        'db' => 'action_type',
                        'th' => 'Action',
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
                        'th' => 'Source',
                        'dt' => 5,
                        'formatter' => true
                    ),
                    array(
                        'db' => 'destination',
                        'th' => 'Dest',
                        'dt' => 6,
                        'formatter' => true
                    ),
                    array(
                        'db' => 'reference',
                        'th' => 'Ref Id',
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
    </div>
</div>

<div id="m-form-deposit" class="d-none">
    <form id="walletDeposit" name="walletRequest" method="post" enctype="multipart/form-data">
        <input type="hidden" name="wallet_type" id="wallet_type" value="0">
        <div class="col-sm-12 mb-3">
            <label for="inputUnit"><span class="text-danger">*</span> Amount /<small>Please specify the amount</small></label>
            <div class="row">
                <div class="input-group mb-3 col">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gradient-info text-white strong" id="sym"></span>
                    </div>
                    <input type="number" class="form-control" min="0.01" max="9999999.99" step="0.01" id="amountWallet" name="amountWallet" placeholder="0.00" required>
                </div>
                <div class="pt-2 col-1">
                    =
                </div>
                <div class="input-group mb-3 col">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gradient-primary text-white strong">$</span>
                    </div>
                    <input type="number" class="form-control" min="0.01" max="9999999.99" step="0.01" id="amountMain" name="amountMain" placeholder="0.00" required>
                </div>
            </div>
            <div class="text-center">
                <small>Current Exchange Rate: <strong class="badge-info px-2" id="walletRate"> </strong></small>
            </div>
        </div>
        <div id="wraper-gw">
            <div class="col-sm-12 mb-3" id="receipt">
                <select class="form-control" id="gateway" name="gateway" required>
                    <option value="1">Wire Bank</option>
                    <option value="3">Credit Card</option>
                </select>
            </div>
        </div>
        <div id="wraper">
            <div class="col-sm-12 mb-3" id="receipt">
                <label for="doc"><span class="text-danger">*</span> Document /<small>Up to 5 file</small></label>
                <div class="custom-file my-1">
                    <input type="file" class="custom-file-input" id="doc" name="doc[]" required="">
                    <label class="custom-file-label" for="doc">Choose your Receipt</label>
                </div>
                <span style="display: none" data-max="4" class="da-addDoc small btn btn-sm btn-light">
                    <i class="fa fa-plus text-success"></i> Add more
                </span>
            </div>
        </div>
        <div class="col-sm-12 mb-3" id="comment">
            <label for="inputComment">Comment /<small>Optional</small></label>
            <textarea class="form-control" type="text" id="comment" name="comment"></textarea>
        </div>
        <hr>
        <div class="col-sm-12">
            <button class="btn btn-primary" type="submit">Submit</button>
            <small class="float-right" style="line-height: 33px;">* Please make sure all the entered data are correct.</small>
            <div id="fRes" class="mt-3 alert" style="display: none;"></div>
        </div>
    </form>
</div>

<div id="m-form-withdrawal" class="d-none">
    <form id="walletWithdrawal" name="walletWithdrawal" method="post" enctype="multipart/form-data">
        <input type="hidden" name="wallet_type" id="wallet_type" value="0">
        <div class="col-sm-12 mb-3">
            <label for="inputUnit"><span class="text-danger">*</span> Amount /<small>Please specify the amount</small></label>
            <div class="row">
                <div class="input-group mb-3 col">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gradient-info text-white strong" id="sym"></span>
                    </div>
                    <input type="number" class="form-control" min="0.01" max="9999999.99" step="0.01" id="amountWallet" name="amountWallet" placeholder="0.00" required>
                </div>
                <div class="pt-2 col-1">
                    =
                </div>
                <div class="input-group mb-3 col">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gradient-primary text-white strong">$</span>
                    </div>
                    <input type="number" class="form-control" min="0.01" max="9999999.99" step="0.01" id="amountMain" name="amountMain" placeholder="0.00" required>
                </div>
            </div>
            <div class="text-center">
                <small>Current Exchange Rate: <strong class="badge badge-info px-2">9.93</strong></small>
            </div>
        </div>
        <div id="wraper-gw"></div>
        <div id="wraper"><div class="col-sm-12 mb-3" id="receipt"><label for="doc">Document /<small>Up to 5 file</small></label><div class="custom-file my-1"><input type="file" class="custom-file-input" id="doc" name="doc[]"><label class="custom-file-label" for="doc">Choose your Receipt</label></div><span style="display: none" data-max="4" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> Add more</span></div><div class="col-sm-12 mb-3" id="bank"><label for="inputComment"><span class="text-danger">*</span> Bank Account /<small>Please choose your bank account</small></label><input class="form-control" type="text" id="bankAccount" name="bankAccount" placeholder="Bank Account Number" required=""></div></div>
        <div class="col-sm-12 mb-3" id="comment">
            <label for="inputComment">Comment /<small>Optional</small></label>
            <textarea class="form-control" type="text" id="comment" name="comment"></textarea>
        </div>

        <hr>
        <div class="col-sm-12">
            <input type="hidden" name="user_id" value="42">
            <button class="btn btn-primary" type="submit">Submit</button>
            <small class="float-right" style="line-height: 33px;">* Please make sure all the entered data are correct.</small>
            <div id="fRes" class="mt-3 alert" style="display: none;"></div>
        </div>
    </form>
</div>

<script>

    var gw_d =
        '<div class="col-sm-12 mb-3" id="receipt">'+
        '<select class="form-control" id="gateway" name="gateway" required>'+
        '<?php if ($gateways) foreach($gateways as $gateway) {?>'+
        '<option value="<?php echo $gateway["id"]; ?>"><?php echo $gateway["name"]; ?></option>'+
        '<?php } ?>'+
        '</select>'+
        '</div>';
    var cc_info =
        '<p class="col-sm-12 mb-3" id="bank">' +
        '<span class="text-danger">*</span> You have to pay in next step.' +
        '</p>';
    var doc_d =
        '<div class="col-sm-12 mb-3" id="receipt">' +
        '<label for="doc"><span class="text-danger">*</span> Document /<small>Up to 5 file</small></label>' +
        '<div class="custom-file my-1">' +
        '<input type="file" class="custom-file-input" id="doc" name="doc[]" required>' +
        '<label class="custom-file-label" for="doc">Choose your Receipt</label>' +
        '</div>' +
        '<span style="display: none" data-max="4" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> Add more</span>' +
        '</div>';
    var doc_w =
        '<div class="col-sm-12 mb-3" id="receipt">' +
        '<label for="doc">Document /<small>Up to 5 file</small></label>' +
        '<div class="custom-file my-1">' +
        '<input type="file" class="custom-file-input" id="doc" name="doc[]">' +
        '<label class="custom-file-label" for="doc">Choose your Receipt</label>' +
        '</div>' +
        '<span style="display: none" data-max="4" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> Add more</span>' +
        '</div>';
    var bank =
        '<div class="col-sm-12 mb-3" id="bank">' +
        '<label for="inputComment"><span class="text-danger">*</span> Bank Account /<small>Please choose your bank account</small></label>' +
        '<input class="form-control" type="text" id="bankAccount" name="bankAccount" placeholder="Bank Account Number" required>' +
        '</div>';

    // Enabel Wallet Type
    $("body").on("click", "#wallets .doA-activeWallet", function(event){
        event.preventDefault();
        let id = $(this).data('id');
        let data = {
            type_id: id,
            user_id: <?= $_SESSION['id'] ?>
        }
        ajaxCall ('wallet', 'enableWallet', data, function(response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error(resObj.e);
            } else if (resObj.res) {
                toastr.success("new wallet type enabled.");
                $("#wg-user_wallet-1 .reload").trigger("click");
            }
        });
    });

    /**
     * Exchange Value
     */
    $("body").on("change keyup","#amountWallet", function() {
        $('#amountMain').addClass('bg-success');
        $('#amountMain').val(($(this).val()/rate).toFixed(2));
        setTimeout(function() {
            $('#amountMain').removeClass('bg-success');
        }, 300);
    });
    $("body").on("change keyup","#amountMain", function() {
        $('#amountWallet').addClass('bg-success');
        $('#amountWallet').val(($(this).val()*rate).toFixed(2));
        setTimeout(function() {
            $('#amountWallet').removeClass('bg-success');
        }, 300);
    });

    /**
     * Deposit
     */
    var rate;
    $("body").on("click", "#wallets .doM-deposit", function(event){
        event.preventDefault();
        let id = $(this).data('id');
        let sym = $(this).data('sym');
        rate = parseFloat($(this).data('rate'));
        // alert(id);
        $('#m-form-deposit #walletDeposit #wallet_type').attr("value",id);
        $('#m-form-deposit #walletDeposit #sym').html(sym);
        $('#m-form-deposit #walletDeposit #walletRate').html(rate);
        let depositForm = $('#m-form-deposit').html();
        makeModal('Deposit to wallet',depositForm,'lg');
    });
    // Payment Type
    $("body").on("change","form#walletDeposit #gateway", function() {
        let gateway = $(this).val();
        if (gateway!=1) $('form#walletDeposit #wraper').html(cc_info);
        if (gateway==1) $('form#walletDeposit #wraper').html(doc_d);
    });

    // Submit Request
    $("body").on("submit","form#walletDeposit", function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        ajaxForm ('wallet', 'walletDepositRequest', formData, function(response){
            let resObj = JSON.parse(response);
            const fResp = $("#walletDeposit #fRes");
            if (resObj.e) {
                fResp.addClass('alert-warning');
                fResp.fadeIn();
                fResp.html('Error, Please Check Inputs!');
            }
            if (resObj.res) {
                fResp.addClass('alert-success');
                fResp.fadeIn();
                fResp.html('Your Request Added.');
                $("#wg-user_wallet-1 .reload").trigger("click");
                setTimeout(function(){
                    $('#modalMain').modal('toggle');
                    $("#wg-user_wallet-1 #request-tab").trigger("click");
                }, 1500);
            }
        });
    });

    /**
     *  Withdrawal
     */
    $("body").on("click", "#wallets .doM-withdrawal", function(event){
        event.preventDefault();
        let id = $(this).data('id');
        let sym = $(this).data('sym');
        let balance = $(this).data('balance');
        rate = parseFloat($(this).data('rate'));
        // alert(id);
        $('#m-form-withdrawal #walletWithdrawal #wallet_type').attr("value",id);
        $('#m-form-withdrawal #walletWithdrawal #amountWallet').attr("max",balance);
        $('#m-form-withdrawal #walletWithdrawal #sym').html(sym);
        $('#m-form-withdrawal #walletWithdrawal #walletRate').html(rate);
        let withdrawalForm = $('#m-form-withdrawal').html();
        makeModal('Withdrawal from wallet',withdrawalForm,'lg');
    });

    // Submit Request
    $("body").on("submit","form#walletWithdrawal", function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        ajaxForm ('wallet', 'walletWithdrawalRequest', formData, function(response){
            let resObj = JSON.parse(response);
            const fResp = $("#walletWithdrawal #fRes");
            if (resObj.e) {
                fResp.removeClass('alert-success');
                fResp.addClass('alert-warning');
                fResp.fadeIn();
                fResp.html('Error, Please Check Inputs!');
            }
            if (resObj.res) {
                fResp.removeClass('alert-warning');
                fResp.addClass('alert-success');
                fResp.fadeIn();
                fResp.html('Your Request Added.');
                $("#wg-user_wallet-1 .reload").trigger("click");
                setTimeout(function(){
                    $('#modalMain').modal('toggle');
                    $("#wg-user_wallet-1 #request-tab").trigger("click");
                }, 1200);
            }
        });
    });

    /**
     * Requests Tab
     */
    $('.dataTable').dataTable();

    $("body").on("click", "#request .doA-cancel", function(event){
        event.preventDefault();
        let data = {
            req_id: $(this).data('id')
        }
        ajaxCall ('wallet', 'cancelRequest', data, function(response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error(resObj.e);
            } else if (resObj.res) {
                toastr.success("Request canceld.");
                $("#wg-user_wallet-1 .reload").trigger("click");
                setTimeout(function(){
                    $("#wg-user_wallet-1 #request-tab").trigger("click");
                }, 350);
            }
        });
    });

    $("body").on("click", "#request .doT-transaction", function(event){
        event.preventDefault();
        let transaction_id = $(this).data('tid');
        $("#wg-user_wallet-1 #transaction-tab").trigger("click");
        $('[data-tableid="DT_user_wallets_transactions"]').val(transaction_id).trigger("change");
    });

    $("body").on("click", "#wg-user_wallet-1 #transaction-tab", function(event){
        $('[data-tableid="DT_user_wallets_transactions"]').val('').trigger("change");
    });

    /**
     * Transfer
     */

    // Wallet Balance and symbol
    var s_sym;
    var d_sym;
    var sym_rate = 1.00;

    // Exchange and sym
   function updateExchangeRate(a,b) {
        $(document.body).css({'cursor' : 'wait'});
        if(a==b){
            sym_rate = 1.00;
            setTimeout(function() {
                $('.s-amount').change();
                $(document.body).css({'cursor' : 'default'});
            }, 100);
        } else {
            let data = {
                s_sym : a,
                d_sym : b
            }
            ajaxCall ('quotes', 'exchangeRate', data, function(response) {
                let resObj = JSON.parse(response);
                sym_rate = resObj.res.rate;
                setTimeout(function() {
                    $('.s-amount').change();
                    $(document.body).css({'cursor' : 'default'});
                }, 100);
            });
        }

       return true;
   }
   function updateDAmount(input) {
       let sAmount = input.closest("form").find('.s-amount:first');
       let dAmount = input.closest("form").find('.d-amount:first');
       dAmount.addClass('bg-success');
       dAmount.val((sAmount.val()*sym_rate).toFixed(2));
       setTimeout(function() {
           dAmount.removeClass('bg-success');
           input.closest("form").find('#walletRate').html(sym_rate.toFixed(2));
       }, 400);
   }
   function updateSAmount(input){
       let sAmount = input.closest("form").find('.s-amount:first');
       let dAmount = input.closest("form").find('.d-amount:first');
       sAmount.addClass('bg-success');
       sAmount.val((dAmount.val()/sym_rate).toFixed(2));
       setTimeout(function() {
           sAmount.removeClass('bg-success');
           input.closest("form").find('#walletRate').html(sym_rate.toFixed(2));
       }, 300);
    }
    $("body").on("change",'#transfer select[name="s_wallet"]', function(e) {
        s_sym = $(this).children("option:selected").data('sym');
        let data = {
            id: $(this).val()
        }
        ajaxCall ('wallet', 'getWallet', data, function(response) {
            let resObj = JSON.parse(response);
            $('#transfer input[name="amountTransfer"]').attr("max",resObj.balance);
            $('#transfer .symTransfer').html(s_sym);
            if(s_sym && d_sym) updateExchangeRate(s_sym, d_sym);
        });
    });
    $("body").on("change",'#transfer select[name="d_mt5"]', function(e) {
        d_sym = $(this).children("option:selected").data('sym');
        $('#transfer .symMT5').html(d_sym);
        if(s_sym && d_sym) updateExchangeRate(s_sym, d_sym);
    });
    $("body").on("change keyup",".s-amount", function() {
        updateDAmount($(this));
    });
    $("body").on("change keyup",".d-amount", function() {
        updateSAmount($(this));
    });

    // Submit to MT5
    $("body").on("submit","form#toMT5", function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        ajaxForm ('wallet', 'toMT5', formData, function(response){
            let resObj = JSON.parse(response);
            const fResp = $("#toMT5 #fRes");
            if (resObj.e) {
                fResp.removeClass('alert-success');
                fResp.addClass('alert-danger');
                fResp.fadeIn();
                fResp.html(resObj.e);
            }
            if (resObj.res) {
                fResp.removeClass('alert-danger');
                fResp.addClass('alert-success');
                fResp.fadeIn();
                fResp.html('Your Transaction is done.');
                $("#wg-user_wallet-1 .reload").trigger("click");
                setTimeout(function(){
                    $("#wg-user_wallet-1 #transaction-tab").trigger("click");
                }, 800);
            }
        });
    });


    // Submit from MT5
    $("body").on("submit","form#fromMT5", function(e) {
        e.preventDefault();
        $('#transfer input[name="amountTransfer"]').attr("max",9999999999);
        var formData = new FormData(this);
        ajaxForm ('wallet', 'fromMT5', formData, function(response){
            let resObj = JSON.parse(response);
            const fResp = $("#fromMT5 #fRes");
            if (resObj.e) {
                fResp.removeClass('alert-success');
                fResp.addClass('alert-danger');
                fResp.fadeIn();
                fResp.html(resObj.e);
            }
            if (resObj.res) {
                fResp.removeClass('alert-danger');
                fResp.addClass('alert-success');
                fResp.fadeIn();
                fResp.html('Your Transaction is done.');
                $("#wg-user_wallet-1 .reload").trigger("click");
                setTimeout(function(){
                    $("#wg-user_wallet-1 #transaction-tab").trigger("click");
                }, 800);
            }
        });
    });


    // Submit to wallet
    $("body").on("submit","form#toWallet", function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        ajaxForm ('wallet', 'toWallet', formData, function(response){
            let resObj = JSON.parse(response);
            const fResp = $("#toWallet #fRes");
            if (resObj.e) {
                fResp.removeClass('alert-success');
                fResp.addClass('alert-danger');
                fResp.fadeIn();
                fResp.html(resObj.e);
            }
            if (resObj.res) {
                fResp.removeClass('alert-danger');
                fResp.addClass('alert-success');
                fResp.fadeIn();
                fResp.html('Your Request Added.');
                $("#wg-user_wallet-1 .reload").trigger("click");
                setTimeout(function(){
                    $("#wg-user_wallet-1 #transaction-tab").trigger("click");
                }, 800);
            }
        });
    });

</script>