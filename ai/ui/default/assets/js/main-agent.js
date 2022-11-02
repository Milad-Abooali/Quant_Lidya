/**
 * Dom Objects
 * @type {*|jQuery|HTMLElement}
 */
const domBody = $("body");


/**
 * HTML Objects
 * @type {string}
 */
const iconSuccess = '<div class="swal2-icon swal2-success swal2-animate-success-icon" style="display: flex;"><div class="swal2-success-circular-line-left" style="background-color: rgb(255, 255, 255);"></div><span class="swal2-success-line-tip"></span><span class="swal2-success-line-long"></span><div class="swal2-success-ring"></div><div class="swal2-success-fix" style="background-color: rgb(255, 255, 255);"></div><div class="swal2-success-circular-line-right" style="background-color: rgb(255, 255, 255);"></div></div>';
const iconError = '<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>';


/**
 * ActivityTimeout
 * @type {number}
 */
var activityTimeout = setTimeout(inActive, 1000*10);


/**
 * Startup
 */

initial();
clearMessagePad();
// Startup - Multi Modal
$(document).on('show.bs.modal', '.modal', function() {
    const zIndex = 1040 + 10 * $('.modal:visible').length;
    $(this).css('z-index', zIndex);
    setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
});
// Startup - Autoload Modal
domBody.on("mousemove", "#wrapper", function(){
    if(visitor.level==='anonymous') $("#settingModal").modal('show');
});
// Startup - Main Loop
// setInterval(function() {
//
//     }, 1000*5);


/**
 * Socket events
 */
var OnlineUsers = {};
var OnlineAgents = {};
var resTimer = {};
// List Chats
socket.on('onlineList', function(data){
    OnlineUsers = data.users;
    OnlineAgents = data.agents;
    let html=' ';
    for (const i in data.users) {
        html += '<li id="chat-'+i+'" data-socket="'+i+'" data-session="'+data.users[i].session+'" class="chat-item">' +
            '            <a><div class="media">' +
            '                <div class="chat-user-img active align-self-center mr-2">' +
            '                    <img src="'+data.users[i].avatar+'" class="rounded-circle avatar-sm" alt="">' +
            '                </div>' +
            '                <div class="media-body overflow-hidden">' +
            '                    <h5 class="text-truncate font-14 mt-0 mb-1 text-danger">'+data.users[i].name+'</h5>' +
            '                    <p class="text-truncate mb-0">' +
            '                        <small class="text-success">'+data.users[i].email+'</small>' +
            '                        <br><small class="text-muted">'+data.users[i].level+'</small>' +
            '                    </p>' +
            '                </div>' +
            '                <div class="font-11"><i class="newMSG mdi mdi-checkbox-blank-circle text-success mr-1 font-11"></i></div>' +
            '            </div></a>' +
            '    </li><hr>';
    }
    $('#chat-list').html(html);
    $('.newMSG').hide();
    scrollChat();
});
// Update Chat
resOffers = {};
socket.on('newClientMessage', function(data){
    resOffers[data.SESSION_ID] = data;
    $('#chat-list #chat-'+data.SESSION_ID+' .newMSG').fadeIn();
    if(data.SESSION_ID === selectedSessionId){
        agentNewHandler();
    }
    progressRes(data.MSG_ID);
});

socket.on('clearNewMsg', function(data){
    delete resOffers[data.SESSION_ID];
    $('#chat-list #chat-'+data.SESSION_ID+' .newMSG').fadeOut();
});

socket.on('SkippedAutoRes', function(data){
    $('#res-'+data+' .skip').remove();
});

socket.on('newClientMessageEnd', function(data){
    $('#chat-list #chat-'+data.SESSION_ID+' .newMSG').fadeOut();
    if(data.SESSION_ID === selectedSessionId){
        $('#res-'+data.MSG_ID).remove();
        handlerUpdateResAgent(resOffers[selectedSessionId].handler,false, function (){
            $('#msg-list .mdi-dots-vertical').hide();
        });
        $('#res-'+data.MSG_ID).removeClass('border border-danger p-3');
        delete resOffers[selectedSessionId];
    }
});


