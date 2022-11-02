
/**
 * Initial App
 */
function initial(){

    // Show If
    $(`*[class^="show-if-"]`).each(function(index){
        if($(this).hasClass(`show-if-${APP.client.role}`))
            $(this).fadeIn();
        else
            $(this).hide();
    });

    // Hide if
    $(`*[class^="hide-if-"]`).each(function(index){
        if($(this).hasClass(`hide-if-${APP.client.role}`))
            $(this).hide();
        else
            $(this).fadeIn();
    });

    if(APP.client.avatar) $('.avatar').attr('src',APP.client.avatar);


}

/**
 * Update Client
 */
function updateClient(data){
    if(data.id) APP.client.id = data.id;
    if(data.role) APP.client.role = data.role;
    if(data.avatar) APP.client.avatar = data.avatar;
}

/**
 * Update Client
 */
function updateClient(data){
    if(data.id) APP.client.id = data.id;
    if(data.role) APP.client.role = data.role;
    if(data.avatar) APP.client.avatar = data.avatar;
}

/**
 * Get Wizard
 */
function getWizard(name, callback){
    const data = {
        client: APP.client,
        name:  name
    }
    socket.emit("getWizard", data, (response) => {
        if (response.e){
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        }
        else{
            callback(response.res);
        }
    });
}

/**
 * Get Form
 */
function getForm(name, params, callback){
    const data = {
        client: APP.client,
        name:  name,
        params: params
    }
    socket.emit("getForm", data, (response) => {
        if (response.e){
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        }
        else{
            callback(response.res);
        }
    });
}

/**
 * Show Screen
 * @param screen
 * @param params
 */
function showScreen(screen, params= {}){
    window.scrollTo({ top: 0, behavior: 'smooth' });
    $('.h-menu .r-menu').fadeOut();
    $('.h-menu .l-menu').fadeOut();
    $('.h-menu #h-jump').fadeOut();
    $('#screen-wrapper .screen').removeClass('active');
    $('#screen-wrapper .screen').addClass('d-hide');
    const data = {
        client: APP.client,
        screen:  screen,
        params: params
    }
    socket.emit("getScreen", data, (response) => {
        if (response.e){
            appAlert('danger', '<i class="fas fa-exclamation-triangle"></i> Error', response.e)
        }
        else{
            $("#alertToast").toast("hide");
            $('#screen-wrapper>.row').html(response.res);
            $('#screen-wrapper .screen#'+screen).removeClass('d-hide').addClass('active');
        }
    });

    // setTimeout( () => {
    //     $(`.b-menu-item[data-screen!=""]`).removeClass('active');
    //     $(`.b-menu-item[data-screen="${screen}"]`).addClass('active');
    //     socket.emit('eWatchdogApp', APP.client);
    // }, 1);

}

/**
 * StatusColor
 */
function statusColor(target, status=0, alert='muted', tTip='Online'){
    let color = (status) ? 'text-success' : 'text-'+alert;
    let title = (status) ? tTip : 'Offline';
    target.removeClass('text-muted text-success').removeClass().addClass('mdi mdi-checkbox-blank-circle mr-1 font-11 '+color).attr('data-original-title',title);
}

/**
 * Show and Hide Tooltip
 * @param element
 * @param time
 */
function flashElement(element, time=150){
    let opacity = element.css('opacity');
    element.css('opacity', '0.30');
    setTimeout(function() {
        element.css('opacity', 1);
    }, time);
}

/**
 * Update Core Status
 */
function updateCoreStatus(status){

    statusColor($('#crm-status'), status.crmStatus,'danger');

    if(status.redisStatus ==='connecting...')
        statusColor($('#redis-status'), 0,'warning', status.redisStatus);
    else
        statusColor($('#redis-status'), status.redisStatus,'danger',status.redisStatus);

    statusColor($('#aiml-status'), status.aiml);

    statusColor($('#ml-status'), status.ml);
    flashElement($('#ml-status'));

    statusColor($('#hi-status'), status.hi);
    flashElement($('#hi-status'));

    $('.online-count').text(status.onlineClient);
    $('.guest-count').text(status.onlineRoleList.guest);
    $('.user-count').text(status.onlineRoleList.user);
    $('.agent-count').text(status.onlineRoleList.agent);
    $('.admin-count').text(status.onlineRoleList.admin);

    initial();
}

/**
 * App Toast
 * @param bg
 * @param color
 * @param strongTitle
 * @param smallTitle
 * @param body
 */
