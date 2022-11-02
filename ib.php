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
    
    //Load Leads//
    if($_SESSION["type"] == "IB"){
        $sql = 'SELECT users.id, users.username, users.email, user_extra.fname, user_extra.lname, user_extra.phone, user_extra.conversion, user_extra.status FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE user_extra.type = 3 AND users.id = "'.$_SESSION["id"].'"';
    } else if($_SESSION["type"] == "Manager") {
        $sql = 'SELECT users.id, users.username, users.email, user_extra.fname, user_extra.lname, user_extra.phone, user_extra.conversion, user_extra.status FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE user_extra.type = 3 AND users.unit = "'.$_SESSION["unit"].'"';
        echo $sql;
    } else {
        $sql = 'SELECT users.id, users.username, users.email, user_extra.fname, user_extra.lname, user_extra.phone, user_extra.conversion, user_extra.status FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE user_extra.type = 3';
    }
    //echo $sql;
    $result = $DB_admin->query($sql);

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
                    <?php
            		    if($_SESSION["type"] !== "IB"){
                    ?>
                    <div class="card pmd-card">
    					<div class="card-body">
            			    <form action="" method="post" autocomplete="off">
                            	<div class="form-row">
                            		<div class="form-group col-md-4">
                            			<label for="inputAgent">Agent</label>
                            			<select class="form-control" id="filterAgent" name="filterAgent" required>
                            			    <option value="">All</option>
                                            <option value="alpt">alpt</option>
                                            <option value="Admin">Admin</option>
                                            <option value="Dilan">Dilan</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                            			<label for="inputStatus">Status</label>
                            			<select class="form-control" id="filterStatus" name="filterStatus" required>
                            			    <option value="">All</option>
                                            <option value="FTD">FTD</option>
                                            <option value="Retention">Retention</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                            			<label for="inputEmail">Email</label>
                            			<input class="form-control" id="filterEmail" name="filterEmail" placeholder="Email Address" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                            			<label for="inputName">Name</label>
                            			<input class="form-control" id="filterName" name="filterName" placeholder="Name" required>
                                    </div>
                                    <div class="form-group col-md-4">
                            			<label for="inputSurname">Surname</label>
                            			<input class="form-control" id="filterSurname" name="filterSurname" placeholder="Surname" required>
                                    </div>
                                    <div class="form-group col-md-4">
                            			<label for="inputPhone">Phone</label>
                            			<input class="form-control" id="filterPhone" name="filterPhone" placeholder="Phone" required>
                                    </div>
                    			</div>
                            </form>
                        </div>
            		</div>
            		<?php 
            		    } 
            		?>
                    <div class="card pmd-card">
    					<div class="card-body">
                            <?php
            					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "IB" ){
                            ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span class="bold size-20">IB <i class="fas fa-angle-right"></i></span>
                                        </div>
                                        <?php
                                		    if($_SESSION["type"] !== "IB"){
                                        ?>
                                        <div class="col-md-6">
                                            <span class="bold size-20 float-right"><button type="button" class="btn bg-gradient-primary text-white btn-sm" id="new_tp"><i class="fas fa-plus-circle"></i> New TP</button></span>
                                        </div>
                                        <?php
                                		    }
                                		?>
                                    </div>
                                    <hr>
                                    <table id="data-table" class="table table-hover" style="width:100%">  
                                        <thead> 
                                            <tr>
                                                <th>Username</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Phone</th>
                                                <th>Agent</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>  
                    					</thead>
                    					<tbody>
                    					    <?php
                    					        if ($result->num_rows > 0) {
                                                    while($row = $result->fetch_assoc()) {
                                            ?>
                        					    <tr>
                        					        <td><?php echo $row['username']; ?></td>
                        					        <td><?php echo $row['fname']; ?></td>
                        					        <td><?php echo $row['lname']; ?></td>
                        					        <td><?php echo $row['phone']; ?></td>
                        					        <?php
                                                        $conv_sql = 'SELECT users.username FROM users WHERE users.id = "'.$row['conversion'].'"';
                                                        $conv = $DB_admin->query($conv_sql);
                                                        if ($conv->num_rows > 0) {
                                                            while($convrow = $conv->fetch_assoc()) {
                                                                $usename_='';
                                                                $usename_=$convrow['username'];;
                                                            }
                            					        }
                                					?>
                                                    <td><?php echo ($usename_) ? $usename : '-' ?></td>
                                                    <?php
                                                        $status_sql = 'SELECT status FROM status WHERE status.id = "'.$row['status'].'" AND cat = "Trader"';
                                                        $status = $DB_admin->query($status_sql);
                                                        if ($status->num_rows > 0) {
                                                        while($statusrow = $status->fetch_assoc()) {
                        					        ?>
                        					        <td><?php echo $statusrow['status']; ?></td>
                        					        <?php
                                                            }
                            					            
                            					        } else {
                            					    ?><td>-</td><?php
                            					        }
                                					?>
                        					        <td class="text-right">
                        					            <a href="javascript:;" class="btn bg-gradient-info text-white btn-sm detail border-0" data-user="<?php echo $row['id']; ?>"><i class="fas fa-user-check"></i> Details</a>
                        					            <?php
                                                		    if($_SESSION["type"] !== "IB"){
                                                        ?>
                                                            <a href="javascript:;" class="btn bg-gradient-danger text-white btn-sm edit border-0" data-user="<?php echo $row['id']; ?>"><i class="fas fa-user-edit"></i> Edit</a>
                                                        <?php
                                                		    }
                                                		?>
                        					        </td>
                        					    </tr>
                        					<?php
                                                    }
                    					            
                    					        }
                        					?>
                    					</tbody>
                                    </table>
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
        	var leads = $('#data-table').DataTable({ 
        		"responsive": true,
        		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
            });
            
            $('#filterAgent').on('change', function () {
                leads.columns(4).search( this.value ).draw();
            } );
            $('#filterStatus').on('change', function () {
                leads.columns(5).search( this.value ).draw();
            } );
            $('#filterEmail').on('change', function () {
                leads.columns(0).search( this.value ).draw();
            } );
            $('#filterPhone').on('change', function () {
                leads.columns(3).search( this.value ).draw();
            } );
            $('#filterName').on('change', function () {
                leads.columns(1).search( this.value ).draw();
            } );
            $('#filterSurname').on('change', function () {
                leads.columns(2).search( this.value ).draw();
            } );
            
            $("#data-table").on('click', '.detail', function(){
                var val = $(this).attr('data-user');
                var valuser = '<?php echo htmlspecialchars($_SESSION["username"]); ?>'; 
                var url = "user-details.php?code="+val+"&type=ib";
                $('.my-modal-cont').load(url,function(result){
                    $(".modal-title").html('Leads Details');
                    $('#myModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#myModal').modal({show:true});
                });
            });
            $("#data-table").on('click', '.edit', function(){
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
        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>

</html>
<?php
    $DB_admin->close();
?>