/**
 * Actions Button
 */
// LI - Load Chat
var selectedSessionId = '';
var selectedSocketId = '';
domBody.on("click", "#chat-list .chat-item", function(){
    selectedSessionId = $(this).data('session');
    selectedSocketId = $(this).data('socket');
    clearMessagePad(1);
    if(selectedSessionId) {
        loadUserToMessagePad(OnlineUsers[selectedSocketId]);
        loadMessages(OnlineUsers[selectedSessionId]);
        if(resOffers[selectedSessionId]){
            agentNewHandler();
        }
    } else {
        alert('User is not online anymore!');
    }

});
// Button - Skip Autosend Timer
domBody.on("click", ".do-a-skip", function(event){
    let MSG_ID = $(this).data('msgid');
    socket.emit('skipAutoRes', {
        MSG_ID: MSG_ID
    });
});
// Button - Send Offered Response
domBody.on("click", ".do-a-sendres", function(event){
    let MSG_ID = $(this).data('msgid');
    $('#res-'+MSG_ID).removeClass('border border-danger p-3');
    resOffers[selectedSessionId].MSG_ID = MSG_ID;
    sendAgentRes(resOffers[selectedSessionId]);
    $(this).remove();
});
// Button - Clear & Initial
domBody.on("click", "#do-f-initial", function(){
    cb.Storage.clear();
    initialAgent();
});
// Button - Re Initial
domBody.on("click", "#do-f-reInitial", function(){
    initialAgent();
});
// Button - Login
domBody.on("click", ".doM-login", function(){
    let email = visitor.email;
    commandLogin(email);
});
// Button - Register
domBody.on("click", ".doM-register", function(){
    let email = visitor.email;
    commandRegister(email);
});
// Button - Update Guest
domBody.on("submit", "#update-guest", function(event){
    event.preventDefault();
    localStorage.email = $(this).children('#email').val();
    toastr.options = {
        timeOut: "1050",
        "progressBar": true,
        "positionClass": "toast-top-right",
    }
    toastr.success("<small>Your Session Is Updated");
    socket.disconnect();
    initialAgent();
});
// Button - Logout
domBody.on("click", "#do-a-newSession", function(){
    commandLogout();
});
// Button - Logout
domBody.on("click", ".core-status", function(){
    socket.emit('loginAgent',visitor);
});
// LI - Select TP Login
domBody.on("click", "#tp-cards li", function(event){
    let login = $(this).data('login');
    selectTpLogin(login);
});
// Button - Logout
domBody.on("click", ".load-history", function(event){
    let datetime = $(this).data('date');
    socket.emit('loadMessages', {"datetime":datetime});
    $('.chat-day-title .load-history').fadeOut();
});
// Button - Feedback
domBody.on("click", ".feedback", function(event){
    let data =
        {
            t: $(this).data('feedback'),
            i: $(this).data('mid'),
        };
    // socket.emit("feedBack", data);
});
// Button - Feedback
domBody.on("click", ".addres2rep", function(event){
    let msgId = $(this).data('mid');
    let question = $("#msg-q-"+msgId).text();
    let body = addres2rep;
    let footer = '<input type="submit" form="addres2rep" class="do-a-addres2rep btn btn-primary" value="Add">';
    footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
    makeModal('Add Answer', body, 'md', footer);
    $('#modalMain #question').val(question);
    $('#modalMain #msgid').val(msgId);
    $('#modalMain #user-id').val(visitor.server.isLogin);
});
// Push to Read Response
domBody.on("click", ".autoRead", function(event){

    let text;
    let speechSpecial = $(this).data('speechSpecial');
    if(speechSpecial) {
        text = $(this).closest('div').next().find('.speechOn').text();
    } else {
        text = $(this).closest('div').next().find('p').text();
    }
    readRes(text,true);
});
// Push to Read Section
domBody.on("click", ".secSpeech", function(event){
    let text = $(this).closest('div.secRes').find('button.secSpeechOn').text();
    readRes(text,true);
    text = $(this).closest('div').next().find('.secSpeechOn').text();
    readRes(text,true);
});

