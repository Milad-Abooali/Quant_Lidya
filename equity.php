<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

include('includes/head.php'); 
?>
    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
<?php 

include('includes/css.php'); 

?>
    <body>

        <!-- Begin page -->
        <div id="wrapper">


<?php 
    include('includes/topbar.php');
    include('includes/sidebar.php');

    $sqlMT4 = 'SELECT SUM(EQUITY) AS Equity,`GROUP` AS `Group` FROM `MT4_USERS` WHERE AGENT_ACCOUNT != "1" GROUP BY `GROUP`';
    $mt4 = $DB_mt4->query($sqlMT4);

$sqlMT5 = 'SELECT SUM(Equity) AS Equity,mt5_users.Group AS `Group` FROM `mt5_accounts` LEFT JOIN mt5_users ON mt5_users.Login = mt5_accounts.Login WHERE mt5_users.Group LIKE "real%4%" GROUP BY `Group`';
    $mt5 = $DB_mt5->query($sqlMT5);
    
?>
 
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Equity Report</h4>
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
    		    <?php
    				if($mt5) while ($rowMT5 = mysqli_fetch_array($mt5)) {
    				    $rowGroup = substr($rowMT5['Group'], 5);
    				    $sqlMtGroups5 = 'SELECT unit FROM mt_groups WHERE type = "2" AND name = "'.$rowGroup.'"';
                        $mtgroups5 = $DB_admin->query($sqlMtGroups5);
    				    while($rowGroups5 = $mtgroups5->fetch_assoc()) {
    				        if($rowGroups5['unit'] == "1"){
    				            $turkey5 += $rowMT5['Equity'];
    				        } else if($rowGroups5['unit'] == "3"){
    				            $farsi5 += $rowMT5['Equity'];
    				        } else if($rowGroups5['unit'] == "4"){
    				            $arab5 += $rowMT5['Equity']; 
    				        } else if($rowGroups5['unit'] == "6"){
    				            $stpl5 += $rowMT5['Equity']; 
    				        } else if($rowGroups5['unit'] == "8"){
    				            $farsi25 += $rowMT5['Equity']; 
    				        } else {
    				            $english5 = "";
    				        }
                        }
    				}
    			?>
    			<div class="col-md-12">
                    <h5>Total Equity (MT5): $<?php echo intval($turkey5)+intval($farsi5)+intval($arab5)+intval($stpl5)+intval($english5)+intval($farsi25); ?></h5>
                </div>
    			<div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card m-b-30 text-center">
                        <div class="card-body">
                            <h4 class="card-title font-16 mt-0">Tukish (4inFX)</h4>
                            <h6 class="card-subtitle font-14 text-muted">Equity Report - <?php echo date('Y/m/d h:i:s'); ?></h6>
                            <hr>
                            <h6 class="card-text">$<?php echo $turkey5; ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card m-b-30 text-center">
                        <div class="card-body">
                            <h4 class="card-title font-16 mt-0">Farsi (4inFX)</h4>
                            <h6 class="card-subtitle font-14 text-muted">Equity Report - <?php echo date('Y/m/d h:i:s'); ?></h6>
                            <hr>
                            <h6 class="card-text">$<?php echo $farsi5; ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card m-b-30 text-center">
                        <div class="card-body">
                            <h4 class="card-title font-16 mt-0">Farsi2 (4inFX)</h4>
                            <h6 class="card-subtitle font-14 text-muted">Equity Report - <?php echo date('Y/m/d h:i:s'); ?></h6>
                            <hr>
                            <h6 class="card-text">$<?php echo $farsi25; ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card m-b-30 text-center">
                        <div class="card-body">
                            <h4 class="card-title font-16 mt-0">Arab (4inFX)</h4>
                            <h6 class="card-subtitle font-14 text-muted">Equity Report - <?php echo date('Y/m/d h:i:s'); ?></h6>
                            <hr>
                            <h6 class="card-text">$<?php echo $arab5; ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('includes/footer.php'); ?>
    </div>
</div>
<?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
$(document).ready( function () {
    
	$(function () {
		$('[data-toggle="tooltip"]').tooltip()
	});
    
});
</script>

<?php include('includes/script-bottom.php'); ?>

</body>

</html>
<?php
    $DB_mt4->close();
    $DB_mt5->close();
    $DB_admin->close();
?>