<?php

    GF::cLog($params,1);

    $mt5api = new mt5API();

    $api_params['ticket']  = $params['position'];
    $mt5api->get('/api/position/get_batch', $api_params);
    $e = $mt5api->Error;
    $api_position = $mt5api->Response;

    if(!$e && $api_position->answer[0]->Login == $params['login']){
        $position = $api_position->answer[0];
        $symbol = eFun::getSymbol($position->Symbol);
    }

?>
<?php $form_name = 'trade-edit-position'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>" data-login="<?= $params['login'] ?>">
    <div class="row">
        <button data-form-params='{"login":"<?= $params['login'] ?>"}' data-login="<?= $params['login'] ?>" data-form-name="trade_view_pending" title="<?= $params['login'] ?> positions" class="doM-form ms-2 btn btn-secondary"><i class="fa fa-clock"></i> Back to pending positions </button>
    </div>
    <table id="positionForm" class="tradepositionForm table table-sm table-dark">
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
                <td> Price (Open) </td>
                <td data-lable="symbol" colspan="2"> <?= $position->PriceOpen ?></td>
            </tr>
            <tr class="item-row">
                <td> Price (Current) </td>
                <td data-lable="symbol" colspan="2"> <?= $position->PriceCurrent ?></td>
            </tr>

            <tr class="item-row pt-3">
                <td> Stop Loss <sup class="text-warning"></sup></td>
                <td class="sl-setter">
                    <input id="stop-loss" type="number" class="stop-loss w-100 text-center " name="sl" placeholder="0" value="<?= ($position->PriceSL>0) ? $position->PriceSL : $position->PriceCurrent; ?>" step="0.<?= str_repeat(0,$symbol->Digits - 1) ?>1" <?= ($position->PriceSL>0)?'':'disabled' ?>>
                </td>
                <td>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="enable-stop-loss" <?= ($position->PriceSL>0)?'checked':'' ?>><label class="form-check-label" for="enable-stop-loss"> </label></div>
                </td>
            </tr>

            <tr class="item-row">
                <td> Take Profit <sup class="text-warning"></sup></td>
                <td class="tp-setter">
                    <input id="take-profit" type="number" class="take-profit w-100 text-center" name="tp" value="<?= ($position->PriceTP>0) ? $position->PriceTP : $position->PriceCurrent; ?>" step="0.<?= str_repeat(0,$symbol->Digits - 1) ?>1" <?= ($position->PriceTP>0)?'':'disabled' ?>>
                </td>
                <td>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="enable-take-profit" <?= ($position->PriceTP>0)?'checked':'' ?>><label class="form-check-label" for="enable-take-profit"> </label></div>
                </td>
            </tr>

        </tbody>
    </table>

    <div id="form-actions" class="text-end">
        <button type="button" class="doA-edit-position btn btn-primary">Submit</button>
    </div>
</form>
<script>

    $(".spe-date").hide();
    $(".spe-datetime").hide();

    var position = <?= json_encode($position) ?>;
    console.log(position);

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