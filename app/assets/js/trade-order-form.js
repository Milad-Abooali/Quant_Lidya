console.log('Order js loaded');

/* Buy - Keeper */
$("body").on("DOMSubtreeModified","#trade-order-form .Ask", function() {
    let takeProfit = $(`.tradeOrderForm .take-profit[data-otype='buy']`);
    let stopLoss = $(`.tradeOrderForm .stop-loss[data-otype='buy']`);
    let pending = $(`#trade-order-form #PriceOrder[data-otype='buy']`);

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
        if( pending.val()=='' ) {
            pending.val(askPrice);
        }
    }

});

/* Sell - Keeper */
$("body").on("DOMSubtreeModified","#trade-order-form .Bid", function() {
    let takeProfit = $(`.tradeOrderForm .take-profit[data-otype='sell']`);
    let stopLoss = $(`.tradeOrderForm .stop-loss[data-otype='sell']`);
    let pending = $(`#trade-order-form #PriceOrder[data-otype='sell']`);
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
        if( pending.val()=='' ) {
            pending.val(bidPrice);
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

let enablePriceTrigger = false;
$("body").on("change","#trade-order-form #enable-price-trigger", function(e) {
    if( $(this).is(':checked') ) {
        $(`.tradeOrderForm #PriceTrigger`).attr('disabled',false)
        enablePriceTrigger = true;
    } else {
        $(`.tradeOrderForm #PriceTrigger`).attr('disabled',true)
        enablePriceTrigger = false;
    }
});


$("body").on("click",".row-symbol .doA-trade", function(e) {
    e.preventDefault();
    const Digits = $(this).data('digits');
    const symbol = $(this).data('symbol');
    const takeProfit = $(`.tradeOrderForm #take-profit`).val();
    const stopLoss = $(`.tradeOrderForm #stop-loss`).val();
    const volume = $(`.tradeOrderForm #volume`).val();
    const type =  $(this).data('type');
    const price = $("#trade-order-form #PriceOrder").val();
    const oType = $("#trade-order-form #order-type").val();
    const sType = (type)?'Sell':'Buy';

    if(oType=='market'){
        if (confirm(`Are you sure you want to ${sType} ${symbol} (size: ${volume})?`)) {
            const data = {
                Digits: Digits,
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
    } else if(oType=='pending'){

        const type_P = $("#trade-order-form #p-order-type").val();

        const TypeTime = $("#trade-order-form #time-type").val();
        const TimeExpiration = $("#trade-order-form #TimeExpiration").val();
        const PriceTrigger = $("#trade-order-form #PriceTrigger").val();

        if (confirm(`Are you sure you want to ${sType} ${symbol} (size: ${volume}) on ${price}?`)) {
            const data = {
                Digits: Digits,
                login: selectedLogin,
                symbol: symbol,
                type: type_P,
                volume: volume,
                PriceOrder: price,
                TypeTime: TypeTime,
                takeProfit: (enableTP) ? parseFloat(takeProfit).toFixed(5) : 0,
                stopLoss:(enableSL) ? parseFloat(stopLoss).toFixed(5) : 0
            }
            if(TimeExpiration) data.TimeExpiration = TimeExpiration;
            if(PriceTrigger) data.PriceTrigger = PriceTrigger;
            console.log(data);
            appAlert('info','<i class="fas fa-spinner fa-spin"></i> In Progress', 'Opening the positions.');

            socket.emit("pendingOrder", data, (response) => {
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
    }

});

$("body").on("change","#trade-order-form .order-type", function(e) {
    let oType = $(this).val();
    if(oType=='market'){
        $("#trade-order-form .pending-otype").fadeOut();
        $("#trade-order-form .pending-otype").prop( "disabled", true );
    } else if(oType=='pending'){
        $("#trade-order-form .pending-otype").fadeIn();
        $("#trade-order-form .pending-otype").prop( "disabled", false );
    }
});

$("body").on("change",".pending-otype #time-type", function(e) {
    let timeType = $(this).val();
    if(timeType==='0' || timeType==='1'){
        $(".pending-otype .spe-datetime").hide();
    } else if(timeType==='2'){
        $(".pending-otype .spe-datetime").prop('type','datetime-local');
        $(".pending-otype .spe-datetime").show();
    } else if(timeType==='3'){
        $(".pending-otype .spe-datetime").prop('type','date');
        $(".pending-otype .spe-datetime").show();
    }
});
