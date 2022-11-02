/**
 *  Brokers List
 */

// View broker modal
$("body").on("click", ".brokers_list .doM-View", function(event){
    let data = {
        id: $(this).data('id'),
        edit: 0
    }
    ajaxCall('global', 'getBroker', data, function (response) {
        makeModal('Broker',response,'lg');
    });
});

// Edit broker modal
$("body").on("click", ".brokers_list .doM-edit", function(event){
    let data = {
        id: $(this).data('id'),
        edit: 1
    }
    ajaxCall('global', 'getBroker', data, function (response) {
        makeModal('Broker',response,'lg');
    });
});

// Delete broker
$("body").on("click", ".brokers_list .doA-delete", function(event){
    event.preventDefault();
    let data = {
        id: $(this).data('id')
    }
    const r = confirm("Delete Broker?");
    if (r === true) {
        ajaxCall('global', 'deleteBroker', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Broker deleted.");
                DT_brokers_list.ajax.reload();
            }
        });
    }
});

// Update Maintenance Mode
$("body").on("click", ".brokers_list .doA-maintenance", function(event){
    const status = $(this).is(':checked') ? 1 : 0;
    let data = {
        id: $(this).data('id'),
        maintenance: status
    }
    ajaxCall('global', 'setBrokerMaintenance', data, function (response) {
        let resObj = JSON.parse(response);
        if (resObj.e) {
            toastr.error("Error on request !");
        } else if (resObj.res) {
            toastr.success("Broker updated.");
        }
    });
});

// Submit Update
$("body").on("submit","form#edit-broker", function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    ajaxForm ('global', 'updateBroker', formData, function(response){
        let resObj = JSON.parse(response);
        const fResp = $("#edit-broker #fRes");
        if (resObj.e) {
            fResp.addClass('alert-warning');
            fResp.fadeIn();
            fResp.html('Error, Please Check Inputs!');
        }
        if (resObj.res) {
            fResp.addClass('alert-success');
            fResp.fadeIn();
            fResp.html('Broker data updated.');
            setTimeout(function(){
                DT_brokers_list.ajax.reload();
            }, 500);
        }
    });
});

/**
 *  Brokers New
 */

// Submit New
$("body").on("submit","form#new-broker", function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    ajaxForm ('global', 'newBroker', formData, function(response){
        let resObj = JSON.parse(response);
        const fResp = $("#new-broker #fRes");
        if (resObj.e) {
            fResp.addClass('alert-warning');
            fResp.fadeIn();
            fResp.html('Error, Please Check Inputs!');
        }
        if (resObj.res) {
            fResp.addClass('alert-success');
            fResp.fadeIn();
            fResp.html('Broker Added.');
            $('form#new-broker').trigger("reset");
        }
    });
});

/**
 *  Brokers Units
 */

// Delete unit
$("body").on("click", ".brokers_units .doA-delete", function(event){
    event.preventDefault();
    let data = {
        id: $(this).data('id')
    }
    const r = confirm("Delete Unit?");
    if (r === true) {
        ajaxCall('global', 'deleteUnit', data, function (response) {
            let resObj = JSON.parse(response);
            if (resObj.e) {
                toastr.error("Error on request !");
            } else if (resObj.res) {
                toastr.success("Unit deleted.");
                DT_units_list.ajax.reload();
            }
        });
    }
});

// Submit New Unit
$("body").on("submit","form#new-unit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    ajaxForm ('global', 'newUnit', formData, function(response){
        let resObj = JSON.parse(response);
        const fResp = $("#new-unit #fRes");
        if (resObj.e) {
            fResp.addClass('alert-warning');
            fResp.fadeIn();
            fResp.html('Error, Please Check Inputs!');
        }
        if (resObj.res) {
            fResp.addClass('alert-success');
            fResp.fadeIn();
            fResp.html('Unit Added.');
            $('form#new-broker').trigger("reset");
            DT_units_list.ajax.reload();
        }
    });
});

$(document).ready( function () {
    $('.form-select3').selectpicker({
        tickIcon: 'fas fa-check',
        liveSearch: true
    });
});