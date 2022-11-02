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

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    $userID = $_POST['mt4_id'];
    $mt4_id = $_POST['mt4_id'];
    $startTime = $_POST['startTime'];
	$endTime = $_POST['endTime'];
    $usunit = intval($_POST['unit']);
    
    $sqlMT4 = 'SELECT * FROM MT4_USERS WHERE LOGIN = "'.$mt4_id.'"';
    $mt4 = $DB_mt4->query($sqlMT4);
    
    $sqlTRADES = 'SELECT * FROM MT4_TRADES WHERE LOGIN = "'.$mt4_id.'" AND CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
    $trades = $DB_mt4->query($sqlTRADES);
    
    $sqlMT42 = 'SELECT * FROM MT4_USERS WHERE LOGIN = "'.$mt4_id.'"';
    $mt42 = $DB_mt4->query($sqlMT42);
    
    $sql3 = 'SELECT * FROM notes WHERE user_id = "'.$userID.'"';
    $result3 = $DB_admin->query($sql3);
    
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
                        <h4 class="page-title">Dashboard</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">
                                Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?= Broker['title'] ?>
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
    			<div class="col-sm-12">
    			    <div class="card pmd-card">
    					<div class="card-body">
            			    <form action="" method="post" autocomplete="off">
                            	<div class="form-row">
                            		<div class="form-group col-md-2">
                            			<label for="inputuserId">Login</label>
                        			    <input type="text" class="form-control" id="mt4_id" name="mt4_id" value="<?php echo $userID; ?>" required>
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
    				<?php
    					//if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){
    					if($mt4) while ($rowMT4 = mysqli_fetch_array($mt4)) {
    				?>
    				<div class="card pmd-card">
    					<div class="card-body">
        				    <div class="row">
            				    <div class="col-md-6">
            				        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="pills-general-mt4-tab" data-toggle="pill" href="#pills-general-mt4" role="tab" aria-controls="pills-general-mt4" aria-selected="true">General Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-2-tab" data-toggle="pill" href="#pills-deposit-mt4" role="tab" aria-controls="pills-deposit-mt4" aria-selected="false">Deposits</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-3-tab" data-toggle="pill" href="#pills-withdraw-mt4" role="tab" aria-controls="pills-withdraw-mt4" aria-selected="false">Withdrawals</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-4-tab" data-toggle="pill" href="#pills-history-mt4" role="tab" aria-controls="pills-history-mt4" aria-selected="false">Trade History</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-5-tab" data-toggle="pill" href="#pills-comment" role="tab" aria-controls="pills-comment" aria-selected="false">Comments</a>
                                        </li>
                                    </ul>
            				    </div>
                                <!--<div class="col-md-6">
                                    <span class="bold size-30 float-right ml-1"><button type="button" class="btn btn-primary btn-sm show-changegroup"><i class="fas fa-users-cog"></i></button></span>
                                    <span class="bold size-30 float-right ml-1"><button type="button" class="btn btn-primary btn-sm show-changeleverage"><i class="fas fa-balance-scale"></i></button></span>
                                    <span class="bold size-30 float-right ml-1"><button type="button" class="btn btn-primary btn-sm show-changepassword"><i class="fas fa-lock"></i></button></span>
                                    <?php if($rowMT4['ENABLE_READONLY'] == 0) { ?>
                                        <span class="bold size-30 float-right ml-1"><button type="button" class="btn btn-primary btn-sm disableac"><i class="fas fa-user-slash"></i></button></span>
                                    <?php } else { ?>
                                        <span class="bold size-30 float-right ml-1"><button type="button" class="btn btn-danger btn-sm enableac"><i class="fas fa-user-slash"></i></button></span>
                                    <?php } ?>
                                    <span class="bold size-30 float-right ml-1"><button type="button" class="btn btn-primary btn-sm show-changebalance"><i class="fas fa-wallet"></i></button></span>
                                </div>-->
                            </div>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                </div>
                                <div class="alert alert-danger alert-dismissible" id="delete" style="display:none;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                </div>
                                <div class="tab-pane fade show active" id="pills-general-mt4" role="tabpanel" aria-labelledby="pills-general-mt4-tab">
                                <?php
                                    $amountDP = 0;
                                    $amountCDP = 0;
                                    $amountBO = 0;
                                    
                                    $amountWT = 0;
                                    $amountWBO = 0;
                                    $amountCWT = 0;
                                    $totalorders = 0;
                                    $totalswaps = 0;
                                    $totalcommisions = 0;
                                    $pnl = 0;
                                    $winning = 0;
                                    $losing = 0;
                                    
                                    $dp = array("Deposit", "DEPOSIT", "DEPOSIT WIRE TRANSFER", "Deposit Wire Transfer", "Deposit Credit Card", "DEPOSIT CREDIT CARD", "Wire In", "wire in", "WIRE IN");
                                    $wd = array("Withdrawal", "WITHDRAWAL", "WITHDRAWAL WIRE TRANSFER", "Withdrawal Wire Transfer", "Withdrawal Credit Card", "WITHDRAWAL CREDIT CARD", "Wire Out", "wire out", "WIRE OUT", "Withdraw", "WITHDRAW");
                                    if($trades) while ($rowTRADES = mysqli_fetch_array($trades)) {
                                        if($rowTRADES['CMD'] == 6){
                                            if($rowTRADES['PROFIT'] >= 0){
                                                if(!in_array($rowTRADES['COMMENT'], $dp)){
                                                    $amountBO += $rowTRADES['PROFIT'];
                                                } else {
                                                    $amountDP += $rowTRADES['PROFIT'];
                                                }
                                    	    } else if($rowTRADES['PROFIT'] <= 0) { 
                                    	        if(!in_array($rowTRADES['COMMENT'], $wd)){
                                                    $amountWBO += $rowTRADES['PROFIT'];
                                                } else if(preg_match('/\btradecorrection\b/',$rowTRADES['COMMENT'])) {
                                                    $amountCWT += $rowTRADES['PROFIT'];
                                                } else {
                                                    $amountWT += $rowTRADES['PROFIT'];
                                                } 
                                    	    }
                                        }
                                        if($rowTRADES['CMD'] < 2 && $rowTRADES['CLOSE_TIME'] <> "1970-01-01 00:00:00" && $rowTRADES['COMMENT'] <> "cancelled") {
                                            $totalorders = $totalorders+1;
                                            $pnl += $rowTRADES['PROFIT'];
                                            $swaps += $rowTRADES['SWAPS'];
                                            $commission += $rowTRADES['COMMISSION'];
                                            $volume += $rowTRADES['VOLUME'] / 100;
        
                                            if($rowTRADES['PROFIT']+$rowTRADES['SWAPS'] >= 0){
                                                $winning++;
                                            } else {
                                                $losing++;
                                            }
                                        }
                                        $totalrate = $winning+$losing;
                                        if($totalrate > 0){
                                            $winningrate = ( $winning / $totalrate ) * 100;
                                            $losingrate = ( $losing / $totalrate ) * 100;
                                        }
                                    }
                                    ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <span class="bold size-24"><?php echo $rowMT4['NAME']; ?></span> - 
                                                <?php if($rowMT4['ENABLE_READONLY'] == "0"){ ?>
                                                    Active
                                                <?php } else { ?><span class="bold size-24 red">Read-Only</span><?php } ?> - 1:<?php echo $rowMT4['LEVERAGE']; ?> (<?php echo $rowMT4['GROUP']; ?>)
                                            </div>
                                            <?php if($_SESSION["type"] == "Admin"){ ?>
                                            <div class="col-md-6 text-right">
                                                <span class="bold dark-gray size-24"><?php echo $rowMT4['LOGIN']; ?>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div class="row m-top-10">
                                            <div class="col-md-4">
                                                Balance: <span class="size-26 dark-gray bold"><?php echo number_format($rowMT4['BALANCE'], 2, '.', ','); ?>$</span>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                Equity: <span class="size-26 blue bold"><?php echo number_format($rowMT4['EQUITY'], 2, '.', ','); ?>$</span>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <div>Margin level: <span class="size-26 bold"><?php echo number_format($rowMT4['MARGIN_LEVEL'], 2, '.', '.'); ?>%</span></div>
                                                <small class="m-top--5 f-right">Margin Used: <span class="size-20 bold"><?php echo number_format($rowMT4['MARGIN'], 2, '.', '.'); ?></small>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4">
                                                Deposit: <span class="size-26 green bold"><?php echo number_format($amountDP, 2, '.', ','); ?>$</span>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                Withdrawal: <span class="size-26 red bold"><?php echo number_format($amountWT, 2, '.', ','); ?>$</span>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                Bonus: <span class="size-26 yellow bold"><?php echo number_format($amountBO+$amountWBO, 2, '.', ','); ?>$</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4">
                                                Total Orders: <span class="bold size-20"><?php echo $totalorders; ?></span>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                Total Volume: <span class="bold size-20"><?php echo number_format($volume, 2, '.', ','); ?>Lot</span>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                Winning Rate: <span class="bold size-20"><?php echo number_format($winningrate, 2, '.', '.'); ?>%</span>
                                                </br>
                                                <small>Losing Rate: <span class="bold size-20"><?php echo number_format($losingrate, 2, '.', '.'); ?>%</span></small>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?= $_L->T('Total_Swap','statistics') ?>: <span class="bold size-20"><?php echo number_format($swaps, 2, '.', ','); ?>$</span>
                                                </br>
                                                <?= $_L->T('Total_Commissions','statistics') ?>: <span class="bold size-20"><?php echo number_format($commission, 2, '.', ','); ?>$</span>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <?= $_L->T('Total_P_L','statistics') ?>: <span class="bold dark-gray size-26"><?php echo number_format($pnl, 2, '.', ','); ?>$</span>
                                                </br>
                                                <?= $_L->T('Total_Raw_P_L','statistics') ?>: <span class="bold green size-26"><?php echo number_format($pnl+$swaps+$commission, 2, '.', ','); ?>$</span>
                                                </br>
                                                <?= $_L->T('Total_P_L_Bonus','statistics') ?>: <span class="bold red size-26"><?php echo number_format(($pnl+$swaps+$commission)+($amountBO+$amountWBO), 2, '.', ','); ?>$</span>
                                            </div>
                                        </div>
                                    <?php
                                        }
                                    ?>
                                </div>
                                <div class="tab-pane fade" id="pills-deposit-mt4" role="tabpanel" aria-labelledby="pills-deposit-mt4-tab">
                                    <table id="data-table-deposit" class="table table-hover" style="width: 100%;">  
                                        <thead>  
                                            <tr>  
                    							<th>ID</th>
                                                <th>Amount</th>
                                                <th>Comment</th>
                    							<th>Time</th>
                                            </tr>
                    					</thead>
                    					<tbody>
                    					    <?php 
                                                $sqlTRANS = 'SELECT * FROM MT4_TRADES WHERE LOGIN = "'.$mt4_id.'" AND CMD = 6 AND PROFIT >= 0';
                                                $trans = $DB_mt4->query($sqlTRANS);
                                                if($trans) while ($rowTRANS = mysqli_fetch_array($trans)) { 
                                            ?>
                                            <tr>
                                                <td><?php echo $rowTRANS['TICKET']; ?></td>
                                                <td><?php echo $rowTRANS['PROFIT']; ?></td>
                                                <td><?php echo $rowTRANS['COMMENT']; ?></td>
                                                <td><?php echo $rowTRANS['OPEN_TIME']; ?></td>
                                            </tr>
                                            <?php } ?>
                    					</tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="pills-withdraw-mt4" role="tabpanel" aria-labelledby="pills-withdraw-mt4-tab">
                                    <table id="data-table-withdraw" class="table table-hover" style="width: 100%;">  
                                        <thead>  
                                            <tr>  
                    							<th>ID</th>
                                                <th>Amount</th>
                                                <th>Comment</th>
                    							<th>Time</th>
                                            </tr>
                    					</thead>
                    					<tbody>
                    					    <?php 
                                                $sqlTRANS2 = 'SELECT * FROM MT4_TRADES WHERE LOGIN = "'.$mt4_id.'" AND CMD = 6 AND PROFIT <= 0';
                                                $trans2 = $DB_mt4->query($sqlTRANS2);
                                                if($trans2) while ($rowTRANS2 = mysqli_fetch_array($trans2)) { 
                                            ?>
                                            <tr>
                                                <td><?php echo $rowTRANS2['TICKET']; ?></td>
                                                <td><?php echo $rowTRANS2['PROFIT']; ?></td>
                                                <td><?php echo $rowTRANS2['COMMENT']; ?></td>
                                                <td><?php echo $rowTRANS2['OPEN_TIME']; ?></td>
                                            </tr>
                                            <?php } ?>
                    					</tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="pills-history-mt4" role="tabpanel" aria-labelledby="pills-history-mt4-tab">
                                    <table id="data-table-history" class="table table-hover" style="width: 100%;">  
                                        <thead>  
                                            <tr>  
                    							<th>Order</th>
                    							<th>Symbol</th>
                    							<th>Type</th>
                    							<th>Volume</th>
                    							<th>Open Price</th>
                    							<th>Open Time</th>
                    							<th>Close Price</th>
                    							<th>Close Time</th>
                    							<th>Stop Loss</th>
                    							<th>Take Profit</th>
                    							<th>Swap</th>
                    							<th>Profit</th>
                    							<th>Comment</th>
                                            </tr>
                    					</thead>
                    					<tbody>
                    					    <?php 
                                                $sqlTRANS2 = 'SELECT * FROM MT4_TRADES WHERE LOGIN = "'.$mt4_id.'" AND CMD <= 5 AND CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
                                                $trans2 = $DB_mt4->query($sqlTRANS2);
                                                if($trans2) while ($rowTRANS2 = mysqli_fetch_array($trans2)) { 
                                            ?>
                                            <tr>
                                                <td><?php echo $rowTRANS2['TICKET']; ?></td>
                                                <td><?php echo $rowTRANS2['SYMBOL']; ?></td>
                                                <?php if($rowTRANS2['CMD'] = 0){ ?>
                                                    <td>Buy</td>
                                                <?php } else if($rowTRANS2['CMD'] = 1) { ?>
                                                    <td>Sell</td>
                                                <?php } else { ?>
                                                    <td><?php echo $rowTRANS2['CMD']; ?></td>
                                                <?php } ?>
                                                <!--<td><?php echo $rowTRANS2['CMD']; ?></td>-->
                                                <td><?php echo $rowTRANS2['VOLUME']/100; ?></td>
                                                <td><?php echo $rowTRANS2['OPEN_PRICE']; ?></td>
                                                <td><?php echo $rowTRANS2['OPEN_TIME']; ?></td>
                                                <td><?php echo $rowTRANS2['CLOSE_PRICE']; ?></td>
                                                <td><?php echo $rowTRANS2['CLOSE_TIME']; ?></td>
                                                <td><?php echo $rowTRANS2['SL']; ?></td>
                                                <td><?php echo $rowTRANS2['TP']; ?></td>
                                                <td><?php echo $rowTRANS2['SWAPS']; ?></td>
                                                <td><?php echo $rowTRANS2['PROFIT']; ?></td>
                                                <td><?php echo $rowTRANS2['COMMENT']; ?></td>
                                            </tr>
                                            <?php } ?>
                    					</tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="pills-comment" role="tabpanel" aria-labelledby="pills-comment-tab">
                                    <form id="fupForm" name="form1" method="post">
                                        <div class="form-row">
                                            <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
                                    			<label for="type">Type:</label>
                                    			<select name="note_type" id="note_type" class="form-control">
                                    				<option value="">Select</option>
                                    				<?php if($_SESSION["type"] == "Backoffice"){ ?>
                                    				<option value="Light Scalper">Light Scalper</option>
                                    				<option value="Heavy Scalper">Heavy Scalper</option>
                                    				<option value="Latency User">Latency User</option>
                                    				<option value="A-Book Potential">A-Book Potential</option>
                                    				<option value="On Watch List">On Watch List</option>
                                    				<option value="Black Listed">Black Listed</option>
                                    				<?php } else { ?>
                                    				<option value="Busy – Call Back Later">Busy – Call Back Later</option>
                                    				<option value="Interested – Call Back Later">Interested – Call Back Later</option>
                                    				<option value="Interested – Send Information">Interested – Send Information</option>
                                    				<option value="No Interest – Reason Given">No Interest – Reason Given</option>
                                    				<option value="Incorect Information">Incorect Information</option>
                                    				<option value="Follow-Up Scheduled">Follow-Up Scheduled</option>
                                    				<option value="Fully Interested">Fully Interested</option>
                                    				<option value="Whatsapp">Whatsapp</option>
                                    				<option value="Other">Other</option>
                                    				<?php } ?>
                                    			</select>
                                    		</div>
                                    		<div class="col-md-4 col-sm-12 col-xs-12 mb-3">
                                    			<label for="note">Note:</label>
                                    			<input type="text" class="form-control" id="note" placeholder="Note" name="note">
                                    		</div>
                                    		<div class="col-md-4 col-sm-12 col-xs-12 mb-3">
                                    		    <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo $userID; ?>">
                                    		    <label for="save">&nbsp;</label>
                                    		    <input type="button" name="save" class="form-control btn btn-primary" value="Save" id="butsave">
                                    		</div>
                                        </div>
                                	</form>
                                	<hr>
                                    <table id="data-table6" class="table table-hover" style="width: 100%;">  
                                        <thead> 
                                            <tr>
                                                <th>Note</th>
                                                <th>Type</th>
                                                <th>Created By</th>
                                                <th>Created At</th>
                                                <th>Updated By</th>
                                                <th>Updated At</th>
                                                <?php if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){ ?>
                                                <th>Action</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if($result3) while ($rowCT = mysqli_fetch_array($result3)) {
                                                    echo "test";
                                                    echo "<tr>";
                                                    echo "<td>".$rowCT['note']."</td>";
                                                    echo "<td>".$rowCT['note_type']."</td>";
                                                    $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowCT['created_by'].'"';
                                                    $users = $DB_admin->query($sqlUSERS);
                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                        echo "<td>".$rowUSERS['username']."</td>";
                                                    }
                                                    echo "<td>".$rowCT['created_at']."</td>";
                                                    $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowCT['updated_by'].'"';
                                                    $users = $DB_admin->query($sqlUSERS);
                                                    if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                        echo "<td>".$rowUSERS['username']."</td>";
                                                    }
                                                    echo "<td>".$rowCT['updated_at']."</td>";
                                                    if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){
                                                    echo "<td align='center'><a href='javascript:;' data-id=".$rowCT['id']." class='butdel'><i class='fas fa-times-circle'></i></a></td>";
                                                    }
                                                    echo "</tr>";
                                               }
                                            ?>
                    					</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
    				<?php
    				//	}
    				?>
    			</div>
    		</div>
        </div>
    </div>
