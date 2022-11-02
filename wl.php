<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
#  A | 10:23 AM Wednesday, July 21, 2021
#          Add Filter Section
#          Add Group Column
#          Fix Date Range
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

    if (isset($_GET['startTime']) )
    {
        $startTime = $_GET['startTime'];
        $endTime = $_GET['endTime'];
        $endTime = date("Y-m-d H:i:s", strtotime($endTime) - 1);
    } else {
        $startTime = date('Y-m-01');
        $endTime = date('Y-m-t');
    }
    
    $query['db']        = 'DB_admin';
    $query['table_html']     = 'win_main';
    $query['group_by'] = 'Deals.Login';
    //SELECT Users.Name, Deals.Login, (SUM(IF(Deals.Profit < 0, 1, 0))/Count(Deal))*100 as WinningRate, SUM(IF(Deals.Profit >= 0, 1, 0)) as WinningOrders, Count(Deal) as TotalOrders, SUM(Deals.Profit+Deals.Storage) as PNL, Users.Group FROM `mt5_deals` as Deals LEFT JOIN mt5_users as Users ON Deals.Login = Users.Login WHERE Time BETWEEN "2021-01-01 00:00:00" AND "2021-04-30 00:00:00" AND Deals.Action < 2 AND Deals.Entry IN (1,3) AND Users.Group LIKE "%real%" GROUP BY Login
    if($_SESSION["type"] == "Admin") $query['query'] = '`lidyapar_mt5`.`mt5_deals` as Deals LEFT JOIN `lidyapar_mt5`.`mt5_users` as Users ON Deals.Login = Users.Login LEFT JOIN `lidyapar_mt5`.`mt5_accounts` as Accounts ON Deals.Login = Accounts.Login WHERE Time BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND Deals.Action <= "1" AND Deals.Entry IN (1,3) AND Users.Group LIKE "real%"';

    $query['key']       = 'Deals.Deal';
    $query['columns']   = array(
        array(
            'db' => 'Users.Name',
            'dt' => 0,
            'th' => 'Name'
        ),
        array(
            'db' => 'Deals.Login',
            'dt' => 1,
            'th' => 'Login'
        ),
        array(
            'db' => '(SUM(IF(Deals.Profit >= 0, 1, 0))/Count(Deal))*100',
            'dt' => 2,
            'th' => 'Winning Rate',
            'formatter' => true
        ),
        array(
            'db' => 'Count(Deal)',
            'dt' => 3,
            'th' => 'Total Orders',
            'formatter' => true
        ),
        array(
            'db' => 'SUM(Deals.Profit+Deals.Storage)',
            'dt' => 4,
            'th' => 'PNL',
            'formatter' => true
        ),
        array(
            'db' => 'Accounts.Balance',
            'dt' => 5,
            'th' => 'Balance',
            'formatter' => true
        ),
        array(
            'db' => 'Accounts.Equity',
            'dt' => 6,
            'th' => 'Equity',
            'formatter' => true
        ),
        array(
            'db' => 'Accounts.Equity-(Accounts.Balance+Accounts.Credit)',
            'dt' => 7,
            'th' => 'Open Positions',
            'formatter' => true
        ),
        array(
            'db' => 'Accounts.Credit',
            'dt' => 8,
            'th' => 'Credit'
        ),
        array(
            'db' => 'Users.Group',
            'dt' => 9,
            'th' => 'Group'
        )
    );
    
    $option = "
        'order': [[4, 'desc']],
        'columnDefs': [
            {
                'targets': [ 8 ],
                'visible': false
            },
            {
                'targets': [ 9 ],
                'visible': false
            }
        ],
        'lengthMenu': [ [5, 10, 25, 50, 100, 200, 300, 400, 500, -1], [5, 10, 25, 50, 100, 200, 300, 400, 500, 'All'] ],
    ";

    $table_win_main = $factory::dataTableComplex(10,$query,$option);
    
    //------------------------------Devider--------------------------------//
    
    $query1['db']        = 'DB_admin';
    $query1['table_html']     = 'loss_main';
    $query1['group_by'] = 'Deals.Login';
    //SELECT Users.Name, Deals.Login, (SUM(IF(Deals.Profit < 0, 1, 0))/Count(Deal))*100 as WinningRate, SUM(IF(Deals.Profit >= 0, 1, 0)) as WinningOrders, Count(Deal) as TotalOrders, SUM(Deals.Profit+Deals.Storage) as PNL, Users.Group FROM `mt5_deals` as Deals LEFT JOIN mt5_users as Users ON Deals.Login = Users.Login WHERE Time BETWEEN "2021-01-01 00:00:00" AND "2021-04-30 00:00:00" AND Deals.Action < 2 AND Deals.Entry IN (1,3) AND Users.Group LIKE "%real%" GROUP BY Login
    if($_SESSION["type"] == "Admin") $query1['query'] = '`lidyapar_mt5`.`mt5_deals` as Deals LEFT JOIN `lidyapar_mt5`.`mt5_users` as Users ON Deals.Login = Users.Login LEFT JOIN `lidyapar_mt5`.`mt5_accounts` as Accounts ON Deals.Login = Accounts.Login WHERE Time BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND Deals.Action <= "1" AND Deals.Entry IN (1,3) AND Users.Group LIKE "real%"';

    $query1['key']       = 'Deals.Deal';
    $query1['columns']   = array(
        array(
            'db' => 'Users.Name',
            'dt' => 0,
            'th' => 'Name'
        ),
        array(
            'db' => 'Deals.Login',
            'dt' => 1,
            'th' => 'Login'
        ),
        array(
            'db' => '(SUM(IF(Deals.Profit < 0, 1, 0))/Count(Deal))*100',
            'dt' => 2,
            'th' => 'Losing Rate',
            'formatter' => true
        ),
        array(
            'db' => 'Count(Deal)',
            'dt' => 3,
            'th' => 'Total Orders',
            'formatter' => true
        ),
        array(
            'db' => 'SUM(Deals.Profit+Deals.Storage)',
            'dt' => 4,
            'th' => 'PNL',
            'formatter' => true
        ),
        array(
            'db' => 'Accounts.Balance',
            'dt' => 5,
            'th' => 'Balance',
            'formatter' => true
        ),
        array(
            'db' => 'Accounts.Equity',
            'dt' => 6,
            'th' => 'Equity',
            'formatter' => true
        ),
        array(
            'db' => 'Accounts.Equity-(Accounts.Balance+Accounts.Credit)',
            'dt' => 7,
            'th' => 'Open Positions',
            'formatter' => true
        ),
        array(
            'db' => 'Accounts.Credit',
            'dt' => 8,
            'th' => 'Credit'
        ),
        array(
            'db' => 'Users.Group',
            'dt' => 9,
            'th' => 'Group'
        )
    );    
    
    $option1 = "
        'order': [[4, 'asc']],
        'columnDefs': [
            {
                'targets': [ 8 ],
                'visible': false
            },
            {
                'targets': [ 9 ],
                'visible': false
            }
        ],
        'lengthMenu': [ [5, 10, 25, 50, 100, 200, 300, 400, 500, -1], [5, 10, 25, 50, 100, 200, 300, 400, 500, 'All'] ],
    ";

    $table_loss_main = $factory::dataTableComplex(10,$query1,$option1);


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
                                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span><?php echo $startTime." - ".$endTime; ?></span> <i class="fa fa-caret-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="card pmd-card">
    					<div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Symbol</label>
                                    <input type="search" class="form-control filterDT" placeholder="Login Search ex: 10001" data-tableid="DT_win_main" data-col="1">
                                </div>
                            </div>
                        </div>
            		</div>-->
            		<div class="row">
                        <div class="col-sm-6">
                            <div class="card pmd-card">
            					<div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Name</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: John Smith" data-tableid="DT_win_main" data-col="0">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Login</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: 10000" data-tableid="DT_win_main" data-col="1">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Desk</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: real\\TURK" data-tableid="DT_win_main" data-col="9">
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
                                                    <span class="bold size-20">Winners <i class="fas fa-angle-right"></i></span>
                                                </div>
                                                <div class="col-md text-right float-right">
                                                    <small class="refPageSel">
                                                        <i class="fas fa-sync" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Table will not reload."></i>
                                                        <select id="refreshTime" name="refreshTime" class="custom-select custom-select-sm col-md-2 mx-2 refreshTime" data-tableid="DT_win_main">
                                                            <option value="0">No</option>
                                                            <option value="3">3 s</option>
                                                            <option value="15">15 s</option>
                                                            <option value="60" selected="">1 M</option>
                                                            <option value="180">3 M</option>
                                                            <option value="300">5 M</option>
                                                            <option value="600">10 M</option>
                                                            <option value="900">15 M</option>
                                                        </select>
                                                        <span></span>
                                                    </small>
                                                    <i id="do-reload" data-tableid="DT_win_main" class="fa fa-spinner alert-success primary rounded-circle do-reload" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Force reload."></i>
                                                </div>
                                            </div>
                                            <hr>
                                            <?= $table_win_main ?>
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
                                            <input type="search" class="form-control filterDT" placeholder="ex: John Smith" data-tableid="DT_loss_main" data-col="0">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Login</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: 10000" data-tableid="DT_loss_main" data-col="1">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Desk</label>
                                            <input type="search" class="form-control filterDT" placeholder="ex: real\\TURK" data-tableid="DT_loss_main" data-col="9">
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
                                                    <span class="bold size-20">Losers <i class="fas fa-angle-right"></i></span>
                                                </div>
                                                <div class="col-md text-right float-right">
                                                    <small class="refPageSel">
                                                        <i class="fas fa-sync" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Table will not reload."></i>
                                                        <select id="refreshTime" name="refreshTime" class="custom-select custom-select-sm col-md-2 mx-2 refreshTime" data-tableid="DT_loss_main">
                                                            <option value="0">No</option>
                                                            <option value="3">3 s</option>
                                                            <option value="15">15 s</option>
                                                            <option value="60" selected="">1 M</option>
                                                            <option value="180">3 M</option>
                                                            <option value="300">5 M</option>
                                                            <option value="600">10 M</option>
                                                            <option value="900">15 M</option>
                                                        </select>
                                                        <span></span>
                                                    </small>
                                                    <i id="do-reload" data-tableid="DT_loss_main" class="fa fa-spinner alert-success primary rounded-circle do-reload" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Force reload."></i>
                                                </div>
                                            </div>
                                            <hr>
                                            <?= $table_loss_main ?>
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
            
            $(function() {;
                <?php
                $stime = strtotime($startTime);
                $etime = strtotime($endTime);
                ?>
                var start = <?php echo date('dd/mm/Y', $stime); ?>;
                var end = <?php echo date('dd/mm/Y', $estime); ?>;
            
                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    opens: 'left',
                    "showDropdowns": true,
                    "timePicker": true,
                    ranges: {
                       'Today': [moment(), moment().add(1, 'days')],
                       'Yesterday': [moment().subtract(1, 'days'), moment()],
                       'Last 7 Days': [moment().subtract(8, 'days'), moment()],
                       'Last 30 Days': [moment().subtract(31, 'days'), moment()],
                       'Last 90 Days': [moment().subtract(91, 'days'), moment()],
                       'This Month': [moment().startOf('month'), moment().endOf('month').add(1, 'days')],
                       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month').add(1, 'days')],
                       'Last 3 Month': [moment().subtract(3, 'month').startOf('month'), moment().endOf('month').add(1, 'days')],
                       'Total': [moment().subtract(300, 'month').startOf('month'), moment().endOf('month').add(1, 'days')]
                    }
                }, function(start, end, label){
                    $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                    
                    window.location.href = "wl.php?startTime="+start.format('YYYY-MM-DD')+"&endTime="+end.format('YYYY-MM-DD');  
                });
            
                //cb(start, end);
            
            });
        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>
</html>