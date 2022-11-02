<?php
/**
 * Trade
 * App - Screen Page
 * By Milad [m.abooali@hotmail.com]
 */
    global $APP;
    global $db;

    $screen_title   = 'Market';
    $screen_id      = 'market';

    if($APP->checkPermit($screen_id, 'view', 1)):

    $login = $params['login'];

    // Get Login Group
    $mt5api = new mt5API();
    $api_params['login']  = $login;
    $mt5api->get('/api/user/get', $api_params);
    $e = $mt5api->Error;
    $api = $mt5api->Response;
    $login_Group = $api->answer->Group;

    // Get symbols
    $mt5api = new mt5API();
    $api_params=[];
    $api_params['symbol']  = '*';
    $api_params['group']  = $login_Group;
    $api_params['trans_id']  = 0;
    $mt5api->get('/api/tick/last_group', $api_params);
    $e = $mt5api->Error;
    $api = $mt5api->Response;

?>

<!-- Home Screen -->
<div id="<?= $screen_id ?>" class="screen d-hide col-xs-12">
    <div class="screen-header">
        <h4><?= $screen_title ?></h4>
    </div>
    <div class="screen-body">

        <div class="row p-3">
            <div class="col">
                Account: <strong class="h5 text-primary"><?= $login ?></strong>
            </div>
            <div class="col text-center">
                <button data-form-params='{"login":"<?= $login ?>"}' data-login="<?= $login ?>" data-form-name="trade_view_login" title="<?= $login ?> Detail" class="doM-form btn btn-primary"><i class="fa fa-info-circle"></i> Detail</button>
            </div>
            <div class="col text-end">
                <button data-screen="trade" class="show-screen btn btn-outline-warning">Back</button>
            </div>
            <hr>
        </div>

        <div class="row text-secondary">
            <div class="col text-start">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="intervalResMarketPrices" checked>
                    <label class="form-check-label" for="intervalResMarketPrices">Live</label>
                </div>
            </div>
            <div class="col text-end">
                <span class="text-opacity-25">Last Update: </span><br>
                <span id="update-time" class="text-light"></span>
            </div>
        </div>

        <div class="wrapper-market container">
            <?php
                if($api->answer) foreach($api->answer as $symbol){
            ?>
                <div class="row-symbol row align-items-center" data-symbol="<?= $symbol->Symbol ?>">
                    <div class="col-4">
                        <span class="float-start Bid"><?= GF::nf($symbol->Bid, $symbol->Digits) ?></span>
                    </div>
                    <div class="col-4">
                        <button data-form-params='{"symbol":"<?= $symbol->Symbol ?>","login":"<?= $login ?>"}' data-login="<?= $login ?>" data-form-name="trade_view_chart-simple" title="<?= $symbol->Symbol ?> Chart - Simple" class="doM-form btn btn-primary"><i class="fa fa-info-circle"></i> Chart</button>
                        <div class="text-white text-center Symbol h6"><strong><?= $symbol->Symbol ?></strong></div>
                    </div>
                    <div class="col-4">
                        <span class="float-end Ask"><?= GF::nf($symbol->Ask, $symbol->Digits) ?></span>
                    </div>
                    <div class="col-12 text-center">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button title="Sell" data-symbol="<?= $symbol->Symbol ?>"  data-type="1" class="doA-trade px-5 btn-sm btn btn-danger">Sell</button>
                            <input id="volume" type="number" class="form-control text-center" min="0.00" max="100000.00" step="0.01" name="lot" placeholder="0,00" value="1.00">
                            <button title="Buy" data-symbol="<?= $symbol->Symbol ?>" data-type="0" class="doA-trade px-5 btn btn-sm btn-success">Buy</button>
                        </div>
                    </div>
                </div>

            <?php } ?>
        </div>

    </div>
    <div class="screen-footer">
        footer
    </div>
</div>
        <script>
            try{
                marketScreen;
            }
            catch(e) {
                if(e.name == "ReferenceError") {
                    marketScreen = true;
                    $.ajax({
                        async: false,
                        url: "app/assets/js/market.js",
                        dataType: "script"
                    });
                }
            }
            $("#market #update-time").text(rDT().dateTime);
            selectedLogin = <?= $login ?>;
            intervalMarketPrices();
        </script>

<?php endif; ?>
