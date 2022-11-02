/**
 *  Waf
 *  session
 */
// End all sessions
$("body").on("click", "#tab-waf .doA-endAll", function(event){
    const r = confirm("End all active session? * current session will not ended. ");
    if (r === true) {
        ajaxCall('waf', 'endAllSess', '', function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("All sessions has been ended.");
                DT_waf_session_archive.ajax.reload();
            }
        });
    }
});
// End Selected sessions
$("body").on("click", "#tab-waf .doA-endSelected", function(event){
    const r = confirm("End Selected session?");
    if (r === true) {

        $("#waf_session_archive input:checked").each(function() {
            let data = {
                sessid: $(this).val()
            }
            ajaxCall('waf', 'endSess', data, function (response) {
                let resObj = JSON.parse(response);
                if (resObj.e) {
                    toastr.error("Error on request !");
                } else if (resObj.res) {
                    toastr.success("Session has been ended.");
                }
            });
        }).promise().done( function(){ DT_waf_session_archive.ajax.reload(); } );

    }
});
// End session
$("body").on("click", "#tab-waf .doA-endSess", function(event){
    let data = {
        sessid: $(this).data('id')
    }
    const r = confirm("End the session?");
    if (r === true) {
        ajaxCall('waf', 'endSess', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Session has been ended.");
                DT_waf_session_archive.ajax.reload();
            }
        });
    }
});
// End session by SEN
$("body").on("click", "#tab-waf .doA-senEnd", function(event){
    let data = {
        sen: $('#sen-id').val()
    }
    const r = confirm("End the session?");
    if (r === true) {
        ajaxCall('waf', 'endSEN', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Session has been ended.");
                DT_waf_session_archive.ajax.reload();
            }
        });
    }
});
$("body").on("click", "#tab-waf #goSearch", function(event){
    let search = $('#tab-waf #searchVal').val();
    $(location).attr('href', securl+'&search='+search)
});


/**
 *  Waf
 *  ip-db
 */
// Add IP
$(".waf_ip-db form#addIP").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('waf', 'addIP',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("IP added successfully.");
            DT_waf_ip.ajax.reload();
        }
    });
});
// Update IP
$("body").on("click", ".waf_ip-db .doA-update", function(event){
    let data = {
        id: $(this).data('id'),
        status: $(this).data('status')
    }
    const r = confirm("Change IP Status?");
    if (r === true) {
        ajaxCall('waf', 'updateIP', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Status has been update.");
                DT_waf_ip.ajax.reload();
            }
        });
    }
});
// Delete IP
$("body").on("click", ".waf_ip-db .doA-delete", function(event){
    event.preventDefault();
    let data = {
        id: $(this).data('id')
    }
    const r = confirm("Delete IP?");
    if (r === true) {
        ajaxCall('waf', 'deleteIP', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("IP deleted.");
                DT_waf_ip.ajax.reload();
            }
        });
    }
});

/**
 *  Waf
 *  login-ip
 */
// Update Status M
$("body").on("click", ".waf_login-ip #waf_m_status", function(event){
    const status = $(this).is(':checked') ? 1 : 0;
    let data = {
        m: 'waf_login_ip',
        status: status
    }
    const r = confirm("Change Status?");
    if (r === true) {
        ajaxCall('waf', 'updateModuleStatus', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Status has been update.");
            }
        });
    }
});

// Add Filter
$(".waf_login-ip form#addFilter").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('waf', 'addFilter',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Filter added successfully.");
            DT_waf_ip_login.ajax.reload();
        }
    });
});
// Delete Filter
$("body").on("click", ".waf_login-ip .doDelete", function(event){
    event.preventDefault();
    let data = {
        id: $(this).data('id')
    }
    const r = confirm("Delete Rule?");
    if (r === true) {
        ajaxCall('waf', 'deleteFilter', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Rule deleted.");
                DT_waf_ip_login.ajax.reload();
            }
        });
    }
});
// Update Filter
$("body").on("click", ".waf_login-ip .doUpdate", function(event){
    const status = $(this).is(':checked') ? 1 : 0;
    let data = {
        id: $(this).data('id'),
        status: status
    }
    ajaxCall('waf', 'updateFilter', data, function (response) {
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on request !");
        } else if (resObj.res) {
            toastr.success("Rule updated.");
            DT_waf_ip_login.ajax.reload();
        }
    });
});
// Add Exception
$(".waf_login-ip form#addException").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('waf', 'addException',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Exception added successfully.");
            DT_waf_ip_login_ex.ajax.reload();
        }
    });
});
// End Exception
$("body").on("click", ".waf_login-ip .doExpire", function(event){
    let data = {
        id: $(this).data('id')
    }
    const r = confirm("End Exception?");
    if (r === true) {
        ajaxCall('waf', 'endException', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Exception has been expired.");
                DT_waf_ip_login_ex.ajax.reload();
            }
        });
    }
});
// Set Time Range
$(".waf_login-ip form#timeRange").submit(function(event){
    event.preventDefault();
    let data = $(this).serialize();
    ajaxCall ('waf', 'updateSetting',data, function(response){
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on saving form !");
        } else if (resObj.res) {
            toastr.success("Time Range Updated successfully.");
            DT_waf_ip_login_ex.ajax.reload();
        }
    });
});


$('#select-all').on('click', function(){
    // Get all rows with search applied
    var rows = DT_waf_session_archive.rows({ 'search': 'applied' }).nodes();
    // Check/uncheck checkboxes for all rows in the table
    $('input[type="checkbox"]', rows).prop('checked', this.checked);
});