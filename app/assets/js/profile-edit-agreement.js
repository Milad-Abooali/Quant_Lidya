console.log('edit agreement');

/**
 * Submit Form
 */
$("body").on("submit","form#profile-edit-agreement", function(e) {
    e.preventDefault();
    const data = {agree:1}
    socket.emit("crmUpdateProfileAgreement", data, (response) => {
        console.log(response);
        if(response.e)
            appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        else{
            showScreen('home');
            setTimeout(()=>{
                appAlert('success','<i class="fas fa-check-circle"></i> Done', 'Your request is done.');
                APP.Modal.hide();
            },500);
        }
    });
});