/**
 * Selector
 */
// General
domBody.on("click", ".do-a-selector", function(event){
    let listener = $(this).data('listener');
    let topic = $(this).data('topic');
    selectOption(listener, topic);
});
// TP Login
domBody.on("click", ".do-a-sel-login", function(event){
    let login = $(this).data('item');
    selectTpLogin(login);
});


/**
 * Submit Forms
 */
// Form - Register
domBody.on("submit", "form#register", function(event){
    event.preventDefault();
    const domFormRegister = $('form#register');
    let data = $(this).serialize();
    aiAjaxCall('commandRegister', data, function(session){
        if(session['IS_LOGIN']===true) {
            $('.do-a-formRegister').hide();
            afterLogin(session, domFormRegister);
        }
        else {
            formFail(session, domFormRegister);
        }
        cb.C.info(session);
    });
});
// Form - Recover
domBody.on("submit", "form#recover", function(event){
    event.preventDefault();
    const domFormRecover = $('form#recover');
    let data = $(this).serialize();
    aiAjaxCall('commandRecover', data, function(session){
        if(session.res) {
            $('.do-a-formRecover').hide();
            domFormRecover.html(iconSuccess);
            domFormRecover.append('<h5>An email sent for recovery password.</h5>');
            setTimeout(() => {
                $('#modalMain').modal('toggle');
                let tempUUID = uuidv4();
                addResMessage(tempUUID);
                rubyStatus('busy');
                $('#msg-list #res-'+tempUUID+' .conversation-text p').html('Please check your email.');
                $('#msg-list #res-'+tempUUID+' #status')
                    .removeClass()
                    .addClass("fa fa-check text-info")
                    .attr('data-original-title','Ruby is online.');
                rubyStatus('online');
                readRes('Please check your email.');
            }, 1800);
        } else {
            formFail(session, domFormRecover);
        }
        cb.C.info(session);
    });
});
// Form - Login
domBody.on("submit", "form#login", function(event){
    event.preventDefault();
    const domFormLogin = $('form#login');
    let data = $(this).serialize();
    aiAjaxCall('commandLogin', data, function(session){
        if(session['IS_LOGIN']===true) {
            $('.do-a-formLogin').hide();
            afterLogin(session, domFormLogin);
        } else {
            formFail(session, domFormLogin);
        }
        cb.C.info(session);
    });
});
// Form - Handler
domBody.on("submit", "form#sendCall", function(event){
    event.preventDefault();
    const domInputText = $('#sendCall #input-text');
    let inputText = domInputText.val();
    let handler = JSON.parse(resOffers[selectedSessionId].handler);
    handler.sessionId = selectedSessionId;
    handler.engine = 'HI';
    handler.res = inputText;
    resOffers[selectedSessionId].handler = JSON.stringify(handler);
    resOffers[selectedSessionId].MSG_ID = handler.MSG_ID;
    sendAgentRes(resOffers[selectedSessionId]);
    $('#res-'+handler.MSG_ID).remove();
    handlerUpdateResAgent(resOffers[selectedSessionId].handler,false, function (){
        $('#msg-list .mdi-dots-vertical').hide();
        scrollChat();
    });
});
// Form - Update Item
domBody.on("submit", "form#update-item", function(event){
    let thisForm = $('form#update-item');
    event.preventDefault();
    let data = $(this).serialize();
    aiAjaxCall('commandUpdateItem', data, function(sess){
        if(sess.res) {
            $('.do-a-update-item').hide();
            let title = $('form#update-item #title').val();
            let newVal = $('form#update-item #val').val();
            thisForm.html(iconSuccess);
            thisForm.append('<h5>Item Updated.</h5>');

            setTimeout(() => {
                $('#modalMain').modal('toggle');

                let tempUUID = uuidv4();
                addResMessage(tempUUID);
                rubyStatus('busy');
                let html = 'Your '+title+' updated to '+newVal;
                $('#msg-list #res-'+tempUUID+' .conversation-text p').html(html);
                $('#msg-list #res-'+tempUUID+' #status')
                    .removeClass()
                    .addClass("fa fa-check text-info")
                    .attr('data-original-title','Ruby is online.');
                rubyStatus('online');
                readRes(html);

            }, 1800);

        } else {
            let old = thisForm.html();
            thisForm.html(iconError);
            thisForm.append('<h5>'+sess.ERROR+'</h5>');
            thisForm.append('<small class="do-a-reForm btn btn-warning"><i class="fa fa-repeat"></i> Try again</small>');

            $("body").on("click", ".do-a-reForm", function(event){
                thisForm.html(old);
                setTimeout(() => {$('#modalMain .modal-body input:first').focus()}, 500);
            });

        }
        console.log(sess);
    });
});
// Form - Add Response
domBody.on("submit", "form#addres2rep", function(event){
    event.preventDefault();
    const domFormAddres2rep = $('form#addres2rep');
    let data = $(this).serialize();
    aiAjaxCall('addRes2Rep', data, function(ajaxRes){
        if(ajaxRes.res>0) {
            $('.do-a-addres2rep').hide();
            domFormAddres2rep.html(iconSuccess);
            domFormAddres2rep.append('<h5>Your answer has been added.</h5>');
            setTimeout(() => {
                $('#modalMain').modal('toggle');
            }, 1800);
        } else {
            formFail(ajaxRes.e, domFormAddres2rep);
        }
    });
});

