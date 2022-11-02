const cb = new cbkits(1);

/**
 * Initial Ruby Client Side
 */
function initial(){
    $('.level-status').html('').addClass('d-none');
    cb.C.group('initial');
    if(visitor.server.isLogin>0){
        cb.C.info('User is login.','Level','CHECK');
        visitor.level = 'user';
        localStorage.name   = visitor.name   = visitor.server.session.user.user_extra.fname;
        localStorage.email  = visitor.email  = visitor.server.session.user.email;
        localStorage.avatar = visitor.avatar = visitor.server.session.avatar;
        cb.C.success(visitor.name,'visitor.name','GET');
        cb.C.success(visitor.email,'visitor.email','GET');
        cb.C.success(visitor.avatar,'visitor.avatar','GET');
        cb.C.success(visitor.level,'visitor.level','SET');
        checkTps();
    } else {
        if(localStorage.email) {
            cb.C.info('User is not login.','Level','CHECK');
            visitor.level  = 'guest';
            visitor.name   = 'guest';
            visitor.email  = localStorage.email;
            cb.C.success(visitor.level,'visitor.level','SET');
            visitor.avatar = 'https://www.gravatar.com/avatar/'+md5(visitor.email);
            cb.C.success(visitor.name,'visitor.name','LOAD');
            cb.C.success(visitor.email,'visitor.email','LOAD');
            cb.C.success(visitor.avatar,'visitor.avatar','LOAD');

            // Check from server if user exist and force to login
            aiAjaxCall('client', {a:'isUserExist',email:visitor.email}, function(resClient){
                if(resClient.isUserExist){
                    cb.C.log('User is exist for the email address, force to login.','Level','CHECK');
                    $('.level-status')
                        .removeClass('d-none')
                        .html('You have an account here, please <span class="doM-login text-primary">login</span> ...');
                } else {
                    cb.C.log('User is not exist for the email address, force to register.','Level','CHECK');
                    $('.level-status')
                        .removeClass('d-none')
                        .html('Welcome guest, you can <span class="doM-register text-primary">creat an account</span> ...');
                }
            });

        } else {
            cb.C.warning('Visitor is anonymous!','Level','CHECK');
            visitor.level  = 'anonymous';
            visitor.name   = null;
            visitor.email  = null;
            visitor.avatar = 'assets/img/guest.png';
            cb.C.success(visitor.level,'visitor.level','SET');
            $('#settingModal #i-form-session').removeClass('d-none');
            $('#settingModal #i-form-login').removeClass('d-none');
        }

    }
    cb.C.gEnd('Initial');
    $('img.visitor-avator').attr('src', visitor.avatar);
    $('span.visitor-name').text(visitor.name);
    $('input.visitor-name').val(visitor.name);
    $('span.visitor-email').text(visitor.email);
    $('input.visitor-email').val(visitor.email);

    if(visitor.email) {
        socket.open();
        console.dir(visitor);
        if(socketLevel==='a'){
            socket.emit('loginAgent',visitor);
        } else if(socketLevel==='c') {
            socket.emit('login',visitor);
        }
    }
    else{
        socket.disconnect();
    }
    interfaceUpdate();
}

/**
 * interfaceUpdate
 */
function interfaceUpdate() {
    $('*[class^="visitor-level-"]').fadeOut('fast');
    $('.visitor-level-'+visitor.level).hide().removeClass('d-none').fadeIn('fast');
    $('#client-name').html(visitor.name || 'Guest');
    $('#client-role').html(visitor.role || visitor.email);

    $('i#voice-output-status')
        .removeClass()
        .addClass((localStorage.configBipSound==1) ? 'fa fa-volume-up text-success' : 'fa fa-volume-off text-suc')
        .attr('data-original-title',(localStorage.configBipSound==1) ? 'Enabled' : 'Disabled');

    $('i#voice-input-status')
        .removeClass()
        .addClass((localStorage.configAutoSpeech==1) ? 'fa fa-microphone text-success' : 'fa fa-microphone text-muted')
        .attr('data-original-title',(localStorage.configAutoSpeech==1) ? 'Enabled' : 'Disabled');

    $('i#auto-send-status')
        .removeClass()
        .addClass((localStorage.configInputVoiceAutosend==1) ? 'mdi mdi-send text-success' : 'mdi mdi-send text-muted')
        .attr('data-original-title',(localStorage.configInputVoiceAutosend==1) ? 'Enabled' : 'Disabled');

    if(localStorage.configInputKeepListening==1) $('#do-a-input_keep_listening').prop("checked", true);
    if(localStorage.configInputVoiceAutosend==1) $('#do-a-input_voice_autosend').prop("checked", true);
    if(localStorage.configInputVoiceInterim==1) $('#do-a-input_voice_interim').prop("checked", true);
    if(localStorage.configWikipedia==1) $('#do-a-engine_wikipedia').prop("checked", true);
    if(localStorage.configGlossary==1) $('#do-a-engine_glossary').prop("checked", true);
    if(localStorage.configAutoSpeech==1) $('#do-a-auto_speech').prop("checked", true);
    if(localStorage.configBipSound==1) $('#do-a-bip_sound').prop("checked", true);

    cb.C.info('Interface Updated.');
}

