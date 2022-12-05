console.log('Order js loaded');

/* Buy - Keeper */
$("body").on("DOMSubtreeModified","#trade-order-form .Ask", function() {
    let takeProfit = $(`.tradeOrderForm .take-profit[data-otype='buy']`);
    let stopLoss = $(`.tradeOrderForm .stop-loss[data-otype='buy']`);

    let askPrice = parseFloat( $(this).text() ) || 0;
    if(askPrice>0) {
        if( takeProfit.val()=='') {
            takeProfit.val(askPrice);
            takeProfit[0].stepUp(1);
        }
        if( stopLoss.val()=='') {
            stopLoss.val(askPrice);
            stopLoss[0].stepDown(1);
        }
    }
});

/* Sell - Keeper */
$("body").on("DOMSubtreeModified","#trade-order-form .Bid", function() {
    let takeProfit = $(`.tradeOrderForm .take-profit[data-otype='sell']`);
    let stopLoss = $(`.tradeOrderForm .stop-loss[data-otype='sell']`);
    let bidPrice = parseFloat( $(this).text() ) || 0;
    if(bidPrice>0){
        if( takeProfit.val()=='') {
            takeProfit.val(bidPrice);
            takeProfit[0].stepDown(1);
        }
        if( stopLoss.val()=='') {
            stopLoss.val(bidPrice);
            stopLoss[0].stepUp(1);
        }
    }
});

let enableSL = false;
$("body").on("change","#trade-order-form #enable-stop-loss", function(e) {
    if( $(this).is(':checked') ) {
        $(`.tradeOrderForm .sl-setter input`).attr('disabled',false);
        $(`.tradeOrderForm .sl-setter button`).attr('disabled',false);
        enableSL = true;
        $('#trade-order-form .Bid').trigger('DOMSubtreeModified');
        $('#trade-order-form .Ask').trigger('DOMSubtreeModified');
    } else {
        $(`.tradeOrderForm .sl-setter input`).attr('disabled',true);
        $(`.tradeOrderForm .sl-setter button`).attr('disabled',true);
        enableSL = false;
    }
});

let enableTP = false;
$("body").on("change","#trade-order-form #enable-take-profit", function(e) {
    if( $(this).is(':checked') ) {
        $(`.tradeOrderForm .tp-setter input`).attr('disabled',false)
        $(`.tradeOrderForm .tp-setter button`).attr('disabled',false)
        enableTP = true;
    } else {
        $(`.tradeOrderForm .tp-setter input`).attr('disabled',true);
        $(`.tradeOrderForm .tp-setter button`).attr('disabled',true);
        enableTP = false;
    }
});

$("body").on("click",".row-symbol .doA-trade", function(e) {
    e.preventDefault();
    const symbol = $(this).data('symbol');
    const takeProfit = $(`.tradeOrderForm #take-profit`).val();
    const stopLoss = $(`.tradeOrderForm #stop-loss`).val();
    const volume = $(`.tradeOrderForm #volume`).val();
    const type = $(this).data('type');

    const sType = (type)?'Sell':'Buy';
    if (confirm(`Are you sure you want to ${sType} ${symbol} (size: ${volume})?`)) {
        const data = {
            login: selectedLogin,
            symbol: symbol,
            type: type,
            volume: volume,
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
    } else {
        return;
    }
});


$("body").on("change","#trade-order-form .order-type", function(e) {
    let oType = $(this).val();
    if(oType=='market'){
        $("#trade-order-form .pending-otype").fadeOut();
    } else if(oType=='pending'){
        $("#trade-order-form .pending-otype").fadeIn();
    }
});
