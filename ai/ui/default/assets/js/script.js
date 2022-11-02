 // Check Local storage
if (typeof(Storage) !== "undefined") {
    toastr.options = {
        timeOut: "1050",
        "progressBar": true,
        "positionClass": "toast-top-right",
    }
    toastr.success("<small>Local/Session | Storage Checked</small>");
} else {
    toastr.options = {
        timeOut: "0",
        extendedTimeOut: "0",
        positionClass: "toast-top-right",
        iconClass: 'fa-spine spinner-border'
    }
    toastr.warning("<small>Sorry! No Web Storage support!</small>");
    rubyStatus('offline');
}

// Ajax Call- Core
var AjaxLock;
function aiAjaxCall (func, data=null, callback) {
    if (AjaxLock === func){
        console.log("AjaxCall Locked: "+AjaxLock);
        return;
    }
    AjaxLock = func;
    $.ajax({
        type: "POST",
        url: aiAppUrl+"/ajax/"+func,
        data: data,
        cache: false,
        global: true,
        async: true,
        success: callback,
        error: function(request, status, error) {
            console.log(error);
        }
    });
    $( document ).ajaxComplete(function( event, xhr, settings ) {
        setTimeout(function() {
            AjaxLock = null;
        }, 50);
    });
}

// Ajax Form- Core
function aiAjaxForm (func, data=null, callback) {
    if (AjaxLock === func){
        console.log("AjaxCall Locked: "+AjaxLock);
        return;
    }
    AjaxLock = func;
    $.ajax({
        type: "POST",
        url: aiAppUrl+"/ajax/"+func,
        data: data,
        cache: false,
        global: true,
        async: true,
        processData: false,
        contentType: false,
        success: callback,
        error: function(request, status, error) {
            console.log(error);
        }
    });
    $( document ).ajaxComplete(function(event, xhr, settings) {
        setTimeout(function() {
            AjaxLock = null;
        }, 50);
    });
}

// blink
function aiBlink (idClass) {
    $(idClass).addClass("bg-info");
    setTimeout(() => {
        $(idClass).removeClass("bg-info")
    }, 50);
}

// UUID
function uuidv4() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

/**
 * Ruby Status
 */
var rubyLastStatus = '';
function rubyStatus(status) {
    rubyLastStatus = status;
    if(status === 'busy' || status === 'offline' || status==='listening') {
        $('#sendCall :input').prop("readonly", true);
    } else {
        $('#sendCall :input').prop("readonly", false);
    }
    $('.ruby-status').hide();
    $('#ruby-'+status).fadeIn('slow');
}

// Unique Array
function uniqueArray(originalArray, prop) {
    let results = {};
    for(let i=0; i<originalArray.length;i++){
      if(originalArray[i]) results[originalArray[i][prop]] = originalArray[i];
    }
    return Object.values(results);
}

// Clean Object
function cleanObj(obj) {
    for (var propName in obj) {
        if (obj[propName] === null || obj[propName] === undefined || obj[propName].length===0) {
            delete obj[propName];
        }
    }
    return obj;
}