/**
 * Update After Login
 */
function afterLogin(session, dom=null){
    if(dom !== null){
        dom.html(iconSuccess);
        dom.append('<h5>Welcome '+session.user.user_extra.fname+'</h5>');
    }
    readRes('Welcome '+session.user.user_extra.fname);
    visitor.server.session = session;
    visitor.server.isLogin = session.user.id;
    visitor.name = session.user.user_extra.fname;
    if(session.avatar) visitor.avatar =  session.avatar;
    initial();
    setTimeout(() => {
        $('#modalMain').modal('hide');
        tpCardsUpdaterLock = false;
        let tempUUID = uuidv4();
        addResMessage(tempUUID);
        rubyStatus('busy');
        $('#msg-list #res-'+tempUUID+' .conversation-text p').html('What you want to do now, '+session.user.user_extra.fname+' ?');
        $('#msg-list #res-'+tempUUID+' #status')
            .removeClass()
            .addClass("fa fa-check text-info")
            .attr('data-original-title','Ruby is online.');
        rubyStatus('online');
        readRes('What you want to do now, '+session.user.user_extra.fname+' ?');
        checkTps();
        $('#settingModal').modal('hide');
    }, 2000);
}

/**
 * Form Fail
 */
function formFail(session, dom){
    let old = dom.html();
    dom.html(iconError);
    dom.append('<h5>'+session.ERROR+'</h5>');
    dom.append('<small class="do-a-reLogin btn btn-warning"><i class="fa fa-repeat"></i> Try again</small>');
    domBody.on("click", ".do-a-reLogin", function(){
        dom.html(old);
        setTimeout(() => {$('#modalMain .modal-body input:first').focus()}, 500);
    });
}

/**
 * Command Login
 */
function commandLogin(email='', element=null){
    let body = formLoginHTML;
    let footer = '<input type="submit" form="login" class="do-a-formLogin btn btn-primary" value="Login">';
    footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
    if(element==null){
        makeModal('Login', body,'sm',footer);
        $('#modalMain #username').val(email);
    }else{
        $(element).html(body+footer);
        $(element+' #username').val(email);
    }
}

/**
 * Command Register
 */
function commandRegister(email='', element=null){
    let body = formRegisterHTML;
    let footer = '<input type="submit" form="register" class="do-a-formRegister btn btn-primary" value="Register">';
    footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
    if(element==null){
        makeModal('Register', body,'sm',footer);
        $('#modalMain #email').val(email);
    }else{
        $(element).html(body+footer);
        $(element+' #email').val(email);
    }
}

/**
 * Command Recover
 */
function commandRecover(email='', element=null){
    let body = formRecoverHTML;
    let footer = '<input type="submit" form="recover" class="do-a-formRecover btn btn-primary" value="Recover Password">';
    footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
    makeModal('Password Recovery', body, 'sm', footer);
    if(element==null){
        makeModal('Password Recovery', body, 'sm', footer);
        $('#modalMain #email').val(email);
    }else{
        $(element).html(body+footer);
        $(element+' #email').val(email);
    }
}

/**
 * Command Logout
 */
function commandLogout(){
    const r = confirm("Logout this session?");
    if (r === true) {
        aiAjaxCall('session', 'clear=1', function(reset){
            readRes('Logout is done.');
            setTimeout(() => {
                location.reload();
            }, 200);
        });
    } else {
        // location.reload();
        $("#input-text").focus();
    }
}

/**
 * Add User Message
 */
