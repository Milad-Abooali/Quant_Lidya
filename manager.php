<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

include('includes/head.php');
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
                                <h4 class="page-title">Web Manager</h4>
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
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stat bg-primary">
                                <div class="card-body mini-stat-img">
                                    <div class="mini-stat-icon" id="open-trades">
                                        <i class="mdi mdi-buffer float-right"></i>
                                    </div>
                                    <div class="text-white">
                                        <h6 class="text-uppercase mb-3">Open Trades</h6>
                                        <h4 class="mb-4">4,465.87 Lot</h4>
                                        <!--<span class="badge badge-info"> +11% </span> <span class="ml-2">From previous period</span>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stat bg-primary">
                                <div class="card-body mini-stat-img">
                                    <div class="mini-stat-icon">
                                        <i class="mdi mdi-buffer float-right"></i>
                                    </div>
                                    <div class="text-white">
                                        <h6 class="text-uppercase mb-3">Closed Trades</h6>
                                        <h4 class="mb-4">4,465.87 Lot</h4>
                                        <!--<span class="badge badge-info"> +11% </span> <span class="ml-2">From previous period</span>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stat bg-primary">
                                <div class="card-body mini-stat-img">
                                    <div class="mini-stat-icon">
                                        <i class="mdi mdi-buffer float-right"></i>
                                    </div>
                                    <div class="text-white">
                                        <h6 class="text-uppercase mb-3">Transactions</h6>
                                        <h4 class="mb-4">4,465.87 Lot</h4>
                                        <!--<span class="badge badge-info"> +11% </span> <span class="ml-2">From previous period</span>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stat bg-primary">
                                <div class="card-body mini-stat-img">
                                    <div class="mini-stat-icon">
                                        <i class="mdi mdi-buffer float-right"></i>
                                    </div>
                                    <div class="text-white">
                                        <h6 class="text-uppercase mb-3">Trading Accounts</h6>
                                        <h4 class="mb-4">4,465.87 Lot</h4>
                                        <!--<span class="badge badge-info"> +11% </span> <span class="ml-2">From previous period</span>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card pmd-card">
    					<div class="card-body">
    					    <div class="row">
    					        <div class="col-md-12">
        					        <table class="table" id="openPositions">
                                        <thead>
                                            <tr>
                                                <th scope="col">Ticket</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Symbol</th>
                                                <th scope="col">Volume</th>
                                                <th scope="col">Open Time</th>
                                                <th scope="col">Open Price</th>
                                                <th scope="col">Profit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
            					        <?php
            					        $trades_rows = array();
                                        $request = new CMT5Request();
                                        if($request->Init('mt5.tradeclan.co.uk:443') && $request->Auth(1000,"@Sra7689227",1950,"WebManager"))
                                        {
                                            $totalURL = '/position_get_total?login=107095';
                                    	    $resultTotal = $request->Get($totalURL);
                                    	    if($resultTotal!=false)
                                    	    {
                                    	        //echo $resultTotal;
                                    		    $jsonTotal=json_decode($resultTotal);
                                    		    $TotalPositions = $jsonTotal->answer->total;
                                    		    echo "Total Positions: ".$TotalPositions;
                                    		    echo "</br></br>";
                                    	    }
                                    	    for($i = 0; $i <= $TotalPositions; $i = $i + 100){ 
                                                $result = $request->Get('/position_get_page?login=107095&offset='.$i.'&total=100');
                                                if($result!=false)
                                        	    {
                                        	        $step_rows = json_decode($result)->answer;
                                        	        if($step_rows) foreach ($step_rows as $row) array_push($trades_rows, $row);
                                        	    }
                                            }
                                            for($s = 0; $s < $TotalPositions; $s++){ 
                                                echo "<tr>";
                                        	    echo "<td>".$trades_rows[$s]->Position."</td>";
                                        	    echo "<td>";
                                        	    if($trades_rows[$s]->Action == "1"){
                                                    echo "Buy";
                                        	    } else {
                                                    echo "Sell";
                                        	    }
                                        	    echo "</td>";
                                        	    echo "<td>".$trades_rows[$s]->Symbol."</td>";
                                        	    echo "<td>".$trades_rows[$s]->Volume."</td>";
                                        	    echo "<td>".$trades_rows[$s]->TimeCreate."</td>";
                                        	    echo "<td>".$trades_rows[$s]->PriceOpen."</td>";
                                        	    echo "<td>".$trades_rows[$s]->Profit."</td>";
                                        	    echo "</tr>";
                                            }
                                    	    //GF::P($trades_rows);
                                        }
                                        $request->Shutdown();
                                        ?>
                                        </tbody>
                                    </table>
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
            
            $('#openPositions').DataTable();
            
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