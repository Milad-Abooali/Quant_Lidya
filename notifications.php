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

    global $notify;
    $notifications = $notify->getAllNotify($_SESSION["id"]);
    
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
                            <table id="data-table" class="table table-hover" style="width:100%">
                                <thead> 
                                    <tr>
                                        <th>#</th>
                                        <th>From (source)</th>
                                        <th>Type</th>
                                        <th>Text</th>
                                        <th>Seen</th>
                                        <th>Time</th>
                                    </tr>  
                                </thead>
                                <tbody>
                                    <?php if($notifications) foreach($notifications as $item) { ?>
                                    <tr>
                                        <td><?= $item['id'] ?></td>
                                        <td><?= $item['source'] ?></td>
                                        <td><?= $item['type'] ?></td>
                                        <td><?= sprintf($item['notify_text'],$item['notify_data']); ?></td>
                                        <td><?= ($item['seen_time'] != '0000-00-00 00:00:00') ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-ban text-muted"></i>'; ?></td>
                                        <td><?= $item['create_time'] ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
        	var notifies = $('#data-table').DataTable({ 
        		"responsive": true,
            });
            

        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>

</html>
<?php
    $DB_admin->close();
?>