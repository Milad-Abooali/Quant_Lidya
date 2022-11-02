
/**
 * Submit Form
 */
$("body").on("submit","form#trade-update-login-password", function(e) {
    e.preventDefault();
    const data = {
        login: $('#trade-update-login-password #f-login').val(),
        mpass: $('#trade-update-login-password #f-main-pass').val(),
        ipass: $('#trade-update-login-password #f-investor-pass').val()
    }
    socket.emit("crmUpdateLoginPassword", data, (response) => {
        //console.log(response);
        if (response.e)
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        if(response.main){
            if (response.main==="0 Done")
                $('#trade-update-login-password #f-main-pass').after(`<span class="result-note"><i class="result-note fa fa-check text-success me-1"></i> Changed</span>`);
            else
                appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.main+' for main!');
        }
        if(response.investor){
            if(response.investor==="0 Done")
                $('#trade-update-login-password #f-investor-pass').after(`<span class="result-note"><i class="fa fa-check text-success me-1"></i> Changed</span>`);
            else
                appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.investor+' for investor!');
        }
        setTimeout(()=>{
            $('.result-note').fadeOut();
        },3500);
    });
});
