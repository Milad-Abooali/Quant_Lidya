<?php

    $mt5api = new mt5API();

    $api_params['ticket']  = $params['order'];
    $mt5api->get('/api/order/get', $api_params);
    $e = $mt5api->Error;
    $api_order = $mt5api->Response;

    if(!$e && $api_order->answer->Login == $params['login']){
        $order = $api_order->answer;
        $symbol = eFun::getSymbol($order->Symbol);
    }

    $EnOrderType = [
        'Buy',
        'Sell',
        'Buy Limit',
        'Sell Limit',
        'Sell Stop',
        'Buy Stop Limit',
        'Sell Stop Limit',
        'Close By'
    ];
    $TypeTime = [
        'Good till Canceled',
        'Intraday',
        'Specified time',
        'Specified day'
    ];
?>
<?php $form_name = 'trade-edit-order'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>" data-login="<?= $params['login'] ?>">
    <div class="row">
        <button data-form-params='{"login":"<?= $params['login'] ?>"}' data-login="<?= $params['login'] ?>" data-form-name="trade_view_pending" title="<?= $params['login'] ?> Pending Orders" class="doM-form ms-2 btn btn-secondary"><i class="fa fa-clock"></i> Back to pending orders </button>
    </div>
    <table id="orderForm" class="tradeOrderForm table table-sm table-dark">
        <tbody>
            <tr class="item-row">
                <td> Login </td>
                <td data-lable="login" colspan="2"> <?= $params['login'] ?></td>
            </tr>
            <tr class="item-row">
                <td> Symbol </td>
                <td data-lable="symbol" colspan="2"> <?= $symbol->Symbol ?></td>
            </tr>
            <tr class="item-row">
                <td> Order Type </td>
                <td data-lable="symbol" colspan="2"> <?= $EnOrderType[$order->Type] ?></td>
            </tr>

            <tr class="item-row pt-3">
                <td> Stop Loss <sup class="text-warning"></sup></td>
                <td class="sl-setter">
                    <input id="stop-loss" type="number" class="stop-loss w-100 text-center " name="sl" placeholder="0" value="<?= ($order->PriceSL>0) ? $order->PriceSL : $order->PriceOrder; ?>" step="0.<?= str_repeat(0,$symbol->Digits - 1) ?>1" <?= ($order->PriceSL>0)?'':'disabled' ?>>
                </td>
                <td>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="enable-stop-loss" <?= ($order->PriceSL>0)?'checked':'' ?>><label class="form-check-label" for="enable-stop-loss"> </label></div>
                </td>
            </tr>

            <tr class="item-row">
                <td> Take Profit <sup class="text-warning"></sup></td>
                <td class="tp-setter">
                    <input id="take-profit" type="number" class="take-profit w-100 text-center" name="tp" value="<?= ($order->PriceTP>0) ? $order->PriceTP : $order->PriceOrder; ?>" step="0.<?= str_repeat(0,$symbol->Digits - 1) ?>1" <?= ($order->PriceTP>0)?'':'disabled' ?>>
                </td>
                <td>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="enable-take-profit" <?= ($order->PriceTP>0)?'checked':'' ?>><label class="form-check-label" for="enable-take-profit"> </label></div>
                </td>
            </tr>

            <tr class="item-row">
                <td> Price <sup class="text-warning"></sup></td>
                <td>
                    <input id="PriceOrder" type="number" data-otype="buy" class="volumeinput text-center" value="<?= $order->PriceOrder ?>" step="0.<?= str_repeat(0,$symbol->Digits - 1) ?>1"  name="price">
                </td>
            </tr>
            <!--
            <tr class="pending-otype item-row">
                <td>Time Type</td>
                <td>
                    <?= $TypeTime[$order->TypeTime] ?>
                    <br>
                    <div class="btn-group d-flex" role="group">
                        <input id="TimeExpiration" type="datetime-local" class="spe-datetime form-control">
                    </div>
                </td>
                <td></td>
            </tr>
            <tr class="pending-otype item-row">
                <td>Price Trigger</td>
                <td>
                    <input id="PriceTrigger" type="number" data-otype="buy" class="w-100 text-center"step="<?= substr_replace($symbol->Last ,"1",-1) ?>"  name="PriceTrigger" value="<?= $order->PriceTrigger ?>" disabled>
                </td>
                <td>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="enable-price-trigger"><label class="form-check-label" for="enable-price-trigger"> </label></div>
                </td>
            </tr> -->
        </tbody>
    </table>

    <div id="form-actions" class="text-end">
        <button type="button" class="doA-edit-order btn btn-primary">Submit</button>
    </div>
</form>
<script>

    $(".spe-date").hide();
    $(".spe-datetime").hide();

    var order = <?= json_encode($order) ?>;
    console.log(order);

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