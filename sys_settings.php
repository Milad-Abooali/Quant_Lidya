<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    include('includes/head.php');

    if ($_SESSION["type"]!=='Admin') {
        header("location: login.php");
        die();
    }
    $href = ($_GET['section']) ?? 'settings_system';
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
                                <h4 class="page-title">Settings <small class="text-info"><?= $parent ?></small></h4>
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
                                        <a class="nav-link" data-toggle="tab" href="#tab-system">System</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-brokers">Brokers</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-gateways">Gateways</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-financial">Financial</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-languages">Languages</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-users">Users</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-ibs">IBs</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-permissions">Permissions</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-waf">WAF</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-email">Emails</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-database">Database</a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">

                                  <div class="tab-pane pt-3 fade" id="tab-system">
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="nav flex-column nav-pills vtab">
                                                <a class="nav-link" data-toggle="pill" href="system_actlog">User ActLog</a>
                                                <a class="nav-link" data-toggle="pill" href="system_actlog-guest">Guest ActLog</a>
                                                <hr>
                                                <a class="nav-link" data-toggle="pill" href="system_jobs">cJobs Manager</a>
                                                <a class="nav-link" data-toggle="pill" href="system_joblogs">cJob Logs</a>

                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <?php if($parent=='system'): ?>
                                                <div class="tab-content">
                                                    <?php if (file_exists($section_file)) include($section_file); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                  </div>

                                    <div class="tab-pane pt-3" id="tab-database">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">
                                                    <a class="nav-link" data-toggle="pill" href="database_tools">Database Tools</a>
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <?php if($parent=='database') { ?>
                                                    <div class="tab-content">
                                                        <?php if (file_exists($section_file)) include($section_file); ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane pt-3" id="tab-brokers">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">
                                                    <a class="nav-link" data-toggle="pill" href="brokers_list">List Brokers</a>
                                                    <a class="nav-link" data-toggle="pill" href="brokers_new">Add New Broker</a>
                                                    <a class="nav-link" data-toggle="pill" href="brokers_units">Manage Units</a>
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <?php if($parent=='brokers') { ?>
                                                    <div class="tab-content">
                                                        <?php if (file_exists($section_file)) include($section_file); ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane pt-3 fade" id="tab-waf">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">
                                                    <h6>Login IP</h6>
                                                    <a class="nav-link" data-toggle="pill" href="waf_ip-db">IP Database</a>
                                                    <a class="nav-link" data-toggle="pill" href="waf_login-ip">Login IP</a>
                                                    <h6>Sessions</h6>
                                                        <a class="nav-link" data-toggle="pill" href="waf_session-active">Active Sessions</a>
                                                        <a class="nav-link" data-toggle="pill" href="waf_session-user">Sessions by User</a>
                                                        <a class="nav-link" data-toggle="pill" href="waf_session-ip">Sessions by IP</a>
                                                        <a class="nav-link" data-toggle="pill" href="waf_session-archive">Archive</a>
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <?php if($parent=='waf'): ?>
                                                    <div class="tab-content">
                                                        <?php if (file_exists($section_file)) include($section_file); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                  <div class="tab-pane pt-3" id="tab-users">
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="nav flex-column nav-pills vtab">
                                                <a class="nav-link" data-toggle="pill" href="users_profile-completion" data-page="test">Profile Completion</a>
                                                <a class="nav-link" data-toggle="pill" href="users_merge" data-page="test">User Merge</a>
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <?php if($parent=='users'): ?>
                                                <div class="tab-content">
                                                    <?php if (file_exists($section_file)) include($section_file); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                  </div>


                                    <div class="tab-pane pt-3" id="tab-ibs">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">
                                                    <a class="nav-link" data-toggle="pill" href="ibs_new-group">New Groups</a>
                                                    <a class="nav-link" data-toggle="pill" href="ibs_groups-list">Groups List</a>
                                                    <a class="nav-link" data-toggle="pill" href="ibs_group-members">Groups Members</a>
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <?php if($parent=='ibs'): ?>
                                                    <div class="tab-content">
                                                        <?php if (file_exists($section_file)) include($section_file); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="tab-pane pt-3" id="tab-permissions">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">
                                                    <a class="nav-link" data-toggle="pill" href="permissions_groups" data-page="test">Manage Groups</a>
                                                    <a class="nav-link" data-toggle="pill" href="permissions_groups-users">Groups users</a>
                                                    <a class="nav-link" data-toggle="pill" href="permissions_paths">Paths</a>
                                                    <hr>
                                                    <a class="nav-link" data-toggle="pill" href="permissions_path-perms">Path Perms</a>
                                                    <a class="nav-link" data-toggle="pill" href="permissions_groups-perms">Group Perms</a>
                                                    <a class="nav-link" data-toggle="pill" href="permissions_users-perms">Users Perms</a>
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <?php if($parent=='permissions'): ?>
                                                    <div class="tab-content">
                                                        <?php if (file_exists($section_file)) include($section_file); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane pt-3" id="tab-email">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">
                                                    <a class="nav-link" data-toggle="pill" href="email_logs">Logs</a>
                                                    <a class="nav-link" data-toggle="pill" href="email_new-theme">Creat New Theme</a>
                                                    <a class="nav-link" data-toggle="pill" href="email_theme-editor">Theme Editor</a>
                                                    <a class="nav-link" data-toggle="pill" href="email_mass-mail">Mass Mail</a>
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


                                    <div class="tab-pane pt-3" id="tab-gateways">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">

                                                <?php
                                                    $gateways = scandir('./gateways');
                                                    unset($gateways[0]);
                                                    unset($gateways[1]);
                                                    foreach ($gateways as $gw) {
                                                        include_once ('./gateways/'.$gw.'/config.php')
                                                ?>
                                                        <h6><?= $gw ?></h6>

                                                        <?php foreach ($menu as $link => $title) { ?>
                                                            <a class="nav-link" data-toggle="pill" href="gateways_<?= $gw ?>_<?= $link ?>"><?= ucwords($title) ?></a>
                                                        <?php } ?>


                                                    <?php } ?>

                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <?php if($parent=='gateways'): ?>
                                                    <div class="tab-content">

                                                        <?php


                                                            $gateway = explode("_",$href)[1];
                                                            $section = explode("_",$href)[2];

                                                            global $db;
                                                            $where = "path='$gateway'";
                                                            $gw_id = $db->selectRow('payment_gateways',$where)['id'];

                                                            $section_file = "./gateways/$gateway/$section.php";
                                                            if (file_exists($section_file)) include($section_file);

                                                        ?>

                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="tab-pane pt-3 fade" id="tab-financial">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">
                                                    <h6>Wallet</h6>
                                                    <a class="nav-link" data-toggle="pill" href="financial_wallet-options">Options</a>
                                                    <a class="nav-link" data-toggle="pill" href="financial_wallet-list">List</a>
                                                    <a class="nav-link" data-toggle="pill" href="financial_wallet-transactions-history">Transactions History</a>
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <?php if($parent=='financial'): ?>
                                                    <div class="tab-content">
                                                        <?php if (file_exists($section_file)) include($section_file); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane pt-3" id="tab-languages">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="nav flex-column nav-pills vtab">
                                                    <a class="nav-link" data-toggle="pill" href="languages_update-tool" data-page="test">Update Tool</a>
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <?php if($parent=='languages'): ?>
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
                window.location.href = 'sys_settings.php?section='+$(this).attr('href');
            });

        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>

</html>