function addUserMessage(user, messageId, avatar, message) {
    if(localStorage.configBipSound==1) callAudio.play();
    let item  = '<li id="msg-'+messageId+'" class="odd"><div class="message-list">';
    item += '<div class="chat-avatar"><img clsss=" " src="'+avatar+'" alt=""><i class="autoRead fa fa-play-circle btn btn-sm btn-light"></i></div>';
    item += '<div class="conversation-text"><div class="ctext-wrap">';
    item += '<span class="user-name">'+user+'</span><p id="msg-q-'+messageId+'">';
    item += message;
    let currentdate = new Date();
    let datetime = currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();

    item += '</p></div><div class="dropdown d-none">'+
        '<a href="#" class="dropdown-toggle arrow-none card-drop font-20" data-toggle="dropdown" aria-expanded="false">'+
        '<i class="mdi mdi-dots-vertical"></i></a>'+
        '<div class="dropdown-menu dropdown-menu-right">'+
        '<a href="javascript:void(0);" class="item-cancel dropdown-item">Cancel Call</a>'+
        '<div class="dropdown-divider"></div>'+
        '<a href="javascript:void(0);" class="item-retry dropdown-item">ReSend</a>'+
        '</div></div>';

    item += '<span class="time"><i id="status" data-toggle="tooltip" data-placement="left" title="Sending..." class="text-warning fa fa-spinner fa-pulse"></i> '+datetime+'</span>';
    item += '</div></div></li>';
    $('#msg-list').append(item);
    $('#sendCall #input-text').val('');
    scrollChat();
}

/**
 * Add Response Message
 */
function addResMessage(resId) {
    let item  = '<li id="res-'+resId+'" class=" "><div class="message-list">';
    item += '<div class="chat-avatar"><img class="bg-light" src="assets/img/profile.png" alt=""><i class="autoRead fa fa-play-circle btn btn-sm btn-light"></i></div>';
    item += '<div class="conversation-text"><div class="ctext-wrap">';
    item += '<span class="user-name">Ruby</span>';
    item += '<p><img class="ai-loading" src="assets/img/loading/ai.gif" alt=""></p>';
    let currentdate = new Date();
    let datetime = currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
    item += '</div><div id="feedback" class="dropdown">'+
        '<a href="#" class="dropdown-toggle arrow-none card-drop font-20" data-toggle="dropdown" aria-expanded="false">'+
        '<i id="feedback-menu" class="mdi mdi-dots-vertical"></i></a>'+
        '<div class="dropdown-menu ">'+
        '<a href="javascript:void(0);" data-mid="'+resId+'" data-feedback="1" class="feedback dropdown-item"><i class="pr-2 far fa-smile text-success"></i> Satisfied</a>'+
        '<div class="dropdown-divider"></div>'+
        '<a href="javascript:void(0);" data-mid="'+resId+'" data-feedback="0" class="feedback dropdown-item"><i class="pr-2 far fa-frown text-danger"></i> Wrong answer</a>'+
        '</div>';
    if(resId>0 && visitor.server.session.type == 'Admin') {
        item += '<div><i data-mid="'+resId+'" class="addres2rep p-2 pt-3 text-muted fa fa-edit"></i></div>';
    }
    item += '</div><span class="time">'+datetime+' <i id="status" data-toggle="tooltip" data-placement="right" title="seen by Ruby" class="fa fa-check text-success"></i></span>';
    item += '</div></div></li>';
    $('#msg-list').append(item);
    scrollChat();
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

    $('#engines-count').text(status.engines);
    $('#addons-count').text(status.addons);
    $('#clients-count').text(status.onlineClients);
    $('#agents-count').text(status.onlineAgents);
}

/**
 * StatusColor
 */
function statusColor(target, status=0, alert='muted',tTip='Online'){
        let color = (status) ? 'text-success' : 'text-'+alert;
        let title = (status) ? tTip : 'Offline';
        target.removeClass('text-muted text-success').removeClass().addClass('mdi mdi-checkbox-blank-circle mr-1 font-11 '+color).attr('data-original-title',title);
}

/**
 * Show and Hide Tooltip
 * @param element
 * @param time
 */
