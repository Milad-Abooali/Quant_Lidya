<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

include('includes/head.php'); ?>

        <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">

<?php include('includes/css.php'); ?>

    <body>

        <!-- Begin page -->
        <div id="wrapper">


<?php 
    include('includes/topbar.php');
    include('includes/sidebar.php');

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    GF::loadCSS('f','assets/css/timeline.css');

    // Transactions
    $query['db']        = 'DB_admin';
    $query['table_html']     = 'transactions_requests';

    if($_SESSION["type"] == "Manager"){
        $query['query']  = '`transactions` LEFT JOIN `user_extra` ON `user_extra`.`user_id` = `transactions`.`user_id` WHERE `user_extra`.`unit` = "'.$_SESSION["unitn"].'" ';
    } else {
        $query['query']  = '`transactions` LEFT JOIN `user_extra` ON `user_extra`.`user_id` = `transactions`.`user_id` WHERE 1 ';
    }

    $query['key']       = '`transactions`.`id`';
    $query['columns']   = array(
        array(
            'db' => 'transactions.user_id',
            'dt' => 0,
            'th' => 'Client',
            'formatter' => true
        ),
        array(
            'db' => 'transactions.type',
            'dt' => 1,
            'th' => 'Type'
        ),
        array(
            'db' => 'transactions.amount',
            'dt' => 2,
            'th' => 'Amount'
        ),
        array(
            'db' => '(SELECT count(id) FROM transactions_docs WHERE transaction_id=transactions.id)',
            'dt' => 3,
            'th' => 'Doc',
        ),
        array(
            'db' => '(SELECT count(id) FROM transactions_comment WHERE transaction_id=transactions.id)',
            'dt' => 4,
            'th' => 'Comment',
        ),
        array(
            'db' => 'transactions.source',
            'dt' => 5,
            'th' => 'Source',
            'formatter' => true
        ),
        array(
            'db' => 'transactions.destination',
            'dt' => 6,
            'th' => 'Destination'
        ),
        array(
            'db' => 'transactions.status',
            'dt' => 7,
            'th' => 'Status'
        ),
        array(
            'db' => 'transactions.desk_verify',
            'dt' => 8,
            'th' => 'Verification',
            'formatter' => true
        ),
        array(
            'db' => 'transactions.updated_by',
            'dt' => 9,
            'th' => 'Updated By',
            'formatter' => true
        ),
        array(
            'db' => 'transactions.updated_at',
            'dt' => 10,
            'th' => 'Updated At',
            'formatter' => true
        ),
        array(
            'db' => 'transactions.finance_verify',
            'dt' => 11,
            'th' => 'finance_verify'
        ),
        array(
            'db' => 'transactions.treasury_verify',
            'dt' => 12,
            'th' => 'treasury_verify'
        ),
        array(
            'db' => 'transactions.id',
            'dt' => 13,
            'th' => 'Timeline',
            'formatter' => true
        ),
        array(
            'db' => 'user_extra.unit',
            'dt' => 14,
            'th' => 'unit'
        ),
        array(
            'db' => 'transactions.created_at',
            'dt' => 15,
            'th' => 'created_at'
        )
    );
    $option = '
          		"responsive": true,
                "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
        		"order": [ 10, "desc" ],
        		"order": [ 10, "desc" ],
                "columnDefs": [
                    {
                        "targets": [ 11 ],
                        "visible": false,
                        "searchable": false
                    },
                    {
                        "targets": [ 12 ],
                        "visible": false
                    },
                    {
                        "targets": [ 14 ],
                        "visible": false
                    },
                    {
                        "targets": [ 15 ],
                        "visible": false
                    }
                ]
    ';

    $transactions_requests = $factory::dataTableComplex(5,$query,$option);

