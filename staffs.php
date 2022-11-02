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

    if($_SESSION["type"] == "Admin"){
        $sql = 'SELECT users.id, users.username, users.email, user_extra.fname, user_extra.lname, user_extra.phone, user_extra.conversion, user_extra.type, user_extra.unit, user_extra.status FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE user_extra.type > 3';
    } else {
        $sql = 'SELECT users.id, users.username, users.email, user_extra.fname, user_extra.lname, user_extra.phone, user_extra.conversion, user_extra.type, user_extra.unit, user_extra.status FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE user_extra.type > 3 AND user_extra.type != 8 AND users.unit = "'.$_SESSION["unit"].'"';
    }
    //Load Leads//
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
                    <div class="card pmd-card">
        				<div class="card-body">
        				    <h5 class="card-title">Filter</h5>
            			    <form action="" method="post" autocomplete="off">
                            	<div class="form-row">
                            		<div class="form-group col-md-4">
                            			<label for="inputAgent">Manager</label>
                            			<select class="form-control" id="filterAgent" name="filterAgent" required>
                            			    <option value="">All</option>
                                            <option value="alpt">alpt</option>
                                            <option value="Admin">Admin</option>
                                            <option value="Dilan">Dilan</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                            			<label for="inputStatus">Type</label>
                            			<select class="form-control" id="filterStatus" name="filterStatus" required>
                            			    <option value="">All</option>
                                            <option value="Sales Agent">Sales Agent</option>
                                            <option value="Retention Agent">Retention Agent</option>
                                            <option value="Desk Manager">Desk Manager</option>
                                            <option value="Country Manager">Country Manager</option>
                                            <option value="Sales Manager">Sales Manager</option>
                                            <option value="Retention Manager">Retention Manager</option>
                                            <option value="Backoffice">Backoffice</option>
                                            <option value="Dealing">Dealing</option>
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
                    <div class="card pmd-card">
        				<div class="card-body">
                            <?php
            					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){
                            ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span class="bold size-20">Staff <i class="fas fa-angle-right"></i></span>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="bold size-20 float-right">
                                                <button type="button" class="btn btn-primary btn-sm" id="new_lead"><i class="fas fa-plus-circle"></i> New Staff</button>
                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    <table id="data-table" class="table table-hover" style="width:100%">  
                                        <thead> 
                                            <tr>
                                                <th></th>
                                                <th>Username</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Agent</th>
                                                <th>Type</th>
                                                <th>Unit</th>
                                                <th>Action</th>
                                            </tr>  
                    					</thead>
                    					<tbody>
                    					    <?php
                    					        $i = 0;
                    					        if ($result->num_rows > 0) {
                                                    while($row = $result->fetch_assoc()) {
                                            ?>
                        					    <tr <?php if($row['status'] == 9){ echo 'class="table-secondary"'; } ?>>
                        					        <td>
                        					            <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="users[<?php echo $i; ?>]" name="users[<?php echo $i; ?>]" value="<?php echo $row["id"]; ?>">
                                                            <label class="custom-control-label" for="users[<?php echo $i; ?>]"> </label>
                                                        </div>
                        					        </td>
                        					        <td><?php echo $row['username']; ?></td>
                        					        <td><?php echo $row['fname']; ?></td>
                        					        <td><?php echo $row['lname']; ?></td>
                        					        <td><?php echo $row['email']; ?></td>
                        					        <td><?php echo $row['phone']; ?></td>
                        					        <?php
                                                        $conv_sql = 'SELECT users.username FROM users WHERE users.id = "'.$row['conversion'].'"';
                                                        $conv = $DB_admin->query($conv_sql);
                                                        if ($conv->num_rows > 0) {
                                                            while($convrow = $conv->fetch_assoc()) {
                                                                $username_='';
                                                                $username_=$convrow['username'];
                                                            }
                                                        }
                                					?>
                                                    <td><?php echo ($username_) ? $username_ : '-' ?></td>
                                                    <?php
                                                        $type_sql = 'SELECT name FROM type WHERE type.id = "'.$row['type'].'"';
                                                        $type = $DB_admin->query($type_sql);
                                                        if ($type->num_rows > 0) {
                                                        while($typerow = $type->fetch_assoc()) {
                        					        ?>
                        					        <td><?php echo $typerow['name']; ?></td>
                        					        <?php
                                                            }
                            					            
                            					        }
                                					?>
                                					<?php
                                                        $unit_sql = 'SELECT name FROM units WHERE units.id = "'.$row['unit'].'"';
                                                        $unit = $DB_admin->query($unit_sql);
                                                        if ($unit->num_rows > 0) {
                                                        while($unitrow = $unit->fetch_assoc()) {
                        					        ?>
                        					        <td><?php echo $unitrow['name']; ?></td>
                        					        <?php
                                                            }
                            					            
                            					        }
                                					?>
                        					        <td class="text-center">
                        					            <a href="javascript:;" class="detail" data-user="<?php echo $row['id']; ?>"><i class="fas fa-user-check"></i></a>
                        					            <a href="javascript:;" class="edit" data-user="<?php echo $row['id']; ?>"><i class="fas fa-user-edit"></i></a>
                        					        </td>
                        					    </tr>
                        					<?php
                    					            $i++;
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
        		"order": [[ 1, "desc" ]],
            });
            
            $('#filterAgent').on('change', function () {
                leads.columns(6).search( this.value ).draw();
            } );
            $('#filterStatus').on('change', function () {
                leads.columns(7).search( '"'+this.value+'"' ).draw();
            } );
            $('#filterEmail').on('change', function () {
                leads.columns(4).search( this.value ).draw();
            } );
            $('#filterPhone').on('change', function () {
                leads.columns(5).search( this.value ).draw();
            } );
            $('#filterName').on('change', function () {
                leads.columns(2).search( this.value ).draw();
            } );
            $('#filterSurname').on('change', function () {
                leads.columns(3).search( this.value ).draw();
            } );
            
            $("#data-table").on('click', '.detail', function(){
                var val = $(this).attr('data-user');
                var valuser = '<?php echo htmlspecialchars($_SESSION["username"]); ?>'; 
                var url = "user-details.php?code="+val;
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
                    $('#myModal .modal-footer').html('<button type="button" class="btn btn-primary" id="saveUser" disabled>Save</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#myModal').modal({show:true});
                });
            });
            
            $("#new_lead").click(function() {
                var valuser3 = '<?php echo htmlspecialchars($_SESSION["id"]); ?>'; 
                var url = "new_lead.php?code="+valuser3+"&page=staff";
                $('.my-modal-cont').load(url,function(result){
                    $(".modal-title").html('Add A New Lead');
                    $('#myModal .modal-footer').html('<button type="submit" class="btn btn-primary" id="addUser" disabled>Add</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#myModal').modal({show:true});
                });
            });
            
        
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
        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>

</html>
<?php
    $DB_admin->close();
?>