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

if(isset($_POST['submit'])){
	$unit = $_POST['unit'];
	$startTime = $_POST['startTime'];
	$endTime = $_POST['endTime'];
	$Time = $_POST['Time'];

    if ($unit == "Turkish"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "1"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
    
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_USERS.NAME as NAME,MT4_TRADES.LOGIN AS SLOGIN,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1) as SCALP,SUM(MT4_TRADES.PROFIT) as PROFIT,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CMD <= 1 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00") as TRADES,(SELECT SUM(MT4_TRADES.PROFIT+MT4_TRADES.SWAPS) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.CMD <= 1) as PL FROM `MT4_TRADES` LEFT JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1 GROUP BY MT4_TRADES.LOGIN';
        $result1 = $conn->query($sql1);
        
    } else if($unit == "Farsi"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "3"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_USERS.NAME as NAME,MT4_TRADES.LOGIN AS SLOGIN,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1) as SCALP,SUM(MT4_TRADES.PROFIT) as PROFIT,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CMD <= 1 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00") as TRADES,(SELECT SUM(MT4_TRADES.PROFIT+MT4_TRADES.SWAPS) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.CMD <= 1) as PL FROM `MT4_TRADES` LEFT JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1 GROUP BY MT4_TRADES.LOGIN';
        $result1 = $conn->query($sql1);
    } else if($unit == "Arabic"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "4"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_USERS.NAME as NAME,MT4_TRADES.LOGIN AS SLOGIN,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1) as SCALP,SUM(MT4_TRADES.PROFIT) as PROFIT,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CMD <= 1 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00") as TRADES,(SELECT SUM(MT4_TRADES.PROFIT+MT4_TRADES.SWAPS) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.CMD <= 1) as PL FROM `MT4_TRADES` LEFT JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1 GROUP BY MT4_TRADES.LOGIN';
        $result1 = $conn->query($sql1);
    } else if($unit == "English") {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "5"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_USERS.NAME as NAME,MT4_TRADES.LOGIN AS SLOGIN,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1) as SCALP,SUM(MT4_TRADES.PROFIT) as PROFIT,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CMD <= 1 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00") as TRADES,(SELECT SUM(MT4_TRADES.PROFIT+MT4_TRADES.SWAPS) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.CMD <= 1) as PL FROM `MT4_TRADES` LEFT JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1 GROUP BY MT4_TRADES.LOGIN';
        $result1 = $conn->query($sql1);
    } else if($unit == "STPL") {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "6"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_USERS.NAME as NAME,MT4_TRADES.LOGIN AS SLOGIN,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1) as SCALP,SUM(MT4_TRADES.PROFIT) as PROFIT,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CMD <= 1 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00") as TRADES,(SELECT SUM(MT4_TRADES.PROFIT+MT4_TRADES.SWAPS) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.CMD <= 1) as PL FROM `MT4_TRADES` LEFT JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1 GROUP BY MT4_TRADES.LOGIN';
        $result1 = $conn->query($sql1);
    } else if($unit == "All") {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        $prefixed_array = str_replace(",", '","real\\\\', $unitn);
        
        $sql1 = 'SELECT 
        	mt5_users.Name as Name,
        	mt5_deals.Login AS SLogin,
        	(SELECT COUNT(mt5_deals.Order) 
        		FROM (SELECT * FROM (SELECT max(`Order`) AS Order,SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT Time SEPARATOR ","), ",", 1), ",", -1) AS OPEN_TIME,SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT Time SEPARATOR ","), ",", 2), ",", -1) AS CLOSE_TIME,max(Profit) AS Profit,max(Storage) AS Storage, max(Storage) AS Storage FROM `mt5_deals` WHERE Time BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND Entry IN ("0","1") GROUP By PositionID) AS Main WHERE OPEN_TIME != CLOSE_TIME)
        		WHERE Login = SLogin 
        		AND mt5_deals.Entry <> "0" 
        		AND mt5_deals.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        		AND TIMESTAMPDIFF(MINUTE,mt5_deals.OPEN_TIME,mt5_deals.CLOSE_TIME) <= "'.$Time.'" 
        		AND mt5_deals.Action <= 1
        	) as Scalp,
        	SUM(mt5_deals.Profit+mt5_deals.Storage) as Profit,
        	(SELECT COUNT(mt5_deals.order) 
        		FROM mt5_deals 
        		WHERE Login = SLogin 
        		AND mt5_deals.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        		AND mt5_deals.Action <= 1 
        		AND mt5_deals.Entry <> "0"
        	) as Trades,
        	(SELECT SUM(mt5_deals.Profit+mt5_deals.Storage) 
        		FROM mt5_deals 
        		WHERE Login = SLogin 
        		AND mt5_deals.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        		AND mt5_deals.Entry <> "0" 
        		AND mt5_deals.Action <= 1
        	) as PL 
        FROM (SELECT * FROM (SELECT max(`Order`) AS Order,SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT Time SEPARATOR ","), ",", 1), ",", -1) AS OPEN_TIME,SUBSTRING_INDEX(SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT Time SEPARATOR ","), ",", 2), ",", -1) AS CLOSE_TIME,max(Profit) AS Profit,max(Storage) AS Storage, max(Storage) AS Storage FROM `mt5_deals` WHERE Time BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND Entry IN ("0","1") GROUP By PositionID) AS Main WHERE OPEN_TIME != CLOSE_TIME)
        LEFT JOIN mt5_users ON mt5_deals.Login = mt5_users.Login 
        WHERE mt5_users.Group IN ("'.$prefixed_array.'") 
        	AND mt5_deals.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
        	AND TIMESTAMPDIFF(MINUTE,mt5_deals.OPEN_TIME,mt5_deals.CLOSE_TIME) <= "'.$Time.'" 
        	AND mt5_deals.Action <= 1 
        GROUP BY mt5_deals.Login';
        echo $sql1;
        $result1 = $conn->query($sql1);
    } else {
        
    }
    
    //$sql1 = 'SELECT MT4_USERS.NAME as NAME,MT4_TRADES.LOGIN AS SLOGIN,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1) as SCALP,SUM(MT4_TRADES.PROFIT) as PROFIT,(SELECT COUNT(MT4_TRADES.TICKET) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CMD <= 1 AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00") as TRADES,(SELECT SUM(MT4_TRADES.PROFIT+MT4_TRADES.SWAPS) FROM MT4_TRADES WHERE LOGIN = SLOGIN AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND MT4_TRADES.CLOSE_TIME <> "1970-01-01 00:00:00" AND MT4_TRADES.CMD <= 1) as PL FROM `MT4_TRADES` LEFT JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_TRADES.LOGIN NOT IN '.$notin.' AND MT4_USERS.GROUP IN '.$in.' AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND TIMESTAMPDIFF(MINUTE,MT4_TRADES.OPEN_TIME,MT4_TRADES.CLOSE_TIME) <= "'.$Time.'" AND MT4_TRADES.CMD <= 1 GROUP BY MT4_TRADES.LOGIN';
    echo $sql1;
    //$result1 = $conn->query($sql1);
    
    $conn->close();
    
}
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
                            		<div class="form-group col-md-2">
                            			<label for="inputUnit">Unit</label>
                            			<select class="form-control" id="unit" name="unit" required>
                                            <?php if ($_SESSION['unit'] == "All"){ ?>
                                			    <option value="All">All</option>
                                                <option value="Turkish">Turkish</option>
                                                <option value="STPL">STPL</option>
                                                <option value="Farsi">Farsi</option>
                                                <option value="Arabic">Arabic</option>
                                                <option value="English">English</option>
                                            <?php } else { ?>
                                                <option value="<?php echo $_SESSION['unit'];?>"><?php echo $_SESSION['unit'];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                            			<label for="inputTime">Time Frame</label>
                            			<input type="text" class="form-control" id="Time" name="Time" value="<?php echo $Time; ?>" required>
                            		</div>
                            		<div class="form-group col-md-3 date">
                            			<label for="inputstartTime">Start Time</label>
                            			<div class="input-group">
                                			<input type="text" class="form-control" id="startTime" name="startTime" value="<?php echo $startTime; ?>" required>
                                			<div class="input-group-append">
                                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                            </div>
                                        </div>
                            		</div>
                            		<div class="form-group col-md-3 date">
                            			<label for="inputendTime">End Time</label><i class="mdi mdi-update"></i>
                            			<div class="input-group">
                            			    <input type="text" class="form-control" id="endTime" name="endTime" value="<?php echo $endTime; ?>" required>
                            			    <div class="input-group-append">
                                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                            </div>
                                        </div>
                            		</div>
                            		<div class="form-group col-md-2">
                            		    <label for="input">&nbsp;</label>
                    			        <input class="btn btn-primary form-control" type="submit" name="submit" value="Submit" />
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
                                            <span class="bold size-20">Scalpers (Less Than <?php echo $Time; ?>min)<i class="fas fa-angle-right"></i></span>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="bold size-20 float-right"><?php echo $startTime; ?> - <?php echo $endTime; ?></span>
                                        </div>
                                    </div>
                                    <hr>
                                    <table id="data-table" class="table table-hover">  
                                        <thead> 
                                            <tr>
                                                <th>Name</th>
                                                <th>UserID</th>
                                                <th>Scalp Trades</th>
                                                <th>Total Trades</th>
                                                <th>Percentage</th>
                                                <th>Scalp PL</th>
                                                <th>Total PL</th>
                                            </tr>  
                    					</thead>
                    					<tbody>
                    					    <?php
                    					        if ($result1->num_rows > 0) {
                                                    while($row1 = $result1->fetch_assoc()) {
                                            ?>
                        					    <tr>
                        					        <td><?php echo $row1['NAME']; ?></td>
                        					        <td><?php echo $row1['SLOGIN']; ?></td>
                        					        <td><?php echo $row1['SCALP']; ?></td>
                        					        <td><?php echo $row1['TRADES']; ?></td>
                        					        <td>
                        					            <?php
                        					                $Percentage = ($row1['SCALP']/$row1['TRADES'])*100;
                        					                $Number = round($Percentage, 2);
                        					                echo number_format($Number, 2, '.', ''); 
                        					            ?> %
                        					        </td>
                        					        <td><?php echo round($row1['PROFIT'], 2); ?></td>
                        					        <td><?php echo round($row1['PL'], 2); ?></td>
                        					    </tr>
                        					<?php
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
                format: 'yyyy-mm-dd' 
        	});
        	$('#endTime').datepicker({ 
                format: 'yyyy-mm-dd' 
        	});
        	$('#data-table').DataTable({ 
        		"responsive": true,
        		"order": [[ 4, "desc" ]],
        		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
        		"columnDefs": [
                    { "type": "any-number", targets: 4 }
                ]
        		
            });
        } );
        </script>
<?php include('includes/script-bottom.php'); ?>

</body>

</html>