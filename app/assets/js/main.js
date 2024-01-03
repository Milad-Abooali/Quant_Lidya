"use strict";
window.scrollTo({ top: 0, behavior: 'smooth' });

$('body').on('click', '.allow-focus', function (e) {
    e.stopPropagation();
});

/* Bootstrap Basic */
// Tooltip
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
// Toast
var toastElList = [].slice.call(document.querySelectorAll('.toast'))
var toastList = toastElList.map(function (toastEl) {
    return new bootstrap.Toast(toastEl, {})
});

// Libs
var countriesLib;

// MT Data
var selectedLogin;
var selectedSymbol;

/**
 * Interval Manager
 */
var intervalModalTemp = {};
$('.modal').on('hidden.bs.modal', function () {
    const modalId = $(this).attr('id');
    stopIntervalModalTempAll();
});

// Screen
var intervalScreenTemp = {};
$("body").on('click','.show-screen', function() {
    APP.screen = $(this).data('screen');
    const params = $(this).data('params') || {};
    console.log(APP.screen, params);
    stopIntervalScreenTempAll();
    showScreen(APP.screen, params);
});

/**
 * Header Menu
 */
$("body").on('click','.h-menu .doO-l-side-menu,.h-menu .l-menu .doP-close', function() {
    $('.h-menu .l-menu').toggle();
    $('.h-menu .r-menu').fadeOut();
    $('.h-menu #h-jump').fadeOut();
});
$("body").on('click','.h-menu .doO-r-side-menu,.h-menu .r-menu .doP-close,.h-menu .r-menu .item', function() {
    $('.h-menu .r-menu').toggle();
    $('.h-menu .l-menu').fadeOut();
    $('.h-menu #h-jump').fadeOut();

});

$("body").on('click','.h-menu .doS-debug', function() {
    APP.screen = 'debug';
    showScreen(APP.screen)
});
$("body").on('click','.h-menu .doS-support', function() {
    APP.screen = 'support';
    showScreen(APP.screen)
});
$("body").on('change, keyup','.h-menu #jump-input', function() {
    const jumpTarget = $(this).val();
    if(jumpTarget.length>2){
        $('.h-menu #doS-jump').fadeIn();
    } else {
        $('.h-menu #doS-jump').hide();
    }
});
$("body").on('click','.h-menu #doS-jump', function(e) {
    e.preventDefault();
    const jumpTarget = $('.h-menu #jump-input').val();
    $('.h-menu #jump-input').val('').trigger('keyup');
    let screenList = [
        'home',
        'trade',
        'profile'
    ];
    if( screenList.includes(jumpTarget) ){
        APP.screen = jumpTarget;
        showScreen(APP.screen)
    } else {
        console.log('Search', jumpTarget);
        appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', `"${jumpTarget}" page not found!`);
    }
    $('.h-menu .r-menu').fadeOut();
    $('.h-menu .l-menu').fadeOut();
});
$("body").on('click','.h-menu .doO-search-menu', function(e) {
    e.preventDefault();
    $("#alertToast").toast("hide");
    $('.h-menu #h-jump').toggle();
});

/**
 * Bottom Menu
 */
$("body").on('click','.b-menu .b-menu-item,.b-menu .b-menu-logo', function() {
    $('.b-menu .b-menu-item').removeClass('active');
    $('.b-menu .b-menu-logo').removeClass('active');
    $(this).addClass('active');
    APP.screen = $(this).data('screen');
    showScreen(APP.screen)
});

/**
 *  CRM
 */
// Guest Forms
$(`body`).on(`click`,`.doF-recovery`, ()=>{
    $('#login form').hide();
    $('#login form#recovery-form').fadeIn();
});
$(`body`).on(`click`,`.doF-login`, ()=>{
    $('#login form').hide();
    $('#login form#login-form').fadeIn();
});
$(`body`).on(`click`,`.doF-register`, ()=>{
    $('#login form').hide();
    $('#login form#register-form').fadeIn();
    socket.emit("listCountries", (res) => {
        if(res)
            {
                countriesLib = res;
                let html = '';
                for(let key in res) {
                    html += `<li><span class="dropdown-item" data-country="${key}">${res[key].flag} ${res[key].country}</span></li>`;
                }
                $('#register-form #countries').html(html);
            }

    });
});
// - Login
$("body").on("submit","form#login-form", function(e) {
    e.preventDefault();
    const data = {
        u:  $('#login-form #username-email').val(),
        p:  $('#login-form #password').val()
    }
    socket.emit("crmLogin", data, (response) => {
        if(response.e)
            appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', 'Login Failed !');
        else{
            updateClient(response);
            socket.emit('eLogin', APP.client);
            appAlert('success','<i class="fas fa-check-circle"></i> Done', 'Welcome back ...');
            showScreen('home');
        }
    });
});
// - Logout
$(`body`).on(`click`,`.doA-logout`, ()=>{
    socket.emit("crmLogout", APP.client, (response) => {
        if(response.e) {
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', 'Logout Failed !');
        }
        else{
            appAlert('success','<i class="fas fa-check-circle"></i> Done', 'You are logged out ...');
            window.location.reload();
        }
    });
});
// - Password Recovery
$("body").on("submit","form#recovery-form", function(e) {
    e.preventDefault();
    const data = {
        u:  $('#recovery-form #username').val(),
    }
    socket.emit("crmRecovery", data, (response) => {
        if(response.e)
            appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e)
        else{
            appAlert('success','<i class="fas fa-check-circle"></i> Done', 'Please check your email for instruction.')
        }
    });
});
// - Register
$(`#register-form`).on(`click`,`ul#countries span.dropdown-item`, function() {
    let country = $(this).data('country');
    $('#countryList').html(countriesLib[country].flag+' '+countriesLib[country].country);
    $('#phone-p').val(countriesLib[country].dialCode.substring(1));
    $('#country').val(countriesLib[country].country);
});
$("body").on("submit","form#register-form", function(e) {
    e.preventDefault();
    const data = {
        fname:  $('#register-form #fname').val(),
        lname:  $('#register-form #lname').val(),
        email:  $('#register-form #email').val(),
        phone:  $('#phone-p').val() + $('#register-form #phone').val(),
        country:  $('#register-form #country').val()
    }
    socket.emit("crmRegister", data, (response) => {
        console.log(response);
        if(response.e)
            appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        else{
            appAlert('success','<i class="fas fa-check-circle"></i> Done', 'You will redirect to dashboard.');
            setTimeout(()=>{
                updateClient(response);
                socket.emit('eLogin', APP.client);
            },3000);
        }
    });
});

