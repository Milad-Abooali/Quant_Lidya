<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    include('includes/head.php');

    if ( !in_array($_SESSION["type"],['Admin','Manager']) ) {
        header("location: login.php");
        die();
    }
    $href = ($_GET['section']) ?? 'panel_dashboard';
    $parent = explode("_",$href)[0];
    $section = explode("_",$href)[1];
    $section_file = "includes/settings/$parent/$section.php";
    GF::loadJS('f', "assets/js/settings/$parent.js");

?>

    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">

<?php include('includes/css.php'); ?>

<body>

    <!-- Begin page -->
    <div id="wrapper">

<?php 
    include('includes/topbar.php');
    include('includes/sidebar.php');
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
                                <h4 class="page-title">Manager <small class="text-info"><?= $parent ?></small></h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?php echo Broker['title']; ?>
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
    					<div id="setting-cats" class="card-body">

                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-email">Emails</a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">

                                    <div class="tab-pane pt-3" id="tab-email">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">
                                                    <a class="nav-link" data-toggle="pill" href="email_logs">Logs</a>
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <?php if($parent=='email'): ?>
                                                <div class="tab-content">
                                                    <?php if (file_exists($section_file)) include($section_file); ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
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
<!-- Modal Window content -->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 1400;">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="my-modal-cont"></div>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>
<?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>

        var section = '<?= $href ?>';
        var securl = 'sys_settings.php?section='+section;

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

            /**
             * Dynamic Sections
             */
            $('a[href="#tab-<?= $parent ?>"]').tab('show');
            $('a[href="<?= $href ?>"]').tab('show');

            $(".vtab a.nav-link").click(function(e){
                e.preventDefault();
                window.location.href = 'manager_panel.php?section='+$(this).attr('href');
            });

        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>

</html>