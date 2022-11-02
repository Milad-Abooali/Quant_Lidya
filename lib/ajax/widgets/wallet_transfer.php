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

<div class="row wallet-transfer">
    <div class="col-3">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="nav-link active" data-toggle="pill" href="#to-wallet" role="tab"><?= $_L->T('To_Other_Wallet','wallet') ?></a>
            <a class="nav-link" data-toggle="pill" href="#to-mt5" role="tab"><?= $_L->T('To_MT5','wallet') ?> <sup class="text-muted"><?= $_L->T('Deposit','transactions') ?></sup></a>
            <a class="nav-link" data-toggle="pill" href="#from-mt5" role="tab"><?= $_L->T('From_MT5','wallet') ?> <sup class="text-muted"><?= $_L->T('Withdrawal','transactions') ?></sup></a>
        </div>
    </div>
    <div class="col-9">
        <div class="tab-content" id="v-pills-tabContent">

            <div class="tab-pane fade show active" id="to-wallet" role="tabpanel" aria-labelledby="to-wallet">
                <p><?= $_L->T('To_Other_Wallet_note','wallet') ?></p>

                <form id="toWallet" name="toWallet" method="post" enctype="multipart/form-data">
                    <div class="col-sm-12 mb-3">
                        <label for="inputUnit"><span class="text-danger">*</span> <?= $_L->T('Source','marketing') ?> /<small><?= $_L->T('Source_note','marketing') ?></small></label>
                        <div class="row">
                            <div class="mb-3 col">
                                <select class="form-control" id="s_wallet" name="s_wallet" required>
                                    <option value="0" disabled selected><?= $_L->T('Select_Wallet','wallet') ?></option>
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
                                <?= $_L->T('To','general') ?>
                            </div>
                            <div class="mb-3 col">
                                <input type="number" class="form-control" min="1" step="1" id="d_wallet" name="d_wallet" placeholder="<?= $_L->T('Receiver_Wallet_Id','wallet') ?>" required>
                            </div>
                        </div>
                        <div class="text-center">
                            <small><?= $_L->T('Wallet_Currency_note','wallet') ?></small>
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
                        <small class="float-right" style="line-height: 33px;">* <?= $_L->T('Correct_Data_note','wallet') ?></small>
                        <div id="fRes" class="mt-3 alert" style="display: none;"></div>
                    </div>
                </form>

            </div>

            <div class="tab-pane fade" id="to-mt5" role="tabpanel" aria-labelledby="to-mt5">
                <p><?= $_L->T('To_MT5_note','wallet') ?></p>
                <?php if($tp_accounts) { ?>
                    <form id="toMT5" name="toMT5" method="post" enctype="multipart/form-data">
                        <div class="col-sm-12 mb-3">
                            <label for="inputUnit"><span class="text-danger">*</span>  <?= $_L->T('Source','marketing') ?> /<small><?= $_L->T('Source_note','marketing') ?></small></label>
                            <div class="row">
                                <div class="mb-3 col">
                                    <select class="form-control" id="s_wallet" name="s_wallet" required>
                                        <option value="0" disabled selected><?= $_L->T('Select_Wallet','wallet') ?></option>
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
                                        <option value="0" disabled selected><?= $_L->T('Select_MT5_Login','wallet') ?></option>
                                        <?php if ($tp_accounts) foreach($tp_accounts as $account) { ?>
                                            <option data-sym="<?= GF::getLoginDetails($account['login'])['Currency'] ?>" value="<?php echo $account['login']; ?>"><?php echo $account['login']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="text-center">
                                <small><?= $_L->T('Current_Exchange_Rate','transactions') ?>: <strong class="badge-info px-2" id="walletRate">1.00</strong></small>
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
                            <button class="btn btn-primary" type="submit"><?= $_L->T('Submit','general') ?></button>
                            <small class="float-right" style="line-height: 33px;">* <?= $_L->T('Correct_Data_note','wallet') ?></small>
                            <div id="fRes" class="mt-3 alert" style="display: none;"></div>
                        </div>
                    </form>
                <?php } else { ?>
                    <p class="alert alert-danger"><?= $_L->T('No_MT5_Real_note','wallet') ?></p>
                <?php } ?>
            </div>

            <div class="tab-pane fade" id="from-mt5" role="tabpanel" aria-labelledby="from-mt5">
                <p><?= $_L->T('From_MT5_note','wallet') ?></p>
                <?php if($tp_accounts) { ?>
                    <form id="fromMT5" name="fromMT5" method="post" enctype="multipart/form-data">
                        <div class="col-sm-12 mb-3">
                            <label for="inputUnit"><span class="text-danger">*</span>  <?= $_L->T('Source','marketing') ?> /<small><?= $_L->T('Source_note','marketing') ?></small></label>
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
                                    <?= $_L->T('To','general') ?>
                                </div>
                                <div class="mb-3 col">
                                    <select class="form-control" id="s_wallet" name="s_wallet" required>
                                        <option value="0" disabled selected><?= $_L->T('Select_Wallet','wallet') ?></option>
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
                                <small><?= $_L->T('Current_Exchange_Rate','transactions') ?>: <strong class="badge-info px-2" id="walletRate">1.00</strong></small>
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
                            <button class="btn btn-primary" type="submit"><?= $_L->T('Submit','general') ?></button>
                            <small class="float-right" style="line-height: 33px;">* <?= $_L->T('Correct_Data_note','wallet') ?></small>
                            <div id="fRes" class="mt-3 alert" style="display: none;"></div>
                        </div>
                    </form>                            <?php } else { ?>
                    <p class="alert alert-danger"><?= $_L->T('No_MT5_Real_note','wallet') ?></p>
                <?php } ?>
            </div>

        </div>
    </div>
</div>


<script>
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
    $("body").on("change",'.wallet-transfer select[name="s_wallet"]', function(e) {
        s_sym = $(this).children("option:selected").data('sym');
        let data = {
            id: $(this).val()
        }
        ajaxCall ('wallet', 'getWallet', data, function(response) {
            let resObj = JSON.parse(response);
            $('.wallet-transfer input[name="amountTransfer"]').attr("max",resObj.balance);
            $('.wallet-transfer .symTransfer').html(s_sym);
            if(s_sym && d_sym) updateExchangeRate(s_sym, d_sym);
        });
    });
    $("body").on("change",'.wallet-transfer select[name="d_mt5"]', function(e) {
        d_sym = $(this).children("option:selected").data('sym');
        $('.wallet-transfer .symMT5').html(d_sym);
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
                $("[id^=wg-wallet_balance] .reload").trigger("click");
                $("[id^=wg-wallet_transactions] .reload").trigger("click");
            }
        });
    });


    // Submit from MT5
    $("body").on("submit","form#fromMT5", function(e) {
        e.preventDefault();
        $('.wallet-transfer input[name="amountTransfer"]').attr("max",9999999999);
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
                $("[id^=wg-wallet_balance] .reload").trigger("click");
                $("[id^=wg-wallet_transactions] .reload").trigger("click");
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
                $("[id^=wg-wallet_balance] .reload").trigger("click");
                $("[id^=wg-wallet_transactions] .reload").trigger("click");
            }
        });
    });


</script>