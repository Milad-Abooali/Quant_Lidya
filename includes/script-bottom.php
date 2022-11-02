</div>

<?php

// LangMan
global $_L;

?>
<div id="scripts-wrapper">
        <!-- App js -->
        <script src="assets/js/app.js"></script>
        <script>
        
            /**
             *  Sidebar Visibility
             */
            $('body').attr('class', localStorage.sidebarVisible);
            $('body').on('click','.open-left', function(){
                localStorage.sidebarVisible = $('body').attr('class');
            });

            /**
             *  Check for Agreement
             */
            <?php if($_SESSION["id"]) if(!$_SESSION["date_approve"] || $_SESSION["date_approve"] == '0000-00-00 00:00:00') { ?>
            $('.content-page').hide();
            $(window).on('load', function() {
                let body = '<div id="agreement" class="row p-2"></div>';
                let footer = '<small class="float-left mr-auto text-danger"><?= $_L->T('Agreement_note','modal') ?></small><a href="logout.php" class="btn btn-outline-danger"><?= $_L->T('Logout','login') ?></a><button id="btn-agree" class="btn btn-success"><?= $_L->T('I_Agree','general') ?></button>';
                makeModal('<?= $_L->T('Agreement','general') ?>',body,'lg',footer,true,true);
                $('#agreement').load("<?php echo Broker['terms_file'] ?>");

                $('#modalMain #btn-agree').on('click', function() {
                    ajaxCall ('global', 'agreeTerms','', function(response){
                        let resObj = JSON.parse(response);
                        if(resObj.res) {
                            $('#modalMain').modal('toggle');
                            $('.content-page').fadeIn();
                        }
                    });
                });

            });
            <?php } ?>


            <?php 
                if($id_verify !== "1" or $poa_verify !== "1"){
            ?>
                    $(".container-fluid").prepend('<div class="clearfix">&nbsp;</div><div class="alert alert-danger mb-0" role="alert"><ul class="mb-0"></ul></div>');
            <?php
                    if ($id_verify == "3"){
            ?>
                    $(".alert ul").append('<li><?= $_L->T('ID_Proof_Upload','doc') ?> <a href="profile.php"><?= $_L->T('Profile','profile') ?></a>.</li>');
            <?php 
                    } 
                    if ($poa_verify == "3"){ 
            ?>
                    $(".alert ul").append('<li><?= $_L->T('ID_Residency_Upload','doc') ?> <a href="profile.php"><?= $_L->T('Profile','profile') ?></a>.</li>');
            <?php
                    }
                    if ($id_verify == "0"){
            ?>
                    $(".alert ul").append('<li><?= $_L->T('ID_Proof_Not','doc') ?> <?= $_L->T('Profile','profile') ?></li>');
            <?php 
                    } 
                    if ($poa_verify == "0"){ 
            ?>
                    $(".alert ul").append('<li><?= $_L->T('ID_Residency_Not','doc') ?></li>');
            <?php
                    }
                } 
            ?>
            
        </script>

    <?php if ($sess->IS_LOGIN): ?>

    <script>
        var filter = "tagGeneral";
        var htmlOut;
        $('.notification-list .noti-icon-badge').hide();
        function ajaxCheckNotify() {
            $('.notification-item-list').html('');
            $('.notification-list .noti-icon-badge').hide();
            $('.notification-list .noti-icon-badge').html('');
            ajaxCall ('notify', 'getNotify','', function(response){
                let resObj = JSON.parse(response);
                if(resObj.e) {
                    if (resObj.e === 'Token is wrong !') window.location.href = "logout.php";
                } else if(resObj.res) {
                    htmlOut = '';
                    var countNotify=0;
                    $.each(resObj.res, function(index,val){
                        $('.notification-list .noti-icon-badge').hide();
                        if (val) pushNotify(val.id,val.type,val.n_text,val.cat,val.notify_data,val.details,val.user);
                        countNotify++;
                    });
                    $('.notification-item-list').html(htmlOut.replace('undefined',''));
                    $('.notification-list .noti-icon-badge').html(countNotify);
                    $('.notification-list .noti-icon-badge').fadeIn();
                    if(countNotify > 0){
                        //document.getElementById('alert_sound').muted = false;
                        //document.getElementById('alert_sound').play();
                    }
                }
            });
        }
        $(document).ready(function(){
            ajaxCheckNotify();
        })
        setInterval(() => {
            ajaxCheckNotify();
        }, 32*1000);

        // Add Notify Item
        function pushNotify(id,title,text,cat,data,details=false,user=false,color='success',icon='cart-outline'){
            if(cat == 'Transactions'){
                var cat_color = 'success';
                var cat_icon = 'wallet';
                var filter_cat = "tagTransactions";
            } else if(cat == 'Follow-Up'){
                var cat_color = 'danger';
                var cat_icon = 'headset';
                var filter_cat = "tagFollow-Up";
            } else {
                var cat_color = 'dark';
                var cat_icon = 'exclamation';
                var filter_cat = "tagGeneral";
            }
            let ptext = text.replace('%s',data);
            if(cat == 'Transactions'){
                htmlOut += '<a id="notify-'+id+'" href="javascript:void(0);" data-cat="'+cat+'" data-id="'+id+'" data-user="'+data+'" class="dropdown-item notify-item '+filter_cat+' detail active">';
                htmlOut += '<div class="notify-icon bg-'+cat_color+'"><i class="mdi mdi-'+cat_icon+'"></i></div>';
                htmlOut += '<p class="notify-details">'+cat+'<span class="text-muted"><b>'+user+'</b> requested <b>$'+details.amount+'</b> '+details.type+'.</span></p></a>';
            } else {
                htmlOut += '<a id="notify-'+id+'" href="javascript:void(0);" data-cat="'+cat+'" data-id="'+id+'" data-user="'+data+'" class="dropdown-item notify-item '+filter_cat+' detail active">';
                htmlOut += '<div class="notify-icon bg-'+cat_color+'"><i class="mdi mdi-'+cat_icon+'"></i></div>';
                htmlOut += '<p class="notify-details">'+cat+'<span class="text-muted">'+ptext+'</span></p></a>';
            }
            return true;
        }
    </script>
    <?php endif; ?>

    <?php if (Broker['pin_lock'] && $sess->IS_LOGIN): ?>
    <script>
    /* 
     * Lock Screen
     */

        let islock = "<?php echo $_SESSION['locksess']; ?>";
        // Server Check
        if(islock){
            $('#wrapper').fadeOut('fast');
            let body = '<div class="form-group text-center"><label><?= $_L->T('enter_the_pin','login') ?></label><input type="number" id="LS-pincode" class="form-control"></div>';
            let footer = '<button type="submit" id="LS-pinverify" class="btn btn-primary">Open Screen</button><button href="logout.php" class="btn btn-danger"><?= $_L->T('Logout','login') ?></button>';
            setTimeout(function() {
                makeModal('Enter Your PIN',body,'sm',footer,true);
            }, lockTimer*1);
            <?php if(DevMod) { ?>
            console.log('locked');
            <?php } else { ?>
            console.log('change to DevMod to see logs!');
            <?php } ?>
        } else {
            // Lock Alert
            var lockTimer = 11*60;
            var lockScreenTimer;
            setTimeout(function() {
                clearTimeout(lockScreenTimer);
                let body = '<div id="lockScreenTimer" class="mt-3 countdown"><div class="countdown-number"></div><svg><circle r="18" cx="20" cy="20"></circle></svg></div>';
                body += '<p class="mt-3 text-center text-danger"><?= $_L->T('screen_lock_note','login') ?> </p>';
                let footer = '<button class="btn btn-block btn-primary" onclick="lockTimerE(lockFunc, lockTimer)" data-dismiss="modal"><?= $_L->T('Do_not_lock','login') ?></button>';
                makeModal('<?= $_L->T('Lock_Screen_Alert','login') ?>', body, 'sm',footer);
                lockScreenTimer = countCircle('lockScreenTimer');
            }, (lockTimer-10)*1000);
            // Lock
            function lockScreenOn() {
              return setTimeout(function() {
                        lockModal();
                    }, lockTimer*1000);
            }
            // new Lock Timer
            function lockTimerE(id, adde){
                clearTimeout(lockFunc);
                setTimeout(function() {
                    lockModal();
                }, lockTimer*1000);
            }
            let lockFunc = lockScreenOn();
        }
        
        // Lock Func
        function lockModal(){
            $('#wrapper').fadeOut('fast');
            ajaxCall ('global', 'lockScreenOn','', function(response){
                let resObj = JSON.parse(response);
                <?php if(DevMod) { ?>
                console.log(resObj);
                <?php } else { ?>
                console.log('change to DevMod to see logs!');
                <?php } ?>
            });
            let body = '<div class="form-group text-center"><label>Please Enter Your Pin</label><input type="number" id="LS-pincode" class="form-control"></div>';
            let footer = '<button type="submit" id="LS-pinverify" class="btn btn-primary">Open Screen</button><button href="logout.php" class="btn btn-danger">Logout</button>';
            makeModal('Enter Your PIN',body,'sm',footer,true);
            <?php if(DevMod) { ?>
            console.log('locked');
            <?php } else { ?>
            console.log('change to DevMod to see logs!');
            <?php } ?>
            return true;
        }
        // Open           
        function lockScreenOff() {
            var pc = $('#LS-pincode').val();
            var data = 'p='+ pc;
            $.ajax({
                type: "POST",
                url: "pin_login.php",
                data: data,
                cache: false,
                global: false,
                async: true,
                success: function(data) {
                    if (data) {
                        $('#wrapper').fadeIn('slow');
                        $('#modalMain').modal('toggle');
                        <?php if(DevMod) { ?>
                        console.log('lock Off');
                        <?php } else { ?>
                        console.log('change to DevMod to see logs!');
                        <?php } ?>
                    }

                },
                error: function(request, status, error) {
                    <?php if(DevMod) { ?>
                    console.log(error);
                    <?php } else { ?>
                    console.log('change to DevMod to see error!');
                    <?php } ?>
                }
            });
        }
        $("body").on("click","#LS-pinverify", function(){
            <?php if(DevMod) { ?>
            console.log('call');
            <?php } else { ?>
            console.log('change to DevMod to see logs!');
            <?php } ?>
            lockScreenOff();
        });
    </script>
    <?php endif; ?>

    <script>
        /*
         * GFunc
         */

        // DatePicker Custom
        $( document ).ajaxComplete(function() {
            $('.dateCustom').datepicker({
                uiLibrary: 'bootstrap',
                iconsLibrary: 'fontawesome',
                format: 'yyyy-mm-dd'
            });
        });

        // Count Down Circle
        function countCircle(id,start=10,speed=1000) {
            const elmid = '#'+id+' .countdown-number';
            var element = $(elmid);
            var countdown = start;
            element.html(countdown);
            setInterval(function() {
              countdown = --countdown <= 0 ? start : countdown;
              element.html(countdown);
            }, speed);
        }

        // Modal Maker - Core
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

        // Ajax reCaptcha
        $("body").on("click", "#doA-reCaptcha", function(event) {
            ajaxCall ('global', 'captcha', '', function(response) {
                $('.captcha-img').attr('src',response).fadeIn();
            });
        });

        // AAjax Call- Core
        var AAjaxLock;
        async function aAjaxCall (callClass, callFunction, data=null, callback) {
            if (AAjaxLock == callClass+callFunction) return;
            AAjaxLock = callClass+callFunction;
            $.ajax({
                type: "POST",
                url: "lib/ajax.php?c="+callClass+'&f='+callFunction+"&t=<?= TOKEN ?>",
                data: data,
                cache: false,
                global: true,
                async: true,
                success: callback,
                error: function(request, status, error) {
                    <?php if(DevMod) { ?>
                    console.log(error);
                    <?php } else { ?>
                    console.log('change to DevMod to see Error!');
                    <?php } ?>
                }
            });
            $( document ).ajaxComplete(function( event, xhr, settings ) {
                setTimeout(function() {
                    AAjaxLock = null;
                }, 50);
            });
        }

        // Ajax Call- Widgets
        function ajaxCallWidgets(data=null, callback) {
            $.ajax({
                type: "POST",
                url: "lib/ajax.php?c=global&f=widget&t=<?= TOKEN ?>",
                data: data,
                cache: false,
                global: true,
                async: true,
                success: callback,
                error: function(request, status, error) {
                    console.log(error);
                }
            });
        }
        // Ajax Call- Core
        var AjaxLock;
        function ajaxCall (callClass, callFunction, data=null, callback) {
            if (AjaxLock == callClass+callFunction) {
                console.log('AjaxLock: '+AjaxLock);
                return;
            }
            AjaxLock = callClass+callFunction;
            $.ajax({
                type: "POST",
                url: "lib/ajax.php?c="+callClass+'&f='+callFunction+"&t=<?= TOKEN ?>",
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
        function ajaxForm (callClass, callFunction, data=null, callback) {
            if (AjaxLock == callClass+callFunction) return;
            AjaxLock = callClass+callFunction;
            $.ajax({
                type: "POST",
                url: "lib/ajax.php?c="+callClass+'&f='+callFunction+"&t=<?= TOKEN ?>",
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

        <?php if ($_SESSION['new_login']): ?>
            // last page after login
            let lastPath = '<?php global $actLog;echo $actLog->lastPageVisited(); ?>';
            toastr.options = {
                "positionClass": "toast-bottom-full-width",
            }
            toastr.warning("<a href='"+lastPath+"'>Your last visited page "+lastPath+"</a>");
        <?php endif; ?>

        $("body").on("click", ".notification-item-list .notify-item", function(event){
            let clicked = $(this);
            let data_user = $(this).data("user");
            let data = {
            	'id': $(this).data("id")
            }
            ajaxCall ('notify', 'seen',data, function(response){
                let resObj = JSON.parse(response);
                if (resObj.e) { // ERROR
                    toastr.error("Error on saving form !");
                } else if (resObj.res) { // SUCCESS
                    if(clicked.data("cat") == "Leads"){
                        $(location).attr('href', 'leads.php');
                    } else if(clicked.data("cat") == "Follow-Up") {
                        var url = "user-details.php?code="+data_user;
                        $('.my-modal-cont').load(url,function(result){
                            $(".modal-title").html('Leads Details');
                            $('#myModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                            $('#myModal').modal({show:true});
                        });
                        ajaxCheckNotify();
                    } else {
                        toastr.success("Notification have been seen.");
                    }
                }
            });
        });

        /* Copy Text */
        function copyText(text) {
            $("#cb-copy").val(text).select();
            document.execCommand("copy");
            $('#cb-copy').remove();
        }
        $('body').on('click','.cb-copy-data-silent', function(){
            $('<input id="cb-copy" value="test">').insertAfter($(this));
            copyText($(this).data('cb-copy'));
            <?php if(DevMod) { ?>
            console.log('Copy Done ...');
            <?php } else { ?>
            console.log('change to DevMod to see logs!');
            <?php } ?>
        });
        $('body').on('click','.cb-copy-data', function(){
            let _this = $(this);
            $('<input id="cb-copy" value="test">').insertAfter($(this));
            copyText(_this.data('cb-copy'));
            let text = _this.html();
            _this.html('<small>Copy Done ...<small>');
            setTimeout(function(){
                _this.html(text);
            }, 300);
            setTimeout(function(){
                _this.data('copy',0);
            }, 300);
            <?php if(DevMod) { ?>
            console.log('Copy Done ...');
            <?php } else { ?>
            console.log('change to DevMod to see logs!');
            <?php } ?>
        });
        $('body').on('click','.cb-copy-html', function(){
            let _this = $(this);
            if (!_this.data('copy')) {
                _this.data('copy',1);
                $('<input id="cb-copy" value="test">').insertAfter($(this));
                let text = _this.html();
                copyText(text);
                _this.html('<small>Copy Done ...<small>');
                setTimeout(function(){
                    _this.html(text);
                }, 300);
                setTimeout(function(){
                    _this.data('copy',0);
                }, 300);
            }
        });
        $('body').on('click','.cb-copy-val', function(){
            let _this = $(this);
            if (!_this.data('copy')) {
                $('<input id="cb-copy" value="test">').insertAfter($(this));
                let text = _this.val();
                copyText(text);
                _this.val('Copy Done ...');
                setTimeout(function(){
                    _this.val(text);
                }, 300);
                setTimeout(function(){
                    _this.data('copy',0);
                }, 300);
            }
        });

        /* Filter DataTable */
        // predefined
        $('.filterDTX').on('click', function () {
            let table = $(this).data('tableid');
            let column = $(this).data('col');
            $('.filterDTX').removeClass('fa fa-angle-right');
            $(this).addClass('fa fa-angle-right');
            window[table].columns(column).search( $(this).data('filter') ).draw();
        } );
        // Input by user
        $('input.filterDT').on('change', function () {
            let table = $(this).data('tableid');
            let column = $(this).data('col');
            window[table].columns(column).search(this.value).draw();
        });
        <?php if ($_GET['dt'] ?? false) { ?>
        // on load by get
        $(window).on('load', function() {
            let filterDTload = JSON.parse('<?= html_entity_decode($_GET['dt']) ?>');
            $.each(filterDTload.cols, function (key, val) {
                if (filterDTload.regex) {
                    window[filterDTload.table].columns(key).search(val,true,false).draw();
                } else {
                    window[filterDTload.table].columns(key).search(val).draw();
                }
                $('input.filterDT[data-col="'+key+'"]').val(val);
            });
        });
        <?php } ?>




        /* Reload DataTable */
        $('#do-reload,.do-reload').on('click', function () {
            let table = $(this).data('tableid');
            $(this).addClass('fa-spin');
            window[table].ajax.reload(null, false);
            $(this).removeClass('fa-spin');
        });
        // Auto Reload
        var nowtime = moment().format('Y-D-M  h:mm:ss');
        $('.refPageSel span').html(nowtime);
        function refreshPage (table) {
            $('.refPageSel').removeClass('text-muted');
            window[table].ajax.reload(null, false);
            $('.refPageSel span').fadeToggle();
            nowtime = moment().format('Y-D-M  h:mm:ss');
            $('.refPageSel span').html(nowtime);
            $('.refPageSel span').fadeToggle();
            $('.refPageSel').addClass('text-info');
            setTimeout(function() {
                $('.refPageSel').removeClass('text-info');
            }, 1300);
        }
        var refPageVal = [];
        var refreshTime = [];
        $('#refreshTime,.refreshTime').on('change', function() {
            refreshTime[$(this).data("tableid")] = $(this).val()*1000;
            if (refPageVal[$(this).data("tableid")]) {
                clearInterval(refPageVal[$(this).data("tableid")]);
            }
            if (refreshTime[$(this).data("tableid")] > 0) {
                let table = $(this).data('tableid');
                refPageVal[$(this).data("tableid")] = setInterval(function() {refreshPage(table)}, refreshTime[$(this).data("tableid")]);
                $('.refPageSel i').addClass('fa-spin');
                $('.refPageSel i').attr('data-original-title','Table will reload every '+refreshTime[$(this).data("tableid")]+' ms.');
            } else {
                $('.refPageSel i').removeClass('fa-spin');
                $('.refPageSel i').attr('data-original-title','Table will not reload.');
            }
            console.log(refreshTime)
        });
        $('#refreshTime, .refreshTime').trigger('change');

        /* Bootstrap Basic - Core */
        $(function () {
            $('body').tooltip({selector: '[data-toggle="tooltip"]'});
        });


        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
        $('input.date').datepicker({
            uiLibrary: 'bootstrap',
            iconsLibrary: 'fontawesome',
            format: 'yyyy-mm-dd'
        });

        /*  Add Uploader */
        $("body").on("change",'.custom-file-input', function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            $(this).closest('.custom-file').find('.da-delDoc').hide();
            $(this).closest('.custom-file').prepend('<i class="fa fa-window-close text-danger da-delDoc ml-n4"></i>');
            $('form .da-addDoc').fadeIn();
        });
        var addDoc = 1;
        $('body').on('click','form .da-addDoc', function() {
            const max = $(this).data('max');
            if (addDoc == max) $(this).hide();
            if (addDoc <= max) {
                const par =
                    '<div class="custom-file my-1">' +
                    '<input type="file" class="custom-file-input" id="doc-'+addDoc+'" name="doc[]">' +
                    '<label class="custom-file-label" for="doc-'+addDoc+'">Choose your Receipt</label>' +
                    '</div>';
                $(this).before(par);
                $(this).hide();
                addDoc++;
            }
        });
        $('body').on('click','.custom-file .da-delDoc', function() {
            $(this).closest('.custom-file').remove();
            $('form .da-addDoc').fadeIn();
            addDoc--;
        });

        /**
         * Widgets Loader
         * @param name
         * @param elid
         */
        $(window).on('load', function() {
            $('.widget-header .datetime').html(nowtime);
            $( ".widget" ).each(function(index) {
                
                const elid = $(this).attr('id');
                const name = $(this).data('wg');
                const vars = $(this).data('vars');
                const autoload = $(this).data('autoload');
                console.log('Widget "'+name+'" detected...');
    
                if (!autoload) {
                    $('#'+elid+' .reload').removeClass('fa-spin');
                } else if (name) {
                    $('#'+elid+' .widget-header .hidewg').fadeIn();
                    setTimeout(function() {
                        ajaxCallWidgets ({1:name,2:vars}, function(response){
                            $('#'+elid+' .widget-body').html(response);
                            // Scroll
                            let scrollSize = $('#'+elid+' .widget-header #widgetScroll').attr('data-size');
                            if(scrollSize > 0){
                                $('#'+elid+' .widget-body').mCustomScrollbar({
                                    setHeight: scrollSize+'px',
                                    theme: 'minimal-dark'
                                });
                            }
                        });
                        console.log('Widget "'+name+'" Auto Loaded.');
                        $('#'+elid+' .reload').removeClass('fa-spin');
                    }, 300);
                }
            });
        });
        $('body').on('click','.widget-header .reload', function() {
            $(this).addClass('fa-spin');
            const group = $(this).closest( ".widget" ).data('widget-group');
            if(group) {
                console.log('Widget group ["'+group+'"] reLoaded.')
                $('[data-widget-group]').each(function() {
                    const name = $(this).data('wg');
                    const elid = $(this).attr('id');
                    const vars = $(this).data('vars');
                    reloadWidget (name, elid, vars);
                });
            } else {
                const name = $(this).closest( ".widget" ).data('wg');
                const elid = $(this).closest( ".widget" ).attr('id');
                const vars = $(this).closest( ".widget" ).data('vars');
                reloadWidget (name, elid, vars);
            }
        });
        function reloadWidget (name,elid,vars){
            var datatime = $('#'+elid+' .widget-header .datetime');
            var reload = $('#'+elid+' .widget-header .reload');
            $('#'+elid+' .widget-header .hidewg').fadeIn();
            $('#'+elid+' .widget-body').mCustomScrollbar("destroy");
            ajaxCallWidgets ({1:name,2:vars}, function(response){
                $('#'+elid+' .widget-body').html(response);
                nowtime = moment().format('Y-D-M  h:mm:ss');
                datatime.html(nowtime).addClass('text-info');
                setTimeout(function() {
                    datatime.removeClass('text-info');
                    console.log('Widget "'+name+'" reLoaded.');
                    reload.removeClass('fa-spin');
                    // Scroll
                    let scrollSize = $('#'+elid+' .widget-header #widgetScroll').attr('data-size');
                    console.log(elid);
                    if(scrollSize > 0){
                        $('#'+elid+' .widget-body').mCustomScrollbar({
                            setHeight: scrollSize+'px',
                            theme: 'minimal-dark'
                        });
                    }
                }, 350);
            });
        }
        $('.hidewg').hide();
        $('body').on('click','.widget-header .hidewg', function() {
            $(this).closest(".widget").find('.widget-body').html('');
            $(this).hide();
        });


        // Search form validator
        var searchType = "<?= ($_SESSION['M']['search_filter']) ?? 'all' ?>";
        function filterSearchForm() {
            if (searchType == 'tp') {
                $('#search').prop({type:"number"});
                $('#search').prop({placeholder:"Search by TP ..."});
            } else if (searchType == 'phone') {
                $('#search').prop({type:"number"});
                $('#search').prop({placeholder:"Search by Phone ..."});
            } else if (searchType == 'name') {
                $('#search').prop({type:"text"});
                $('#search').prop({placeholder:"Search by Name ..."});
            } else if (searchType == 'email') {
                $('#search').prop({type:"email"});
                $('#search').prop({placeholder:"Search by Email ..."});
            } else if (searchType == 'all') {
                $('#search').prop({type:"text"});
                $('#search').prop({placeholder:"Search Any ..."});
            }
        }
        function validateSearchForm() {
            let string = $('#search').val();
            if (searchType == 'tp' && string.length<6) {
                alert("TP login must be at less 6 digits !");
                return false;
            } else if (searchType == 'phone' && string.length<6) {
                alert("Phone must be at less 5 digits !");
                return false;
            } else if (searchType == 'name' && string.length<5) {
                alert("Name must be at less 5 characters !");
                return false;
            } else if (searchType == 'email' && string.length<3) {
                alert("Entered email is not valid !");
                return false;
            } else if (searchType == 'all' && string.length<5) {
                alert("Search need at less 5 characters/digits");
                return false;
            }
        }
        
        $("form#searchForm #filter").change(function(){
            searchType = $(this).val();
            filterSearchForm();
        });
        
        $("form#searchForm #filter").val('<?= ($_SESSION['M']['search_filter']) ?? 'all' ?>');
        
        filterSearchForm();
        
        var elem = document.documentElement;
        function openFullscreen() {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) { /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { /* IE11 */
                elem.msRequestFullscreen();
            }
            $('#fullscreen .requestfullscreen').hide();
		    $('#fullscreen .exitfullscreen').show();
        }
        
        function closeFullscreen() {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) { /* Safari */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { /* IE11 */
                document.msExitFullscreen();
            }
            $('#fullscreen .requestfullscreen').show();
		    $('#fullscreen .exitfullscreen').hide();
        }

        /**
         * Fix Tooltip hide
         */
        $('body').on('click','*', function() {
            $('[data-toggle="tooltip"], .tooltip').tooltip("hide");
        });

        // Add To mergee list
        $('body').on('click','.admin-tools .doA-addMerge', function() {
            let id = $(this).data('userid');
            const r = confirm("Do you want to add the user to merge list?");
            if (r === true) {
                ajaxCall('users', 'addMerge', {"id":id}, function (Res) {
                    toastr.success('User added to merge list');
                });
            }
        });

        // Delete Ueser
        $("body").on("click touchstart", '.admin-tools .doA-deleteUser', function() {
            let data = {
                id: $(this).data('id'),
            }
            const r = confirm("Delete User?");
            if (r === true) {
                ajaxCall('users', 'delete', data, function (response) {
                    let resObj = JSON.parse(response);
                    if (resObj.e) {
                        toastr.error("Error on request !");
                    } else if (resObj.res) {
                        toastr.success("User Deleted.");
                    }
                });
            }
        });

    </script>

<?php

    echo factory::footer();

    // ActLog View
    $act_type = 'View';
    $_page_path = (basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING'])) ?? 'index';
    $detail_act['post'] = $_POST;
    $detail_act['get'] = $_GET;
    # Search Results
    if ($_page_path == 'search.php') {
        $act_type = 'Search';
        $countF['All'] = $count;
        $detail_act['res_count'] = $countF;
    }
    $actLog->add($act_type, null, 1, json_encode($detail_act));

?>