
    // Load Response
    $("body").on("click","#paynet_payment_orders .doM-response", function(e) {
        let resp = $(this).data("resp");
        let body = "<div id='response'></div>";
        makeModal("Bank Response",body,"lg");
        setTimeout(function() {
            $('#response').jsonView(resp);
            console.log(resp);
        }, 100);
    });


    // Refund
    $("body").on("click","#paynet_payment_orders .doA-refund", function(e) {

    });