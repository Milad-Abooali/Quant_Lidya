console.log('edit extra');

/**
 * Submit Form
 */
$("body").on("submit","form#profile-edit-extra", function(e) {
    e.preventDefault();
    const data = {
        city:           $('#profile-edit-extra #f-city').val(),
        address:        $('#profile-edit-extra #f-address').val(),
        interests:      $('#profile-edit-extra #f-interests').val(),
        hobbies:        $('#profile-edit-extra #f-hobbies').val(),
        job_cat:        $('#profile-edit-extra #f-job_cat').val(),
        job_title:      $('#profile-edit-extra #f-job_title').val(),
        exp_fx_year:    $('#profile-edit-extra #f-exp_fx_year').val(),
        exp_cfd_year:   $('#profile-edit-extra #f-exp_cfd_year').val(),
        income:         $('#profile-edit-extra #f-income').val(),
        investment:     $('#profile-edit-extra #f-investment').val(),
        strategy:       $('#profile-edit-extra #f-strategy').val(),

    }
    socket.emit("crmUpdateProfileE", data, (response) => {
        console.log(response);
        if(response.e)
            appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        else{
            showScreen('profile');
            setTimeout(()=>{
                appAlert('success','<i class="fas fa-check-circle"></i> Done', 'Your request is done.');
                APP.Modal.hide();
            },500);
        }
    });
});