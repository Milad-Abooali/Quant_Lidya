
/**
 *  System
 *  actlog
 */

// Revert
$("body").on("click", ".do-revert", function(event){
    let data = {
        id: $(this).data('id'),
        type: $(this).data('type'),
        json: $(this).data('json')
    };
    const r = confirm("Revert the action on "+data.json.length+" items?");
    if (r === true) {
        ajaxCall('settings', 'revertAct', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Action has been reverted.");
            }
        });
    }
});


// Load Detail
$("body").on("click", ".do-loadDetail", function(event){
    let type = $(this).data('type');
    let json = $(this).data('json');
    let body;
    if(type == 'Edit') {
        body += "<div id=\"act-detail\"><table class='table-striped table table-sm'><thead><th>#</th><th>Old</th><th>NEW</th></thead><tbody>";
        $.each(json, function (key, val) {
            body += "<tr><td>"+key+"</td>";
            body += "<td>"+val.old+"</td><td>"+val.New+"</td>";
            body += "</tr>";
        });
        body += "</tbody></table></div>";
        makeModal('Action Detail: <span class="text-primary">'+type+'</span>',body,'lg');
    } else {
        body += '<div id="act-detail"></div>';
        makeModal('Action Detail: <span class="text-primary">'+type+'</span>',body,'lg');
        $("#act-detail").JSONView(json);
    }
    let detail = $("#act-detail").html();
    $("#modalMain .modal-body").html(detail);
});

// Update Avoid
$("body").on("click", ".system_jobs .doA-update", function(event){
    const status = $(this).is(':checked') ? 1 : 0;
    const col = $(this).data('col');
    let data = {
        id: $(this).data('id'),
        status: status,
        col: col
    }
    const r = confirm("Update job "+col+"?");
    if (r === true) {
        ajaxCall('settings', 'updateJob', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Job has been update.");
                DT_waf_ip.ajax.reload();
            }
        });
    }
});

$('body').on('change', '.DT_CustomOperation_date', function(event){
    let customJson = '{"columns":"timestamp","operator":"BETWEEN","params":["'+$('#date_start').val()+'","'+$('#date_end').val()+'"]}';
    $('#DT_actlog_user_time').val(customJson);
    setTimeout(function() {
        if($('#date_end').val()) DT_actlog_user.ajax.reload();
    }, 500);
});

$("body").on("click",".clear-date", function(e) {
    $("#DT_actlog_user_time").val('');
    $(".DT_CustomOperation_date").val('');
    DT_actlog_user.ajax.reload();
});