function flashTooltip(element,time=1750){
    element.trigger("mouseenter")
    setTimeout(function(){
        element.trigger("mouseleave");
    }, time);
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
 * Shortcuts
 */
window.onkeydown = function(e) {
    e = e || window.event;
    var k = e.keyCode || e.which;
    switch(k) {
        case 27: // Esc
            $(":focus").blur();
            $('#plateMenu').slideUp();
            $("#input-text").focus();
            return false;
        case 32:    // Space
            if( e.ctrlKey) {
                $("#input-text").focus();
                $('#plateMenu').slideDown();
                updateFavList(1);
                return false;
            } else {
                return true;
            }
            return false;
        case 39:    // Right Arrow
            if( e.ctrlKey) {
                $( "#do-a-Send" ).trigger( "click" );
                $('#plateMenu').slideUp();
                $("#input-text").focus();
                return false;
            } else {
                return true;
            }
            return false;
        case 76:    // L
            if( e.altKey ) {
                selectOption( (visitor.server.isLogin>0) ? 'CommandLogout' : 'CommandLogin' );
                return false;
            } else {
                return true;
            }
            return false;
        case 82:    // R
            if( e.altKey ) {
                if(visitor.server.isLogin>0) selectOption('CommandRegister');
                return false;
            } else {
                return true;
            }
            return false;
        case 80:    // P
            if( e.altKey ) {
                selectOption('CommandRecover');
                return false;
            } else {
                return true;
            }
            return false;
        case 13: // Enter
            $( "#do-a-Send" ).trigger( "click" );
            $('#plateMenu').slideUp();
            $("#input-text").focus();
            return false;
    }
    return true;
}

/**
 * Auto Scroll Chat
 */
function scrollChat(){
    var scrollTo_int = $('#msg-list').prop('scrollHeight') + 'px';
    $('#msg-list').slimScroll({
        scrollTo : scrollTo_int,
        start: 'bottom',
        alwaysVisible: true
    });
}

/**
 * Insert Chat Separator
 */
function insertChatSeparator(datetime) {
    const date = datetime.getFullYear() + "-" + (datetime.getMonth()+1) + "-" + datetime.getDate();
    const timestamp   = '<span data-placement="top" data-toggle="tooltip" title="'+date+'"class="text-light">'+datetime.toDateString()+'</span>';
    const historyLink = '<span data-date="'+date+'" class="load-history visitor-level-user ml-1 btn btn-sm btn-outline-light2 rounded-circle" data-placement="top" data-toggle="tooltip" title="Load More"><i class="fa fa-angle-up"></i></span>';
    $('#msg-list').prepend('<li><div class="chat-day-title"><span class="title">'+timestamp+historyLink+'</span></div></li>');
}

/**
 * Reset Status to Active
 */
function resetActive(){
    rubyStatus('online');
    clearTimeout(activityTimeout);
    activityTimeout = setTimeout(inActive, 1000*10);
}

/**
 * Set Idle Status
 */
function inActive(){
    rubyStatus('idle');
}

/**
 * Suppress Other than English
 * @param EventKey
 * @returns {boolean}
 */
function suppressNonEng(EventKey) {
    var key = EventKey.which || EventKey.keyCode;
    if (key > 128) {
        toastr.error("Only English is allowed");
        return false;
    }
    else {
        return true;
    }
}

/**
 * Archive Message
 * @param msgId
 */
function archiveMsg(msgId) {
    if(socketLevel==='c'){
        let archive = {
            msg_id: msgId,
            msg: $('#msg-list #msg-'+msgId).html(),
            res: $('#msg-list #res-'+msgId).html()
        }
        socket.emit('archiveMessages', archive);
    }
}

/**
 * Modal Maker
 * @param title
 * @param body
 * @param size
 * @param footer
 * @param dissClose
 */
function makeModal(title,body,size='md',footer=null,dissClose=false) {
    $("#modalMain .modal-dialog").removeClass().addClass('modal-dialog modal-'+size);
    $("#modalMain .modal-title").html('').html(title);
    $("#modalMain .modal-body").html('').html(body);
    $("#modalMain .modal-footer").html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
    if (footer) $("#modalMain .modal-footer").html(footer);
    if (dissClose) {
        $("#modalMain").data('keyboard',false).data('backdrop','static')
        $("#modalMain .close").hide();
    } else {
        $("#modalMain").data('keyboard',true).data('backdrop',true);
        $("#modalMain .show").hide();
    }
    $("#modalMain").modal('show');
}

// Clear Message Pad
function clearMessagePad(header=false) {
    $('#msg-list').html('');
    if(header) $('#setting-status h5').fadeOut();
}

// Client Handler
function handlerUpdateRes(data, archive, callback=null) {
    const handler = JSON.parse(data);
    const tempId = handler['UUID'] || '';
    if(handler.e || !handler['MSG_ID']) {

        // Handler Error
        $('#msg-list #msg-'+tempId+' #status')
            .removeClass()
            .addClass("fa fa-times text-danger")
            .attr('data-original-title','Error on sending!')
            .attr('title','Error on sending!')
            .data('title','Error on sending!');
        rubyStatus('offline');

    } else {

        // Replace UUID with MsgId
        $('#msg-list #msg-'+tempId).attr("id", 'msg-'+handler['MSG_ID']);
        $('#msg-list #msg-q-'+tempId).attr("id", 'msg-q-'+handler['MSG_ID']);

        // Add response
        addResMessage(handler['MSG_ID']);

        // Update response status
        $('#msg-list #msg-'+handler['MSG_ID']+' #status')
            .removeClass()
            .addClass("fa fa-check text-success")
            .attr('data-original-title', 'seen by Ruby')
            .attr('title', 'seen by Ruby')
            .data('title', 'seen by Ruby');

        // Update engine detail
        $('input[name="listener"]').val(handler.listener);
        $('input[name="topic"]').val(handler.topic);
        $('input[name="walker"]').val(handler.walker);
        $('input[name="step"]').val(handler.step);
        $('input[name="seltpaccount"]').val(handler.seltpaccount);
        $('input[name="engine"]').val(handler.engine);
        $('input[name="nengine"]').val(handler.nengine);
        $('input[name="section"]').val(handler.section);
        selectTpLogin(handler.seltpaccount || localStorage.selectedLogin);

        /*
         * Process handler response
         */

        //  Engine > Brain > FAQ
        if (handler.engine === 'FAQ')
        {

            // Process response
            let answer = '';
            let faqObj = handler.brain.FIG_TYPE['FAQ'] || handler.brain.FIG_TYPE['isFaq'];
            delete faqObj['def'];
            if(Object.keys(faqObj).length < 2) {

                // Generate answer
                answer += faqObj[0].a;

                // Seal Process
                seal(handler, answer);

            } else {

                // Generate answer
                answer += '<span class="speechOn">Which one is your desired information?</span>';
                answer += '<hr><div id="accordion-'+handler['MSG_ID']+'">';
                $.each(faqObj, function (index) {
                    if(this['faq']) {
                        answer += '<div class="secRes"><div class=" ">';
                        answer += '<button class="btn btn-sm btn-block btn-light mb-1 speechOn secSpeechOn" data-toggle="collapse" data-target="#i-' + handler['MSG_ID'] + '-' + index + '">' + this.faq + '</button></div>';
                        answer += '<div id="i-' + handler['MSG_ID'] + '-' + index + '" class="collapse card p-2" data-parent="#accordion-' + handler['MSG_ID'] + '">';
                        answer += '<div class="text-right"><i class="secSpeech fa fa-play-circle btn btn-sm btn-outline-secondary"></i></div>';
                        answer += '<div class=" text-dark"><p class="secSpeechOn">' + this.a + '</p></div>';
                        answer += '</div></div>';
                        if(Object.keys(faqObj).pop() !== index) {
                            answer += '<v class="speechOn">?  or  </v>';
                        } else {
                            answer += '<v class="speechOn">?</v>';
                        }
                    }
                });
                answer += '</div>';

                // Seal Process
                seal(handler, answer, true);

            }

        }
        //  Engine > Brain > Form
        else if(handler.engine === 'Form')
        {
            inputRecognizingStopper=true;

            if (handler.listener === 'CommandRegister') {

                // Generate form
                let body = formRegisterHTML;
                let footer = '<input type="submit" form="register" class="do-a-formRegister btn btn-primary" value="Register">';
                footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                makeModal('Register', body, 'sm', footer);
                $('#modalMain #email').val(handler.topic);

                // Generate answer

                // Seal Process
                seal(handler);

            } else if(handler.listener === 'CommandRecover') {

                // Generate form
                let body = formRecoverHTML;
                let footer = '<input type="submit" form="recover" class="do-a-formRecover btn btn-primary" value="Recover Password">';
                footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                makeModal('Password Recovery', body, 'sm', footer);
                $('#modalMain #email').val(handler.topic);

                // Generate answer

                // Seal Process
                seal(handler);

            } else if (handler.listener === 'CommandLogin') {

                // Generate form
                let body = formLoginHTML;
                let footer = '<input type="submit" form="login" class="do-a-formLogin btn btn-primary" value="Login">';
                footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                makeModal('Login', body, 'sm', footer);
                $('#modalMain #username').val(handler.topic);

                // Generate answer

                // Seal Process
                seal(handler);

            }
            if (handler.listener === 'CommandLogout') {

                // Load Confirmation alert
                $("#do-a-newSession").trigger('click');

            }
        }
        //  Engine > Brain > Command
        else if(handler.engine === 'Command')
        {

            // Generate answer
            let answer = '';
            let cmdObjUnique = uniqueArray(handler['res'], 'res');
            if(Object.keys(cmdObjUnique).length < 2) {
                if(cmdObjUnique[0]) {
                    $('#listener').val(cmdObjUnique[0].listener);
                    $('#topic').val(cmdObjUnique[0].topic);
                    answer += cmdObjUnique[0].res;
                } else {
                    answer += 'This command is not available now!';
                }

                // Seal Process
                seal(handler, answer);

            } else {
                answer += '<span class="speechOn">Which one is your desired command?</span>';
                answer += '<hr><div id="accordion-' + handler['MSG_ID'] + '">';
                $.each(cmdObjUnique, function (index) {
                    if (this.listener) {
                        if (index > 0) {
                            answer += '<v class="speechOn">  or  </v>';
                        }
                        answer += '<div class="secRes"><div class=" ">';
                        answer += '<button class="do-a-selector btn btn-sm btn-block btn-light mb-1 speechOn secSpeechOn" data-listener="Command' + this.listener + '"  data-topic="' + this.topic + '" data-toggle="collapse" data-target="#i-' + handler['MSG_ID'] + '-' + index + '">' + this.res + '</button></div>';
                        answer += '</div>';
                    } else {
                        answer += '<div class="secRes"><div class=" ">';
                        answer += '<span class="btn btn-sm btn-outline-light disabled btn-light mb-1" data-toggle="collapse" data-target="#i-' + handler['MSG_ID'] + '-' + index + '">' + (this.res || 'This command is not available now!') + '</span></div>';
                        answer += '</div>';
                    }
                });
                answer += '</div>';

                // Seal Process
                seal(handler, answer, true);

            }

        }
        //  Engine > Brain > Handy
        else if(handler.engine === 'Handy')
        {
            socket.emit('apiAIML', data);
            rubyStatus('online');

        }
        //  Engine > Brain > QA
        else if(handler.engine === 'QA')
        {
            if (handler['res'] !== handler.engine) {

                // Seal Process
                seal(handler);

            } else {
                socket.emit('apiAIML', data);
                rubyStatus('online');
            }
        }
        //  Engine > Brain > AIO
        else if(handler.engine === 'AIO')
        {
            socket.emit('apiAIML', data);
            rubyStatus('online');
        }
        //  Engine > Brain > Glossary
        else if(handler.engine === 'Glossary')
        {

            // Process response
            let glossaryObj = handler.brain.FIG_TYPE['isGlossary'];
            let items = [];
            $.each(glossaryObj, function (index) {
                delete this['def'];
                $.each(this, function (index) {
                    items.push(this);
                });
            });
            glossary[handler['MSG_ID']] = items;
            sessionStorage.glossary = JSON.stringify(glossary);
            let glossaryText = '<div class="border-top pt-2">';
            glossaryText += '<button  data-msgId="' + handler['MSG_ID'] + '" class="doM-glossary btn btn-block btn-light text-secondary"><i data-toggle="tooltip" data-placement="left" title="Engine: Glossary"  class="fab fa-leanpub text-warning"></i> ' + Object.keys(items).length + ' result</button>';
            glossaryText += '</div>';
            $('#res-' + handler['MSG_ID'] + ' p').html(glossaryText);
            $('#msg-list #res-' + handler['MSG_ID'] + ' #status')
                .removeClass()
                .addClass("fa fa-cog text-info")
                .attr('data-original-title', 'Engine: ' + handler.engine)
                .attr('title', 'Engine: ' + handler.engine)
                .data('title', 'Engine: ' + handler.engine);
            if (localStorage.configBipSound) resAudio.play();
            rubyStatus('online');

        }
        //  Engine > AIPF
        else if(handler.engine === 'AIPF')
        {
            // Process handler response
            if(typeof handler['res'] === 'object' && handler['res'] !== null) {
                let clearRes = cleanObj(handler['res']);

                let answer = '';
                if(Object.keys(clearRes).length>1) {

                    alert('Res > 1'); /////////////////////////////

                    $.each(clearRes, function (index) {
                        $.each(this, function (index) {
                            let resRes = this.res;
                            let resItem = this.item;
                            let resTitle = this.title;
                            let resSection = this.section;

                            if(this.selectable === 1){
                                if(typeof this.res === 'object' && this.res !==  null){
                                    answer += '<span class="speechOn">There are '+Object.keys(resRes).length+' results for '+resTitle+' in '+resSection+':</span>';
                                    $.each(this.res, function (index) {
                                        answer += '<div class="secRes row mt-2">';
                                        answer += '<span class="btn btn-sm btn-block btn-light mx-2 cb-copy-data do-a-sel-'+resItem+'" data-item="'+this[resItem]+'" data-cb-copy="'+this[resItem]+'">' + this[resItem] + '</span>';
                                        answer += '</div>';
                                    });
                                } else {
                                    answer +=  (resSection === 'f') ? 'Your '+resTitle+' in '+resSection+' for <span class="btn btn-sm btn-light disabled mx-2 cb-copy-data" data-cb-copy="'+localStorage.selectedLogin+'">'+localStorage.selectedLogin+'</span> is' : 'Your '+resTitle+' in '+resSection+' is';
                                    answer += '<span class="btn btn-sm btn-light mx-2 cb-copy-data do-a-sel-'+resItem+'" data-item="'+this['res']+'" data-cb-copy="'+this['res']+'">' + this['res'] + '</span><br><br>';
                                }
                            } else {
                                if(typeof this.res === 'object' && this.res !== null){
                                    answer += '<span class="speechOn">There are '+Object.keys(resRes).length+' results for '+resTitle+' in '+resSection+':</span>';
                                    $.each(this.res, function (index) {
                                        answer += '<div class="secRes row mt-2">';
                                        answer += '<span class="btn btn-sm btn-block btn-light disabled mx-2 cb-copy-data" data-cb-copy="'+this[resItem]+'">' + this[resItem] + '</span>';
                                        answer += '</div>';
                                    });
                                } else {
                                    answer +=  (resSection === 'MT5') ? 'Your '+resTitle+' in '+resSection+' for <span class="btn btn-sm btn-light disabled mx-2 cb-copy-data" data-cb-copy="'+localStorage.selectedLogin+'">'+localStorage.selectedLogin+'</span> is' : 'Your '+resTitle+' in '+resSection+' is';
                                    answer += '<span class="btn btn-sm btn-light disabled mx-2 cb-copy-data" data-cb-copy="'+this['res']+'">' + this['res'] + '</span><br><br>';
                                }
                            }
                        });
                    });

                    // Seal Process
                    seal(handler, answer, true);

                } else {        // Fin

                    $.each(clearRes[Object.keys(clearRes)[0]], function (index) {
                        let resRes = this.res;
                        let resItem = this.item;
                        let resTitle = this.title;
                        let resSection = this.section;

                        if(this.selectable === 1) {
                            if(typeof this.res === 'object' && this.res !== null){
                                answer += '<span class="speechOn">There are '+Object.keys(resRes).length+' results for '+resTitle+' from '+resSection+':</span>';
                                $.each(this.res, function (index) {
                                    answer += '<div class="secRes row mt-2">';
                                    answer += '<span class="btn btn-sm btn-block btn-light mx-2 cb-copy-data do-a-sel-'+resItem+'" data-item="'+this[resItem]+'" data-cb-copy="'+this[resItem]+'">' + this[resItem] + '</span>';
                                    answer += '</div>';
                                });
                            } else {
                                answer +=  'Your '+resTitle+' from '+resSection+' is'
                                answer += '<span class="btn btn-sm btn-light mx-2 cb-copy-data do-a-sel-'+resItem+'" data-item="'+this['res']+'" data-cb-copy="'+this['res']+'">' + this['res'] + '</span><br><br>';
                            }
                        } else {
                            if(typeof this.res === 'object' && this.res !== null){
                                answer += '<span class="speechOn">There are '+Object.keys(resRes).length+' results for '+resTitle+' from '+resSection+':</span>';
                                $.each(this.res, function (index) {
                                    answer += '<div class="secRes row mt-2">';
                                    answer += '<span class="btn btn-sm btn-block btn-light disabled mx-2 cb-copy-data" data-cb-copy="'+this[resItem]+'">' + this[resItem] + '</span>';
                                    answer += '</div>';
                                });
                            } else {
                                answer +=  'Your '+resTitle+' from '+resSection+' is'
                                answer += '<span class="btn btn-sm btn-light disabled mx-2 cb-copy-data" data-cb-copy="'+this['res']+'">' + this['res'] + '</span><br><br>';
                            }
                        }
                    });

                    // Seal Process
                    seal(handler, answer);
                }


            } else if(handler['res'] !== null) {

                if (handler.AIPF[0].handler_type === 'getter') {
                    seal(handler);
                } else if (handler.AIPF[0].handler_type === 'setter') {
                    inputRecognizingStopper=true;
                    seal(handler);
                    if(handler.listener==='CommandUpdate') {
                        let body = window[handler.form[0].form_type];
                        let footer = '<input type="submit" form="update-item" class="do-a-update-item btn btn-primary" value="Update">';
                        footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                        makeModal('Update '+handler.form[0].title, body, 'sm', footer);

                        $('#modalMain input#val').attr('type',handler.form[0].val_html_type);
                        $('#modalMain input#val').val(handler.form[0].value);
                        $('#modalMain input#class').val(handler.form[0].class);
                        $('#modalMain input#block').val(handler.form[0].block);
                        $('#modalMain input#item').val(handler.form[0].item);
                        $('#modalMain input#title').val(handler.form[0].title);
                    }
                } else {
                    seal(handler);
                }

            } else {
                socket.emit('apiAIML', data);
            }
        }
        //  Engine ~ Other
        else
        {

            // Seal Process
            seal(handler);

        }

        /*
         * AddOns
         */

        // AddOns > Glossary
        if(handler.brain.FIG_TYPE) if(localStorage.configGlossary == 1 && handler.brain.FIG_TYPE['isGlossary'])
        {
            let dicObj = handler.brain.FIG_TYPE['isGlossary'];
            let itemsGlossary =[];
            $.each(dicObj, function() {
                delete this['def'];
                $.each(this, function() {
                    itemsGlossary.push(this);
                });
            });
            glossary[handler['MSG_ID']] = itemsGlossary;
            sessionStorage.glossary = JSON.stringify(glossary);
            let glossaryText = '<div class="border-top pt-2">';
            glossaryText += '<button  data-msgId="'+handler['MSG_ID']+'" class="doM-glossary btn btn-block btn-light text-secondary"><i data-toggle="tooltip" data-placement="left" title="AddOn: Glossary"  class="fab fa-leanpub text-warning"></i> '+Object.keys(itemsGlossary).length+ ' result</button>';
            glossaryText += '</div>';
            $('#msg-'+handler['MSG_ID']+' p').after(glossaryText);
            console.log('glossaryText');
        }
        console.log(handler);
        // AddOns > Wikipedia
        if( (localStorage.configWikipedia == 1) )
        {
            console.log('dateWikipedia');

            let dateWikipedia = {
                w: handler.sanitize.trim
            };
            aiAjaxCall('addon-wikipedia', dateWikipedia, function(wikipedia){
                wikipedia[handler['MSG_ID']] = wikipedia.res;
                sessionStorage.wikipedia = JSON.stringify(wikipedia);
                let wikipediaText = '<div class="border-top pt-2">';
                wikipediaText += '<button  data-msgId="'+handler['MSG_ID']+'" class="doM-wikipedia btn btn-block btn-light text-secondary"><i data-toggle="tooltip" data-placement="left" title="AddOn: Wikipedia"  class="fab fa-wikipedia-w text-muted"></i> '
                wikipediaText += (wikipedia.res) ? Object.keys(wikipedia.res).length : 0;
                wikipediaText += ' result</button>';
                wikipediaText += '</div>';
                $('#msg-'+handler['MSG_ID']+' p').after(wikipediaText);
            });
        }

    }
    if(callback) callback();
}