/**
 * Config
 */
// Config - Input Keep Listening
domBody.on("change", "#do-a-input_keep_listening", function(){
    localStorage.configInputKeepListening = $('#do-a-input_keep_listening').is(":checked") ? 1 : 0;
    interfaceUpdate();
});
// Config - Input Voice Autosend
domBody.on("change", "#do-a-input_voice_autosend", function(){
    localStorage.configInputVoiceAutosend = $('#do-a-input_voice_autosend').is(":checked") ? 1 : 0;
    interfaceUpdate();
});
// Config - Input Voice Interim
domBody.on("change", "#do-a-input_voice_interim", function(){
    localStorage.configInputVoiceInterim = $('#do-a-input_voice_interim').is(":checked") ? 1 : 0;
    interfaceUpdate();
});
// Config - Engine Wikipedia
domBody.on("change", "#do-a-engine_wikipedia", function(){
    localStorage.configWikipedia = $('#do-a-engine_wikipedia').is(":checked") ? 1 : 0;
    interfaceUpdate();
});
// Config - Engine Glossary
domBody.on("change", "#do-a-engine_glossary", function(){
    localStorage.configGlossary = $('#do-a-engine_glossary').is(":checked") ? 1 : 0;
    interfaceUpdate();
});
// Config - Auto Speech
domBody.on("change", "#do-a-auto_speech", function(){
    localStorage.configAutoSpeech = $('#do-a-auto_speech').is(":checked") ? 1 : 0;
    window.speechSynthesis.cancel();
    interfaceUpdate();
});
// Config - Bip Sound
domBody.on("change", "#do-a-bip_sound", function(){
    localStorage.configBipSound = $('#do-a-bip_sound').is(":checked") ? 1 : 0;
    interfaceUpdate();
});


/**
 * Plate Menu
 */
