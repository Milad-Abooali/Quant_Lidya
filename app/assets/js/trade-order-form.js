console.log('Order js loaded');


/* Buy - Keeper */
$("body").on("DOMSubtreeModified","#trade-order-form .Ask", function() {
    let takeProfit = $(`.tradeOrderForm .take-profit[data-otype='buy']`);
    let stopLoss = $(`.tradeOrderForm .stop-loss[data-otype='buy']`);
    let askprice = parseFloat( $(this).text() ) || 0;
    if(askprice>0) {
        if( askprice>takeProfit.val() || takeProfit.val()=='') {
            takeProfit.val(askprice);
            takeProfit[0].stepUp(1);
        }
        takeProfit.attr('min', askprice);

        stopLoss.attr('min', 0);
        stopLoss.attr('max', askprice);

    }
});

/* Sell - Keeper */
$("body").on("DOMSubtreeModified","#trade-order-form .Bid", function() {
    let takeProfit = $(`.tradeOrderForm .take-profit[data-otype='sell']`);
    let stopLoss = $(`.tradeOrderForm .stop-loss[data-otype='sell']`);
    let bidPrice = parseFloat( $(this).text() ) || 0;
    if(bidPrice>0){
        if( bidPrice<takeProfit.val() || takeProfit.val()=='') {
            takeProfit.val(bidPrice);
            takeProfit[0].stepDown(1);
        }
        takeProfit.attr('min', 0);
        takeProfit.attr('max', bidPrice);
        stopLoss.attr('min', bidPrice);
    }
});

let enableSL = false;
$("body").on("change","#trade-order-form #enable-stop-loss", function(e) {
    if( $(this).is(':checked') ) {
        $(`.tradeOrderForm #stop-loss`).attr('disabled',false)
        enableSL = true;
    } else {
        $(`.tradeOrderForm #stop-loss`).attr('disabled',true);
        enableSL = false;
    }
});

let enableTP = false;
$("body").on("change","#trade-order-form #enable-take-profit", function(e) {
    if( $(this).is(':checked') ) {
        $(`.tradeOrderForm #take-profit`).attr('disabled',false)
        enableTP = true;
    } else {
        $(`.tradeOrderForm #take-profit`).attr('disabled',true);
        enableTP = false;
    }
});


$("body").on("click",".row-symbol .doA-trade", function(e) {
    e.preventDefault();
    const symbol = $(this).data('symbol');
    const takeProfit = $(`.tradeOrderForm #take-profit`).val();
    const stopLoss = $(`.tradeOrderForm #stop-loss`).val();
    const data = {
        login: selectedLogin,
        symbol: symbol,
        type:$(this).data('type'),
        volume: $(`.tradeOrderForm #volume`).val(),
        takeProfit: (enableTP) ? parseFloat(takeProfit).toFixed(5) : 0,
        stopLoss:(enableSL) ? parseFloat(stopLoss).toFixed(5) : 0
    }
    console.log(data);
    appAlert('info','<i class="fas fa-spinner fa-spin"></i> In Progress', 'Opening the positions.');

    socket.emit("simpleOrder", data, (response) => {
        console.log(response);
        if (response.e){
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        }
        else{
            appAlert('success','<i class="fas fa-exclamation-triangle"></i> Done', 'Position is opened - <strong>'+symbol+'</strong>');
        }
    });
});