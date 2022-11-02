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

<div class="wallet-request">
    <?php
    $where = "user_id=".$_SESSION['id'];
    $requests = $db->select('wallet_requests',$where,'*',0,'ID DESC');
    ?>
    <table id="wallet-requests" class="table table-sm table-striped dataTable">
        <thead>
        <tr>
            <th><?= $_L->T('Num_Sign','general') ?></th>
            <th><?= $_L->T('Type','general') ?></th>
            <th><?= $_L->T('Amount','trade') ?></th>
            <th><?= $_L->T('Status','general') ?></th>
            <th><?= $_L->T('Manage','general') ?></th>
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
                        <span data-rid="<?= $req['id'] ?>" class="float-right small btn btn-info" ><i class="fa fa-newspaper mr-2"></i> <?= $_L->T('Transaction','transactions') ?>: <?= $req['transaction_id'] ?></span>
                    <?php } else if ($req['req_type']=='deposit') { ?>
                        <?php if($req['status']=='processing') { ?>
                        <div class="text-center"><i class="fa fa-spinner fa-spin mr-2"></i> <?= $_L->T('Please_Wait','general') ?></div>
                    <?php  } else if($req['status']=='payment') { ?>
                            <div class="btn-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-money-bill mr-2"></i> <?= $_L->T($req['otherside_ref'],'gateway') ?></span>
                                </div>
                                <input type="button" data-req_id="<?= $req['id'] ?>" data-now="<?= strtotime("now") ?>" data-expire="<?= strtotime($req['created_at']) + (5 * 60) ?>" data-amount="<?= $req['amountMain'] ?>" data-gateway="<?= $req['otherside_ref'] ?>" class="doM-payment btn btn-sm btn-outline-success px-3" value="<?= $_L->T('Pay_Now','transactions') ?>">
                            </div>
                        <?php } else { ?>

                        <?php } ?>
                    <?php } else if ($req['req_type']=='withdrawal') { ?>

                    <?php } if(!in_array($req['status'], ['cancelled','accepted','processing'])) { ?>
                        <button data-id="<?= $req['id'] ?>" class="doA-cancel float-right small btn btn-outline-danger"><i class="fa fa-minus-circle mr-2"></i> <?= $_L->T('Cancel','general') ?></button>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>

    $('#wallet-requests').dataTable({
        "order": [[ 0, "desc" ]]
    });

    setInterval(function(){
        $("[id^=wg-wallet_requests] .reload").trigger("click");
    }, 1000*60*3);

    $("body").on("click", ".wallet-request .doA-cancel", function(event){
        event.preventDefault();
        let data = {
            req_id: $(this).data('id')
        }
        ajaxCall ('wallet', 'cancelRequest', data, function(response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error(resObj.e);
            } else if (resObj.res) {
                toastr.success("Request canceled.");
                $("[id^=wg-wallet_requests] .reload").trigger("click");
            }
        });
    });

    $("body").on("click", ".wallet-request .doM-payment", function(event){
        event.preventDefault();

        // Control the Payment Expire Time
        if ($(this).data('now') > $(this).data('expire')) {
            const data = {
                'req_id': $(this).data('req_id')
            }
            console.log(data);
            ajaxCall ('wallet', 'cancelRequest', data, function(response){
                let resObj = JSON.parse(response);
                if (resObj.e) {
                    console.log(resObj);
                } else {
                    alert ('<?= $_L->T('request_expired','transactions') ?>')
                    setTimeout(function(){
                        $("[id^=wg-wallet_requests] .reload").trigger("click");
                    }, 50);
                }
            });
            return;
        }

        /**
         * @todo PayPro Payment form
         */



    });
</script>