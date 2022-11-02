console.log('edit general');

/**
 * Select Country
 */
$(`body`).on(`click`,`#profile-edit-general ul#countries span.dropdown-item`, function() {
    let country = $(this).data('country');
    $('#profile-edit-general #countryList').html(countriesLib[country].flag+' '+countriesLib[country].country);
    $('#profile-edit-general #f-country').val(countriesLib[country].country);
    $('#profile-edit-general #f-phone-p').val(countriesLib[country].dialCode.substring(1));
});

/**
 * Submit Form
 */
$("body").on("submit","form#profile-edit-general", function(e) {
    e.preventDefault();
    const data = {
        fname:   $('#profile-edit-general #f-fname').val(),
        lname:   $('#profile-edit-general #f-lname').val(),
        phone:   $('#profile-edit-general #f-phone-p').val() + $('#profile-edit-general #f-phone').val(),
        country: $('#profile-edit-general #f-country').val()
    }
    socket.emit("crmUpdateProfileG", data, (response) => {
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