<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
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
    $query['table_html']     = 'exposure_main';
    $query['group_by'] = '`mt5_positions`.`Symbol`';

if ($_SESSION["type"] == "Admin") $query['query'] = "`lidyapar_mt5`.`mt5_positions` LEFT JOIN `lidyapar_mt5`.`mt5_users` ON `mt5_users`.`Login` = `mt5_positions`.`Login` WHERE `mt5_users`.`Group` LIKE '%real%4%'";

    $query['key']       = '`mt5_positions`.`Profit`';
    $query['columns']   = array(
        array(
            'db' => '`mt5_positions`.`Symbol`',
            'dt' => 0,
            'th' => 'Symbol',
            'having' => true
        ),
        array(
            'db' => 'Sum(IF(`mt5_positions`.`Action`= 0, (`mt5_positions`.`Volume`)/10000, 0))',
            'dt' => 1,
            'th' => 'Buy Volume',
            'formatter' => true
        ),
        array(
            'db' => 'Sum(IF(`mt5_positions`.`Action`= 0, (`mt5_positions`.`PriceOpen` * ((`mt5_positions`.`Volume`)/10000)), 0))',
            'dt' => 2,
            'th' => 'Buy AVG',
            'formatter' => true
        ),
        array(
            'db' => 'Sum(IF(`mt5_positions`.`Action`= 1, (`mt5_positions`.`Volume`)/10000, 0))',
            'dt' => 3,
            'th' => 'Sell Volume',
            'formatter' => true
        ),
        array(
            'db' => 'Sum(IF(`mt5_positions`.`Action`= 1, (`mt5_positions`.`PriceOpen` * ((`mt5_positions`.`Volume`)/10000)), 0))',
            'dt' => 4,
            'th' => 'Sell AVG',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_positions`.`Volume`',
            'dt' => 5,
            'th' => 'Net Volume',
            'formatter' => true
        ),
        array(
            'db' => 'SUM(`mt5_positions`.`Profit`+`mt5_positions`.`Storage`)',
            'dt' => 6,
            'th' => 'Profit',
            'formatter' => true
        ),
        array(
            'db' => '`mt5_positions`.`PriceCurrent`',
            'dt' => 7,
            'th' => 'Current Price',
            'formatter' => true
        )
    );
    
    $option = "
        'order': [[5, 'desc']],
        'dom': 'tl'
    ";

    $table_exposure_main = $factory::dataTableComplex(100,$query,$option);


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
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Symbol</label>
                                    <input type="search" class="form-control filterDT" placeholder="Symbol Search ex: XAUUSD" data-tableid="DT_exposure_main" data-col="0">
                                </div>
                                <div class="col-md text-right float-right">
                                    <label for="refreshTime">Reload</label><br>
                                    <small class="refPageSel">
                                        <i class="fas fa-sync" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Table will not reload."></i>
                                        <select id="refreshTime" name="refreshTime" class="custom-select custom-select-sm col-md-2 mx-2" data-tableid="DT_exposure_main">
                                            <option value="0" selected="">No</option>
                                            <option value="3">3 s</option>
                                            <option value="15">15 s</option>
                                            <option value="60">1 M</option>
                                            <option value="180">3 M</option>
                                            <option value="300">5 M</option>
                                            <option value="600">10 M</option>
                                            <option value="900">15 M</option>
                                        </select>
                                        <span></span>
                                    </small>
                                    <i id="do-reload" data-tableid="DT_exposure_main" class="fa fa-spinner alert-success primary rounded-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Force reload."></i>
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
                                            <span class="bold size-20">Exposure <i class="fas fa-angle-right"></i></span>
                                        </div>
                                    </div>
                                    <?= $table_exposure_main ?>
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
<!-- Modal Window content -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 1400;">
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