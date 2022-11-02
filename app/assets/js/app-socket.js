/**
 * Socket
 */
const socket = io(APP.socket, {
    'reconnection': true,
    'reconnectionDelay': 3000,
    'reconnectionAttempts': 1000,
    'rejectUnauthorized': false
});
const socketStatusDOM = $('#socket-status');

socket.on("connect", () => {
    socket.emit('eLogin', APP.client);
    socket.emit('eUpdateClient', APP.client, (response) => {
        updateClient(response);
        $('div#over-screen').fadeOut('fast');
    });
    console.log('Socket connect');
    statusColor($('#app-status #socket-status'), 1);
});
socket.on("disconnect", () => {
    console.log('Socket disconnect');
    socketStatusDOM.attr('data-original-title','Disconnected');
    statusColor(socketStatusDOM, 0);
});
socket.on('connect_error', function(e){
    $('div#over-screen #error-text').html(e);
    $('div#over-screen').fadeIn('slow');
    socketStatusDOM.attr('data-original-title','Offline');
    statusColor(socketStatusDOM, 0);
});

socket.on('eMonitor', function(data){
    updateCoreStatus(data);
});

socket.on('handlerMessage', function(data){
    handlerUpdateRes(data,true);
});

socket.on('resAIML', function(handy){
    $('#msg-list #res-'+handy['MSG_ID']+' .conversation-text p').html(handy['response']);
    $('#msg-list #res-'+handy['MSG_ID']+' #status')
        .removeClass()
        .addClass("fa fa-cog text-info")
        .attr('data-original-title','Engine: '+handy['AIE'])
        .attr('title','Engine: '+handy['AIE'])
        .data('title','Engine: '+handy['AIE']);
    if(localStorage.configBipSound===1) resAudio.play();
    readRes(handy['response']);
    archiveMsg(handy['MSG_ID']);
    flashElement($('#aiml-status'));
    if(socketLevel==='a'){
        let handler = JSON.parse(resOffers[selectedSocketId].handler);
        handler.engine = 'AIML';
        handler.res = handy.response;
        resOffers[selectedSocketId].handler = JSON.stringify(handler);
    }
});

socket.on('historyMessages', function(history){
    if(history.messages) {
        // clearMessagePad();
        for (let i in history.messages) {
            const message = history.messages[i];
            const msglist = $('#msg-list');
            if(message.msg && message.res) {
                if($('#res-'+message.id).length===0) msglist.prepend('<li id="res-'+message.id+'" class=" ">'+message.res+'</li>');
                if($('#msg-'+message.id).length===0) msglist.prepend('<li id="msg-'+message.id+'" class="odd">'+message.msg+'</li>');
            }
        }
        if(socketLevel==='a'){
            $('#msg-list .mdi-dots-vertical').hide();
            scrollChat();
        }
    }
    if(history.date_old) {
        const date = new Date(history.date_old);
        insertChatSeparator(date);
    }
    flashElement($('#redis-status'));
});

socket.on('uFeedbackAdd', function (data) {
    let feedClass = (data.t==1) ? 'px-2 far fa-smile text-success' : 'px-2 far fa-frown text-danger';
    $('#res-'+data.i+' #feedback-menu').removeClass().addClass(feedClass);
    archiveMsg(data.i);
    flashElement($('#ml-status'));
});