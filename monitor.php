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

    $query['db']        = 'DB_mt5';
    $query['table_html']     = 'margincall_main';
    $query['group_by'] = '`mt5_users`.`Login`';

    if($_SESSION["type"] == "Admin") $query['query'] = "`lidyapar_mt5`.`mt5_users` LEFT JOIN `lidyapar_mt5`.`mt5_accounts` USING(`Login`) WHERE `mt5_users`.`Group` LIKE '%real%' AND `mt5_accounts`.`MarginLevel` > 0 AND `mt5_accounts`.`MarginLevel` < 100";
    
    $query['key']       = '`mt5_users`.`Login`';
    $query['columns']   = array(
        array(
            'db' => '`mt5_users`.`Name`',
            'dt' => 0,
            'th' => 'Name',
            'having' => true
        ),
        array(
            'db' => '`mt5_users`.`Login`',
            'dt' => 1,
            'th' => 'Login',
            'having' => true
        ),
        array(
            'db' => '`mt5_accounts`.`Balance`',
            'dt' => 2,
            'th' => 'Balance',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_accounts`.`Equity`',
            'dt' => 3,
            'th' => 'Equity',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_accounts`.`MarginLevel`',
            'dt' => 4,
            'th' => 'Margin Level',
            'formatter' => true
        ),
        array(
            'db' => 'SUM(`mt5_accounts`.`Profit`+`mt5_accounts`.`Storage`)',
            'dt' => 5,
            'th' => 'Profit',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_accounts`.`Margin`',
            'dt' => 6,
            'th' => 'Used Margin',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_accounts`.`MarginFree`',
            'dt' => 7,
            'th' => 'Free Margin',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_users`.`Group`',
            'dt' => 8,
            'th' => 'Group'
        )
    );
    
    $option = "
        'order': [[4, 'asc']],
        'columnDefs': [
            {
                'targets': [ 8 ],
                'visible': false
            }
        ]
    ";
    
    $table_margincall_main = $factory::dataTableComplex(10,$query,$option);
    
    //------------------------------Devider--------------------------------//
        
    $query1['db']        = 'DB_mt5';
    $query1['table_html']     = 'accounts_main';
    $query1['group_by'] = '`mt5_users`.`Login`';
    
    if($_SESSION["type"] == "Admin") $query1['query'] = "`lidyapar_mt5`.`mt5_users` LEFT JOIN `lidyapar_mt5`.`mt5_accounts` USING(`Login`) WHERE `mt5_users`.`Group` LIKE '%real%'";

    $query1['key']       = '`mt5_users`.`Login`';
    $query1['columns']   = array(
        array(
            'db' => '`mt5_users`.`Name`',
            'dt' => 0,
            'th' => 'Name',
            'having' => true
        ),
        array(
            'db' => '`mt5_users`.`Login`',
            'dt' => 1,
            'th' => 'Login',
            'having' => true
        ),
        array(
            'db' => '`mt5_accounts`.`Balance`',
            'dt' => 2,
            'th' => 'Balance',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_accounts`.`Equity`',
            'dt' => 3,
            'th' => 'Equity',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_accounts`.`MarginLevel`',
            'dt' => 4,
            'th' => 'Margin Level',
            'formatter' => true
        ),
        array(
            'db' => 'SUM(`mt5_accounts`.`Profit`+`mt5_accounts`.`Storage`)',
            'dt' => 5,
            'th' => 'Profit',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_accounts`.`Margin`',
            'dt' => 6,
            'th' => 'Used Margin',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_accounts`.`MarginFree`',
            'dt' => 7,
            'th' => 'Free Margin',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_users`.`Group`',
            'dt' => 8,
            'th' => 'Group'
        )
    );
    
    $option1 = "
        'order': [[5, 'asc']],
        'columnDefs': [
            {
                'targets': [ 8 ],
                'visible': false
            }
        ]
    ";

    $table_accounts_main = $factory::dataTableComplex(10,$query1,$option1);


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
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card pmd-card">
            					<div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Name</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: John Smith" data-tableid="DT_margincall_main" data-col="0">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Login</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: 10000" data-tableid="DT_margincall_main" data-col="1">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Desk</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: real\\TURK" data-tableid="DT_margincall_main" data-col="8">
                                        </div>
                                    </div>
                                </div>
                    		</div>
                            <div class="card pmd-card">
            					<div class="card-body">
                                    <?php
                    					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent" ){
                                    ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span class="bold size-20">Margin Call <i class="fas fa-angle-right"></i></span>
                                                </div>
                                                <div class="col-md text-right float-right">
                                                    <small class="refPageSel">
                                                        <i class="fas fa-sync" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Table will not reload."></i>
                                                        <select id="refreshTime" name="refreshTime" class="custom-select custom-select-sm col-md-2 mx-2" data-tableid="DT_margincall_main">
                                                            <option value="0">No</option>
                                                            <option value="3">3 s</option>
                                                            <option value="15" selected="">15 s</option>
                                                            <option value="60">1 M</option>
                                                            <option value="180">3 M</option>
                                                            <option value="300">5 M</option>
                                                            <option value="600">10 M</option>
                                                            <option value="900">15 M</option>
                                                        </select>
                                                        <span></span>
                                                    </small>
                                                    <i id="do-reload" data-tableid="DT_margincall_main" class="fa fa-spinner alert-success primary rounded-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Force reload."></i>
                                                </div>
                                            </div>
                                            <hr>
                                            <?= $table_margincall_main ?>
                                    <?php
            
                    					}
                    				?>
                    			</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="card pmd-card">
            					<div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Name</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: John Smith" data-tableid="DT_accounts_main" data-col="0">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Login</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: 10000" data-tableid="DT_accounts_main" data-col="1">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Desk</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: real\\TURK" data-tableid="DT_accounts_main" data-col="8">
                                        </div>
                                    </div>
                                </div>
                    		</div>
                            <div class="card pmd-card">
            					<div class="card-body">
                                    <?php
                    					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent" ){
                                    ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span class="bold size-20">Accounts <i class="fas fa-angle-right"></i></span>
                                                </div>
                                                <div class="col-md text-right float-right">
                                                    <small class="refPageSel">
                                                        <i class="fas fa-sync" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Table will not reload."></i>
                                                        <select id="refreshTime" name="refreshTime" class="custom-select custom-select-sm col-md-2 mx-2" data-tableid="DT_accounts_main">
                                                            <option value="0">No</option>
                                                            <option value="3">3 s</option>
                                                            <option value="15" selected="">15 s</option>
                                                            <option value="60">1 M</option>
                                                            <option value="180">3 M</option>
                                                            <option value="300">5 M</option>
                                                            <option value="600">10 M</option>
                                                            <option value="900">15 M</option>
                                                        </select>
                                                        <span></span>
                                                    </small>
                                                    <i id="do-reload" data-tableid="DT_accounts_main" class="fa fa-spinner alert-success primary rounded-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Force reload."></i>
                                                </div>
                                            </div>
                                            <hr>
                                            <?= $table_accounts_main ?>
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