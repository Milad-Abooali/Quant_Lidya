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

    $query['db']        = 'DB_admin';
    $query['table_html']     = 'leads_main';
    $query['query']     = "`user_extra` AS `user_extra` LEFT JOIN `users` AS `users` ON `users`.`id` = `user_extra`.`user_id` LEFT JOIN `status` AS `status` ON `user_extra`.`status` = `status`.`id` LEFT JOIN `user_marketing` ON `user_marketing`.`user_id` = `user_extra`.`user_id` WHERE ";
    if ($_SESSION["type"] == "Admin")       $query['query']     .= ' user_extra.type = 1 ';
    if ($_SESSION["type"] == "Manager")     $query['query']     .= ' user_extra.type = 1 AND user_extra.unit = "'.$_SESSION["unitn"].'" AND user_extra.status != 16';
    if ($_SESSION["type"] == "Sales Agent") $query['query']     .= ' user_extra.type = 1 AND user_extra.unit = "'.$_SESSION["unitn"].'" AND user_extra.conversion = "'.$_SESSION["id"].'" AND user_extra.status != 16';
    $query['key']       = '`users`.`id`';
    $query['columns']   = array(
                                    array(
                                        'db' => 'users.id',
                                        'dt' => 0,
                                        'field' => 'id',
                                        'th' => '<input type="checkbox" name="select_all" value="1" id="select-all">'
                                    ),
                                    array(
                                        'db' => 'user_extra.fname',
                                        'dt' => 1,
                                        'th' => 'First Name',
                                    ),
                                    array(
                                        'db' => 'user_extra.lname',
                                        'dt' => 2,
                                        'th' => 'Last Name'
                                    ),
                                    array(
                                        'db' => 'users.username',
                                        'dt' => 3,
                                        'th' => 'Username'
                                    ),
                                    array(
                                        'db' => 'users.email',
                                        'dt' => 4,
                                        'th' => 'Email'
                                    ),
                                    array(
                                        'db' => 'user_extra.phone',
                                        'dt' => 5,
                                        'th' => 'Phone'
                                    ),
                                    array(
                                        'db' => 'DATE_FORMAT(user_extra.created_at,"%y/%m/%d %H:%i:%s")',
                                        'dt' => 6,
                                        'th' => 'Created At'
                                    ),
                                    array(
                                        'db' => '(SELECT username FROM users WHERE user_extra.conversion = users.id)',
                                        'dt' => 7,
                                        'th' => 'Agent'
                                    ),
                                    array(
                                        'db' => 'status.status',
                                        'dt' => 8,
                                        'th' => 'Status',
                                        'formatter'=> true
                                        ),
                                    array(
                                        'db' => 'DATE_FORMAT(user_extra.lastnotedate, "%y/%m/%d %H:%i:%s")',
                                        'dt' => 9,
                                        'th' => 'Last Note'
                                    ),
                                    array(
                                        'db' => 'user_marketing.lead_src',
                                        'dt' => 10,
                                        'th' => 'Source',
                                        'formatter'=> true
                                    ),
                                    array(
                                        'db' => 'users.unit',
                                        'dt' => 11,
                                        'th' => 'Unit'
                                    ),
                                    array(
                                        'db' => 'DATE_FORMAT(user_extra.updated_at, "%y/%m/%d %H:%i:%s")',
                                        'dt' => 12,
                                        'th' => 'Updated At'
                                    ),
                                    array(
                                        'db' => 'users.id',
                                        'dt' => 13,
                                        'th' => 'Action'
                                    ),
                                    array(
                                        'db' => 'user_extra.country',
                                        'dt' => 14,
                                        'th' => 'user_extra.country'
                                    ),
                                    array(
                                        'db' => 'user_marketing.lead_camp',
                                        'dt' => 15,
                                        'th' => 'Campaign'
                                    ),
                                    array(
                                        'db' => 'user_marketing.campaign_extra',
                                        'dt' => 16,
                                        'th' => 'campaign_extra'
                                    ),
                                    array(
                                        'db' => 'user_extra.logins',
                                        'dt' => 17,
                                        'th' => 'tp.logins'
                                    ),
                                    array(
                                        'db' => '(SELECT username FROM users WHERE user_marketing.affiliate = users.id)',
                                        'dt' => 18,
                                        'th' => 'affiliate'
                                    ),
                                );
    $option = "
        		'columnDefs': [
        		        {
                            'targets': 0,
                            'searchable':false,
                            'orderable':false,
                            'className': 'dt-body-center',
                            'render': function (data, type, full, meta){
                                return '<input type=\"checkbox\" name=\"id[]\" value=\"'+ $('<div/>').text(data).html() + '\">';
                            }
        		        },
        		        {
                            'targets': 13,
                            'searchable':false,
                            'orderable':false,
                            'className': 'dt-body-center',
                            'render': function (data, type, full, meta){
                                return '<div class=\"row m-0\"><a href=\"javascript:;\" class=\"bg-gradient-info text-white btn-sm detail col-md-12 col-sm-6 mb-1 text-center\" data-user=\"'+data+'\"><i class=\"fas fa-user-check\"></i> View</a><a href=\"javascript:;\" class=\"bg-gradient-danger text-white btn-sm edit col-md-12 col-sm-6 text-center\" data-user=\"'+data+'\"><i class=\"fas fa-user-edit\"></i> Edit</a></div>';
                            }
        		        },
        		        {
                            'targets': 4,
                            'orderable':false,
                            'visible': false
        		        },
        		        {
                            'targets': 11,
                            'orderable':false,
                            'visible': false
        		        },
        		        {
                            'targets': 12,
                            'orderable':true,
                            'visible': false
        		        },
        		        {
                            'targets': 14,
                            'orderable':false,
                            'visible': false
        		        },
        		        {
                            'targets': 15,
                            'orderable':false,
                            'visible': false
        		        },
        		        {
                            'targets': 16,
                            'orderable':false,
                            'visible': false
        		        },
        		        {
                            'targets': 17,
                            'orderable':false,
                            'visible': false
        		        },
        		        {
                            'targets': 18,
                            'orderable':false,
                            'visible': false
        		        }
                ],
                'lengthMenu': [ [5, 10, 25, 50, 100, 200, 300, 400, 500, -1], [5, 10, 25, 50, 100, 200, 300, 400, 500, 'All'] ],
                'order': [[12, 'desc']]
    ";

    $table_leads_main = $factory::dataTableComplex(100,$query,$option);

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
                                <h4 class="page-title">Dashboard</h4>
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
                    <div class="card pmd-card">
        				<div class="card-body">
            			    <form action="" method="post" autocomplete="off">
                            	<div class="form-row">
                            		<div class="form-group col-md-3">
                            			<label for="filterAgent">Agent</label>
                                        <input id="DT_leads_main_agent" class="DT_leads_main_CustomOperation DT_CustomOperation" value="" type="hidden" readonly="">
                                        <button type="button" class="do-clear-filters btn btn-sm" data-target="filterAgent" data-itar="DT_leads_main_agent"><i class="fa fa-times-circle"></i></button>
                                        <select class="form-control form-select3" id="filterAgent" name="filterAgent" multiple="multiple" required>
                                            <?php
                                                if($_SESSION["type"] == "Admin"){
                                                    $sqlUSERS = 'SELECT id, username FROM users WHERE type IN ("Sales Agent", "Manager", "Retention Agent") ORDER BY users.username ASC';
                                                } else if($_SESSION["type"] == "Manager"){
                                                    $sqlUSERS = 'SELECT users.id, users.username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Sales Agent", "Manager") AND user_extra.status != 9 ORDER BY users.username ASC';
                                                } else if($_SESSION["type"] == "Sales Agent"){
                                                    $sqlUSERS = 'SELECT users.id, users.username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.id = "'.$_SESSION["id"].'" AND user_extra.status != 9 ORDER BY users.username ASC';
                                                } else {
                                                    $sqlUSERS = 'SELECT users.id, users.username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Sales Agent", "Manager") AND user_extra.status != 9 ORDER BY users.username ASC';
                                                }
                                                $users = $DB_admin->query($sqlUSERS);
                                                if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                    echo "<option value='".$rowUSERS['id']."'>".$rowUSERS['username']."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="filterStatus">Status</label>
                                        <input id="DT_leads_main_status" class="DT_leads_main_CustomOperation DT_CustomOperation" value="" type="hidden" readonly="">
                                        <button type="button" class="do-clear-filters btn btn-sm" data-target="filterStatus" data-itar="DT_leads_main_status"><i class="fa fa-times-circle"></i></button>
                                        <select class="form-control form-select3" id="filterStatus" name="filterStatus" multiple="multiple" required>
                                            <?php
                                                $sqlUSERS = 'SELECT status FROM status WHERE cat = "Leads"';
                                                $users = $DB_admin->query($sqlUSERS);
                                                if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                    echo "<option value='".$rowUSERS['status']."'>".$rowUSERS['status']."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="filterEmail">Email</label>
                            			<input class="form-control" id="filterEmail" name="filterEmail" placeholder="Email Address" required>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="filterName">Name</label>
                            			<input class="form-control" id="filterName" name="filterName" placeholder="Name" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                            			<label for="filterSurname">Surname</label>
                            			<input class="form-control" id="filterSurname" name="filterSurname" placeholder="Surname" required>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="filterPhone">Phone</label>
                            			<input class="form-control" id="filterPhone" name="filterPhone" placeholder="Phone" required>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="filterCountry">Country</label>
                            			<select class="form-control" id="filterCountry" name="filterCountry" required>
                            			    <option value="">All</option>
                                            <?php
                                                $sqlUSERS = 'SELECT country_name FROM countries';
                                                $users = $DB_admin->query($sqlUSERS);
                                                if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                    echo "<option value='".$rowUSERS['country_name']."'>".$rowUSERS['country_name']."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="filterUnit">Unit</label>
                            			<input class="form-control" id="filterUnit" name="filterUnit" placeholder="Unit" required>
                                    </div>
                    			</div>
                    			<div class="form-row">
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" class=" " id="filterTp" name="filterTp" required> <label for="filterLogin">Tp Login</label>
                                        <input class="form-control" id="filterLogin" name="filterLogin" placeholder="000000" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="inputNew">New Leads</label>
                                        <select class="form-control" id="filterNew" name="filterNew" required>
                                            <option value="">Please Select</option>
                                            <option value="adsleads@lidyafx.com">New Leads</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="filterSource">Source</label>
                            			<input class="form-control" id="filterSource" name="filterSource" placeholder="Source" required>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="filterAffiliate">Affiliate</label>
                            			<input class="form-control" id="filterAffiliate" name="filterAffiliate" placeholder="Affiliate" required>
                                    </div>
                                    <div class="form-group col-md-2">
                            			<label for="fiterCreatedAt">Created At</label>
                                        <input class="form-control" id="fiterCreatedAt" name="fiterCreatedAt" placeholder="21/01/00" >
                                    </div>
                                </div>
                            </form>
                            <?php if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){ ?>
                            <hr>
                            <div class="form-row form-inline">
                                <div class="form-group col-md-12">
                                    <label for="changeAgent">Change Conversion Agent to </label>
                                    <select class="form-control form-control-sm mx-md-3" id="changeAgent" name="changeAgent" required>
                                        <option value="">Please Select an Agent</option>
                                        <?php
                                            if($_SESSION["type"] == "Admin"){
                                                $sqlUSERS = 'SELECT id, username FROM users WHERE type IN ("Sales Agent", "Manager")';
                                            } else {
                                                $sqlUSERS = 'SELECT users.id, users.username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Sales Agent", "Manager") AND user_extra.status != 9';
                                            }
                                            $users = $DB_admin->query($sqlUSERS);
                                            if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                echo "<option value='".$rowUSERS['id']."'>".$rowUSERS['username']."</option>";
                                            }
                                        ?>
                                    </select>
                                    <button type="button" class="btn bg-gradient-primary text-white btn-sm" id="editUsersAgent"><i class="fas fa-edit-circle"></i> Change Agent</button>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
            		</div>
                    <div class="card pmd-card">
        				<div class="card-body">
                            <?php
            					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Sales Agent" ){
            					    if($_SESSION["unit"] == "Turkish" AND $_SESSION["type"] == "Sales Agent"){ 
            				?>
            				        <div class="row">
                                        <div class="col-md-6">
                                            <span class="bold size-20">Leads <i class="fas fa-angle-right"></i></span>
                                        </div>
                                    </div>
                                    <hr>
                                    <?= $table_leads_main ?>
            				<?php
            					    } else {
                            ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span class="bold size-20">Leads <i class="fas fa-angle-right"></i></span>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="bold size-20 float-right">
                                                <button type="button" class="btn bg-gradient-primary text-white btn-sm" id="new_lead"><i class="fas fa-plus-circle"></i> New Leads</button>
                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    <?= $table_leads_main ?>
                            <?php
            					    }
            					}
            				?>
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

