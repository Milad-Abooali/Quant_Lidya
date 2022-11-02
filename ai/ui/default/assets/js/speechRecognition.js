$(document).ready(function(){
    if ("webkitSpeechRecognition" in window) {
        toastr.options = {
            timeOut: "2150",
            "progressBar": true,
            "positionClass": "toast-top-right",
        }
        toastr.success("<small>Speech Recognition Checked</small>");
        $('#do-a-input-audio').addClass('start');
        $('#speechResult #interim').html(' ');

        /**
         * Input Speech Recognition Engine
         */
        let final_input_transcript = "";
        inputSpeechRecognition = new webkitSpeechRecognition();
        inputSpeechRecognition.continuous = false;
        inputSpeechRecognition.interimResults = localStorage.configInputVoiceInterim;
        inputSpeechRecognition.lang ='en-US';

        /* On Start - Input Speech Recognition */
        inputSpeechRecognition.onstart = () => {
            speechEngineStatus = 0;
            inputRecognizingStatus = 1;
            interfaceRecognizingStatus = 0;

            final_input_transcript = "";

            $('#do-a-input-audio-stop').removeClass('d-none');
            $('#do-a-input-audio').addClass('blink');

            console.info('Input Start Listening');
            rubyStatus('listening');
        };

        /* On Sound - Input Speech Recognition */
        inputSpeechRecognition.onsoundstart = () => {
            $('#do-a-input-audio').removeClass('blink').addClass('blinkbg');
            console.info('Input Hared');
        };

        /* On End - Input Speech Recognition */
        inputSpeechRecognition.onend = () => {

            $('#do-a-input-audio-stop').addClass('d-none');
            $('#do-a-input-audio').removeClass('blink blinkbg');

            $('#speechResult #interim').html('');

            console.info('Input Stop Listening');
            rubyStatus('online');

            if(localStorage.configInputVoiceAutosend==true) {
                $( "#do-a-Send" ).trigger( "click" );
            } else {
                if(!interfaceRecognizingStatus) $( "#do-a-interface-audio" ).trigger( "click" );
            }

            speechEngineStatus = 0;
            inputRecognizingStatus = 0;
            interfaceRecognizingStatus = 0;
        };

        /* On Error - Input Speech Recognition */
        inputSpeechRecognition.onError = () => {

            $('#do-a-input-audio-stop').addClass('d-none');
            $('#do-a-input-audio').removeClass('blink blinkbg').addClass('disabled');

            toastr.options = {
                timeOut: "0",
                extendedTimeOut: "0",
                positionClass: "toast-top-right",
                iconClass: 'fa-spine spinner-border'
            }
            toastr.warning("<small>Sorry! Error on Input Speech Recognition.</small>");

            console.info('Input Error on Listening');
            rubyStatus('online');

            speechEngineStatus = 0;
            inputRecognizingStatus = 0;
            interfaceRecognizingStatus = 0;
        };

        /* On Result - Input Speech Recognition */
        inputSpeechRecognition.onresult = (event) => {

            let interim_input_transcript = "";
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    final_input_transcript += event.results[i][0].transcript;
                } else {
                    interim_input_transcript += event.results[i][0].transcript;
                }
            }
            if(localStorage.configInputVoiceInterim==1) $('#speechResult #interim').html(interim_input_transcript);
            let final_input_transcript_lower = final_input_transcript.toLowerCase();

            // Stop Input Listening
            let stopInputVoiceKeywords = [
                "stop listening",
                "end listening",
                "ruby stop",
                "stop ruby",
                "stop movie",
                "movie stop",
                "herbie stop",
                "stop herbie",
                "stop for me",
                "stop rubia",
                "rubia stop",
                "kill ruby",
                "ruby close",
                "end ruby",
                "ruby end",
            ];
            let callStopInputVoice = false;
            $.each(stopInputVoiceKeywords, function( index, value ) {
                if ( final_input_transcript_lower.includes(value) ) callStopInputVoice=true;
            });
            if (callStopInputVoice===true) {
                inputRecognizingStopper = true;
                $( "#do-a-input-audio-stop" ).trigger( "click" );
                readRes("ok,if you need any help just call me",true);
                console.info('Call to Stop Input Listening');
            } else {
                inputRecognizingStopper = false;
                $('#input-text').val(final_input_transcript);
            }

        };

        /* Start Listening - Input Speech Recognition */
        $("body").on("click", "#do-a-input-audio", function(event){
            window.speechSynthesis.cancel();
            interfaceSpeechRecognition.stop();
            setTimeout(function() {
                if(!inputRecognizingStatus) inputSpeechRecognition.start();
            }, 100);
        });

        /* Stop Listening - Input Speech Recognition */
        $("body").on("click", "#do-a-input-audio-stop", function(event){
            inputSpeechRecognition.stop();
            setTimeout(function() {
                if(!interfaceRecognizingStatus) interfaceSpeechRecognition.start();
            }, 100);
        });


        /**
         * Interface Speech Recognition Engine
         */
        let final_interface_transcript = "";
        interfaceSpeechRecognition = new webkitSpeechRecognition();
        interfaceSpeechRecognition.continuous = true;
        interfaceSpeechRecognition.interimResults = false;
        interfaceSpeechRecognition.lang ='en-US';

        /* On Start - Interface Speech Recognition */
        interfaceSpeechRecognition.onstart = () => {

            speechEngineStatus = 0;
            inputRecognizingStatus = 0;
            interfaceRecognizingStatus = 1;

            final_interface_transcript = "";

            console.info('Interface Start Listening');
        };

        /* On Sound - Interface Speech Recognition */
        interfaceSpeechRecognition.onsoundstart = () => {
            console.info('Interface Hared');
        };

        /* On End - Interface Speech Recognition */
        interfaceSpeechRecognition.onend = () => {

            console.info('Interface Stop Listening');

            speechEngineStatus = 0;
            inputRecognizingStatus = 0;
            interfaceRecognizingStatus = 0;
        };

        /* On Error - Interface Speech Recognition */
        interfaceSpeechRecognition.onError = () => {

            $('#do-a-interfaceAudio').addClass('disabled');
            toastr.options = {
                timeOut: "0",
                extendedTimeOut: "0",
                positionClass: "toast-top-right",
                iconClass: 'fa-spine spinner-border'
            }
            toastr.warning("<small>Sorry! Error on Interface Speech Recognition.</small>");

            console.info('Interface Error on Listening');

            speechEngineStatus = 0;
            inputRecognizingStatus = 0;
            interfaceRecognizingStatus = 0;
        };

        /* On Result - Interface Speech Recognition */
        interfaceSpeechRecognition.onresult = (event) => {

            let interface_input_transcript = "";
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    final_interface_transcript += event.results[i][0].transcript;
                } else {
                    interface_input_transcript += event.results[i][0].transcript;
                }
            }
            let final_interface_transcript_lower = final_interface_transcript.toLowerCase();
            console.info('Interface Heard: '+final_interface_transcript);

            // Start Input Listening
            let startInputVoiceKeywords = [
                "start listening",
                "input listening",
                "ruby start",
                "start ruby",
                "start movie",
                "movie start",
                "herbie start",
                "start herbie",
                "start for me",
                "start rubia",
                "rubia start",
                "ruby ruby",
                "hi ruby",
                "hello ruby",
                "hey ruby",
                "my ruby"
            ];
            let callStartInputVoice = false;
            $.each(startInputVoiceKeywords, function( index, value ) {
                if ( final_interface_transcript_lower.includes(value) ) callStartInputVoice=true;
            });
            if (callStartInputVoice===true) {
                inputRecognizingStopper = false;
                readRes("I'm listening",true);
                setTimeout(() => {
                    $( "#do-a-input-audio" ).trigger( "click" );
                }, 1200);
                console.info('Call to Start Input Listening');
            }

            // Refresh Interface
            let refreshInterfaceKeywords = [
                "new ruby",
                "refresh ruby",
                "rephresh ruby",
                "fresh ruby",
                "ruby refresh",
                "restart ruby",
                "ruby reset",
                "ruby reload",
                "new interface",
                "refresh interface",
                "rephresh interface",
                "fresh interface",
                "interface refresh",
                "restart interface",
                "interface reset",
                "interface reload"
            ];
            let callRefreshInterface = false;
            $.each(refreshInterfaceKeywords, function( index, value ) {
                if ( final_interface_transcript_lower.includes(value) ) callRefreshInterface=true;
            });
            if (callRefreshInterface===true) {
                console.info('Call to Refresh Interface');
                readRes("Interface will reload",true);
                setTimeout(() => {
                    location.reload();
                }, 750);
                return;
            }

            // Close Form
            let closeFormKeywords = [
                "close it",
                "close this",
                "close form",
                "close the form",
                "close 4",
                "close the 4",
                "closed form",
                "closed the form",
                "closed 4",
                "close popup",
                "close the popup",
                "close pop-up",
                "close the pop-up",
                "close pop up",
                "close the pop up",
                "cancel form",
                "cancel the form",
                "cancel 4",
                "cancel the 4",
                "cancel popup",
                "cancel the popup",
                "cancel pop-up",
                "cancel the pop-up",
                "cancel pop up",
                "cancel the pop up",
                "close the phone",
                "close phone",
                "closed phone",
                "cancel phone",
                "cancel the phone"
            ];
            let callCloseForm = false;
            $.each(closeFormKeywords, function( index, value ) {
                if ( final_interface_transcript_lower.includes(value) ) callCloseForm=true;
            });
            if (callCloseForm===true) {
                console.info('Call to Close Form');
                readRes("OK, Popup Closed",true);
                setTimeout(() => {
                    $('.modal').modal('hide');
                }, 150);
                return;
            }

        };

        /* StartListening - Interface Speech Recognition */
        $("body").on("click", "#do-a-interface-audio", function(event){
            setTimeout(function() {
                if(!interfaceRecognizingStatus) interfaceSpeechRecognition.start();
            }, 100);
        });

        /* Stop Listening - Interface Speech Recognition */
        $("body").on("click", "#do-a-interface-audio-stop", function(event){
            interfaceSpeechRecognition.stop();
        });

        // Start Interface Listening on Load
        setTimeout(function() {
            if(!interfaceRecognizingStatus) $( "#do-a-interface-audio" ).trigger( "click" );
        }, 100);
    } else {
        toastr.options = {
            timeOut: "0",
            extendedTimeOut: "0",
            positionClass: "toast-top-right",
            iconClass: 'fa-spine spinner-border'
        }
        toastr.warning("<small>Sorry! No Speech Recognition support on your system.</small>");
        rubyStatus('offline');
        console.info("Speech Recognition Not Available");
        $('#do-a-input-audio').addClass('disabled');
    }

});