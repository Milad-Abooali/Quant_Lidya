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
    $query['table_html']     = 'notes_main';

    if($_SESSION["type"] == "Admin") $query['query'] = "`notes` WHERE 1";
    if($_SESSION["type"] == "Manager") $query['query'] = "`notes` LEFT JOIN `users` ON `notes`.`user_id`=`users`.`id` WHERE `users`.`unit`='".$_SESSION['unit']."'";
    if($_SESSION["type"] == "Sales Agent" || $_SESSION["type"] == "Retention Agent") $query['query'] = "`notes` WHERE updated_by=".$_SESSION['id'];

    $query['key']       = '`notes`.`id`';
    $query['columns']   = array(
        array(
            'db' => '`notes`.`note`',
            'dt' => 0,
            'th' => 'Note'
        ),
        array(
            'db' => '`notes`.`note_type`',
            'dt' => 1,
            'th' => 'Type'
        ),
        array(
            'db' => '(SELECT username from users WHERE id=`notes`.`user_id`)',
            'dt' => 2,
            'th' => 'Client'
        ),
        array(
            'db' => '(SELECT username from users WHERE id=`notes`.`created_by`)',
            'dt' => 3,
            'th' => 'Created By'
        ),
        array(
            'db' => '`notes`.`created_at`',
            'dt' => 4,
            'th' => 'Created At'
        ),
        array(
            'db' => '(SELECT username from users WHERE id=`notes`.`updated_by`)',
            'dt' => 5,
            'th' => 'Updated By'
        ),
        array(
            'db' => '`notes`.`updated_at`',
            'dt' => 6,
            'th' => 'Updated At'
        )
    );
    $option = "
                    'order': [[4, 'desc']]
        ";

    $table_notes_main = $factory::dataTableComplex(100,$query,$option);


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
                            		<div class="form-group col-md-4">
                            			<label for="inputNote">Note</label>
                                        <input type="search" class="form-control filterDT" placeholder="Note" data-tableid="DT_notes_main" data-col="0">

                                    </div>
                                    <div class="form-group col-md-4">
                            			<label for="inputType">Type</label>
                            			<select class="form-control" id="filterType" name="filterType" required>
                            			    <option value="">All</option>
                                            <option value="Light Scalper">Light Scalper</option>
                            				<option value="Heavy Scalper">Heavy Scalper</option>
                            				<option value="Latency User">Latency User</option>
                            				<option value="A-Book Potential">A-Book Potential</option>
                            				<option value="On Watch List">On Watch List</option>
                            				<option value="Black Listed">Black Listed</option>
                            				<option value="Busy – Call Back Later">Busy – Call Back Later</option>
                            				<option value="Interested – Call Back Later">Interested – Call Back Later</option>
                            				<option value="Interested – Send Information">Interested – Send Information</option>
                            				<option value="No Interest – Reason Given">No Interest – Reason Given</option>
                            				<option value="Incorect Information">Incorect Information</option>
                            				<option value="Follow-Up Scheduled">Follow-Up Scheduled</option>
                            				<option value="Fully Interested">Fully Interested</option>
                            				<option value="Whatsapp">Whatsapp</option>
                            				<option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                            			<label for="inputCreatedBy">Created By</label>
                                            <input type="search" class="form-control filterDT" placeholder="Created By" data-tableid="DT_notes_main" data-col="3">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                            			<label for="inputCreatedAt">Created At</label>
                                        <input type="search" class="form-control date filterDT" placeholder="1970-01-00" data-tableid="DT_notes_main" data-col="4">
                                    </div>
                                    <div class="form-group col-md-4">
                            			<label for="inputUpdatedBy">Updated By</label>
                                        <input type="search" class="form-control filterDT" placeholder="Updated By" data-tableid="DT_notes_main" data-col="5">
                                    </div>
                                    <div class="form-group col-md-4">
                            			<label for="inputUpdatedAt">UpdatedAt</label>
                                        <input type="search" class="form-control date filterDT" placeholder="1970-01-00" data-tableid="DT_notes_main" data-col="6">
                                    </div>
                    			</div>
                            </form>
                        </div>
            		</div>
                    <div class="card pmd-card">
    					<div class="card-body">
                            <?php
            					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent" ){
                            ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span class="bold size-20">Notes <i class="fas fa-angle-right"></i></span>
                                        </div>
                                    </div>
                                    <hr>
                                    <?= $table_notes_main ?>
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

            $('#filterNote').on('change', function () {
                DT_notes_main.columns(0).search( this.value ).draw();
            } );
            $('#filterType').on('change', function () {
                DT_notes_main.columns(1).search( this.value ).draw();
            } );
            $('#filterCreatedBy').on('change', function () {
                DT_notes_main.columns(2).search( this.value ).draw();
            } );
            $('#filterCreatedAt').on('change', function () {
                DT_notes_main.columns(3).search( this.value ).draw();
            } );
            $('#filterUpdatedBy').on('change', function () {
                DT_notes_main.columns(4).search( this.value ).draw();
            } );
            $('#filterUpdatedAt').on('change', function () {
                DT_notes_main.columns(5).search( this.value ).draw();
            } );
        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>
</html>