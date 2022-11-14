<?php
    global $db;
GF::p($params);

    $mt5api = new mt5API();
    $api_params['symbol'] = $params['symbol'];
    $api_params['from'] = time()-86400;
    $api_params['to'] = time();
    $api_params['data'] = 'dohlc';
    $mt5api->get('/api/chart/get', $api_params);
    $e = $mt5api->Error;
    $api = $mt5api->Response;
    $chart_data = array_map("eFun::epoch2date", $api->answer);
    // GF::cLog($chart_data,1);


?>
<?php $form_name = 'trade_view_chart-simple'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>" data-symbol="<?= $params['symbol'] ?>" data-login="<?= $params['login'] ?>">

    <div class="row text-secondary">
        <div class="col text-start">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="intervalResLoginPositions" checked>
                <label class="form-check-label" for="intervalResLoginPositions">Live</label>
            </div>
        </div>
        <div class="col text-end">
            <span class="text-opacity-25">Last Update: </span><br>
            <span id="update-time" class="text-light"></span>
        </div>
    </div>
    <br>

    <div id="container" style="width: 100%; height: 550px"></div>

    <div id="form-actions" class="text-end d-none">
        <button type="button" class="btn btn-outline-danger me-2" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>

<script>
    $("form#trade_view_chart-simple #update-time").text(rDT().dateTime);
    selectedSymbol = $('form#trade_view_chart-simple').data('symbol');

    anychart.onDocumentReady(function () {
        var table = anychart.data.table();
        table.addData(<?= json_encode($chart_data) ?>);

        var mapping = table.mapAs();
        mapping.addField('open', 1);
        mapping.addField('high', 2);
        mapping.addField('low', 3);
        mapping.addField('close', 4);

        var chart = anychart.stock();

        var series = chart.plot(0).candlestick(mapping);
        series.name(selectedSymbol);

        chart.container("container");

        chart.draw();
    });

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
    intervalSymbolChart();
    updateChart();
</script>