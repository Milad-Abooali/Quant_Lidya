<?php

    $symbol = eFun::getPrice($params['symbol'])[0];
    GF::cLog($params,1);
    GF::cLog($symbol,1);

?>
<?php $form_name = 'trade-order-form'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>" data-login="<?= $params['login'] ?>">

    <div class="text-end">
        <span class="text-secondary">Order Type:</span>
        <select class="ms-2 custom-select order-type">
            <option value="market" selected>Market</option>
            <option value="pending">Pending</option>
        </select>
        <hr>
    </div>

    <table id="orderForm" class="tradeOrderForm table table-sm table-dark">
        <tbody>
            <tr class="item-row">
                <td> Login </td>
                <td data-lable="login" colspan="2"> <?= $params['login'] ?></td>
            </tr>
            <tr class="item-row">
                <td> Symbol </td>
                <td data-lable="symbol" colspan="2"> <?= $params['symbol'] ?></td>
            </tr>
            <tr class="item-row pt-3">
                <td> Stop Loss <sup class="text-warning"></sup></td>
                <td class="sl-setter">
                    <input id="stop-loss" data-otype="<?= ($params['type']==1) ? 'sell' : 'buy' ?>" type="number" class="stop-loss w-100 text-center " name="sl" placeholder="0" step="<?= substr_replace($symbol->Last ,"1",-1) ?>" disabled>
                </td>
                <td>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="enable-stop-loss"><label class="form-check-label" for="enable-stop-loss"> </label></div>
                </td>
            </tr>

            <tr class="item-row">
                <td> Take Profit <sup class="text-warning"></sup></td>
                <td class="tp-setter">
                    <input id="take-profit" data-otype="<?= ($params['type']==1) ? 'sell' : 'buy' ?>"  type="number" class="take-profit w-100 text-center" name="tp" placeholder="0" step="<?= substr_replace($symbol->Last ,"1",-1) ?>" disabled>
                </td>
                <td>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="enable-take-profit"><label class="form-check-label" for="enable-take-profit"> </label></div>
                </td>
            </tr>
            <?php if($params['type'] ==1) { ?>
            <tr class="item-row">
                <td colspan="2" data-lable="market-price">
                    <div class="row-symbol row align-items-center" data-symbol="<?= $symbol->Symbol ?>">
                        <div class="col-4">
                            <span class="float-start Bid mb-2 mt-2"><?= GF::nf($symbol->Bid, $symbol->Digits) ?></span>
                        </div>
                        <div class="col-4">
                            <div class="text-white text-center Symbol h6"><strong><?= $symbol->Symbol ?></strong></div>
                        </div>
                        <div class="col-4">
                            <span class="float-end Ask mb-2 mt-2"><?= GF::nf($symbol->Ask, $symbol->Digits) ?></span>
                        </div>
                        <div class="col-12 text-center">
                            <div class="btn-group d-flex" role="group" aria-label="Basic example">
                                <button title="Sell" data-symbol="<?= $symbol->Symbol ?>"  data-type="1" class="doA-trade px-5 btn-sm btn btn-danger">Sell</button>
                                <input id="volume" type="number" class="volumeinput text-center" step="0.01" name="lot">
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <?php } else if($params['type']==0) {?>
                <tr class="item-row">
                    <td colspan="2" data-lable="market-price">
                        <div class="row-symbol row align-items-center" data-symbol="<?= $symbol->Symbol ?>">
                            <div class="col-4">
                                <span class="float-start Bid mb-2 mt-2"><?= GF::nf($symbol->Bid, $symbol->Digits) ?></span>
                            </div>
                            <div class="col-4">
                                <div class="text-white text-center Symbol h6"><strong><?= $symbol->Symbol ?></strong></div>
                            </div>
                            <div class="col-4">
                                <span class="float-end Ask mb-2 mt-2"><?= GF::nf($symbol->Ask, $symbol->Digits) ?></span>
                            </div>
                            <div class="col-12 text-center">
                                <div class="btn-group d-flex" role="group" aria-label="Basic example">
                                    <input id="price" type="number" class="pending-otype volumeinput text-center"step="<?= substr_replace($symbol->Last ,"1",-1) ?>"  name="price" disabled>
                                    <input id="volume" type="number" class="volumeinput text-center" step="0.01" name="lot">
                                    <button title="Buy" data-symbol="<?= $symbol->Symbol ?>" data-type="0" class="doA-trade px-5 btn btn-sm btn-success">Buy</button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="form-actions" class="text-end d-none">
        <button type="button" class="btn btn-outline-danger me-2" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
<script>

    var orderLot = $('.row-symbol[data-symbol="<?= $params['symbol'] ?>"] #volume').val();
    $('#orderForm #volume').val(orderLot);
    $("#trade-order-form .pending-otype").fadeOut();
    try{
        <?= str_replace('-','_', $form_name) ?>;
    }
    catch(e) {
        if(e.name == "ReferenceError") {
            <?= str_replace('-','_', $form_name) ?> = true;
            $.ajax({
                async: false,
                url: "app/assets/js/<?= $form_name ?>.js",
                dataType: "script"
            });
        }
    }


</script>