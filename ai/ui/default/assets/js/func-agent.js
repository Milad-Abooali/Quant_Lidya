
// Date Line - Today
function dateLineToday(){
    const currentDate = new Date();
    insertChatSeparator(currentDate);
}


// Load Selected User Chat
function loadUserToMessagePad(user) {
    $('#setting-status h5').text(user.name).fadeIn();
}

// Load Selected User Messages
function loadMessages(user) {
    // dateLineToday();
    const currentDate = new Date();
    const date = currentDate.getFullYear() + "-" + (currentDate.getMonth()+1) + "-" + currentDate.getDate();
    socket.emit('loadMessages', {"datetime":date, sessionId:selectedSessionId});
    $('.chat-day-title .load-history').fadeOut();
}

// Send Agent Response
function sendAgentRes(data) {
    socket.emit('agentRes', {
        MSG_ID: data.MSG_ID,
        sessionId: selectedSessionId,
        res: data.handler
    });
    $('#chat-list #chat-'+data.sessionId+' .newMSG').fadeOut();
}

// Agent New Handler
function agentNewHandler() {
    const handler = JSON.parse(resOffers[selectedSessionId].handler)
    addUserMessage(OnlineUsers[selectedSessionId].name, handler.MSG_ID, OnlineUsers[selectedSessionId].avatar, handler.input);
    handlerUpdateResAgent(resOffers[selectedSessionId].handler,false, function (){
        $('#res-'+handler.MSG_ID).addClass('border border-danger p-3');
        $('#msg-list .mdi-dots-vertical').hide();
        $('#res-'+handler.MSG_ID+' .time').append(
            '<div class=" "><small class="skip">Auto send in <strong class="timer text-danger"></strong> Minutes<br></small>'+
            '<progress class="skip" value="0" max="600" id="progressBarRes-'+handler.MSG_ID+'"></progress>'+
            '<br><button data-msgid="'+handler.MSG_ID+'" class="skip do-a-skip btn btn-sm btn-outline-warning">Skip</button>'+
            '<button data-msgid="'+handler.MSG_ID+'" class="do-a-sendres btn btn-sm btn-success mx-2">Send</button></div>');
        scrollChat();
    });
}

// Agent Handler
function handlerUpdateResAgent(data, archive, callback=null) {
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

            seal(handler, handler.listener, true);

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

                    alert('Res > 1');

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

                        seal(handler, handler.listener, true);
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

    }
    if(callback) callback();
}

// Progress Response
function progressRes(msg_id){
    const max    = 60*15;
    let timeLeft = max;
    resTimer[msg_id] = setInterval(function(){
        if(timeLeft <= 0){
            clearInterval(resTimer[msg_id]);
        }
        $("#progressBarRes-"+msg_id).val(max - timeLeft);
        $("#res-"+msg_id+' .timer').text((timeLeft/60).toFixed(2));
        timeLeft -= 1;
    }, 1000);
}