const plateMenuE = $('#plateMenu');
// Plate Menu - Show
$('#profile-menu').on('click', function() {
    setTimeout(() => {plateMenuE.slideDown();}, 200);
});
// Plate Menu - Hide
window.addEventListener('click', function(e){
    if (document.getElementById('plateMenu').contains(e.target)){
        // Clicked in box
    } else{
        plateMenuE.slideUp();
    }
});
// Plate Menu - Modal Hide
domBody.on("click", "#plateMenu .domodal li", function(){
    plateMenuE.slideDown();
    setTimeout(() => {
        let link = $(this).data('link');
        if(link === 'myProfile'){
            let body = '<div class="card pmd-card" style="padding: 0;"><div class="card-body" id="load-user-details" style="padding: 15px 0;"></div></div>';
            makeModal('Profile', body,'xl');
            let url = "../../user-details.php?code=1&type=profile";
            $('#load-user-details').load(url,function(result){});
        } else {
            let topic = $(this).data('topic');
            selectOption(link, topic);
        }
    }, 500);
});
// Plate Menu - Edit History
domBody.on("click", "#plateMenu .do-a-historyEdit", function(){
    let text = $(this).parent().prev().html();
    $('#input-text').val(text);
});
// Plate Menu - Send History
domBody.on("click", "#plateMenu .do-a-historySend", function(){
    let text = $(this).parent().prev().html();
    $('#input-text').val(text);
});
// Plate Menu - Add favorite
domBody.on("click", "#plateMenu .do-a-favoriteAdd", function(){
    let realThis = $(this);
    let msgId = $(this).data('msgid');
    let data = {
        id:msgId,
        favorite:1
    }
    aiAjaxCall ('favMSG', data, function(res){
        if(res.res) {
            updateFavList(1);
            realThis.hide();
            $("[data-toggle='tooltip']").tooltip('hide');
        }
    });
});
// Plate Menu - Del favorite
domBody.on("click", "#plateMenu .do-a-favoriteDrop", function(){
    let msgId = $(this).data('msgid');
    let data = {
        id:msgId,
        favorite:0
    }
    aiAjaxCall ('favMSG', data, function(res){
        if(res.res) updateFavList(1);
        $("[data-toggle='tooltip']").tooltip('hide');
    });
});


/**
 * End Fixes
 */
// Scroll
domBody.on("click", "#msg-list .dropdown-toggle", function(){
    scrollChat();
});
// Stop Audio
domBody.on("keypress", "#input-text", function() {
    $( "#do-a-input-audio-stop" ).trigger( "click" );
});
// Body Click
$(document).bind('click', function(){

});
// Body Keyup
$(document).bind('keyup', function(){
    resetActive();
});
// Body Mouseover
$(document).bind('mouseover', function(){

});
// Input English only
$(document).on("keypress", "#sendCall input#input-text", function (event) {
    return suppressNonEng(event);
});


/**
 * Mods
 */
// Wikipedia
var wikipedia = {};
domBody.on("click", ".doM-wikipedia", function(event){
    let wikipediaMsgId = $(this).attr('data-msgId');
    let title = 'Wikipedia';
    let msgGlossary = JSON.parse(sessionStorage.wikipedia)[wikipediaMsgId];
    let body = '<table class="table table-sm table-striped table-bordered">';
    body    += '<thead><tr><th>Related Pages</th><th>Snippet</th></tr></thead>';
    $.each(msgGlossary, function(index) {
        body    += '<tr class=" ">';
        body    += '<td class=" ">'+this.title+'</td>';
        body    += '<td class=" ">'+this.snippet+'</td>';
        body    += '</tr>';
    });
    body    += '</table>';
    makeModal(title,body,'lg');
});
// Glossary
var glossary = {};
domBody.on("click", ".doM-glossary", function(event){
    let glossaryMsgId = $(this).attr('data-msgId');
    let title = 'Glossary';
    let msgGlossary = JSON.parse(sessionStorage.glossary)[glossaryMsgId];
    let body = '<table class="table table-sm table-striped table-bordered">';
    body    += '<thead><tr><th>Related Phrase</th><th>Category</th><th>Source</th><th>Detail</th></tr></thead>';
    $.each(msgGlossary, function(index) {
        let link = this.glossary.replace(" ", "-");
        body    += '<tr class=" ">';
        body    += '<td class=" ">'+this.glossary+'</td>';
        body    += '<td class=" ">'+this.cat+'</td>';
        body    += '<td class=" ">'+this.set_name+'</td>';
        body    += '<td class=" "><a target="_blank" href="https://www.babypips.com/forexpedia/'+link+'">Show</a></td>';
        body    += '</tr>';
    });
    body    += '</table>';
    makeModal(title,body,'lg');
});