?>
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
    		<div class="row">
    			<div class="col-lg-12">
    				<div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Requests</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?php echo Broker['title'];?>
                                    </li>
                                </ol>
                                <div class="state-information d-none d-sm-block">
                                    <div class="state-graph">
                                        <div id="header-chart-1"></div>
                                        <div class="info">Leads 1500</div>
                                    </div>
                                    <div class="state-graph">
                                        <div id="header-chart-2"></div>
                                        <div class="info">Converted 40</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            		<div class="row">
            		    <div class="col-md-12">
                            <div class="card pmd-card">
            					<div class="card-body">
                                    <?php
                    					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){
                                    ?>
                                            <div class="row">

                                                <div class="col-md">
                                                    <span class="bold size-20">Transactions <i class="fas fa-angle-right"></i></span>
                                                </div>

                                                <div class="form-group col-md">
                                                    <div class="">
                                                        <label>Type</label><br>
                                                        <button class="filterDTX btn btn-sm btn-light" data-tableid="DT_transactions_requests" data-col="1" data-filter="">&nbsp; ALL</button>
                                                        <button class="filterDTX btn btn-sm btn-outline-success" data-tableid="DT_transactions_requests" data-col="1" data-filter="Deposit">&nbsp; Deposit</button>
                                                        <button class="filterDTX btn btn-sm btn-outline-danger" data-tableid="DT_transactions_requests" data-col="1" data-filter="Withdraw">&nbsp; Withdraw</button>
                                                    </div>
                                                    <div class="">
                                                        <label>Status</label><br>
                                                        <button class="filterDTX btn btn-sm btn-light" data-tableid="DT_transactions_requests" data-col="7" data-filter="">&nbsp; ALL</button>
                                                        <button class="filterDTX btn btn-sm btn-outline-warning" data-tableid="DT_transactions_requests" data-col="7" data-filter="Pending">&nbsp; Pending</button>
                                                        <button class="filterDTX btn btn-sm btn-outline-success" data-tableid="DT_transactions_requests" data-col="7" data-filter="Done">&nbsp; Done</button>
                                                        <button class="filterDTX btn btn-sm btn-outline-info" data-tableid="DT_transactions_requests" data-col="7" data-filter="Payment">&nbsp;Payment</button>
                                                        <button class="filterDTX btn btn-sm btn-outline-danger" data-tableid="DT_transactions_requests" data-col="7" data-filter="Canceled"> &nbsp;Canceled</button>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md">
                                                    <div class="form-group row">
                                                        <label class="label">From: </label>
                                                        <input id="date_start" class="DT_CustomOperation_date form-control input-sm"  type="datetime-local">
                                                        <label class="label">To: </label>
                                                        <input id="date_end" class="DT_CustomOperation_date form-control input-sm" type="datetime-local">
                                                        <button class="clear-date btn btn-light">Clear</button>
                                                        <div class="d-none">
                                                            <input id="DT_transactions_requests_time" class="DT_transactions_requests_CustomOperation" value="" type="text" readonly="">
                                                        </div>

                                                    </div>
                                                </div>

                                                <?php
                                                    GF::makeJS('f','
                                                        $(\'body\').on(\'change\', \'.DT_CustomOperation_date\', function(event){
                                                            let customJson = \'columns":"transactions.created_at","operator":"BETWEEN","param":["\'+$(\'#date_start\').val()+\'","\'+$(\'#date_end\').val()+\'"]}\';
                                                            $(\'#DT_transactions_requests_time\').val(customJson);
                                                            setTimeout(function() {
                                                                if($(\'#date_end\').val()) DT_transactions_requests.ajax.reload();
                                                            }, 500);
                                                        });                                                         
                                                    ');
                                                ?>

                                                <div class="col-md text-right float-right">
                                                    <label for="refreshTime">Reload</label><br>
                                                    <small class="refPageSel">
                                                        <i class="fas fa-sync" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Table will not reload."></i>
                                                        <select id="refreshTime" name="refreshTime" class="custom-select custom-select-sm col-md-2 mx-2" data-tableid="DT_transactions_requests">
                                                            <option value="0" selected="">No</option>
                                                            <option value="3">3 s</option>
                                                            <option value="15">15 s</option>
                                                            <option value="60">1 M</option>
                                                            <option value="180">3 M</option>
                                                            <option value="300">5 M</option>
                                                            <option value="600">10 M</option>
                                                            <option value="900">15 M</option>
                                                        </select>
                                                        <span>2021-12-1  3:31:16</span>
                                                    </small>
                                                    <i id="do-reload" data-tableid="DT_transactions_requests" class="fa fa-spinner alert-success primary rounded-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Force reload."></i>
                                                </div>

                                            </div>
                                            <hr>
                                            <?= $transactions_requests; ?>
                                    <?php

                    					}
                    				?>
                    			</div>
                            </div>
                        </div>
                    </div>
    			</div>
    		</div>
        </div>
        <?php include('includes/footer.php'); ?>
    </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->
</div>
<!-- END wrapper -->

<div id="JS_veifyForm" class="d-none">
    <form id="tUpdate" name="tUpdate" method="post" enctype="multipart/form-data">
        <div class="col-sm-12 mb-3" id="receipt">
            <label for="doc">New Document /<small>Up to 3 file</small></label>
            <div class="custom-file my-1">
                <input type="file" class="custom-file-input" id="doc" name="doc[]">
                <label class="custom-file-label" for="doc">Choose your Receipt</label>
            </div>
            <span style="display: none" data-max="2" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> Add more</span>
        </div>
        <div class="col-sm-12 mb-3" id="comment">
            <label for="inputComment">Comment /<small>Optional</small></label>
            <textarea class="form-control" type="text" id="comment" name="comment"></textarea>
        </div>
        <div class="col-sm-12">
            <input type="hidden" name="transaction_id" value="">
            <input type="hidden" name="user_id" value="<?= $_SESSION['id'] ?>">
            <button class="btn btn-primary"  type="submit">Submit</button>
            <div id="fRes" class="mt-3 alert" style="display: none;"></div>
        </div>
    </form>
</div>

<div id="JS_veifyFooterD" class="d-none">
    <div class="container row">
        <div id="dform" class="col-5"></div>
        <div class="col-2"></div>
        <div class="col-5 text-right">
            <div id="dCheck" class="text-left py-2">
                <input class="form-check-input dCheck" type="checkbox" id="dnamecheck"><label for="dnamecheck" > Name Check </label>
                <br>
                <input class="form-check-input dCheck" type="checkbox" id="dreqcheck"><label for="dreqcheck"> Request Data Check (Bank account/Documents)</label>
                <br>
                <input class="form-check-input dCheck" type="checkbox" id="dothercheck"><label for="dothercheck"> Anything other</label>
            </div>
            <button type="button" class="btn btn-success disabled" id="btn-dverify">Verify</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<div id="JS_veifyFooterTW" class="d-none">
    <div class="container row">
        <div id="tform" class="col-5"></div>
        <div class="col-2"></div>
        <div class="col-5 text-right">

            <div class="text-left">
                <span id="withdrawreq"></span>
                <br>
                <span>Withdraw Available: <span id="withdrawavailable"><strong></strong><button id="doG-withdrawavailable" class="ml-2 btn btn-sm btn-info"><i class="fa fa-spinner"></i> Check</button> </span></span>
            </div>

            <div id="tCheckW" class="text-left py-2">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Bonus Out</span>
                    </div>
                    <input id="boamount" type="number" class="form-control col-md-4" value="0" min="0">
                </div>
                <div class="ml-4">
                    <input class="form-check-input tCheck" type="checkbox" id="tnamecheck">
                    <label for="tnamecheck"> Confirm All Information</label>
                </div>
            </div>

            <button type="button" class="btn btn-success disabled" id="btn-tverify">Verify</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<div id="JS_veifyFooterTD" class="d-none">
    <div id="tform" class="col-5"></div>
    <div class="col-md-2"></div>
    <div class="col-5 text-right">
        <div class="text-left mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input tCheck" type="checkbox" id="checkftd" name="" value="option1">
                <label class="form-check-label mr-2 tCheck" for="checkftd">FTD</label>
                <input class="form-check-input tCheck" type="checkbox" id="checkret" name="" value="option1">
                <label class="form-check-label tCheck" for="checkret">RET</label>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="form-check form-check-inline float-left col-md-6 col-sm-12">
                <input class="form-check-input" type="radio" name="radio" id="checkbonus" value="option1">
                <label class="form-check-label" for="checkbonus">Bonus</label>
            </div>
            <div class="form-check form-check-inline float-left col-md-6 col-sm-12">
                <input class="form-check-input" type="radio" name="radio" id="checkspecial" value="option1">
                <label class="form-check-label" for="checkspecial">Special</label>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="mt-1">
                <select class="form-control tCheck" style="display:none" id="selectbonus" name="selectbonus">
                </select>
                <input type="text" class="form-control tCheck" style="display:none" placeholder="Amount in Dollar" name="inputspecial" id="inputspecial">
            </div>
        </div>

        <div id="tCheck" class="text-left py-2">
            <input class="form-check-input tCheck" type="checkbox" id="tallcheck"><label for="tallcheck"> Confirm All Information</label>
        </div>

        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success disabled" id="btn-tverify">Verify</button>
    </div>
</div>

<?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
        $(document).ready( function () {
        	$(function () {
        		$('[data-toggle="tooltip"]').tooltip();
        	})
        	$('#filterCreatedAt').datepicker({ 
        	    uiLibrary: 'bootstrap',
                iconsLibrary: 'fontawesome', 
                format: 'yyyy-mm-dd' 
        	});
        	$('#filterUpdatedAt').datepicker({ 
        	    uiLibrary: 'bootstrap',
                iconsLibrary: 'fontawesome', 
                format: 'yyyy-mm-dd' 
        	});

            $("body").on("click",".clear-date", function(e) {
                $("#DT_transactions_requests_time").val('');
                $(".DT_CustomOperation_date").val('');
                DT_transactions_requests.ajax.reload();
            });

            $("body").on("click touchstart", '#media-table .mverify', function () {
        		let id =  $(this).data('id');
        		let status = $(this).data('status');
        		$.ajax({
        			url: "verify_media.php",
        			type: "POST",
        			data: {
        				id: id,
        				status: status
        			},
        			cache: false,
        			success: function(dataResult){
        			    //console.log(dataResult);
        				toastr.success("Status has been updated.");
        			}
        		});
        	});
        	

            $("body").on("click touchstart", '#adminTools .doA-agree', function() {
                let data = {
                    id: $(this).data('id'),
                    status: $(this).data('status')
                }
                const r = confirm("Change Agreement Status?");
                if (r === true) {
                    ajaxCall('global', 'agreeSet', data, function (response) {
                        let resObj = JSON.parse(response);
                        if (resObj.e) {
                            toastr.error("Error on request !");
                        } else if (resObj.res) {
                            toastr.success("Status has been update.");
                        }
                    });
                }
            });
            
            $("body").on("click touchstart", '#adminTools .doA-allow', function() {
                let data = {
                    id: $(this).data('id'),
                    status: $(this).data('status')
                }
                const r = confirm("Allow E-Book Download?");
                if (r === true) {
                    ajaxCall('global', 'allowSet', data, function (response) {
                        let resObj = JSON.parse(response);
                        if (resObj.e) {
                            toastr.error("Error on request !");
                        } else if (resObj.res) {
                            toastr.success("Status has been update.");
                        }
                    });
                }
            });

        <?php if (in_array($_SESSION['type'],array('Admin'))) { ?>
            // Cancel Request
            $("body").on("click",".doA-cancel", function() {
                if (confirm("Are you sure?")) {
                    const data = {
                        'transaction_id': $(this).data('tid')
                    }
                    ajaxCall ('transaction', 'cancel', data, function(response){
                        let resObj = JSON.parse(response);
                        if (resObj.e) {
                            console.log(resObj);
                        } else {
                            setTimeout(function(){
                                DT_transactions_requests.ajax.reload();
                            }, 50);
                        }
                    });

                }
            });
        <?php } ?>



        	/* Update Request */
            $("body").on("submit","form#tUpdate", function(e) {
                e.preventDefault();
                let commentTU = $('form#tUpdate textarea#comment').val();
                let docTU = $('form#tUpdate #doc').val();
                const fResp = $("form#tUpdate #fRes");
                if (commentTU.length >1 || docTU) {
                    let formData = new FormData(this);
                    ajaxForm ('transaction', 'update', formData, function(response){
                        let resObj = JSON.parse(response);
                        if (resObj.e) {
                            fResp.removeClass('alert-success').addClass('alert-warning');
                            fResp.fadeIn();
                            fResp.html('Error, Please Check Inputs!');
                        }
                        if (resObj.res) {
                            fResp.removeClass('alert-warning').addClass('alert-success');
                            fResp.fadeIn();
                            fResp.html('Your Request Added.');
                            document.getElementById("tUpdate").reset();
                            setTimeout(function(){
                                fResp.fadeOut();
                                $("#attachments").load(" #attachments");
                            }, 1500);
                        }
                    });
                } else {
                    fResp.removeClass('alert-success').addClass('alert-warning');
                    fResp.fadeIn();
                    fResp.html('Error, Please Check Inputs!');
                }
            });
            //chat type
            $("body").on("submit","#modalMain form#tUpdate", function(e) {
                e.preventDefault();
                let commentTU = $('#modalMain form#tUpdate textarea#comment').val();
                let docTU = $('#modalMain form#tUpdate #doc').val();
                const fResp = $("#modalMain form#tUpdate #fRes");
                if (commentTU.length >1 || docTU) {
                    let formData = new FormData(this);
                    ajaxForm ('transaction', 'update', formData, function(response){
                        let resObj = JSON.parse(response);
                        if (resObj.e) {
                            fResp.removeClass('alert-success').addClass('alert-warning');
                            fResp.fadeIn();
                            fResp.html('Error, Please Check Inputs!');
                        }
                        if (resObj.res) {
                            fResp.removeClass('alert-warning').addClass('alert-success');
                            fResp.fadeIn();
                            fResp.html('Your Request Added.');
                            document.getElementById("tUpdate").reset();
                            setTimeout(function(){
                                fResp.fadeOut();

                                const data = {
                                    'transaction_id':$('#modalMain').data('tid')
                                }
                                ajaxCall ('transaction', 'timeline', data, function(response){
                                    $('#modalMain .modal-body').html(response);
                                });

                            }, 1500);
                        }
                    });
                } else {
                    fResp.removeClass('alert-success').addClass('alert-warning');
                    fResp.fadeIn();
                    fResp.html('Error, Please Check Inputs!');
                }
            });

        	/* Timeline */
            $('body').on('click', '#transactions_requests .doM-timeline', function () {
                let tid = $(this).data('tid');
                const data = {
                    'transaction_id':tid
                }
                $('#modalMain').data('tid',tid);
                ajaxCall ('transaction', 'timeline', data, function(response){
                    makeModal('Transaction Timeline',response,'lg');
                });
            });

            /* function Add Commas */
            function addCommas(nStr) {
                nStr += '';
                var x = nStr.split('.');
                var x1 = x[0];
                var x2 = x.length > 1 ? '.' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }
                return x1 + x2;
            }

            /* function verify */
            function verify(data,side) {
                ajaxCall ('transaction', side, data, function(response){
                    let resObj = JSON.parse(response);
                    if (resObj.e) {
                        console.log(resObj);
                    } else {
                        $('#myModal').modal('hide');
                        DT_transactions_requests.ajax.reload();
                    }
                });
            }

            /* Desk Verify  */

            // Modal
            $('body').on('click', '#transactions_requests .desk', function () {
                var userid = $(this).attr('data-user');
        		var id =  $(this).attr('data-id');
        		var status = "1";
                var url = "user-details.php?code="+userid;
                $('.my-modal-cont').load(url,function(result) {
                    $(".modal-title").html('User Details');
                    let JS_veifyFooterD = $('#JS_veifyFooterD').html();
                    $('#myModal .modal-footer').html(JS_veifyFooterD);
                    let JS_veifyForm = $('#JS_veifyForm').html();
                    $('#myModal .modal-footer #dform').html(JS_veifyForm);
                    $(' #dform input[name="transaction_id"]').val(id);
                    $('#myModal .modal-footer #btn-dverify').data('id',id);

                    $('#myModal').modal({show:true});
                });
            });

            // Check Marks
            $("body").on("change","#dCheck .dCheck", function() {
                var dverifay = 0;
                $('#dCheck input.dCheck').each(function(i, obj) {
                    if (this.checked) dverifay++;
                });
                if (dverifay==3) {
                    $('#myModal .modal-footer #btn-dverify').removeClass('disabled');
                    $('#myModal .modal-footer #btn-dverify').addClass('doA-dVerify');
                } else {
                    $('#myModal .modal-footer #btn-dverify').addClass('disabled');
                    $('#myModal .modal-footer #btn-dverify').removeClass('doA-dVerify');
                }
            });

            // Verify
            $("body").on("click","#myModal .modal-footer .doA-dVerify", function() {
                let data = {
                    'transaction_id':$(this).data('id'),
                    'user_id':<?= $_SESSION['id'] ?>
                }
                verify(data,'deskVerify');
            });

        	<?php if($_SESSION["type"] !== "Manager"){ ?>

            /* Finance Verify */
            $("body").on("click","#transactions_requests .finance", function() {
                let data = {
                    'transaction_id':$(this).data('id'),
                    'user_id':<?= $_SESSION['id'] ?>
                }
                verify(data,'financeVerify');
            });

            /* Treasury Verify */
            // Modal
            $('body').on('click', '#transactions_requests .treasury', function () {
                var userid = $(this).attr('data-user');
                var userUnit = $(this).attr('data-unit');
                var id =  $(this).attr('data-id');
        		var tpid =  $(this).attr('data-tp');
        		var reqamount =  $(this).attr('data-amount');
        		var status = "1";
        		var type = $(this).attr('data-type');
                var url = "user-details.php?code="+userid;
                $('.my-modal-cont').load(url,function(result){
                    $(".modal-title").html('User Details');
                    if(type == "Deposit") {
                        ajaxCall ('transaction', 'getBonus', {'unit':userUnit}, function(response){
                            $('#selectbonus').html(response);
                        });
                        let JS_veifyFooterTD = $('#JS_veifyFooterTD').html();
                        $('#myModal .modal-footer').html(JS_veifyFooterTD);
                    } else if(type == "Withdraw") {
                        let JS_veifyFooterTW = $('#JS_veifyFooterTW').html();
                        $('#myModal .modal-footer').html(JS_veifyFooterTW);
                        $('#myModal .modal-footer #withdrawreq').html('Req Amount: <strong>$'+reqamount+'</strong>');
                    }
                    let JS_veifyForm = $('#JS_veifyForm').html();
                    $('#myModal .modal-footer #tform').html(JS_veifyForm);
                    $('#tform input[name="transaction_id"]').val(id);
                    $('#myModal .modal-footer #btn-tverify').data('id',id);
                    $('#myModal').modal({show:true});

                    var doA_tVerify = false;
        			if(type == "Deposit")
        			{

            			var ftd = "";
            			var ret = "";
            			var btype = "";
            			var bamount = "";
            			var comment = "";
            			
            			$("#checkbonus").change(function() {
                            if($("#checkbonus").is(':checked')){
            			        $("#selectbonus").show();
                                $("#inputspecial").hide();
            			    } else {
            			        $("#selectbonus").hide();
                                $("#inputspecial").hide();
            			    }
                        });
                        
                        $("#checkspecial").change(function() {
                            if($("#checkspecial").is(':checked')){
            			        $("#selectbonus").hide();
                                $("#inputspecial").show();
            			    } else {
            			        $("#selectbonus").hide();
                                $("#inputspecial").hide();
            			    }
                        });

                        $("#checkftd").change(function() {
                            if($("#checkftd").is(':checked')) {
            			        ftd = "1";
            			        $("#checkret").prop("checked", false);
            			        $("optgroup[label='Select]").show();
            			        $("optgroup[label='FTD']").show();
            			        $("optgroup[label='Campaign']").show();
            			        $("optgroup[label='Retention']").hide();
            			        ret = "0";
            			    } else {
            			        ftd = "0";
            			    }
                        });
            		    $("#checkret").change(function() {
            			    if($("#checkret").is(':checked')){
            			        ret = "1";
            			        $("#checkftd").prop("checked", false);
            			        $("optgroup[label='Select]").show();
            			        $("optgroup[label='Retention']").show();
            			        $("optgroup[label='Campaign']").show();
            			        $("optgroup[label='FTD']").hide();
            			        ftd = "0";
            			    } else {
            			        ret = "0";
            			    }
                        });

                        // Check Marks
                        $("body").on("change",".tCheck,#checkspecial,#checkbonus", function() {

                            var tverifay = 0;
                            $('input.tCheck').each(function(i, obj) {
                                if (this.checked) tverifay++;
                            });
                            if ($('#inputspecial').val() > 0 || $('#checkbonus').is(':checked')) tverifay++;
                            if (tverifay===3) {
                                $('#myModal .modal-footer #btn-tverify').removeClass('disabled');
                                $('#myModal .modal-footer #btn-tverify').addClass('doA-tDVerify');
                            } else {
                                $('#myModal .modal-footer #btn-tverify').addClass('disabled');
                                $('#myModal .modal-footer #btn-tverify').removeClass('doA-tDVerify');
                            }
                        });

                        $('body').on('click','#btn-tverify.doA-tDVerify', function () {
                            // API - Deposit Transaction
                            let ftd_ret;
                            if($("#checkret").is(':checked')) ftd_ret = 'RET';
                            if($("#checkftd").is(':checked')) ftd_ret = 'FTD';
                            let data_D  = {
                                is_bonus: 0,
                                tp_id: tpid,
                                type: 'Deposit',
                                amount: reqamount,
                                comment: 'CRM - Deposit '+ ftd_ret
                            };
                            ajaxCall ('transaction', 'api', data_D, function(response){
                                // API - Bonus In Transaction
                                if ($('#checkbonus').is(':checked')) {
                                    var bonusIn =  parseInt($('#selectbonus').val()) * reqamount / 100;
                                    var bonusComment =  $("#selectbonus option:selected").text();
                                } else {
                                    var bonusIn =  $('#inputspecial').val();
                                    var bonusComment =  'Special by Treasury';
                                }
                                if(bonusIn > 0) {
                                    let data_D_B  = {
                                        is_bonus: 1,
                                        tp_id: tpid,
                                        type: 'Deposit',
                                        amount: bonusIn,
                                        comment: 'CRM - Bonus In: '+ bonusComment,
                                    };
                                    ajaxCall('transaction', 'api_bonus', data_D_B, function (response) {
                                        // API - CRM Transaction - Deposit
                                        let data_v = {
                                            'transaction_id':$('.doA-tDVerify').data('id'),
                                            'user_id':<?= $_SESSION['id'] ?>
                                        };
                                        verify(data_v,'treasuryVerify');
                                    });
                                } else {
                                    // API - CRM Transaction - Deposit
                                    let data_v = {
                                        'transaction_id':$('.doA-tDVerify').data('id'),
                                        'user_id':<?= $_SESSION['id'] ?>
                                    };
                                    verify(data_v,'treasuryVerify');
                                }
                            });
            			});

        			}
        			else if(type == "Withdraw")
        			{
        			    var boamount = "";
        			    var withdrawable = "";
                        $("#withdrawavailable strong").html('');

        			    var mt4RunClick;
                        // Check Withdraw / Bonus
                        $('body').on('click', '#myModal .modal-footer #doG-withdrawavailable', function () {
                            if (mt4RunClick) return;
                            mt4RunClick=true;
                            $(this).data('run',true);
                            $("#doG-withdrawavailable i").addClass("fa-spin");
                            $("#myModal #pills-4-tab").trigger("click");
                            $(".tp-"+tpid).trigger("click");
                        });

                        var isRun=false;
                        $(document).ajaxStop(function(){

                            if (mt4RunClick) {
                                if(!isRun) {
                                    isRun = true;
                                    setTimeout(function(){
                                        var boamounts = $("#myModal .BonusAmount").text();
                                        boamount = parseFloat(boamounts.replace(',', ''));
                                        $("#boamount").val((boamount > 0) ? boamount : 0);
                                        var equityamounts = $(".EquityAmount").text();
                                        equityamount = parseFloat(equityamounts.replace(',', ''));
                                        withdrawable = (equityamount - boamount).toFixed(2);
                                        $("#withdrawavailable strong").html('$'+withdrawable);
                                        $("#doG-withdrawavailable i").removeClass("fa-spin");
                                        mt4RunClick=false;
                                        isRun = false;
                                        
                                    }, 10);
                                }
                            }

                        });


                        // Check Marks
                        $("body").on("change","#tCheckW .tCheck", function() {
                            var tverifay = 0;
                            $('#tCheckW input.tCheck').each(function(i, obj) {
                                if (this.checked) tverifay++;
                            });                            
                            if (tverifay == '1' && (parseFloat(withdrawable) >= parseFloat(reqamount)) ) {
                                $('#myModal .modal-footer #btn-tverify').removeClass('disabled');
                                $('#myModal .modal-footer #btn-tverify').addClass('doA-tWVerify');
                            } else {
                                $('#myModal .modal-footer #btn-tverify').addClass('disabled');
                                $('#myModal .modal-footer #btn-tverify').removeClass('doA-tWVerify');
                            }
                        });
                        $('body').on('click','.doA-tWVerify', function () {

                            let data_W  = {
                                is_bonus: 0,
                                tp_id: tpid,
                                type: 'Withdraw',
                                amount: reqamount,
                                comment: 'CRM - Withdraw'
                            };
                            ajaxCall('transaction', 'api', data_W, function (response) {
                                // API - Bonus Out Transaction
                                var bonusOut = $("#boamount").val();
                                if(bonusOut > 0) {
                                    let data_W_B  = {
                                        is_bonus: 1,
                                        tp_id: tpid,
                                        type: 'Withdraw',
                                        amount: bonusOut,
                                        comment: 'CRM - Bonus Out'
                                    };
                                    ajaxCall('transaction', 'api_bonus', data_W_B, function (response) {
                                        // API - CRM Transaction - Deposit
                                        let data_v = {
                                            'transaction_id':$('.doA-tWVerify').data('id'),
                                            'user_id':<?= $_SESSION['id'] ?>
                                        };
                                        verify(data_v,'treasuryVerify');
                                    });
                                } else {
                                        // API - CRM Transaction - Withdraw
                                    let data_v = {
                                        'transaction_id':$('.doA-tWVerify').data('id'),
                                        'user_id':<?= $_SESSION['id'] ?>
                                    };
                                    verify(data_v,'treasuryVerify');
                                }
                            });

        			    });
        			}
                });
            });
        	<?php } ?>
        });
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>

</html>
<?php
    $DB_admin->close();
?>