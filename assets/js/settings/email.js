/**
 *  Email
 *  Creat New Theme
 */
$(".email_new-theme form#newTheme").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('email', 'creat',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Theme added successfully.");
        }
    });
});

$(".email_theme-editor form#editTheme").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('email', 'update',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Theme added successfully.");
        }
    });
});

// Show Email
$("body").on("click", ".email_logs .doA-ShowEmail", function(event){
    let data = {
        id: $(this).data('id')
    }
    ajaxCall('email', 'loadContent', data, function (response) {
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on request !");
        } else if (resObj.res) {
            makeModal(resObj.res.subject, resObj.res.content,'lg');
        }
    });
});

// Resend Email
$("body").on("click", ".email_logs .doA-resend", function(event){
    let data = {
        id: $(this).data('id')
    }
    ajaxCall('email', 'resend', data, function (response) {
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on request !");
        } else if (resObj.res) {
            toastr.success("Email sent successfully.");
            DT_email_log.ajax.reload();
        }
    });
});