<?php

    // LangMan
    global $_L;

    $user_id = ($_POST['user_id']) ?? $_SESSION['id'];

    require_once "autoload/transaction.php";
    $Transaction = new Transaction();

    $is_pending_transaction = $Transaction->checkTransactionWaiting($user_id);

    global $db;
    $where = "status=1";
    $gateways = $db->select('payment_gateways',$where, '*',null,'id ASC');

    if($_SESSION['unit'] === 'Turkish') {
        global $db;
        $where = "Symbol='USDTRY'";
        $usdtry_rate = $db->selectRow('lidyapar_mt5.mt5_prices', $where)['AskLast'];
    }

    if($is_pending_transaction) {

        $pending_transaction = $Transaction->getTransactionWaiting($user_id);
        $docs = $Transaction->loadDocs($pending_transaction['id']);


        if(($pending_transaction['type']=='Deposit') && (in_array($pending_transaction['source'],[7,9])) && $pending_transaction['status']=='Payment') {
            $where = "status=1 AND transactions_id=".$pending_transaction['id'];
            $payment = $db->selectRow('payment_orders',$where);
            if ($payment) {
                // Desk
                // Finance

                // Status
                $Transaction->setStatus($pending_transaction['id'],'Pending');
                $Transaction->verify($pending_transaction['id'], $_SESSION['id'], 'desk_verify');
                $Transaction->verify($pending_transaction['id'], $_SESSION['id'], 'finance_verify');
                $Transaction->addComment($pending_transaction['id'], 'Payment recived by credit card. 
                 <br> Order ID: '.$payment['id']);
                $pending_transaction['status'] = 'Pending';
                $pending_transaction['desk_verify']=1;
            }
        }

    ?>


<p><?= $_L->T('Active_Eequest','transactions') ?></p>
        <div id="tActive" class="card mini-stat border-secondary">
            <div class="card-body mini-stat-img">
                <div class="row">
                    <div class="col">
                        <h6 class="text-uppercase mb-3 text-secondary"><?= $_L->T('Type','general') ?></h6>
                        <h4 class="mb-4 text-primary"><?= $pending_transaction['type'] ?></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-uppercase mb-3 text-secondary"><?= $_L->T('Amount','trade') ?></h6>
                        <h4 class="mb-4">$ <?= $pending_transaction['amount'] ?></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-uppercase mb-3 text-secondary"><?= $_L->T('Status','general') ?></h6>
                        <h4 class="mb-4 text-warning"><?= $pending_transaction['status'] ?></h4>
                    </div>
                </div>

                <?php if($pending_transaction['type']=='Deposit' && in_array($pending_transaction['source'],[7,9])  && $pending_transaction['status']=='Payment') { ?>
                    <div class="float-left">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-money-bill mr-2"></i> <span id="selected-gateway"><?= $db->selectId('payment_gateways',$pending_transaction['source'],'name')['name']; ?></span></span>
                            </div>
                            <input type="button" data-tid="<?= $pending_transaction['id'] ?>"  data-now="<?= strtotime("now") ?>"  data-expire="<?= strtotime($pending_transaction['created_at']) + (15 * 60) ?>" data-amount="<?= $pending_transaction['amount'] ?>" data-gateway="<?= $pending_transaction['source'] ?>" class="doM-payNow btn btn-sm btn-outline-success px-3" value="<?= $_L->T('Pay_Now','transactions') ?>">
                        </div>
                    </div>
                <?php } ?>


                <?php if(!$pending_transaction['desk_verify']) {  ?>
                <button data-tid="<?= $pending_transaction['id'] ?>" class="doA-cancel float-right small btn btn-sm btn-outline-danger"><i class="fa fa-minus-circle mr-2"></i> <?= $_L->T('Cancel','general') ?></button>
                <?php } else { ?>
                    <span class="float-right small btn btn-sm btn-outline-secondary" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?= $_L->T('Cancel_cant','transactions') ?>"><i class="fa fa-minus-circle mr-2"></i> <?= $_L->T('Cancel','general') ?></span>
                <?php }  ?>


            </div>
        </div>

        <div class="card border-secondary py-2">
            <small class="mx-auto"><?= ($docs) ? count($docs) : 'No' ?> <?= $_L->T('Attachment','doc') ?></small>
            <div class="card-deck overflow-auto row" style="max-height: 150px;">
                <?php if ($docs) foreach ($docs as $doc) { ?>
                    <div class="col-6">
                        <div class="mt-1 card-body text-center">
                            <span><?= $doc['created_at'] ?></span>
                            <br>
                            <a href="media/transaction/<?= $doc['filename'] ?>" target="_blank"><img src="media/transaction/<?= $doc['filename'] ?>" class="img-thumbnail w-25"></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

            <form id="tUpdate" name="tUpdate" method="post" enctype="multipart/form-data">
                <div class="col-sm-12 mb-3" id="receipt">
                    <label for="doc"><?= $_L->T('New_Document','doc') ?> /<small><?= $_L->T('New_up_to','transactions',3) ?></small></label>
                    <div class="custom-file my-1">
                        <input type="file" class="custom-file-input" id="doc" name="doc[]">
                        <label class="custom-file-label" for="doc"><?= $_L->T('Choose_Receipt','transactions') ?></label>
                    </div>
                    <span style="display: none" data-max="2" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> <?= $_L->T('Add_more','doc') ?></span>
                </div>
                <div class="col-sm-12 mb-3" id="comment">
                    <label for="inputComment"><?= $_L->T('Comment','note') ?> /<small><?= $_L->T('Optional','general') ?></small></label>
                    <textarea minlength="3" class="form-control" type="text" id="comment" name="comment"></textarea>
                </div>
                <div class="col-sm-12">
                    <input type="hidden" name="transaction_id" value="<?= $pending_transaction['id'] ?>">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <button class="btn btn-primary"  type="submit"><?= $_L->T('Submit','general') ?></button>
                    <small class="float-right" style="line-height: 33px;">* <?= $_L->T('data_note','transactions') ?></small>
                    <div id="fRes" class="mt-3 alert" style="display: none;"></div>
                </div>
            </form>

<?php
    }
    else {
        global $db;
        $where = "user_id=$user_id AND group_id=2";
        $tp_accounts = $db->select('tp', $where);
        if ($tp_accounts) {
?>

    <form id="tRequest" name="tRequest" method="post" enctype="multipart/form-data">
        <div class="form-row px-2">
            <div class="col-sm-6 my-3">
                <label for="inputType"><span class="text-danger">*</span> <?= $_L->T('Type','general') ?></label>
                <select class="form-control" id="transferType" name="type" required>
                    <option value="deposit" selected><?= $_L->T('Deposit','transactions') ?></option>
                    <option value="withdraw"><?= $_L->T('Withdraw','transactions') ?></option>
                </select>
            </div>
            <div class="col-sm-6 my-3">
                <label for="inputType"><span class="text-danger">*</span> <?= $_L->T('Trading_Accounts','trade') ?></label>
                <select class="form-control" id="logintp" name="tp" required>
                    <?php if ($tp_accounts) foreach($tp_accounts as $key => $account) {?>
                        <option value="<?php echo $account['login']; ?>" <?= ($key === array_key_first($tp_accounts)) ? 'selected' : '' ?>><?php echo $account['login']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12 mb-3">
            <label for="inputUnit"><span class="text-danger">*</span> <?= $_L->T('Amount','trade') ?> /<small><?= $_L->T('Please_specify_the_amount','transactions') ?></small></label>
            <?php if($_SESSION['unit'] === 'Turkish') {

                global $db;
                $where ="Symbol='USDTRY'";
                $usdtry_rate = $db->selectRow('lidyapar_mt5.mt5_prices',$where)['AskLast'];

                ?>
                <div class="row">
                    <div class="input-group mb-3 col">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-gradient-info text-white strong" id="basic-addon1">₺</span>
                        </div>
                        <input type="number" class="form-control" type="number" min="0.00" max="100000.00" step="0.01" id="transferAmountTL" name="amounttl" placeholder="0,00">
                    </div>
                    <div class="pt-2 col-1">
                        =
                    </div>
                    <div class="input-group mb-3 col">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-gradient-primary text-white strong" id="basic-addon1">$</span>
                        </div>
                        <input type="number" class="form-control" type="number" min="0.00" max="10000.00" step="0.01" id="transferAmount" name="amount" placeholder="0,00">
                    </div>
                </div>
                <div class="text-center">
                    <small><?= $_L->T('Current_Exchange_Rate','transactions') ?>: <strong class="badge badge-info px-2"><?= number_format($usdtry_rate, 2, '.', '') ?></strong></small>
                </div>
            <?php } else { ?>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">$</span>
                    </div>
                    <input type="number" class="form-control" type="number" min="0.00" max="10000.00" step="0.01" id="transferAmount" name="amount" placeholder="0,00">
                </div>
            <?php }  ?>
        </div>
        <div id="wraper-gw">

        </div>
        <div id="wraper">

        </div>
        <div class="col-sm-12 mb-3" id="comment">
            <label for="inputComment"><?= $_L->T('Comment','note') ?> /<small><?= $_L->T('Optional','general') ?></small></label>
            <textarea class="form-control" type="text" id="comment" name="comment"></textarea>
        </div>

        <hr>
        <div class="col-sm-12">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            <button class="btn btn-primary" type="submit"><?= $_L->T('Submit','general') ?></button>
            <small class="float-right" style="line-height: 33px;">* <?= $_L->T('data_note','transactions') ?></small>
            <div id="fRes" class="mt-3 alert" style="display: none;"></div>
        </div>
    </form>


<?php
        } else {
           echo ' <p class="alert alert-warning">'.$_L->T('no_trad_account','transactions').'</p>';
        }
    }
?>


    <script>
<?php if($_SESSION['unit'] === 'Turkish') { ?>
        // turkish amount
        var usdTry = <?= number_format($usdtry_rate, 2, '.', '') ?>;

        $("body").on("change keyup","#transferAmountTL", function() {
            $('#transferAmount').addClass('bg-success');
            $('#transferAmount').val(($(this).val()/usdTry).toFixed(2));
            setTimeout(function() {
                $('#transferAmount').removeClass('bg-success');
            }, 300);
        });
        $("body").on("change keyup","#transferAmount", function() {
            $('#transferAmountTL').addClass('bg-success');
            $('#transferAmountTL').val(($(this).val()*usdTry).toFixed(2));
            setTimeout(function() {
                $('#transferAmountTL').removeClass('bg-success');
            }, 300);
        });

        <?php } ?>
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
            '<span class="text-danger">*</span> <?= $_L->T('pay_next_step','transactions') ?>' +
            '</p>';
        var doc_d =
            '<div class="col-sm-12 mb-3" id="receipt">' +
            '<label for="doc"><span class="text-danger">*</span> <?= $_L->T('Document','doc') ?> /<small><?= $_L->T('New_up_to','transactions',5) ?></small></label>' +
            '<div class="custom-file my-1">' +
            '<input type="file" class="custom-file-input" id="doc" name="doc[]" required>' +
            '<label class="custom-file-label" for="doc"><?= $_L->T('Choose_your_Receipt','transactions') ?></label>' +
            '</div>' +
            '<span style="display: none" data-max="4" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> <?= $_L->T('Add_more','doc') ?></span>' +
            '</div>';
        var doc_w =
            '<div class="col-sm-12 mb-3" id="receipt">' +
            '<label for="doc"><?= $_L->T('Document','doc') ?> /<small><?= $_L->T('New_up_to','transactions',5) ?></small></label>' +
            '<div class="custom-file my-1">' +
            '<input type="file" class="custom-file-input" id="doc" name="doc[]">' +
            '<label class="custom-file-label" for="doc"><?= $_L->T('Choose_your_Receipt','transactions') ?></label>' +
            '</div>' +
            '<span style="display: none" data-max="4" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> <?= $_L->T('Add_more','doc') ?></span>' +
            '</div>';
        var bank =
            '<div class="col-sm-12 mb-3" id="bank">' +
            '<label for="inputComment"><span class="text-danger">*</span> <?= $_L->T('Bank_Account','transactions') ?> /<small><?= $_L->T('choose_Bank_Account','transactions') ?></small></label>' +
            '<input class="form-control" type="text" id="bankAccount" name="bankAccount" placeholder="<?= $_L->T('Bank_Account_Number','transactions') ?>" required>' +
            '</div>';

        $('form#tRequest #wraper-gw').html(gw_d);
        $('form#tRequest #wraper').html(doc_d);

        // Gateway
        $("body").on("change","form#tRequest #gateway", function() {
            let gateway = $('#gateway').val();
            if (gateway==='7') $('form#tRequest #wraper').html(cc_info);
            if (gateway==='9') $('form#tRequest #wraper').html(cc_info);
            if (gateway==='1') $('form#tRequest #wraper').html(doc_d);
        });

        function detectCardType(number) {
            var re = {
                electron: /^(4026|417500|4405|4508|4844|4913|4917)\d+$/,
                maestro: /^(5018|5020|5038|5612|5893|6304|6759|6761|6762|6763|0604|6390)\d+$/,
                dankort: /^(5019)\d+$/,
                interpayment: /^(636)\d+$/,
                unionpay: /^(62|88)\d+$/,
                visa: /^4[0-9]{12}(?:[0-9]{3})?$/,
                mastercard: /^5[1-5][0-9]{14}$/,
                amex: /^3[47][0-9]{13}$/,
                diners: /^3(?:0[0-5]|[68][0-9])[0-9]{11}$/,
                discover: /^6(?:011|5[0-9]{2})[0-9]{12}$/,
                jcb: /^(?:2131|1800|35\d{3})\d{11}$/
            }
            for(let key in re) {
                if(re[key].test(number)) {
                    return key
                }
            }
        }
        
        var oAmount;
        // Check Type
        $("body").on("change keyup","form#cc-pay #card-number", function() {
            oAmount = (oAmount > 0) ? oAmount : $('form#cc-pay #amount_o').val();
            const ccNumber = $(this).val();
            const ccType = detectCardType(ccNumber);
            let ccimg = (ccType) ? '<img src="/assets/images/cc-logo/'+ccType+'.png" class="img-thumbnail">' : '';
            $('#cc-type').html(ccimg);
            let ccTypeId = 0;
            if (ccType == 'visa' || ccType == 'electron') ccTypeId = 1;
            if (ccType == 'mastercard') ccTypeId = 2;
            $('#cardType').val(ccTypeId);
            ajaxCall('gateway', 'rateUSDTRY', '', function(res) {
                let resObj = JSON.parse(res);
                if (resObj.res) {
                    let amount_o = oAmount * resObj.res;
                    $('form#cc-pay #amount_o').val(Number(amount_o).toFixed(2));
                    $('form#cc-pay #USDTRY').val(Number(resObj.res).toFixed(2));
                }
            });
        });
        // Check Type
        $("body").on("change","form#tRequest #transferType", function() {
            if ($(this).val() == 'deposit') {
                $('form#tRequest #wraper-gw').html(gw_d);
                $('form#tRequest #wraper').html(doc_d);
            } else {
                $('form#tRequest #wraper-gw').html('');
                $('form#tRequest #wraper').html(doc_w + bank);
            }
        });

        var bankCheck;
        // Submit Request
        $("body").on("click","#tActive .doM-payNow", function(e) {
            if ($(this).data('now') > $(this).data('expire')) {
                const data = {
                    'transaction_id': $(this).data('tid')
                }
                ajaxCall ('transaction', 'expire', data, function(response){
                    let resObj = JSON.parse(response);
                    if (resObj.e) {
                        console.log(resObj);
                    } else {
                        alert ('<?= $_L->T('request_expired','transactions') ?>')
                        setTimeout(function(){
                            $("#wg-transaction .reload").trigger("click");
                        }, 50);
                    }
                });
                return;
            }
            const orderData = {
                transactions_id: $(this).data('tid'),
                user_id: <?= $_SESSION['id'] ?>,
                amount: (parseFloat($(this).data('amount'))*parseFloat(<?= $usdtry_rate ?>) ).toFixed(2),
                gateway_id: $(this).data('gateway'),
                USDTRY: '<?= $usdtry_rate ?>'
            }
            ajaxCall('transaction', 'orderAdd', orderData, function(orderRes){
                let orderResObj = JSON.parse(orderRes);
                if (orderResObj.res > 0) {
                    console.log(orderResObj);

                    let func_data = {
                        'amount': orderData.amount,
                        'id': orderResObj.res
                    };
                    const selected_gateway = $('#selected-gateway').html().toLowerCase();
                    let data = {
                        'GW': selected_gateway,
                        'FUNC': 'paymentLink',
                        'DATA': func_data
                    };

                    ajaxCall('gateway', 'gatewayDo', data, function (gwResponse) {
                        let gwResObj = JSON.parse(gwResponse);
                        setTimeout(function() {
                            window.open(gwResObj.link,'_blank');
                        }, 1000);
                        bankCheck = setInterval(function() {
                            // Check Result
                            bankResp(orderResObj.res);
                        }, 1500);
                    });
                }
            });
            let body = `<div id="check-bank" class="text-center"><i class="fa fa-spinner fa-spin text-secondary fa-4x"></i></center>`;
            makeModal('Check Payment', body);
        });


        // Bank response
        function bankResp(orderID) {
            let orderData = {
                id: orderID
            };
            ajaxCall('transaction', 'orderCheck', orderData, function(bankRes){
                let bankResObj = JSON.parse(bankRes);
                console.log('Check Bank:'+bankResObj.res);
                if (bankResObj.res==1) {
                    const resHTML =
                        '            <div class="card card-bank bg-light text-center p-4">' +
                        '            <i class="fa fa-cart text-success fa-4x"></i><hr>' +
                        '            <p class="border-bottom border-success">Transaction Done.</p>' +
                        '            </div>';
                    clearInterval(bankCheck);
                    $( "#check-bank" ).html(resHTML);
                    $("#wg-transaction .reload").trigger("click");
                } else if (bankResObj.res > 0) {
                    const resHTML =
                        '            <div class="card card-bank bg-light text-center p-4">' +
                        '            <i class="fa fa-cart text-danger fa-4x"></i><hr>' +
                        '            <p class="border-bottom border-danger">Error, Code: '+bankResObj.res+'</p>' +
                        '            </div>';
                    $( "#check-bank" ).html(resHTML);
                }
            });
        }


// Submit Request
        $("body").on("submit","form#tRequest", function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            ajaxForm ('transaction', 'add', formData, function(response){
                let resObj = JSON.parse(response);
                const fResp = $("#tRequest #fRes");
                if (resObj.e) {
                    fResp.addClass('alert-warning');
                    fResp.fadeIn();
                    fResp.html('Error, Please Check Inputs!');
                }
                if (resObj.res) {
                    fResp.addClass('alert-success');
                    fResp.fadeIn();
                    fResp.html('<?= $_L->T('Request_Added','transactions') ?>');
                    setTimeout(function(){
                        $("#wg-transaction .reload").trigger("click");
                    }, 1500);
                }
            });
        });

        // Submit Update
        $("body").on("submit","form#tUpdate", function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            ajaxForm ('transaction', 'update', formData, function(response){
                let resObj = JSON.parse(response);
                const fResp = $("#tUpdate #fRes");
                if (resObj.e) {
                    fResp.addClass('alert-warning');
                    fResp.fadeIn();
                    fResp.html('<?= $_L->T('Check_Inputs','transactions') ?>');
                }
                if (resObj.res) {
                    fResp.addClass('alert-success');
                    fResp.fadeIn();
                    fResp.html('<?= $_L->T('Request_Added','transactions') ?>');
                    setTimeout(function(){
                        $("#wg-transaction .reload").trigger("click");
                    }, 1500);
                }
            });
        });

        // Submit Request
        $("body").on("click","#tActive .doA-cancel", function() {
            const data = {
                'transaction_id': $(this).data('tid')
            }
            ajaxCall ('transaction', 'cancel', data, function(response){
                let resObj = JSON.parse(response);
                if (resObj.e) {
                    console.log(resObj);
                } else {
                    setTimeout(function(){
                        $("#wg-transaction .reload").trigger("click");
                    }, 50);
                }
            });
        });
        setInterval(function(){
            $("#wg-transaction .reload").trigger("click");
        }, 1000*60*3);

    </script>