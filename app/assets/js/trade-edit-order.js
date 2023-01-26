console.log('Order js loaded');

var enableSL = false;
$("body").on("change","#trade-edit-order #enable-stop-loss", function(e) {
    if( $(this).is(':checked') ) {
        $(`.tradeOrderForm .sl-setter input`).attr('disabled',false);
        $(`.tradeOrderForm .sl-setter button`).attr('disabled',false);
        enableSL = true;
        $('#trade-edit-order .Bid').trigger('DOMSubtreeModified');
        $('#trade-edit-order .Ask').trigger('DOMSubtreeModified');
    } else {
        $(`.tradeOrderForm .sl-setter input`).attr('disabled',true);
        $(`.tradeOrderForm .sl-setter button`).attr('disabled',true);
        enableSL = false;
    }
});

var enableTP = false;
$("body").on("change","#trade-edit-order #enable-take-profit", function(e) {
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


let date = new Date(order.TimeExpiration * 1000);
let iso = date.toISOString().match(/(\d{4}\-\d{2}\-\d{2})T(\d{2}:\d{2}:\d{2})/);

let timeType = order.TypeTime;
if(timeType==='0' || timeType==='1'){
    $(".pending-otype .spe-datetime").hide();
} else if(timeType==='2'){
    $("#trade-edit-order .spe-datetime").prop('type','datetime-local');
    $("#trade-edit-order .spe-datetime").prop('type','date');
    $("#trade-edit-order .spe-datetime").val(iso.slice(0, 16));
    $("#trade-edit-order .spe-datetime").show();
} else if(timeType==='3'){
    $("#trade-edit-order .spe-datetime").prop('type','date');

    $("#trade-edit-order .spe-datetime").val(iso.slice(0, 9));
    $("#trade-edit-order .spe-datetime").show();
}

$("body").on("click",".doA-edit-order", function(e) {
    e.preventDefault();
    const Order = order.Order;
    const Digits = order.Digits;
    const symbol = order.Symbol;
    const takeProfit = $(`.tradeOrderForm #take-profit`).val();
    const stopLoss = $(`.tradeOrderForm #stop-loss`).val();
    const volume = order.VolumeCurrent;
    const price = $("#trade-edit-order #PriceOrder").val();
    const type_P = order.Type;
    const TypeTime = order.TypeTime;
    const TimeExpiration = iso[0];
    const PriceTrigger = order.PriceTrigger;

    if (confirm(`Are you sure to update order?`)) {
        const data = {
            Order: Order,
            Digits: Digits,
            login: selectedLogin,
            symbol: symbol,
            volume:volume,
            type: type_P,
            PriceOrder: price,
            TypeTime: TypeTime,
            takeProfit: (enableTP) ? parseFloat(takeProfit).toFixed(5) : 0,
            stopLoss:(enableSL) ? parseFloat(stopLoss).toFixed(5) : 0
        }
        if(TimeExpiration) data.TimeExpiration = TimeExpiration;
        if(PriceTrigger) data.PriceTrigger = PriceTrigger;
        console.log(data);
        appAlert('info','<i class="fas fa-spinner fa-spin"></i> In Progress', 'Updating the order.');

        socket.emit("pendingOrderEdit", data, (response) => {
            console.log(response);
            if (response.e){
                appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            }
            else{
                appAlert('success','<i class="fas fa-exclamation-triangle"></i> Done', 'The order is edited');
            }
        });
    } else {
        return;
    }

});