/**
 * Public buttons
 */
// Load Form to Modal
$("body").on('click','.doM-form', function(e) {
    e.preventDefault();
    const title = $(this).attr('title');
    const name = $(this).data('form-name');
    const params = $(this).data('form-params')|| {};
    getForm(name, params, (mBody)=>{
        makeModal(title, mBody);
    });
});
// Load Wizard to Modal
$("body").on('click','.doM-wizard', function(e) {
    e.preventDefault();
    const title = $(this).attr('title');
    const name = $(this).data('wizard-name');
    getWizard(name,(mBody)=>{
        makeModal(title, mBody);
    });
});

/**
 * Profile
 */
// Avatar Upload
$("body").on('click','.doA-upload-avatar', function(e) {
    e.preventDefault();
    $('input#avatar-file').trigger('click');
});
$("body").on('change','input#avatar-file', function() {
    let formData = new FormData();
    formData.append("file", $('#avatar-file')[0].files[0]);
    formData.append('type', 'avatar');
    $.ajax({
        url: 'app/upload.php',
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            if(response.e)
                appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            else if(response.res){
                APP.client.avatar = response.avatar;
                initial();
            }
        },
    });
});
// IdCard Upload
$("body").on('click','.doA-upload-idcard ', function(e) {
    e.preventDefault();
    $('input#idcard-file').trigger('click');
});
$("body").on('change','input#idcard-file', function() {
    let formData = new FormData();
    formData.append("file", $('#idcard-file')[0].files[0]);
    formData.append('type', 'ID');
    $.ajax({
        url: 'app/upload.php',
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            if(response.e)
                appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            else if(response.res){
                appAlert('success','<i class="fas fa-check-circle"></i> Done', 'File uploaded');
            }
        },
    });
});
// Bill Upload
$("body").on('click','.doA-upload-bill ', function(e) {
    e.preventDefault();
    $('input#bill-file').trigger('click');
});
$("body").on('change','input#bill-file', function() {
    let formData = new FormData();
    formData.append("file", $('#bill-file')[0].files[0]);
    formData.append('type', 'Bill');
    $.ajax({
        url: 'app/upload.php',
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            if(response.e)
                appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
            else if(response.res){
                appAlert('success','<i class="fas fa-check-circle"></i> Done', 'File uploaded');
            }
        },
    });
});
// Update Detail


/**
 * Wizards
 */
// Get Platform Groups
$("body").on("click","form#open-tp-demo #group", function(e) {
    e.preventDefault();
    getPlatformGroups();
});
$("body").on("change","form#open-tp-demo #platform", function(e) {
    e.preventDefault();
    getPlatformGroups();
});
$("body").on("change","form#open-tp-demo #type", function(e) {
    e.preventDefault();
    getPlatformGroups();
});
// Meta Open TP
$("body").on("submit","form#open-tp-demo", function(e) {
    e.preventDefault();
    const data = {
        platform:  $('#open-tp-demo #platform').val(),
        type:  $('#open-tp-demo #type').val(),
        group:  $('#open-tp-demo #group').val(),
        amount:  $('#open-tp-demo #amount').val()
    }
    socket.emit("crmMetaOpenTP", data, (response) => {
        if(response.e)
            appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        else{
            console.log(response);
            appAlert('success','<i class="fas fa-exclamation-triangle"></i> Done', 'Your TP Login Is: '+response.tp.Login);
            setTimeout(()=>{
                showScreen('trade');
                APP.Modal.hide();
            },2000);
        }
    });
});


/**
 * Trade Account
 */




/**
 * Tests
 */
$("body").on('click','.do-test', function(e) {
    e.preventDefault();
    showScreen('profile');

/*

    makeModal("Test Modal", "test body");

    const data = { }
    socket.emit("crmGetProfile", data, (response) => {
        if(response.e)
            appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', 'Test Is Failed !')
        else{
            appAlert('success','<i class="fas fa-check-circle"></i> Done', 'Test Is Done')
        }
        console.log(response);
    });
*/

});