</div>
<?php if($mt42) while ($rowMT42 = mysqli_fetch_array($mt42)) { ?>
    <div style="position: fixed; top: 25px; right: 20px; min-width: 300px; display:none;" id="ttools">
        <div class="toast fade hide dragit" id="changeleverage" data-autohide="false">
            <div class="toast-header bg-white">
                <strong class="mr-auto"><i class="fas fa-balance-scale"></i> Change Leverage</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-white">
                <div class="row">
                    <div class="col-sm-8">
                        <input class="form-control" type="text" id="leverage" placeholder="<?php echo $rowMT42['LEVERAGE']; ?>" value="<?php echo $rowMT42['LEVERAGE']; ?>"/>
                    </div>
                    <div class="col-sm-4">
                        <a class="btn btn-primary changeleverage" href="">Submit</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="toast fade hide dragit" id="changegroup" data-autohide="false">
            <div class="toast-header bg-white">
                <strong class="mr-auto"><i class="fas fa-users-cog"></i> Change Group</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-white">
                <div class="row">
                    <div class="col-sm-8">
                        <select class="form-control" name="group" id="group">
                        <?php
                            
                            $sqlUSER = 'SELECT unit FROM user_extra WHERE user_id = "'.$userID.'"';
                            //echo "<option >".$sqlUSER."</option>";
                            $mt_USER = $DB_admin->query($sqlUSER);
                            
                            if($mt_USER) while ($rowUSER = mysqli_fetch_array($mt_USER)) {
                                $sqlGRP = 'SELECT name FROM mt_groups WHERE unit = "'.$rowUSER['unit'].'"';
                                $mt_groups = $DB_admin->query($sqlGRP);
                                if($mt_groups) while ($rowGRP = mysqli_fetch_array($mt_groups)) {
                                    if($rowMT42['GROUP'] == $rowGRP['name']){
                                        echo "<option value='".$rowGRP['name']."' selected>".$rowGRP['name']."</option>";
                                    } else {
                                        echo "<option value='".$rowGRP['name']."'>".$rowGRP['name']."</option>";
                                    }
                                }
                            }
                        ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <a class="btn btn-primary changegroup" href="">Submit</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="toast fade hide dragit" id="changepassword" data-autohide="false">
            <div class="toast-header bg-white">
                <strong class="mr-auto"><i class="fas fa-lock"></i> Change Password</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-white">
                <div class="row">
                    <div class="col-sm-8">
                        <input class="form-control" type="text" id="password" placeholder="New Password" />
                    </div>
                    <div class="col-sm-4">
                        <a class="btn btn-primary changepassword" href="">Submit</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="toast fade hide dragit" id="changebalance" data-autohide="false">
            <div class="toast-header bg-white">
                <strong class="mr-auto"><i class="fas fa-wallet"></i> Balance Change</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-white">
                <div class="row">
                    <div class="col-sm-12 mb-3">
            			<label for="inputType">Type</label>
            			<select class="form-control" id="Type" name="Type" required>
            			    <option value="Deposit">Deposit</option>
                            <option value="Withdraw">Withdraw</option>
                            <option value="Bonus">Bonus</option>
                            <option value="Credit">Credit</option>
                            <option value="Internal">Internal Transfer</option>
                            <option value="Correction">Trade Correction</option>
                        </select>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputUnit">Amount</label>
            			<input class="form-control" type="text" id="Amount" placeholder="0,00" required/>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputComment">Comment</label>
            			<select class="form-control" id="Comment" name="Comment" required>
            			    <option value="Deposit">Deposit</option>
                            <option value="Withdraw">Withdraw</option>
                            <option value="Deposit Bonus">Deposit Bonus</option>
                            <option value="Withdraw Bonus">Withdraw Bonus</option>
                            <option value="Internal">Internal</option>
                        </select>
            		</div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary changebalance" href="">Submit</a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
$(document).ready( function () {
	
	$('#data-table-deposit').DataTable({  
		"responsive": true,
		"deferRender": true,
		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
		"order": [[ 3, "desc" ]],
    });
    
    $('#data-table-withdraw').DataTable({  
		"responsive": true,
		"deferRender": true,
		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
		"order": [[ 3, "desc" ]],
    });
    
    $('#data-table-history').DataTable({  
		"responsive": true,
		"deferRender": true,
		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
		"order": [[ 7, "desc" ]],
    });
    
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    
	$(function () {
		$('[data-toggle="tooltip"]').tooltip()
	});
	
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
	
	$('.disableac').click(function(e){
	    e.preventDefault();
        $.ajax({
          method: 'post',
          url: 'api-accountstatus.php',
          data: {
            'userId': "<?php echo $mt4_id; ?>",
            'status': "1"
          },
          success: function(data) {
            toastr.success("Account #<?php echo $mt4_id; ?> is Read-Only.", "Account Read-Only");
          }
        }).done(function() {
            $.ajax({
                method: 'post',
                url: 'mt4.php',
                data: {
                    'user_id': "<?php echo $user_ID; ?>",
                    'mt4_id': "<?php echo $mt4_id; ?>",
                    'unit': "<?php echo $_POST['unit']; ?>"
                },
                cache: false,
			    success: function(dataResult){
				    $('#mt4').html(dataResult);
			    }
            });
        });
    });
    
    $('.enableac').click(function(e){
        e.preventDefault();
        $.ajax({
          method: 'post',
          url: 'api-accountstatus.php',
          data: {
            'userId': "<?php echo $mt4_id; ?>",
            'status': "0"
          },
          success: function(data) {
            toastr.success("Account #<?php echo $mt4_id; ?> is able to Trade.", "Account Active");
          }
        }).done(function() {
            $.ajax({
                method: 'post',
                url: 'mt4.php',
                data: {
                    'user_id': "<?php echo $user_ID; ?>",
                    'mt4_id': "<?php echo $mt4_id; ?>",
                    'unit': "<?php echo $_POST['unit']; ?>"
                },
                cache: false,
			    success: function(dataResult){
				    $('#mt4').html(dataResult);
			    }
            });
        });
    });
    
    $('.changeleverage').click(function(e){
        e.preventDefault();
        var leverage = $( "#leverage" ).val();
        $.ajax({
          method: 'post',
          url: 'api-changeleverage.php',
          data: {
            'userId': "<?php echo $mt4_id; ?>",
            'leverage': leverage
          },
          success: function(data) {
            toastr.success("Account #<?php echo $mt4_id; ?> leverage has been changed to 1:"+leverage+".", "Account Enabled");
          }
        }).done(function() {
            $.ajax({
                method: 'post',
                url: 'mt4.php',
                data: {
                    'user_id': "<?php echo $user_ID; ?>",
                    'mt4_id': "<?php echo $mt4_id; ?>",
                    'unit': "<?php echo $_POST['unit']; ?>"
                },
                cache: false,
			    success: function(dataResult){
				    $('#mt4').html(dataResult);
			    }
            });
        });
    });
    
    $('.changegroup').click(function(e){
        e.preventDefault();
        var group = $( "#group" ).val();
        var userId = "<?php echo $mt4_id; ?>";
        $.ajax({
            method: 'post',
            url: 'api-changegroup.php',
            data: {
                'userId': "<?php echo $mt4_id; ?>",
                'group': group
            },
            success: function(data) {
                toastr.success("Account #<?php echo $mt4_id; ?> group has been changed to "+group+".", "Account Enabled");
            }
        }).done(function() {
            $.ajax({
                method: 'post',
                url: 'mt4.php',
                data: {
                    'user_id': "<?php echo $user_ID; ?>",
                    'mt4_id': "<?php echo $mt4_id; ?>",
                    'unit': "<?php echo $_POST['unit']; ?>"
                },
                cache: false,
			    success: function(dataResult){
				    $('#mt4').html(dataResult);
			    }
            });
        });
    });
    
    $('.changepassword').click(function(e){
        e.preventDefault();
        var password = $( "#password" ).val();
        $.ajax({
          method: 'post',
          url: 'api-changepassword.php',
          data: {
            'userId': "<?php echo $mt4_id; ?>",
            'password': password
          },
          success: function(data) {
            $( "#password" ).val("");
            toastr.success("Account #<?php echo $mt4_id; ?> password has been changed to "+password+".", "Password Changed");
          }
        });
    });
    
    $('.changebalance').click(function(e){
        e.preventDefault();
        var amount = $( "#Amount" ).val();
        var type = $( "#Type" ).val();
        var comment = $( "#Comment" ).val();
        $.ajax({
          method: 'post',
          url: 'api-changebalance.php',
          data: {
            'userId': "<?php echo $mt4_id; ?>",
            'Amount': amount,
            'Type': type,
            'Comment': comment,
          },
          success: function(data) {
            $( "#Amount" ).val("");
            toastr.success("Account #<?php echo $mt4_id; ?> had "+amount+"$ "+type+".", "Balance Changed");
          }
        }).done(function() {
            $.ajax({
                method: 'post',
                url: 'mt4.php',
                data: {
                    'user_id': "<?php echo $user_ID; ?>",
                    'mt4_id': "<?php echo $mt4_id; ?>",
                    'unit': "<?php echo $_POST['unit']; ?>"
                },
                cache: false,
			    success: function(dataResult){
				    $('#mt4').html(dataResult);
			    }
            });
        });
    });
    
    $(".show-changeleverage").click(function(){
        if ( $('#ttools').css('display') == 'none' || $('#ttools').css("visibility") == "hidden"){
            $('#ttools').show();
        }
        $("#changeleverage").toast('show');
    });
    
    $(".show-changegroup").click(function(){
        if ( $('#ttools').css('display') == 'none' || $('#ttools').css("visibility") == "hidden"){
            $('#ttools').show();
        }
        $("#changegroup").toast('show');
    });
    
    $(".show-changepassword").click(function(){
        if ( $('#ttools').css('display') == 'none' || $('#ttools').css("visibility") == "hidden"){
            $('#ttools').show();
        }
        $("#changepassword").toast('show');
    });
    
    $(".show-read-only").click(function(){
        if ( $('#ttools').css('display') == 'none' || $('#ttools').css("visibility") == "hidden"){
            $('#ttools').show();
        }
        $("#read-only").toast('show');
    });
    
    $(".show-changebalance").click(function(){
        if ( $('#ttools').css('display') == 'none' || $('#ttools').css("visibility") == "hidden"){
            $('#ttools').show();
        }
        $("#changebalance").toast('show');
    });
    
    var draggable = $('.dragit'); //element 

    draggable.on('mousedown', function(e){
    	var dr = $(this).addClass("drag").css("cursor","move");
    	height = dr.outerHeight();
    	width = dr.outerWidth();
    	ypos = dr.offset().top + height - e.pageY,
    	xpos = dr.offset().left + width - e.pageX;
    	$(document.body).on('mousemove', function(e){
    		var itop = e.pageY + ypos - height;
    		var ileft = e.pageX + xpos - width;
    		if(dr.hasClass("drag")){
    			dr.offset({top: itop,left: ileft});
    		}
    	}).on('mouseup', function(e){
    			dr.removeClass("drag");
    	});
    });
    
    $('#data-table6').DataTable({  
		"responsive": false,
		"deferRender": true,
		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
		"order": [[ 3, "desc" ]],
    });
    
    $('#butsave').on('click', function() {
		$("#butsave").attr("disabled", "disabled");
		var note = $('#note').val();
		var note_type = $('#note_type').val();
		var user_id = $('#user_id').val();
		if(note!="" && note_type!="" && user_id!=""){
			$.ajax({
				url: "note-save.php",
				type: "POST",
				data: {
					note: note,
					note_type: note_type,
					user_id: user_id
				},
				cache: false,
				success: function(dataResult){
					var dataResult = JSON.parse(dataResult);
					if(dataResult.statusCode==200){
						$('#fupForm').find('input:text').val('');
						
						toastr.success('Note added successfully!');
					}
					else if(dataResult.statusCode==201){
					   alert("Error occured!");
					}
					
				}
			});
		}
		else{
			alert('Please fill all the field!');
		}
	});
	
	$('.butdel').on('click', function() {
	    var note_id = "";
	    var user_id = $('#user_id').val();
		note_id =  $(this).attr('data-id');
		if(note_id!=""){
			$.ajax({
				url: "note-delete.php",
				type: "POST",
				data: {
					note_id: note_id,
					user_id: user_id
				},
				cache: false,
				success: function(dataResult){
					var dataResult = JSON.parse(dataResult);
					if(dataResult.statusCode==200){
						toastr.success('Note deleted successfully!');
					}
					else if(dataResult.statusCode==201){
					   alert("Error occured!");
					}
					
				}
			});
		}
		else{
			alert('Something is wrong!');
		}
	});
    
});
</script>

<?php include('includes/script-bottom.php'); ?>

</body>

</html>
<?php
    $DB_mt4->close();
?>