<?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
        $(document).ready( function () {
            $('.form-select3').selectpicker({
                tickIcon: 'fas fa-check',
                liveSearch: true
            });

        	$(function () {
        		$('[data-toggle="tooltip"]').tooltip()
        	})
        	$('#startTime').datepicker({ 
        	    uiLibrary: 'bootstrap',
                iconsLibrary: 'fontawesome', 
                format: 'yyyy-mm-dd' 
        	});
        	$('#endTime').datepicker({ 
        	    uiLibrary: 'bootstrap',
                iconsLibrary: 'fontawesome', 
                format: 'yyyy-mm-dd' 
        	});

            $('.do-clear-filters').on('click', function() {
                let target = '#'+$(this).data('target')
                let itar = '#'+$(this).data('itar')
                $(target).selectpicker('deselectAll');
                $(itar).val('');
                setTimeout(function() {
                    DT_leads_main.ajax.reload();
                }, 50);
            });
            $('#filterAgent').on('change', function() {
                const selected = $(this).val();
                if(selected.length > 0) {
                    const customJson = (selected.includes("**")) ? '' : '{"columns":"user_extra.conversion","operator":"IN","params":'+JSON.stringify(selected)+'}';
                    $('#DT_leads_main_agent').val(customJson);
                    setTimeout(function() {
                        DT_leads_main.ajax.reload();
                    }, 50);
                }
            } );
            $('#filterStatus').on('change', function () {
                const selected = $(this).val();
                if(selected.length > 0) {
                    const customJson = (selected.includes("**")) ? '' : '{"columns":"status.status","operator":"IN","params":' + JSON.stringify(selected) + '}';
                    $('#DT_leads_main_status').val(customJson);
                    setTimeout(function () {
                        DT_leads_main.ajax.reload();
                    }, 50);
                }
            } );
            $('#filterEmail').on('change', function () {
                DT_leads_main.columns(3).search( this.value ).draw();
            } );
            $('#filterPhone').on('change', function () {
                DT_leads_main.columns(5).search( this.value ).draw();
            } );
            $('#filterName').on('change', function () {
                DT_leads_main.columns(1).search( this.value ).draw();
            } );
            $('#filterNew').on('change', function () {
                DT_leads_main.columns(7).search( this.value ).draw();
            } );
            $('#filterSurname').on('change', function () {
                DT_leads_main.columns(2).search( this.value ).draw();
            } );
            $('#filterCountry').on('change', function () {
                DT_leads_main.columns(14).search( this.value ).draw();
            } );
            $('#filterAffiliate').on('change', function () {
                DT_leads_main.columns(18).search( this.value ).draw();
            } );
            $('#filterUnit').on('change', function () {
                DT_leads_main.columns(11).search( this.value ).draw();
            } );
            $('#filterLogin').on('change', function () {
                DT_leads_main.columns(17).search( this.value ).draw();
            } );
            $('#filterSource').on('change', function () {
                DT_leads_main.columns(10).search( this.value ).draw();
            } );
            $('#fiterCreatedAt').on('change', function () {
                DT_leads_main.columns(6).search( this.value ).draw();
            } );

            $('#filterTp').on('change', function () {
                if ($(this).is(':checked')) {
                    DT_leads_main.columns(17).search('.', true, false).draw();
                } else {
                    DT_leads_main.columns(17).search('').draw();
                }
            } );


            $("body").on('click', '#leads_main .detail', function(){
                var val = $(this).attr('data-user');
                var valuser = '<?php echo htmlspecialchars($_SESSION["username"]); ?>'; 
                var url = "user-details.php?code="+val;
                $('.my-modal-cont').load(url,function(result){
                    $(".modal-title").html('Leads Details');
                    $('#myModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#myModal').modal({show:true});
                });
            });
            $("body").on('click', '#leads_main .edit', function(){
                var val2 = $(this).attr('data-user');
                var valuser2 = '<?php echo htmlspecialchars($_SESSION["username"]); ?>'; 
                var url = "user-edit.php?code="+val2;
                $('.my-modal-cont').load(url,function(result){
                    $(".modal-title").html('Are You Sure? '+valuser2);
                    $('#myModal .modal-footer').html('<button type="button" class="btn bg-gradient-primary text-white" id="saveUser" disabled>Save</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#myModal').modal({show:true});
                });
            });
            
            $("#new_lead").click(function() {
                var valuser3 = '<?php echo htmlspecialchars($_SESSION["id"]); ?>'; 
                var url = "new_lead.php?code="+valuser3;
                $('.my-modal-cont').load(url,function(result){
                    $(".modal-title").html('Add A New Lead');
                    $('#myModal .modal-footer').html('<button type="submit" class="btn bg-gradient-primary text-white" id="addUser" disabled>Add</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#myModal').modal({show:true});
                });
            });
            
            
            $('#select-all').on('click', function(){
                // Get all rows with search applied
                var rows = DT_leads_main.rows({ 'search': 'applied' }).nodes();
                // Check/uncheck checkboxes for all rows in the table
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
            
            $('body').on( 'click', '#leads_main tbody tr', function () {
                var rowData = DT_leads_main.row( this ).nodes();
                if($('input', rowData).prop("checked") == true){
                    $('input', rowData).prop('checked', false);
                    $(this).removeClass("bg-gradient-primary text-white");
                }
                else if($('input', rowData).prop("checked") == false){
                    $('input', rowData).prop('checked', true);
                    $(this).addClass("bg-gradient-primary text-white");
                }
                //$('input', rowData).prop('checked', true);
                //alert(rowData);
            } );
            
            $('#editUsersAgent').on('click', function() {
        		var conversion = $("#changeAgent").val();
        		var users = new Array();
                $("input:checked").each(function() {
                    users.push($(this).val());
                });
        
        		if(conversion!=""){
        			$.ajax({
        				url: "edit-mconversion.php",
        				type: "POST",
        				data: {
                            conversion: conversion,
        					users: users
        				},
        				cache: false,
        				success: function(dataResult){
        				    console.log(dataResult);
        					var dataResult = JSON.parse(dataResult);
        					if(dataResult.statusCode==200){
        					    toastr.success("Leads agents updated successfully!");
        					}
        					else if(dataResult.statusCode==201){
        					   alert(dataResult.statusCode);
        					}
        					
        				}
        			});
        		}
        		else{
        			alert('Please fill all the field!');
        		}
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
        	
        
        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>
</html>