// update Favorite List
function updateFavList(history=0) {
    aiAjaxCall ('msgHistory', '', function(msgHistory){
        if(msgHistory.res) {
            $('#plateMenu ul#msg-history').html('');
            $('#plateMenu ul#msg-favourite').html('');
            $.each(msgHistory.res, function(index) {
                let li='';
                if(this.favorite==1){
                    li  = '<li id="fav-'+this.id+'">';
                    li += '<span><i class="mr-1 small fa fa-minus-circle text-danger do-a-favoriteDrop" data-msgid="'+this.id+'" data-toggle="tooltip" data-placement="left" title="Remove"></i></span>';
                    li += '<span class="small">'+this.input_text+'</span>';
                    li += '<span class="float-right small"><button type="button" class="btn btn-sm btn-outline-secondary do-a-historyEdit" data-toggle="tooltip" data-placement="top" title="Edit before send">Edit</button>';
                    li += '<button class="mx-2 btn btn-sm btn-primary do-a-historySend">Send <i class="mdi mdi-send"></i></button></span>';
                    li += '</li>';
                    $('#plateMenu ul#msg-favourite').append(li);
                }
                let star = (this.favorite==1) ? 'text-warning' : 'text-muted';
                if(history==1) {
                    li  = '<li id="history-'+this.id+'">';
                    li += '<span><i class="mr-1 small fa fa-star '+star+' do-a-favoriteAdd" data-msgid="'+this.id+'" data-toggle="tooltip" data-placement="left" title="Add to favorite"></i></span>';
                    li += '<span class="small" >'+this.input_text+'</span>';
                    li += '<span class="float-right small"><span class="px-2 small text-black-50">'+this.time+'</span>'
                    li += '<button type="button" class="btn btn-sm btn-outline-secondary do-a-historyEdit" data-toggle="tooltip" data-placement="top" title="Edit before send">Edit</button>';
                    li += '<button class="mx-2 btn btn-sm btn-primary do-a-historySend">Send <i class="mdi mdi-send"></i></button></span>';
                    li += '</li>';
                    $('#plateMenu ul#msg-history').append(li);
                }
            });
        }
    });}


/**
 * Auto Read Voices
 */
var voices;
function populateVoiceList() {
    if(typeof speechSynthesis === 'undefined') {
        return;
    }

    voices = speechSynthesis.getVoices();

    for(var i = 0; i < voices.length; i++) {
        var option = document.createElement('option');
        option.textContent = voices[i].name + ' (' + voices[i].lang + ')';

        if(voices[i].default) {
            option.textContent += ' -- DEFAULT';
        }

        option.setAttribute('data-lang', voices[i].lang);
        option.setAttribute('data-name', voices[i].name);
        option.setAttribute('value', i);
        document.getElementById("voiceSelect").appendChild(option);
    }
}
populateVoiceList();
if (typeof speechSynthesis !== 'undefined' && speechSynthesis.onvoiceschanged !== undefined) {
    speechSynthesis.onvoiceschanged = populateVoiceList;
}

/**
 * Read Response
 */
function readRes(text, forceRead=false) {
    text = text.trim();
    if(text.length>0) {

        if(inputRecognizingStatus) inputSpeechRecognition.stop();
        if(interfaceRecognizingStatus) interfaceSpeechRecognition.stop();

        // Skip reading
        if(text.length>150 && forceRead===false) text = 'The text too long,';
        if(localStorage.configAutoSpeech===0 && forceRead===false) return;
        speechEngineStatus = 1;

        let speechEngine = new SpeechSynthesisUtterance();
        let vid = $('#voiceSelect').val() || 2;
        speechEngine.voice = voices[vid];
        speechEngine.text = text;
        speechReadObj = window.speechSynthesis.speak(speechEngine);

        speechEngine.onend = function(event) {
            speechEnginStatus = 0;

            if(localStorage.configInputKeepListening==1 && !inputRecognizingStopper) {
                $( "#do-a-input-audio" ).trigger( "click" );
            }

        }
    }
}

/*
 * Seal Response Messages
 */
function seal(handler, answer=null, sSpecial=false, rStatus='online') {
    if(answer == null) answer = handler.res;
    $('#msg-list #res-'+handler.MSG_ID+' .chat-avatar i').data('speechSpecial', sSpecial);
    $('#msg-list #res-'+handler.MSG_ID+' .conversation-text p').html(answer);
    $('#msg-list #res-'+handler.MSG_ID+' #status')
        .removeClass()
        .addClass("fa fa-cog text-info")
        .attr('data-original-title', 'Engine: '+handler.engine)
        .attr('title', 'Engine: '+handler.engine)
        .data('title', 'Engine: '+handler.engine);
    if(localStorage.configBipSound) resAudio.play();
    if(localStorage.configAutoSpeech) $('#msg-list #res-'+handler.MSG_ID+' .autoRead').trigger( "click" );
    rubyStatus(rStatus);
    scrollChat();
    archiveMsg(handler['MSG_ID']);
    if(socketLevel==='a') $('#msg-list .mdi-dots-vertical').hide();
}
