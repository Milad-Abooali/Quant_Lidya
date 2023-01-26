console.log('Position Edit js loaded');

var enableSL = false;
$("body").on("change","#trade-edit-position #enable-stop-loss", function(e) {
    if( $(this).is(':checked') ) {
        $(`.tradepositionForm .sl-setter input`).attr('disabled',false);
        $(`.tradepositionForm .sl-setter button`).attr('disabled',false);
        enableSL = true;
        $('#trade-edit-position .Bid').trigger('DOMSubtreeModified');
        $('#trade-edit-position .Ask').trigger('DOMSubtreeModified');
    } else {
        $(`.tradepositionForm .sl-setter input`).attr('disabled',true);
        $(`.tradepositionForm .sl-setter button`).attr('disabled',true);
        enableSL = false;
    }
});

var enableTP = false;
$("body").on("change","#trade-edit-position #enable-take-profit", function(e) {
    if( $(this).is(':checked') ) {
        $(`.tradepositionForm .tp-setter input`).attr('disabled',false)
        $(`.tradepositionForm .tp-setter button`).attr('disabled',false)
        enableTP = true;
    } else {
        $(`.tradepositionForm .tp-setter input`).attr('disabled',true);
        $(`.tradepositionForm .tp-setter button`).attr('disabled',true);
        enableTP = false;
    }
});

$("body").on("click",".doA-edit-position", function(e) {
    e.preventDefault();
    const takeProfit = $(`.tradepositionForm #take-profit`).val();
    const stopLoss = $(`.tradepositionForm #stop-loss`).val();

    if (confirm(`Are you sure to update position?`)) {
        const data = {
            login: selectedLogin,
            symbol: position.Symbol,
            Position: position.Position,
            takeProfit: (enableTP) ? parseFloat(takeProfit).toFixed(5) : 0,
            stopLoss:(enableSL) ? parseFloat(stopLoss).toFixed(5) : 0
        }
        console.log(data);
        appAlert('info','<i class="fas fa-spinner fa-spin"></i> In Progress', 'Updating the position.');

        socket.emit("positionEdit", data, (response) => {
            console.log(response);
            if (response.e){
                appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            }
            else{
                appAlert('success','<i class="fas fa-exclamation-triangle"></i> Done', 'The position is edited');
            }
        });
    } else {
        return;
    }

});