function appToast(bg, color, strongTitle, smallTitle, body){
    $('#alertToast').removeClass().addClass(`toast bg-${bg} text-${color} fade`);
    $('#alertToast .toast-header').removeClass().addClass(`toast-header bg-${bg} text-${color}`);
    $('#alertToast .toast-header button').removeClass().addClass(`btn-close btn-close-${color}`);
    $('#alertToast .toast-header strong').html(strongTitle);
    $('#alertToast .toast-header small').html(smallTitle);
    $('#alertToast .toast-body').html(body);
    $("#alertToast").toast("show");
}

/**
 *
 * @param type
 * @param title
 * @param text
 */
function appAlert(type, title, text){
    appToast(type, 'white', title, '', text);
}

/**
 * Modal Maker
 * @param title
 * @param body
 * @param size
 * @param footer
 * @param dissClose
 */
function makeModal(title, body, size='fullscreen', footer=null, dissClose=false) {
    APP.Modal  = bootstrap.Modal.getOrCreateInstance(document.getElementById('app-main-modal'));
    $("#app-main-modal .modal-dialog").removeClass().addClass('modal-dialog modal-dialog-scrollable modal-dialog-centered modal-'+size);
    $("#app-main-modal .modal-title").html('').html(title);
    $("#app-main-modal .modal-body").html('').html(body);
    if (footer) $("#app-main-modal .modal-footer").html(footer + '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
    if (dissClose) {
        $("#app-main-modal").data('keyboard',false).data('backdrop','static')
        $("#app-main-modal .close").hide();
    } else {
        $("#app-main-modal").data('keyboard',true).data('backdrop',true);
        $("#app-main-modal .show").hide();
    }
    APP.Modal.show();
}

/**
 * Modal Frame Maker
 * @param title
 * @param body
 * @param size
 * @param footer
 * @param dissClose
 */
function makeFrameModal(title, body, size='fullscreen', footer=null, dissClose=false) {
    APP.ModalFrame  = bootstrap.Modal.getOrCreateInstance(document.getElementById('app-frame-modal'));
    $("#app-frame-modal .modal-dialog").removeClass().addClass('modal-dialog modal-dialog-scrollable modal-dialog-centered modal-'+size);
    $("#app-frame-modal .modal-title").html('').html(title);
    $("#app-frame-modal .modal-body").html('').html(body);
    if (footer) $("#app-frame-modal .modal-footer").html(footer + '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
    if (dissClose) {
        $("#app-frame-modal").data('keyboard',false).data('backdrop','static')
        $("#app-frame-modal .close").hide();
    } else {
        $("#app-frame-modal").data('keyboard',true).data('backdrop',true);
        $("#app-frame-modal .show").hide();
    }
    APP.ModalFrame.show();
}

/**
 * Get Platform Groups
 */
function getPlatformGroups(){
    $('form#open-tp-demo #group').html('');
    const data = {
        server:  $('#open-tp-demo #platform').val(),
        type:  $('#open-tp-demo #type').val()
    }
    socket.emit("crmGetPlatformGroups", data, (response) => {
        if(response.e)
            appAlert('danger','<i class="fas fa-exclamation-triangle"></i> Error', response.e);
        else{
            if(response.options.length>1){
                $('form#open-tp-demo #group').html(response.options);
            } else {
                appAlert('warning','<i class="fas fa-exclamation-triangle"></i> Error', 'There is not active group for selected platform!');
            }
        }
    });
}

function stopInterval(target){
    clearInterval(target);
    target = null;
}

function stopIntervalModalTempAll(){
    for(let key in intervalModalTemp) {
        if(intervalModalTemp.hasOwnProperty(key)){
            console.log("stopInterval: "+key);
            stopInterval(intervalModalTemp.key);
        }
    }
}

function stopIntervalScreenTempAll(){
    for(let key in intervalModalTemp) {
        if(intervalModalTemp.hasOwnProperty(key)){
            console.log("stopInterval: "+key);
            stopInterval(intervalModalTemp.key);
        }
    }
}

function rDT(when=0){
    let today = new Date();
    const output={};
    output.tdate = new Date();
    if(when!==0)
        output.tdate.setDate(today.getDate() + when);
    output.date = output.tdate.getFullYear()+'-'+(output.tdate.getMonth()+1)+'-'+output.tdate.getDate();
    output.time = output.tdate.getHours() + ":" + output.tdate.getMinutes() + ":" + output.tdate.getSeconds();
    output.dateTime = output.date+' '+output.time;
    return output;
}

function numChangeColor(new_number, old_number){
    if(new_number === old_number)
        return `<span>${new_number}</span>`;
    if (new_number > old_number)
        return `<span class="text-success flush-green">${new_number} <i class="text-white bg-success fa fa-arrow-up"></i></span>`;
    if (new_number < old_number)
        return `<span class="text-danger flush-red">${new_number} <i class="text-white bg-danger fa fa-arrow-down"></i></span>`;
}