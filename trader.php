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

    $query['db']        = 'DB_admin';
    $query['table_html']     = 'traders_main';
    $query['query']     = "users AS users LEFT JOIN user_extra AS user_extra ON users.id = user_extra.user_id LEFT JOIN status AS status ON user_extra.status = status.id LEFT JOIN user_marketing ON user_marketing.user_id = user_extra.user_id WHERE ";
    if ($_SESSION["type"] == "Admin")       $query['query']     .= ' user_extra.type = 2 ';
    if ($_SESSION["type"] == "Manager")     $query['query']     .= ' user_extra.type = 2 AND user_extra.unit = "'.$_SESSION["unitn"].'" ';
    if ($_SESSION["type"] == "Retention Manager") $query['query']     .= ' user_extra.type = 2 AND user_extra.unit = "'.$_SESSION["unitn"].'" ';
    if ($_SESSION["type"] == "Retention Agent") $query['query']     .= ' user_extra.type = 2 AND user_extra.unit = "'.$_SESSION["unitn"].'" AND user_extra.retention = "'.$_SESSION["id"].'" ';
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
            'th' => 'First Name'
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
            'db' => 'DATE_FORMAT(user_extra.updated_at,"%y/%m/%d %H:%i:%s")',
            'dt' => 6,
            'th' => 'Updated At'
        ),
        array(
            'db' => '(SELECT username FROM users WHERE user_extra.retention = users.id)',
            'dt' => 7,
            'th' => 'RET Agent'
        ),
        array(
            'db' => '(SELECT username FROM users WHERE user_extra.conversion = users.id)',
            'dt' => 8,
            'th' => 'FTD Agent'
        ),
        array(
            'db' => 'status.status',
            'dt' => 9,
            'th' => 'Status',
            'formatter'=>true
        ),
        array(
            'db' => 'DATE_FORMAT(user_extra.lastnotedate, "%y/%m/%d %H:%i:%s")',
            'dt' => 10,
            'th' => 'Last Note'
        ),
        array(
            'db' => 'user_marketing.lead_src',
            'dt' => 11,
            'th' => 'Source',
            'formatter'=>true
        ),
        array(
            'db' => 'users.id',
            'dt' => 12,
            'th' => 'Action'
        ),
        array(
            'db' => 'users.unit',
            'dt' => 13,
            'th' => 'Unit'
        ),
        array(
            'db' => 'user_extra.country',
            'dt' => 14,
            'th' => 'Country'
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
        )
    );
    if($_SESSION["type"] !== "Retention Agent"  ){
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
                            'targets': 12,
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
                            'visible': false,
        		        },
        		        {
                            'targets': 13,
                            'orderable':false,
                            'visible': false,
        		        },
        		        {
                            'targets': 14,
                            'orderable':false,
                            'visible': false,
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
        		        }
                        
                ],
        ";
    } else {
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
                            'targets': 12,
                            'searchable':false,
                            'orderable':false,
                            'className': 'dt-body-center',
                            'render': function (data, type, full, meta){
                                return '<div class=\"row m-0\"><a href=\"javascript:;\" class=\"btn-info btn-sm detail col-md-12 col-sm-6 mb-1 text-center\" data-user=\"'+data+'\"><i class=\"fas fa-user-check\"></i> View</a><a href=\"javascript:;\" class=\"btn-danger btn-sm edit col-md-12 col-sm-6 text-center\" data-user=\"'+data+'\"><i class=\"fas fa-user-edit\"></i> Edit</a></div>';
                            }
                        },
                        {
                            'targets': 6,
                            'orderable':false,
                            'visible': false,
                        },
                        {
                            'targets': 4,
                            'orderable':false,
                            'visible': false,
                        },
                ],
        ";
    }
    $option .= "
                'lengthMenu': [ [5, 10, 25, 50, 100, 200, 300, 400, 500, -1], [5, 10, 25, 50, 100, 200, 300, 400, 500, 'All'] ],
                'order': [[ 6, 'desc' ]]
    ";

    $table_traders_main = $factory::dataTableComplex(100,$query,$option);

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
                        <div class="col-sm-9">
                            <div class="page-title-box">
                                <h4 class="page-title">Dashboard</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?php echo Broker['title'];?>
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-sm-3" id="quotes">
                                    
                        </div>
                    </div>
                    <div class="card pmd-card">
    					<div class="card-body">
            			    <form action="" method="post" autocomplete="off">
                            	<div class="form-row">
                            		<div class="form-group col-md-3">
                            			<label for="inputAgent">Agent</label>
                                        <input id="DT_traders_main_agent" class="DT_traders_main_CustomOperation DT_CustomOperation" value="" type="hidden" readonly="">
                                        <button type="button" class="do-clear-filters btn btn-sm" data-target="filterAgent" data-itar="DT_traders_main_agent"><i class="fa fa-times-circle"></i></button>
                                        <select class="form-control form-select3" id="filterAgent" name="filterAgent" multiple="multiple" required>
                                            <?php
                                                if($_SESSION["type"] == "Admin"){
                                                    $sqlUSERS = 'SELECT id, username FROM users WHERE type IN ("Sales Agent", "Manager", "Retention Manager", "Retention Agent") ORDER BY users.username ASC';
                                                } else {
                                                    $sqlUSERS = 'SELECT users.id, users.username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Retention Agent", "Manager") AND user_extra.status != 9 ORDER BY users.username ASC';
                                                }
                                                $users = $DB_admin->query($sqlUSERS);
                                                while ($rowUSERS = mysqli_fetch_array($users)) {
                                                    echo "<option value='".$rowUSERS['id']."'>".$rowUSERS['username']."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="inputStatus">Status</label>
                                        <input id="DT_traders_main_status" class="DT_traders_main_CustomOperation DT_CustomOperation" value="" type="hidden" readonly="">
                                        <button type="button" class="do-clear-filters btn btn-sm" data-target="filterStatus" data-itar="DT_traders_main_status"><i class="fa fa-times-circle"></i></button>
                                        <select class="form-control form-select3" id="filterStatus" name="filterStatus" multiple="multiple" required>
                                            <option value="FTD">FTD</option>
                                            <option value="Retention">Retention</option>
                                            <option value="Ret - Not Interested">Ret - Not Interested</option>
                                            <option value="Wrong Details">Wrong Details</option>
                                            <option value="Do not call">Do Not Call</option>
                                            <option value="Follow Up - Continuous">Follow Up - Continuous</option>
                                            <option value="Follow Up - Ongoing">Follow Up - Ongoing</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="inputEmail">Email</label>
                            			<input class="form-control" id="filterEmail" name="filterEmail" placeholder="Email Address" required>
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="inputName">Name</label>
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
                                    <div class="form-group col-md-3">
                            			<label for="filterAffiliate">Main Campaign</label>
                            			<input type="search" class="form-control filterDT mb-2" placeholder="Main Campaign" data-tableid="DT_traders_main" data-col="15">
                                    </div>
                                    <div class="form-group col-md-3">
                            			<label for="fiterCreatedAt">Extra Campaign</label>
                            			<input type="search" class="form-control filterDT mb-2" placeholder="Extra Campaign" data-tableid="DT_traders_main" data-col="16">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input type="checkbox" class=" " id="filterTp" name="filterTp" required> <label for="filterLogin">Tp Login</label>
                                        <input class="form-control" id="filterLogin" name="filterLogin" placeholder="000000" required>
                                    </div>
                			    </div>
                            </form>
                            <?php if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){ ?>
                            <h6 class="card-title">Change Agent</h6>
                            <div class="form-row form-inline">
                                <div class="form-group col-md-4">
                                    <select class="form-control form-control-sm mr-md-3" id="changeAgent" name="changeAgent" required>
                                        <option value="">Please Select an Agent</option>
                                        <?php
                                            if($_SESSION["type"] == "Admin"){
                                                $sqlUSERS = 'SELECT id, username FROM users WHERE type IN ("Manager", "Retention Agent", "Admin")';
                                            } else {
                                                if($_SESSION["unitn"] == "3"){
                                                    $sqlUSERS = 'SELECT users.id, users.username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Sales Agent", "Retention Manager", "Retention Agent", "Manager") AND user_extra.status != 9';
                                                } else {
                                                    $sqlUSERS = 'SELECT users.id, users.username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Retention Manager", "Retention Agent", "Manager") AND user_extra.status != 9';
                                                }
                                            }
                                            $users = $DB_admin->query($sqlUSERS);
                                            while ($rowUSERS = mysqli_fetch_array($users)) {
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
            					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent"  ){
                            ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span class="bold size-20">Traders <i class="fas fa-angle-right"></i></span>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="bold size-20 float-right"><button type="button" class="btn bg-gradient-primary text-white btn-sm" id="new_tp"><i class="fas fa-plus-circle"></i> New TP</button></span>
                                        </div>
                                    </div>
                                    <hr>
                                    <?= $table_traders_main ?>
                            <?php
    
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
                    DT_traders_main.ajax.reload();
                }, 50);
            });
            $('#filterAgent').on('change', function() {
                const selected = $(this).val();
                if(selected.length > 0) {
                    const customJson = (selected.includes("**")) ? '' : '{"columns":"user_extra.retention","operator":"IN","params":'+JSON.stringify(selected)+'}';
                    $('#DT_traders_main_agent').val(customJson);
                    setTimeout(function() {
                        DT_traders_main.ajax.reload();
                    }, 50);
                }
            } );
            $('#filterStatus').on('change', function () {
                const selected = $(this).val();
                if(selected.length > 0) {
                    const customJson = (selected.includes("**")) ? '' : '{"columns":"status.status","operator":"IN","params":' + JSON.stringify(selected) + '}';
                    $('#DT_traders_main_status').val(customJson);
                    setTimeout(function () {
                        DT_traders_main.ajax.reload();
                    }, 50);
                }
            } );
            $('#filterEmail').on('change', function () {
                DT_traders_main.columns(3).search( this.value ).draw();
            } );
            $('#filterPhone').on('change', function () {
                DT_traders_main.columns(5).search( this.value ).draw();
            } );
            $('#filterName').on('change', function () {
                DT_traders_main.columns(1).search( this.value ).draw();
            } );
            $('#filterSurname').on('change', function () {
                DT_traders_main.columns(2).search( this.value ).draw();
            } );
            $('#filterUnit').on('change', function () {
                DT_traders_main.columns(13).search( this.value ).draw();
            } );
            $('#filterCountry').on('change', function () {
                DT_traders_main.columns(14).search( this.value ).draw();
            } );

            $('#filterTp').on('change', function () {
                if ($(this).is(':checked')) {
                    DT_traders_main.columns(17).search('.', true, false).draw();
                } else {
                    DT_traders_main.columns(17).search('').draw();
                }
            } );

            $("body").on('click', '#traders_main .detail', function(){
                var val = $(this).attr('data-user');
                var valuser = '<?php echo htmlspecialchars($_SESSION["username"]); ?>'; 
                var url = "user-details.php?code="+val;
                $('.my-modal-cont').load(url,function(result){
                    $(".modal-title").html('Leads Details');
                    $('#myModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#myModal').modal({show:true});
                });
            });
            $("body").on('click', '#traders_main .edit', function(){
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
                var rows = DT_traders_main.rows({ 'search': 'applied' }).nodes();
                // Check/uncheck checkboxes for all rows in the table
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
            
            $('body').on( 'click', '#traders_main tr', function () {
                var rowData = DT_traders_main.row( this ).nodes();
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
        		var retention = $("#changeAgent").val();
        		var users = new Array();
                $("input:checked").each(function() {
                    users.push($(this).val());
                });
        
        		if(retention!=""){
        			$.ajax({
        				url: "edit-mretention.php",
        				type: "POST",
        				data: {
                            retention: retention,
        					users: users
        				},
        				cache: false,
        				success: function(dataResult){
        				    console.log(dataResult);
        					var dataResult = JSON.parse(dataResult);
        					if(dataResult.statusCode==200){
        					    toastr.success("Traders agents updated successfully!");
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
            
            let data = {
            	'symbols': 'EURUSD,XAUUSD,USDTRY'
            }
                ajaxCall ('quotes', 'rates',data, function(response){
                    let resObj = JSON.parse(response);
                    if (resObj.e) { // ERROR
                        toastr.error(resObj.e);
                    } else if (resObj.res) { // SUCCESS
                        $("#quotes").html( "<div class='row mt-3 mb-3'><div class='col-4 text-center pr-1'><span class='spread badge bg-gradient-lighter text-dark px-2 text-center'>"+resObj.res[0].spread+"</span><div class='bg-gradient-lighter rounded p-2'><div class='font-weight-bold text-dark mb-1'>"+resObj.res[0].symbol+"</div><div class='font-weight-bold bg-gradient-success text-white mb-1 pl-2 rounded text-left'>"+resObj.res[0].bid+"</div><div class='font-weight-bold bg-gradient-danger text-white rounded text-left pl-2'>"+resObj.res[0].ask+"</div></div></div><div class='col-4 text-center pr-1 pl-1'><span class='spread badge bg-gradient-lighter text-dark px-2 text-center'>"+resObj.res[1].spread+"</span><div class='bg-gradient-lighter rounded p-2'><div class='font-weight-bold text-dark mb-1'>"+resObj.res[1].symbol+"</div><div class='font-weight-bold bg-gradient-success text-white rounded mb-1 pl-2 text-left'>"+resObj.res[1].bid+"</div><div class='font-weight-bold bg-gradient-danger text-white rounded text-left pl-2'>"+resObj.res[1].ask+"</div></div></div><div class='col-4 text-center pl-1'><span class='spread badge bg-gradient-lighter text-dark px-2 text-center spreadlast'>"+resObj.res[2].spread+"</span><div class='bg-gradient-lighter rounded p-2'><div class='font-weight-bold text-dark mb-1'>"+resObj.res[2].symbol+"</div><div class='font-weight-bold bg-gradient-success text-white rounded mb-1 pl-2 text-left'>"+resObj.res[2].bid+"</div><div class='font-weight-bold bg-gradient-danger text-white rounded text-left pl-2'>"+resObj.res[2].ask+"</div></div></div></div>" );
                        //console.log(resObj.res);
                    }
                });
            setInterval( function () {
                ajaxCall ('quotes', 'rates',data, function(response){
                    let resObj = JSON.parse(response);
                    if (resObj.e) { // ERROR
                        toastr.error(resObj.e);
                    } else if (resObj.res) { // SUCCESS
                        $("#quotes").html( "<div class='row mt-3 mb-3'><div class='col-4 text-center pr-1'><span class='spread badge bg-gradient-lighter text-dark px-2 text-center'>"+resObj.res[0].spread+"</span><div class='bg-gradient-lighter rounded p-2'><div class='font-weight-bold text-dark mb-1'>"+resObj.res[0].symbol+"</div><div class='font-weight-bold bg-gradient-success text-white mb-1 pl-2 rounded text-left'>"+resObj.res[0].bid+"</div><div class='font-weight-bold bg-gradient-danger text-white rounded text-left pl-2'>"+resObj.res[0].ask+"</div></div></div><div class='col-4 text-center pr-1 pl-1'><span class='spread badge bg-gradient-lighter text-dark px-2 text-center'>"+resObj.res[1].spread+"</span><div class='bg-gradient-lighter rounded p-2'><div class='font-weight-bold text-dark mb-1'>"+resObj.res[1].symbol+"</div><div class='font-weight-bold bg-gradient-success text-white rounded mb-1 pl-2 text-left'>"+resObj.res[1].bid+"</div><div class='font-weight-bold bg-gradient-danger text-white rounded text-left pl-2'>"+resObj.res[1].ask+"</div></div></div><div class='col-4 text-center pl-1'><span class='spread badge bg-gradient-lighter text-dark px-2 text-center spreadlast'>"+resObj.res[2].spread+"</span><div class='bg-gradient-lighter rounded p-2'><div class='font-weight-bold text-dark mb-1'>"+resObj.res[2].symbol+"</div><div class='font-weight-bold bg-gradient-success text-white rounded mb-1 pl-2 text-left'>"+resObj.res[2].bid+"</div><div class='font-weight-bold bg-gradient-danger text-white rounded text-left pl-2'>"+resObj.res[2].ask+"</div></div></div></div>" );
                        //console.log(resObj.res);
                    }
                });
            }, 1000)
            
        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>
</html>