<?php

    $user_id = $_POST[2]['uid'];

    require_once "autoload/transaction.php";
    $Transaction = new Transaction();

    $is_pending_transaction = $Transaction->checkTransactionWaiting($user_id);

    global $db;
    $where = "status=1";
    $gateways = $db->select('payment_gateways',$where, '*',null,'id ASC');


    if($is_pending_transaction) {

        $pending_transaction = $Transaction->getTransactionWaiting($user_id);
        $docs = $Transaction->loadDocs($pending_transaction['id']);


        if(($pending_transaction['type']=='Deposit') && ($pending_transaction['source']==3) && $pending_transaction['status']=='Payment') {
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


<p><?= $_L->T('You_Have_An_Active_Pending_Request','transactions') ?>.</p>
        <div id="tActive" class="card mini-stat border-secondary">
            <div class="card-body mini-stat-img">
                <div class="row">
                    <div class="col">
                        <h6 class="text-uppercase mb-3 text-secondary">Type</h6>
                        <h4 class="mb-4 text-primary"><?= $pending_transaction['type'] ?></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-uppercase mb-3 text-secondary">Amount</h6>
                        <h4 class="mb-4">$ <?= $pending_transaction['amount'] ?></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-uppercase mb-3 text-secondary">Status</h6>
                        <h4 class="mb-4 text-warning"><?= $pending_transaction['status'] ?></h4>
                    </div>
                </div>

                <?php if($pending_transaction['type']=='Deposit' && $pending_transaction['source']==3  && $pending_transaction['status']=='Payment') { ?>
                    <div class="float-left">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-money-bill mr-2"></i> <?= $db->selectId('payment_gateways',$pending_transaction['source'],'name')['name']; ?></span>
                            </div>
                        </div>
                    </div>
                <?php } ?>


                <?php if(!$pending_transaction['desk_verify']) {  ?>
                <button data-tid="<?= $pending_transaction['id'] ?>" class="doA-cancel float-right small btn btn-sm btn-outline-danger"><i class="fa fa-minus-circle mr-2"></i> Cancel</button>
                <?php } else { ?>
                    <span class="float-right small btn btn-sm btn-outline-secondary" data-toggle="tooltip" data-placement="right" title="" data-original-title="Your request in process, You cant cancel"><i class="fa fa-minus-circle mr-2"></i> Cancel</span>
                <?php }  ?>


            </div>
        </div>

        <div class="card border-secondary py-2">
            <small class="mx-auto"><?= ($docs) ? count($docs) : 'No' ?> Attachment</small>
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
                    <label for="doc">New Document /<small>Up to 3 file</small></label>
                    <div class="custom-file my-1">
                        <input type="file" class="custom-file-input" id="doc" name="doc[]">
                        <label class="custom-file-label" for="doc">Choose your Receipt</label>
                    </div>
                    <span style="display: none" data-max="2" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> Add more</span>
                </div>
                <div class="col-sm-12 mb-3" id="comment">
                    <label for="inputComment">Comment /<small>Optional</small></label>
                    <textarea minlength="3" class="form-control" type="text" id="comment" name="comment"></textarea>
                </div>
                <div class="col-sm-12">
                    <input type="hidden" name="transaction_id" value="<?= $pending_transaction['id'] ?>">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <button class="btn btn-primary"  type="submit">Submit</button>
                    <small class="float-right" style="line-height: 33px;">* Please make sure all the entered data are correct.</small>
                    <div id="fRes" class="mt-3 alert" style="display: none;"></div>
                </div>
            </form>

<?php
    } else {
        global $db;
        $where = "user_id=$user_id AND group_id=2";
        $tp_accounts = $db->select('tp', $where);
        if ($tp_accounts) {
?>

    <form id="tRequest" name="tRequest" method="post" enctype="multipart/form-data">
        <div class="form-row col-sm-12">
            <div class="col-sm-6 my-3">
                <label for="inputType"><span class="text-danger">*</span> Type</label>
                <select class="form-control" id="transferType" name="type" required>
                    <option value="deposit" selected>Deposit</option>
                    <option value="withdraw">Withdraw</option>
                </select>
            </div>
            <div class="col-sm-6 my-3">
                <label for="inputType"><span class="text-danger">*</span> Trading Accounts</label>
                <input class="form-control" id="logintp" name="tp" value="<?= $_POST[2]['tpid'] ?>" readonly>
            </div>
        </div>
        <div class="col-sm-12 mb-3">
            <label for="inputUnit"><span class="text-danger">*</span> Amount /<small>Please specify the amount</small></label>
            <?php if(in_array($_SESSION['unitn'], array(1,3,4,5,6,7,8))) {

                global $db;
                $where ="Symbol='USDTRY'";
                $usdtry_rate = $db->selectRow('lidyapar_mt5.mt5_prices',$where)['AskLast'];

                ?>
                <div class="row">
                    <div class="input-group mb-3 col">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-gradient-info text-white strong" id="basic-addon1">₺</span>
                        </div>
                        <input type="number" class="form-control" type="number" min="0.00" max="16105500.00" step="0.01"
                               id="transferAmountTL" name="amounttl" placeholder="0,00">
                    </div>
                    <div class="pt-2 col-1">
                        =
                    </div>
                    <div class="input-group mb-3 col">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-gradient-primary text-white strong" id="basic-addon1">$</span>
                        </div>
                        <input type="number" class="form-control" type="number" min="0.00" max="500000.00" step="0.01"
                               id="transferAmount" name="amount" placeholder="0,00">
                    </div>
                </div>
                <div class="text-center">
                    <small>Current Exchange Rate: <strong class="badge badge-info px-2"><?= number_format($usdtry_rate, 2, '.', '') ?></strong></small>
                </div>
            <?php } else { ?>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">$</span>
                    </div>
                    <input type="number" class="form-control" type="number" min="0.00" max="20000.00" step="0.01"
                           id="transferAmount" name="amount" placeholder="0,00">
                </div>
            <?php }  ?>
        </div>
        <div id="wraper-gw">

        </div>
        <div id="wraper">

        </div>
        <div class="col-sm-12 mb-3" id="comment">
            <label for="inputComment">Comment /<small>Optional</small></label>
            <textarea class="form-control" type="text" id="comment" name="comment"></textarea>
        </div>

        <hr>
        <div class="col-sm-12">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            <button class="btn btn-primary" type="submit">Submit</button>
            <small class="float-right" style="line-height: 33px;">* Please make sure all the entered data are correct.</small>
            <div id="fRes" class="mt-3 alert" style="display: none;"></div>
        </div>
    </form>


<?php
        } else {
           echo ' <p class="alert alert-warning">You dont have any trading account !</p>';
        }
    }
?>


    <script>
<?php if(in_array($_SESSION['unitn'], array(1,3,4,5,6,7,8))) { ?>
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

        $('form#tRequest #wraper-gw').html(gw_d);
        $('form#tRequest #wraper').html(doc_d);

        // Gateway
        $("body").on("change","form#tRequest #gateway", function() {
            let gateway = $('#gateway').val();
            if (gateway!=1) $('form#tRequest #wraper').html(cc_info);
            if (gateway==1) $('form#tRequest #wraper').html(doc_d);
            if (gateway==7) $('form#tRequest #wraper').html(doc_d);
            if (gateway==8) $('form#tRequest #wraper').html(doc_d);
            if (gateway==9) $('form#tRequest #wraper').html(doc_d);
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

        // Check Type
        $("body").on("change keyup","form#cc-pay #card-number", function() {
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
                    let amount_o = $('form#cc-pay #amount_o').val() * resObj.res;
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

        // Submit Request
        $("body").on("click","#tActive .doM-payNow", function(e) {

            let body =
                '    <div class="row d-flex justify-content-center">' +
                '        <div class="col-sm-12"><form id="cc-pay" action="gateways/halkbank/3d.php" method="post">' +
                '            <div class="card card-bank bg-light text-center p-4" style="display:none;">' +
                '            <i class="fa fa-spinner fa-spin text-secondary fa-4x"></i><hr>' +
                '            <p class="border-bottom border-success">Wait for bank response ...</p>' +
                '            </div>' +
                '            <div class="card">' +
                '                <div class="card-body">' +
                '                    <div class="row">' +
                '                        <div class="col-sm-12">' +
                '                        <div id="saved-cards"></div>' +
                '                            <div class="form-group">' +
                '                                <div class="float-right"><input class="form-check-input" type="checkbox" id="rsave" name="save" value="1">' +
                '                                <label class="form-check-label" for="rsave">Save Card</label></div>' +
                '                                <label for="name">Name</label>' +
                '                                <input class="form-control" id="name" name="holder" type="text" placeholder="Enter your name" required >' +
                '                            </div>' +
                '                        </div>' +
                '                    </div>' +
                '                    <div class="row">' +
                '                        <div class="col-sm-12">' +
                '                            <div class="form-group">' +
                '                                <label for="ccnumber">Credit Card Number</label>' +
                '                                <div class="input-group">' +
                '                                    <input class="form-control" type="text" id="card-number" name="card" placeholder="0000 0000 0000 0000" autocomplete="email" required >' +
                '                                    <div class="input-group-append">' +
                '                                        <span class="input-group-text">' +
                '                                            <i class="mdi mdi-credit-card"></i>' +
                '                                        </span>' +
                '                                    </div>' +
                '                                </div>' +
                '                            </div>' +
                '                        </div>' +
                '                    </div>' +
                '                    <div id="cc-type" class="p-3 text-center"></div>' +
                '                    <div class="row">' +
                '                        <div class="form-group col-sm-4">' +
                '                            <label for="ccmonth">Month</label>' +
                '                            <select class="form-control" id="ccmonth" name="exp_mm" required >' +
                '                                <option>01</option>' +
                '                                <option>02</option>' +
                '                                <option>03</option>' +
                '                                <option>04</option>' +
                '                                <option>05</option>' +
                '                                <option>06</option>' +
                '                                <option>07</option>' +
                '                                <option>08</option>' +
                '                                <option>09</option>' +
                '                                <option>10</option>' +
                '                                <option>11</option>' +
                '                                <option>12</option>' +
                '                            </select>' +
                '                        </div>' +
                '                        <div class="form-group col-sm-4">' +
                '                            <label for="ccyear">Year</label>' +
                '                            <select class="form-control" id="ccyear" name="exp_yy" required >' +
                '                                <option>2014</option>' +
                '                                <option>2015</option>' +
                '                                <option>2016</option>' +
                '                                <option>2017</option>' +
                '                                <option>2018</option>' +
                '                                <option>2019</option>' +
                '                                <option>2020</option>' +
                '                                <option>2021</option>' +
                '                                <option>2022</option>' +
                '                                <option>2023</option>' +
                '                                <option>2024</option>' +
                '                                <option>2025</option>' +
                '                                <option>2026</option>' +
                '                                <option>2027</option>' +
                '                                <option>2028</option>' +
                '                                <option>2029</option>' +
                '                                <option>2030</option>' +
                '                            </select>' +
                '                        </div>' +
                '                        <div class="col-sm-4">' +
                '                            <div class="form-group">' +
                '                                <label for="cvv">CVV/CVC</label>' +
                '                                <input class="form-control" id="cvv" type="text" placeholder="123" name="cvv" required >' +
                '                            </div>' +
                '                        </div>' +
                '                    </div>' +
                '                </div>' +
                '                <div class="card-footer">' +
                '                    <input type="hidden" name="gateway_id" value="3" required >' +
                '                    <input type="hidden" id="amount_o" name="amount" value="<?= $pending_transaction["amount"] ?? 0 ?>" required >' +
                '                    <input type="hidden" name="user_id" value="<?= $pending_transaction["user_id"] ?? 0 ?>" required >' +
                '                    <input type="hidden" name="transactions_id" value="<?= $pending_transaction["id"] ?? 0 ?>" required >' +
                '                    <input type="hidden" id="order_id" name="order_id" value="" required >' +
                '                    <input type="hidden" id="cc_id" name="cc_id" value="" required >' +
                '                    <input type="hidden" id="USDTRY" name="USDTRY" value="" required >' +
                '                    <input type="hidden" id="cardType" name="cardType" value="" required >' +
                '                    <button class="doA-ccSave btn btn-sm btn-primary float-right" type="submit">' +
                '                        <i class="mdi mdi-gamepad-circle"></i> Continue <i class="fa fa-angle-right"></i> </button>' +
                '                    <button class="btn btn-sm btn-danger" type="reset"><i class="mdi mdi-lock-reset"></i> Clear</button>' +
                '                </div>' +
                '            </div>' +
                '     </form></div>' +
                '</div>';
            makeModal('Payment', body);
            $('#saved-cards').html('');
            ajaxCall('transaction', 'loadCC', 'user_id=<?= $_SESSION['id'] ?>', function(orderRes){
                let orderResObj = JSON.parse(orderRes);
                savedCC = orderResObj.res;
                if (savedCC) {
                    $.each( savedCC, function( k, v ) {
                        const ccType = detectCardType(v.number);
                        let ccimg = (ccType) ? '<img src="/assets/images/cc-logo/'+ccType+'.png" class="d-inline" style="max-height: 40px; max-width: 40px;">' : '';
                        let card =  '<div id="card-'+v.id+'" class="alert alert-info row mx-2" class="mb-1">'+
                                    '<div class="col-md-4"><button type="button" data-id="'+v.id+'" data-num="'+v.number+'" class="btn doA-removeCC mr-2 text-danger"><i class="fa fa-times-circle"></i></button>'+ccimg+
                                    '   </div><div class="col-md-5">'+
                                    '   <div class="small text-primary">'+v.holder+'</div>'+
                                    '   <div class="strong mt-1 text-muted">'+v.number+'</div></div>'+
                                    '   <div class="col-md-3 pt-2"><button data-card=\''+JSON.stringify(v)+'\' type="button" class="doA-selectCC btn float-right btn-sm btn-outline-secondary">Select</button></div>'+
                                    '</div>';
                        $('#saved-cards').append(card);

                    });
                }
            });

        });

        // Select CC
        $("body").on("click","form#cc-pay .doA-selectCC", function(e) {
            let card = $(this).data('card');
            $("form#cc-pay #name").val(card.holder);
            $("form#cc-pay #card-number").val(card.number);
            $("form#cc-pay #ccmonth").val(card.exp_mm);
            $("form#cc-pay #ccyear").val(card.exp_yy);
            $("form#cc-pay #cvv").val(card.cvv);
            $('form#cc-pay #card-number').trigger("keyup");
        });

        // Remove CC
        $("body").on("click","form#cc-pay .doA-removeCC", function(e) {
            let card = $(this).data('id');
            let num = $(this).data('num');
            let delData = {
                'card_id': card,
                'card_num': num
            };
            ajaxCall('transaction', 'removeCC', delData, function(delRes){
                let delResObj = JSON.parse(delRes);
                if (delResObj.res) {
                    $('#card-'+card).fadeOut();
                }
            });
        });

        // Submit Payment to bank
        var bankCheck;
        $("body").on("submit","form#cc-pay", function(e) {
            e.preventDefault();
            let ccData = $( "form#cc-pay" ).serialize();
            let realThis = this;
            ajaxCall('transaction', 'ccAdd', ccData, function(ccRes){
                $('form#cc-pay .card').fadeOut();
                $('form#cc-pay .card-bank').fadeIn();
                let ccResObj = JSON.parse(ccRes);
                let ccid = ccResObj.res;
                let orderData = ccData+'&ccID='+ccid;
                $('form#cc-pay #cc_id').val(ccid);
                ajaxCall('transaction', 'orderAdd', orderData, function(orderRes){
                    let orderResObj = JSON.parse(orderRes);
                    if (orderResObj.res > 0) {
                        $('form#cc-pay #order_id').val(orderResObj.res);
                        setTimeout(function() {
                            window.open('', 'form_cc', 'width=400,height=600,resizeable,scrollbars');
                            realThis.target = 'form_cc';
                            realThis.submit();
                        }, 1000);
                        setTimeout(function() {
                            bankCheck = setInterval(function() {bankResp(orderResObj.res)}, 1000*5);
                        }, 1000*12);
                    }
                });
            });
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
                    $( "form#cc-pay" ).html(resHTML);
                    $("#wg-transaction .reload").trigger("click");
                } else if (bankResObj.res > 0) {
                    const resHTML =
                        '            <div class="card card-bank bg-light text-center p-4">' +
                        '            <i class="fa fa-cart text-danger fa-4x"></i><hr>' +
                        '            <p class="border-bottom border-danger">Error, Code: '+bankResObj.res+'</p>' +
                        '            </div>';
                    $( "form#cc-pay" ).html(resHTML);
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
                    fResp.html('Your Request Added.');
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
                    fResp.html('Error, Please Check Inputs!');
                }
                if (resObj.res) {
                    fResp.addClass('alert-success');
                    fResp.fadeIn();
                    fResp.html('Your Request Added.');
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
    </script>