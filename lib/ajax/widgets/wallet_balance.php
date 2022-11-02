<?php
/**
 * @TODOD Exchange Rate Updater
 * @TODOD GW Selector
 */

    global $db;
    global $_L;

    $where = "broker_id IN (0, ".Broker['id'].") AND status=1";
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
    }

?>

<div class="row wallet-balance">
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
                                    <button data-id="<?= $wallet_data['id'] ?>" class="my-1 btn btn-outline-primary doA-activeWallet"><?= $_L->T('Active_This','wallet') ?></button>
                                <?php } ?>
                                <div class="btn-group mt-1">
                                    <?php if ($wallet_data['user'] && $wallet_data['user']['status']==0){ ?>
                                        <button data-id="<?= $wallet_data['user']['id'] ?>" data-rate="<?= $wallet_data['rate'] ?>" data-sym="<?= $wallet_data['sym'] ?>" class="btn btn-sm btn-success doM-deposit">Deposit</button>
                                        <button data-id="<?= $wallet_data['user']['id'] ?>" data-balance="<?= $wallet_data['user']['balance'] ?>" data-rate="<?= $wallet_data['rate'] ?>" data-sym="<?= $wallet_data['sym'] ?>" class="btn btn-sm btn-danger doM-withdrawal">Withdrawal</button>
                                    <?php } else if ($wallet_data['user'] && $wallet_data['user']['status']==1) { ?>
                                        <strong class="alert-danger btn-sm"><?= $_L->T('Blocked','wallet') ?> </strong>
                                    <?php } else { ?>
                                        <button class="btn btn-sm btn-light disabled" disabled><?= $_L->T('Deposit','transactions') ?></button>
                                        <button class="btn btn-sm btn-light disabled" disabled><?= $_L->T('Withdrawal','transactions') ?></button>
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

<div id="m-form-deposit" class="d-none">
    <form id="walletDeposit" name="walletRequest" method="post" enctype="multipart/form-data">
        <input type="hidden" name="wallet_id" id="wallet_id" value="0">
        <div class="col-sm-12 mb-3">
            <label for="inputUnit"><span class="text-danger">*</span> <?= $_L->T('Amount','trade') ?> /<small><?= $_L->T('Please_Specify_The_Amount','transactions') ?></small></label>
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
                <small><?= $_L->T('Current_Exchange_Rate','transactions') ?>: <strong class="badge-info px-2" id="walletRate"> </strong></small>
            </div>
        </div>

        <div id="wrapper-gateway">

            <div class="col-sm-12 mb-3" id="receipt">
                <select class="form-control" id="gateway" name="gateway" required>
                    <?php if($gateways) { ?>
                        <option value="0" disabled selected><?= $_L->T('Select_Gateway','gateway') ?></option>
                    <?php foreach ($gateways as $gateway) { ?>
                        <option value="<?= $gateway['path'] ?>"><?= $_L->T($gateway['path'],'gateway') ?></option>
                    <?php } ?>
                    <?php } else { ?>
                        <option value="0" disabled selected><?= $_L->T('NO_Active_Gateway','gateway') ?></option>
                    <?php } ?>
                </select>
            </div>

            <div id="gateway-box"> </div>
        </div>



        <div class="col-sm-12 mb-3" id="comment">
            <label for="inputComment"><?= $_L->T('Comment','note') ?> /<small><?= $_L->T('Optional','general') ?></small></label>
            <textarea class="form-control" type="text" id="comment" name="comment"></textarea>
        </div>
        <hr>
        <div class="col-sm-12">
            <button class="btn btn-primary" type="submit"><?= $_L->T('Submit','general') ?></button>
            <small class="float-right" style="line-height: 33px;">* <?= $_L->T('Correct_Data_note','wallet') ?></small>
            <div id="fRes" class="mt-3 alert" style="display: none;"></div>
        </div>
    </form>
</div>

<div id="m-form-withdrawal" class="d-none">
    <form id="walletWithdrawal" name="walletWithdrawal" method="post" enctype="multipart/form-data">
        <input type="hidden" name="wallet_id" id="wallet_id" value="0">
        <div class="col-sm-12 mb-3">
            <label for="inputUnit"><span class="text-danger">*</span> <?= $_L->T('Amount','trade') ?> /<small><?= $_L->T('Please_Specify_The_Amount','transactions') ?></small></label>
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
                <small><?= $_L->T('Current_Exchange_Rate','transactions') ?>: <strong class="badge badge-info px-2">9.93</strong></small>
            </div>
        </div>
        <div id="wraper-gw"></div>
        <div id="wraper"><div class="col-sm-12 mb-3" id="receipt"><label for="doc"><?= $_L->T('Document','doc') ?> /<small><?= $_L->T('up_to_5_file','doc') ?></small></label><div class="custom-file my-1"><input type="file" class="custom-file-input" id="doc" name="doc[]"><label class="custom-file-label" for="doc"><?= $_L->T('Choose_Your_Receipt','doc') ?></label></div>
                <span style="display: none" data-max="4" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> <?= $_L->T('Add_more','doc') ?></span></div><div class="col-sm-12 mb-3" id="bank"><label for="inputComment"><span class="text-danger">*</span> <?= $_L->T('Bank_Account','transactions') ?> /<small><?= $_L->T('choose_Bank_Account','transactions') ?></small></label><input class="form-control" type="text" id="bankAccount" name="bankAccount" placeholder="Bank Account Number" required=""></div></div>
        <div class="col-sm-12 mb-3" id="comment">
            <label for="inputComment"><?= $_L->T('Comment','note') ?> /<small><?= $_L->T('Optional','general') ?></small></label>
            <textarea class="form-control" type="text" id="comment" name="comment"></textarea>
        </div>

        <hr>
        <div class="col-sm-12">
            <input type="hidden" name="user_id" value="42">
            <button class="btn btn-primary" type="submit"><?= $_L->T('Submit','general') ?></button>
            <small class="float-right" style="line-height: 33px;">* <?= $_L->T('Correct_Data_note','wallet') ?></small>
            <div id="fRes" class="mt-3 alert" style="display: none;"></div>
        </div>
    </form>
</div>

<script>

    // Enable Wallet Type
    $("body").on("click", ".wallet-balance .doA-activeWallet", function(event){
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
                $("#wg-wallet_balance-1 .reload").trigger("click");
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
    $("body").on("click", ".wallet-balance .doM-deposit", function(event){
        event.preventDefault();
        let id = $(this).data('id');
        let sym = $(this).data('sym');
        rate = parseFloat($(this).data('rate'));
        //alert(id);

        //$('#m-form-deposit div.bank-detail').hide();
        //$(`#m-form-deposit div.bank-detail[data-currency="${sym}"]`).show();

        $('#m-form-deposit #walletDeposit #wallet_id').attr("value",id);
        $('#m-form-deposit #walletDeposit #sym').html(sym);
        $('#m-form-deposit #walletDeposit #walletRate').html(rate);
        $('#m-form-deposit #walletDeposit #gateway-box').html('');

        let depositForm = $('#m-form-deposit').html();

        makeModal('Deposit to wallet',depositForm,'lg');
    });
    // Payment Type
    $("body").on("change","form#walletDeposit #gateway", function() {
        let gateway = $(this).val();
        ajaxCall("gateway", "load", {'path':gateway}, function(response) {
            $('form#walletDeposit #gateway-box').html(response);
        });
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
                $("[id^=wg-wallet_requests] .reload").trigger("click");
                setTimeout(function(){
                    $('#modalMain').modal('toggle');
                }, 1500);
            }
        });
    });

    /**
     *  Withdrawal
     */
    $("body").on("click", ".wallet-balance .doM-withdrawal", function(event){
        event.preventDefault();
        let id = $(this).data('id');
        let sym = $(this).data('sym');
        let balance = $(this).data('balance');
        rate = parseFloat($(this).data('rate'));
        // alert(id);
        $('#m-form-withdrawal #walletWithdrawal #wallet_id').attr("value",id);
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
                $("[id^=wg-wallet_requests] .reload").trigger("click");
                setTimeout(function(){
                    $('#modalMain').modal('toggle');
                }, 1200);
            }
        });
    });

</script>