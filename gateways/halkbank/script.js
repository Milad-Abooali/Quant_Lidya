
    // Load resp
    $("body").on("click","#halkbank_payment_orders .doM-response", function(e) {
        let resp = $(this).data("resp");
        let body = "<div id='response'></div>";
        makeModal("Bank Response",body,"lg");
        setTimeout(function() {
            $('#response').jsonView(resp);
            console.log(resp);
        }, 100);
    });


    // Refund
    $("body").on("click","#halkbank_payment_orders .doA-refund", function(e) {
        let func_data = {
            'tid': $(this).data('tid'),
            'id': $(this).data('id')
        };
        let data = {
            'GW': 'halkbank',
            'FUNC': 'refund',
            'DATA': func_data
        };
        ajaxCall('gateway', 'gatewayDo', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Transaction refunded and canceled.");
                DT_halkbank_payment_orders.ajax.reload();
            }
        });
    });