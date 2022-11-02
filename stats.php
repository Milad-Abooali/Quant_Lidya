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
	
	$servername = "localhost";
    $username = "lidyapar_mt4";
    $password = "@Sra7689227";
    $dbname = "lidyapar_mt4";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($unit == "Turkish"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "1"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
    
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_TRADES.TICKET as TICKET, MT4_TRADES.COMMENT as COMMENT, MT4_TRADES.CMD as CMD,MT4_TRADES.OPEN_TIME as OPEN_TIME, FORMAT(MT4_USERS.EQUITY,2) as EQUITY, MT4_USERS.LOGIN as LOGIN, MT4_USERS.NAME as NAME, MT4_TRADES.PROFIT AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $conn->query($sql1);
        
    } else if($unit == "Farsi"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "3"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_TRADES.TICKET as TICKET, MT4_TRADES.COMMENT as COMMENT, MT4_TRADES.CMD as CMD,MT4_TRADES.OPEN_TIME as OPEN_TIME, FORMAT(MT4_USERS.EQUITY,2) as EQUITY, MT4_USERS.LOGIN as LOGIN, MT4_USERS.NAME as NAME, MT4_TRADES.PROFIT AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $conn->query($sql1);
    } else if($unit == "Arabic"){
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "4"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_TRADES.TICKET as TICKET, MT4_TRADES.COMMENT as COMMENT, MT4_TRADES.CMD as CMD,MT4_TRADES.OPEN_TIME as OPEN_TIME, FORMAT(MT4_USERS.EQUITY,2) as EQUITY, MT4_USERS.LOGIN as LOGIN, MT4_USERS.NAME as NAME, MT4_TRADES.PROFIT AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $conn->query($sql1);
    } else if($unit == "English") {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "5"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_TRADES.TICKET as TICKET, MT4_TRADES.COMMENT as COMMENT, MT4_TRADES.CMD as CMD,MT4_TRADES.OPEN_TIME as OPEN_TIME, FORMAT(MT4_USERS.EQUITY,2) as EQUITY, MT4_USERS.LOGIN as LOGIN, MT4_USERS.NAME as NAME, MT4_TRADES.PROFIT AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $conn->query($sql1);
    } else if($unit == "STPL") {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups WHERE unit = "6"';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_TRADES.TICKET as TICKET, MT4_TRADES.COMMENT as COMMENT, MT4_TRADES.CMD as CMD,MT4_TRADES.OPEN_TIME as OPEN_TIME, FORMAT(MT4_USERS.EQUITY,2) as EQUITY, MT4_USERS.LOGIN as LOGIN, MT4_USERS.NAME as NAME, MT4_TRADES.PROFIT AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $conn->query($sql1);
    } else if($unit == "All") {
        $sqlMtGroups = 'SELECT GROUP_CONCAT(name) as name FROM mt_groups';
        $mtgroups = $DB_admin->query($sqlMtGroups);
        while($rowGroups = $mtgroups->fetch_assoc()) {
            $unitn = $rowGroups['name'];
        }
        
        $array = str_replace(",", '","', $unitn);
        
        $sql1 = 'SELECT MT4_TRADES.TICKET as TICKET, MT4_TRADES.COMMENT as COMMENT, MT4_TRADES.CMD as CMD,MT4_TRADES.OPEN_TIME as OPEN_TIME, FORMAT(MT4_USERS.EQUITY,2) as EQUITY, MT4_USERS.LOGIN as LOGIN, MT4_USERS.NAME as NAME, MT4_TRADES.PROFIT AS PROFIT FROM MT4_TRADES JOIN MT4_USERS ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN WHERE MT4_USERS.AGENT_ACCOUNT <> "1" AND MT4_USERS.GROUP IN ("'.$array.'") AND MT4_TRADES.OPEN_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
        $result1 = $conn->query($sql1);
    } else {
        
    }
    
    $sql2 = 'SELECT MT4_USERS.LOGIN as LOGIN FROM MT4_USERS WHERE MT4_USERS.LOGIN NOT IN '.$notin.' AND MT4_USERS.GROUP IN '.$in.' GROUP BY LOGIN';
    $result2 = $conn->query($sql2);
    
    $conn->close();

}

if(isset($_POST['submit2'])){
    $unit2 = $_POST['unit2'];
	$startTime2 = $_POST['startTime2'];
	$endTime2 = $_POST['endTime2'];
	
    if ($unit2 == "Turkish"){
        $groups2 = array("KUVVARSTUSD", "KUVVARISLUSD", "KUVVARPLUSD", "KUVVARGOUSD", "KUVVARGOEUR", "KUVHEOPLUSD", "KUVFIXPLUSD", "KUSTDFIXUSD", "KUVVARPLEUR", "KUV2VARPLUSD", "KUV2VARSTDUSD", "KUV2VARSTMREDUS", "KUV2VARGOUSD");
        $exclude2 = array("2402", "2401", "1310074264", "1310158404", "1310087188", "1310079769", "461988", "35", "1310227716", "1310227718", "1310289914", "1310310012");
    } else if($unit2 == "Turkey 2"){
        $groups2 = array("");
        $exclude2 = array("", "1310227716", "1310227718");
    } else if($unit2 == "Farsi"){
        $groups2 = array("KUVIAVIPREBUSD", "KUVIAVIPREBEUR", "KUVISTDUSD", "KUVIESTDUSD", "KUVIAPLREBUSD", "KUVIASTDREBUSD", "KUVIAVIPSFUSD", "KUVIAKLUSD");
        $exclude2 = array("461988", "1310227716", "1310227718" ,"1310317724", "1310081558");
    } else if($unit2 == "Arabic"){
        $groups2 = array("KUVAVARPLUSD", "KUVAVARSTDUSD");
        $exclude2 = array("", "1310227716", "1310227718");
    } else if($unit2 == "English") {
        $groups2 = array("KUVEVARPLUSD", "KUVEVARSTDUSD");
        $exclude2 = array("2402", "2401", "1310074264", "1310158404", "1310087188", "1310079769", "461988", "35", "1310227716", "1310227718");
    } else if($unit2 == "STPL") {
        $groups2 = array("KUV3VARGOUSD", "KUV3VARPLUSD", "KUV3VARSTDUSD", "KUV3VARSFGUSD", "KUV3VARKLUSD");
        $exclude2 = array("1310336969");
    } else if($unit2 == "All") {
        $groups2 = array("KUVFIXPLUSD", "KUVIAKLUSD", "KUVIAVIPSFUSD", "KUVHEOPLUSD", "KUVAVARPLUSD", "KUVVARGOEUR", "KUVAVARSTDUSD", "KUVEVARPLUSD", "KUVEVARSTDUSD", "KUVVARSTUSD", "KUVVARISLUSD", "KUVVARPLUSD", "KUVVARGOUSD", "KUV2VARPLUSD", "KUV2VARSTDUSD", "KUV2VARSTMREDUS", "KUV2VARGOUSD", "KUVIAVIPREBUSD", "KUVISTDUSD", "KUVIESTDUSD", "KUVIAPLREBUSD", "KUVIASTDREBUSD", "KUSTDFIXUSD", "KUVVARPLEUR", "KUV3VARGOUSD", "KUV3VARPLUSD", "KUV3VARSTDUSD", "KUV3VARSFGUSD", "KUV3VARKLUSD");
        $exclude2 = array("2402", "2401", "1310087188", "1310079769", "461988", "35", "1300377707", "1310227716", "1310227718", "1310289914", "1310310012", "1310081558", "1310336969");
    } else {
        
    }
    
    $in2 = '("' . implode('","', $groups2) .'")';
    $notin2 = '("' . implode('","', $exclude2) .'")';

}
 
?>
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
                                        Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?php echo $broker_name;?>
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
    				<div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="units" role="tabpanel" aria-labelledby="units-tab">
            				<div class="card pmd-card">
            					<div class="card-body">
                    			    <form action="" method="post" autocomplete="off">
                                    	<div class="form-row">
                                    		<div class="form-group col-md-3">
                                    			<label for="inputUnit">Unit</label>
                                    			<?php if($_SESSION["type"] == "Admin"){ ?>
                                    			<select class="form-control" id="unit" name="unit" required>
                                    			    <option value="All">All</option>
                                                    <option value="Turkish">Turkey</option>
                                                    <option value="STPL">STPL</option>
                                                    <option value="Farsi">Farsi</option>
                                                    <option value="Arabic">Arabic</option>
                                                    <option value="English">English</option>
                                                </select>
                                                <?php } ?>
                                                <?php if($_SESSION["unit"] == "Turkish"){ ?>
                                                <select class="form-control" id="unit" name="unit" required>
                                                    <option value="Turkey">Turkey</option>
                                                </select>
                                                <?php } ?>
                                                <?php if($_SESSION["unit"] == "STPL"){ ?>
                                                <select class="form-control" id="unit" name="unit" required>
                                                    <option value="STPL">STPL</option>
                                                </select>
                                                <?php } ?>
                                                <?php if($_SESSION["unit"] == "Farsi"){ ?>
                                                <select class="form-control" id="unit" name="unit" required>
                                                    <option value="Farsi">Farsi</option>
                                                </select>
                                                <?php } ?>
                                                <?php if($_SESSION["unit"] == "English"){ ?>
                                                <select class="form-control" id="unit" name="unit" required>
                                                    <option value="English">English</option>
                                                </select>
                                                <?php } ?>
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
                                    		<div class="form-group col-md-3">
                                    		    <label for="input">&nbsp;</label>
                            			        <input class="btn btn-primary form-control" type="submit" name="submit" value="Submit" />
                            			    </div>
                            			</div>
                                    </form>
                                </div>
                    		</div>
                    		<div class="clearfix">&nbsp;</div>
                            <div class="card pmd-card">
            					<div class="card-body">
                                    <?php
                    					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Agent"){
                        					$deposit32 = 0;
                        					$withdraw32 = 0;
                        					$bonusgiven = 0;
                        					$bonusreleased = 0;
                                            if ($result1->num_rows > 0) {
                                                while($row1 = $result1->fetch_assoc()) {
                                                   if($row1['CMD'] == 6){
                                                        if($row1['PROFIT'] >= 0){
                                                            $deposit32 += $row1['PROFIT'];
                                                        } else if($row1['PROFIT'] <= 0) {
                                                            $withdraw32 += $row1['PROFIT'];
                                                        }
                                                    }
                                                }
                                            }
                                    ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span class="bold size-24"><?php echo $unit; ?> - <?php echo $result2->num_rows ?> Clients</span>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <span class="bold size-20"><?php echo $startTime; ?> - <?php echo $endTime; ?></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    Total Deposit: <span class="bold blue size-26"><?php echo number_format($deposit32, 2, '.', ','); ?>$ </span><span class="blue size-14"> (Bonus Given:<?php echo number_format($bonusgiven, 2, '.', ','); ?>$)</span>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    Total Withdraw: <span class="bold red size-26"><?php echo number_format($withdraw32, 2, '.', ','); ?>$ </span><span class="red size-14"> (Bonus Released:<?php echo number_format($bonusreleased, 2, '.', ','); ?>$)</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row mb-0">
                                                        <label for="agent-search" class="col-sm-4 col-form-label">Filter By Agent: </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control mb-2" id="agent-search" placeholder="Agent Name">
                                                        </div>
                                                        <hr>
                                                        <label for="transaction-search" class="col-sm-4 col-form-label">Transaction Type: </label>
                                                        <div class="col-sm-8">
                                                            <select class="form-control" id="transaction-search">
                                                                <option value="">Select</option>
                                                                <option>RED</option>
                                                                <option>FTD</option>
                                                                <option>BONUS</option>
                                                                <option>R-WD</option>
                                                                <option>ZERO</option>
                                                            </select>
                                                            <!--<input type="text" class="form-control" id="transaction-search" placeholder="Type">-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row mb-0">
                                                        <label for="agent-search" class="col-sm-4 col-form-label">MT4 Login: </label>
                                                        <div class="input-group col-sm-8">
                                                            <div class="input-group-prepend" style="height:33px">
                                                                <div class="input-group-text">
                                                                    <input type="checkbox" aria-label="Checkbox for following text input" id="exclude">
                                                                </div>
                                                            </div>
                                                            <input type="text" class="form-control mb-2" id="mt4login-search" placeholder="MT4 Login">
                                                        </div>
                                                        <hr>
                                                        <label for="transaction-search" class="col-sm-4 col-form-label">Ticket Number: </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="ticket-search" placeholder="Ticket Number">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table id="trans-table" class="table table-bordered" width="100%">  
                                                        <thead>  
                                                            <tr>
                                    							<th>Ticket</th>
                                    							<th>Login</th>
                                    							<th>Name</th>
                                                                <th>Type</th>
                                                                <th>Amount</th>
                                                                <th>Comment</th>
                                                                <th>Date</th>
                                                                <th>OWNER</th>
                                                                <th>FTD</th>
                                                            </tr>
                                    					</thead>
                                    					<tbody>
                                                            
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="9"></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <span class="bold size-20">Closed Trades <i class="fas fa-angle-right"></i></span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <?= $_L->T('Total_Swap','statistics') ?>: <span class="bold size-20" id="TotalSwap">00.00</span><span class="bold size-20">$</span>
                                                    </br>
                                                    <?= $_L->T('Total_Commissions','statistics') ?>: <span class="bold size-20">00.00</span><span class="bold size-20">$</span>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    Closed P&L: <span class="bold dark-gray size-26" id="TotalProfit">00.00</span><span class="bold dark-gray size-26">$</span>
                                                    </br>
                                                    Raw P&L: <span class="bold green size-26" id="TotalRawProfit">00.00</span><span class="bold green size-26">$</span>
                                                </div>
                                            </div>
                                            <hr>
                                            
                                            <table id="data-table" class="table table-bordered closedTable" width="100%">
                                                <thead>  
                                                    <tr>  
                            							<th>Order</th>
                                                        <th>Account</th>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Volume</th>
                            							<th>Symbol</th>
                                                        <th>Stop Loss</th>
                            							<th>Take Profit</th>
                            							<th>Open Time</th>
                            							<th>Close Time</th>
                            							<th>Swap</th>
                            							<th>Profit</th>
                            							<th>RET-Owner</th>
                                                    </tr>  
                            					</thead>
                            					<tfoot>
                                                    <tr>
                                                        <th colspan="13"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <span class="bold size-20">Open Trades <i class="fas fa-angle-right"></i></span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <?= $_L->T('Total_Swap','statistics') ?>: <span class="bold size-20" id="OTotalSwap">00.00</span><span class="bold size-20">$</span>
                                                    </br>
                                                    <?= $_L->T('Total_Commissions','statistics') ?>: <span class="bold size-20">00.00</span><span class="bold size-20">$</span>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    Open P&L: <span class="bold dark-gray size-26" id="OTotalProfit">00.00</span><span class="bold dark-gray size-26">$</span>
                                                    </br>
                                                    Raw P&L: <span class="bold green size-26" id="OTotalRawProfit">00.00</span><span class="bold green size-26">$</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <table id="data-table2" class="table table-bordered" width="100%">  
                                                <thead>  
                                                    <tr>  
                            							<th>Order</th>
                                                        <th>Account</th>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Volume</th>
                            							<th>Symbol</th>
                                                        <!--<th>Stop Loss</th>
                            							<th>Take Profit</th>-->
                            							<th>Open Price</th>
                            							<th>Open Time</th>
                            							<!--<th>Close Time</th>-->
                            							<th>Swap</th>
                            							<th>Profit</th>
                                                    </tr>  
                            					</thead>
                            					<tfoot>
                                                    <tr>
                                                        <th colspan="10"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                    <?php
                    					}
                    				?>
                    			</div>
                    		</div>
                    	</div>
                    	<div class="tab-pane fade" id="exposure" role="tabpanel" aria-labelledby="exposure-tab">
            				<div class="card pmd-card">
            					<div class="card-body">
            					    <table id="data-exposure" class="table table-bordered" width="100%">  
                                        <thead>  
                                            <tr>  
                    							<th>Symbol</th>
                                                <th>Total</th>
                                                <th>P&L</th>
                                                <th>Buy</th>
                                                <th>Sell</th>
                                                <th>AVG Price</th>
                                                <th>AVG KAM</th>
                                            </tr>  
                    					</thead>
                                    </table>
            					</div>
            				</div>
                    	</div>
                        <div class="tab-pane fade" id="performance" role="tabpanel" aria-labelledby="performance-tab">
                            <div class="card pmd-card">
            					<div class="card-body">
                    			    <form action="" method="post" autocomplete="off">
                                    	<div class="form-row">
                                    		<div class="form-group col-md-3">
                                    			<label for="inputUnit">Unit</label>
                                    			<?php if($_SESSION["type"] == "Admin"){ ?>
                                    			<select class="form-control" id="unit2" name="unit2" required>
                                    			    <option value="All">All</option>
                                                    <option value="Turkey">Turkey</option>
                                                    <option value="Turkey 2">Turkey 2</option>
                                                    <option value="Farsi">Farsi</option>
                                                    <option value="Arabic">Arabic</option>
                                                    <option value="English">English</option>
                                                </select>
                                                <?php } ?>
                                                <?php if($_SESSION["unit"] == "Turkish"){ ?>
                                                <select class="form-control" id="unit2" name="unit2" required>
                                                    <option value="Turkey">Turkey</option>
                                                </select>
                                                <?php } ?>
                                                <?php if($_SESSION["unit"] == "Turkish2"){ ?>
                                                <select class="form-control" id="unit2" name="unit2" required>
                                                    <option value="Turkey 2">Turkey 2</option>
                                                </select>
                                                <?php } ?>
                                    		</div>
                                    		<div class="form-group col-md-3">
                                    			<label for="inputstartTime">Start Time</label>
                                    			<input type="text" class="form-control" id="startTime2" name="startTime2" required>
                                    		</div>
                                    		<div class="form-group col-md-3">
                                    			<label for="inputendTime">End Time</label>
                                    			<input type="text" class="form-control" id="endTime2" name="endTime2" required>
                                    		</div>
                                    		<div class="form-group col-md-3">
                                    		    <label for="input">&nbsp;</label>
                            			        <input class="btn btn-primary form-control" type="submit" name="submit2" value="Submit" />
                            			    </div>
                            			</div>
                                    </form>
                                </div>
                    		</div>
                    		<div class="clearfix">&nbsp;</div>
                            <div class="card pmd-card">
            					<div class="card-body">
            					    <div class="row">
                                        <div class="col-md-12">
                                            <table id="performance-table" class="table table-bordered" width="100%">  
                                                <thead>  
                                                    <tr>
                            							<th>Ticket</th>
                            							<th>Login</th>
                            							<th>Name</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Comment</th>
                                                        <th>Date</th>
                                                        <th>OWNER</th>
                                                        <th>FTD</th>
                                                        <th>RET-Owner</th>
                                                    </tr>
                            					</thead>
                            					<tbody>
                                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="10"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
            					</div>
            				</div>
                        </div>
                    </div>
    			</div>
    		</div>
    	</div>
    </div>
</div>
<!-- Modal Window content -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
            <div class="modal-body">
                <div class="my-modal-cont">
                     <table id="symbol-positions" class="table table-bordered" width="100%">  
                        <thead>  
                            <tr>  
    							<th>Order</th>
                                <th>Account</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Volume</th>
    							<th>Symbol</th>
                                <th>Stop Loss</th>
    							<th>Take Profit</th>
    							<th>Open Price</th>
    							<th>Open Time</th>
    							<th>Swap</th>
    							<th>Profit</th>
                            </tr>  
    					</thead>
    					<tfoot>
                            <tr>
                                <th colspan="12"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
    </div>
  </div>
</div>
<?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
        var recordsTotalLOT;
        var recordsTotalSWAP;
        var recordsTotalPROFIT;
        
        var recordsTotalLOT2;
        var recordsTotalSWAP2;
        var recordsTotalPROFIT2;
        var RawPL;
        var RawPL2;
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
	$('#startTime2').datepicker({ 
	    uiLibrary: 'bootstrap',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd' 
	});
	$('#endTime2').datepicker({ 
	    uiLibrary: 'bootstrap',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd' 
	});
<?php if(isset($_POST['submit2'])){ ?> 
	var performance = $('#performance-table').DataTable( {
    	//"serverSide": true,
    	"ajax": "server_processing.php?unit=<?php echo $unit2; ?>&startTime=<?php echo $startTime2; ?>&endTime=<?php echo $endTime2; ?>",
    	"order": [[ 7, 'desc' ],[8, 'asc']],
        "deferRender": true,
    	"lengthMenu": [ [ -1, 5, 10, 25, 50,], ["All", 5, 10, 25, 50] ],
    	"rowGroup": {
    	    "emptyDataGroup": 'Desk Manager',
            "startRender": function ( rows, group ) {
                var ftdn = 0;
                var retn = 0;
                var zeron = 0;
                var ftdwdn = 0;
                var retwdn = 0;
                var bonusn = 0;
                var ftda = 0;
                var reta = 0;
                var ftdwda = 0;
                var retwda = 0;
                var bonusa = 0;
                var zeroa = 0;
                var totaln = 0;
                var totalB = 0;
                var BonusAm = 0;
                
                var ftdN = rows
                    .data()
                    .pluck(8)
                    .reduce( function (a, b) {
                        if (a == "FTD" || b == "FTD") {
                            ftdn++;
                            ftda = a;
                        }
                        if (a == "RED" || b == "RED") {
                            retn++;
                            reta = a;
                        }
                        if (a == "R-WD" || b == "R-WD") {
                            retwdn++;
                            retwda = a;
                        }
                        if (a == "FTD-WD" || b == "FTD-WD") {
                            ftdwdn++;
                            ftdwda = a;
                        }
                        if (a == "BONUS" || b == "BONUS") {
                            bonusn++;
                            bonusa = a;
                        }
                        if (a == "ZERO" || b == "ZERO") {
                            zeron++;
                            zeroa = a;
                        }
                        totaln = ftdn+retn+retwdn+ftdwdn+bonusn+zeron;
                        return totaln;
                    }, 0);
                    
 
                var ftd = rows
                    .data()
                    .pluck(4)
                    .reduce( function (a, b) {
                        return a + b*1;
                    }, 0);
 
                return $('<tr/>')
                    .append( '<td colspan="4">'+group+'</td>' )
                    .append( '<td>$'+ftd.toFixed(2)+'</td>' )
                    .append( '<td colspan="4">Transcation No.</td>' )
                    .append( '<td>'+ftdN+'</td>' );
            },
            "dataSrc": [ 7, 8 ]
        },
    	"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            var ftd = 0;
            var ret = 0;
 
            // Total over all pages
            totalDeposit = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotalDeposit = api
                .column( 4, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            // Total over this page
            pageTotalFTD = api
                .column( 8, { search: 'applied'} )
                .data()
                .reduce(function (a, b) {
                    var cur_index = api.column(8).data().indexOf(b);
                    if (api.column(8).data()[cur_index] == "FTD") {
                        ftd++;
                        return ftd;
                    }
                }, 0 );
            
            pageTotalRET = api
                .column( 8, { search: 'applied'} )
                .data()
                .reduce(function (a, b) {
                    var cur_index = api.column(8).data().indexOf(b);
                    if (api.column(8).data()[cur_index] == "RET") {
                        ret++;
                        return ret;
                    }
                }, 0 );
 
            // Update footer
            $( api.column( 0 ).footer() ).html(
                '<div class="row text-center"><div class="col-md-3">Filtered Total In/Out: $'+pageTotalDeposit.toFixed(2)+'</div><div class="col-md-3">Total In/Out: $'+totalDeposit.toFixed(2)+'</div><div class="col-md-3">FTD: '+pageTotalFTD+'</div><div class="col-md-3">RED: '+pageTotalRET+'</div></div>'
            );
        },
        "drawCallback": function( settings ) {
            $(this).find('[role=row]').slideToggle();
            $("#performance-table thead").find('[role=row]').slideToggle();
        }
    });

    $('#performance-table tbody').on( 'click', '.dtrg-level-0', function () {
        $(this).nextUntil('.dtrg-level-0').not('.dtrg-level-1').slideToggle();
    } );
    
    $('#performance-table').on('search.dt', function (e, settings) {
        $(this).find('[role=row]').slideToggle();
        $("#performance-table thead").find('[role=row]').slideToggle();
    });
    
    setInterval( function () {
		performance.ajax.reload( null, false ); // user paging is not reset on reload
	}, 180000 );
<?php } ?>
<?php if(isset($_POST['submit'])){ ?> 
	var transactions = $('#trans-table').DataTable( {
    	//"serverSide": true,
    	"ajax": "server_processing.php?unit=<?php echo $unit; ?>&startTime=<?php echo $startTime; ?>&endTime=<?php echo $endTime; ?>",
        "deferRender": true,
        "order": [ 6, 'desc' ],
    	"lengthMenu": [ [ 5, 10, 25, 50, -1], [ 5, 10, 25, 50, "All"] ],
    	"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            var dptotal = 0;
            var bototal = 0;
            var wdtotal = 0;
            var zerototal = 0;
            
            var ftd = 0;
            var ret = 0;
            var arr = [0];
            var arr1 = [0];
            var arr2 = [0];
            
            var arr3 = [0];
            var arr4 = [0];
            var arr5 = [0];
            
            var arr6 = [0];
            var arr7 = [0];
            
            function getSum(total, num) {
              return total + num;
            }
 
            // Total over all pages
            totalDeposit = api
                .column( 4 )
                .data()
                .each(function(value, index) {
                    if (api.column(8).data()[index] == "FTD" || api.column(8).data()[index] == "RED") {
                        arr.push(parseFloat(value));  
                    } else if (api.column(8).data()[index] == "BONUS") {
                        arr1.push(parseFloat(value));
                    } else if (api.column(8).data()[index] == "ZERO") {
                        arr6.push(parseFloat(value));
                    } else {
                        arr2.push(parseFloat(value));
                    }
                });
 
            // Total over this page
            pageTotalDeposit = api
                .column( 4, { search: 'applied'} )
                .data()
                .each(function(value, index) {
                    if (api.column(8, { search: 'applied'}).data()[index] == "FTD" || api.column(8, { search: 'applied'}).data()[index] == "RED") {
                        arr3.push(parseFloat(value));  
                    } else if (api.column(8, { search: 'applied'}).data()[index] == "BONUS") {
                        arr4.push(parseFloat(value));
                    } else if (api.column(8, { search: 'applied'}).data()[index] == "ZERO") {
                        arr7.push(parseFloat(value));
                    } else {
                        arr5.push(parseFloat(value));
                    }
                });
                
                dptotal = arr.reduce(getSum);
                bototal = arr1.reduce(getSum);
                wdtotal = arr2.reduce(getSum);
                
                dptotal2 = arr3.reduce(getSum);
                bototal2 = arr4.reduce(getSum);
                wdtotal2 = arr5.reduce(getSum);
                
                zerototal = arr6.reduce(getSum);
                zerototal2 = arr7.reduce(getSum);
                //console.log(test);
                //.reduce( function (a, b) {
                //    return intVal(a) + intVal(b);
                //}, 0 );
            
            // Total over this page
            pageTotalFTD = api
                .column( 8, { search: 'applied'} )
                .data()
                .reduce(function (a, b) {
                    var cur_index = api.column(8).data().indexOf(b);
                    if (api.column(8).data()[cur_index] == "FTD" || api.column(8).data()[cur_index] == "RED") {
                        //alert(a);
                        //return intVal(a) + intVal(b);
                        ftd++;
                        return ftd;
                    }
                }, 0 );
            
            pageTotalRET = api
                .column( 8, { search: 'applied'} )
                .data()
                .reduce(function (a, b) {
                    var cur_index = api.column(8).data().indexOf(b);
                    if (api.column(8).data()[cur_index] == "RED") {
                        ret++;
                        return ret;
                    }
                }, 0 );
 
            // Update footer
            $( api.column( 0 ).footer() ).html(
                '<div class="row text-center"><div class="col-md-3">Filtered Deposit: $'+dptotal2.toFixed(2)+'</div><div class="col-md-3">Filtered Withdrawal: $'+wdtotal2.toFixed(2)+'</div><div class="col-md-3">Filtered Bonus: $'+bototal2.toFixed(2)+'</div><div class="col-md-3">Filtered Zero: $'+zerototal.toFixed(2)+'</div></div><hr><div class="row text-center"><div class="col-md-3">Total Deposit: $'+dptotal.toFixed(2)+'</div><div class="col-md-3">Total Withdrawal: $'+wdtotal.toFixed(2)+'</div><div class="col-md-3">Total Bonus: $'+bototal.toFixed(2)+'</div><div class="col-md-3">Total Zero: $'+zerototal.toFixed(2)+'</div></div>'
            );
        },
    });
    
    setInterval( function () {
		transactions.ajax.reload( null, false ); // user paging is not reset on reload
	}, 50000 );
	
	var closed = $('#data-table').DataTable({  
		//"serverSide": true,
		"deferRender": true,
    	//"ajax": "server_processing2.php?unit=<?php echo $unit; ?>&startTime=<?php echo $startTime; ?>&endTime=<?php echo $endTime; ?>",
    	"ajax": {
            "url": "server_processing2.php?unit=<?php echo $unit; ?>&startTime=<?php echo $startTime; ?>&endTime=<?php echo $endTime; ?>",
            "dataSrc": function ( data ) {
                recordsTotalLOT = data.recordsTotalLOT;
                recordsTotalPROFIT = parseFloat(data.recordsTotalPROFIT, 10);
                recordsTotalSWAP = parseFloat(data.recordsTotalSWAP, 10);
                return data.data;
            } 
        },
        "buttons": [
            'excelHtml5'
        ],
        "order": [ 9, 'desc' ],
        "columnDefs": [
            { 
                 "targets": [2],
                 "visible": false,
            },
            { 
                 "targets": [6],
                 "visible": false,
            },
            { 
                 "targets": [7],
                 "visible": false,
            },
        ],
		"responsive": true,
		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
		"search": {
            "regex": true
        },
		
		"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            var totallot = 0;
            var pageTotallot = 0;
            var totalprofit = 0;
            var pageTotalprofit = 0;
            var totalswap = 0;
            var pageTotalswap = 0;
            var pagePL = 0;
            var PL = 0;
 
            // Total over all pages
            totallot = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotallot = api
                .column( 4, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            // Total over all pages
            totalprofit = api
                .column( 11 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotalprofit = api
                .column( 11, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            // Total over all pages
            totalswap = api
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotalswap = api
                .column( 10, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            pagePL = pageTotalprofit + pageTotalswap;
            PL = totalprofit + totalswap;
 
            // Update footer
            $( api.column( 0 ).footer() ).html(
                '<div class="row text-center"><div class="col-md-3">Filtered Traded Lot: '+pageTotallot.toFixed(2)+'</div><div class="col-md-3">Filtered Swap: $'+pageTotalswap.toFixed(2)+'</div><div class="col-md-3">Filtered Profit: $'+pageTotalprofit.toFixed(2)+'</div><div class="col-md-3">Filtered PL: $'+pagePL.toFixed(2)+'</div></div><hr><div class="row text-center"><div class="col-md-3">Total Traded Lot: '+ intVal(recordsTotalLOT / 100).toFixed(2)+'</div><div class="col-md-3"><?= $_L->T('Total_Swap','statistics') ?>: $'+ intVal(recordsTotalSWAP).toFixed(2)+'</div><div class="col-md-3">Total Profit: $'+ intVal(recordsTotalPROFIT).toFixed(2)+'</div><div class="col-md-3">Total PL: $'+ (intVal(recordsTotalPROFIT)+recordsTotalSWAP).toFixed(2)+'</div></div>'
            );
        }
    });
    
    var buttons = new $.fn.dataTable.Buttons(closed, {
        buttons: [
            {
                extend: 'excelHtml5',
                autoFilter: true,
                className: 'btn-sm'
            }
        ]
    }).container().appendTo($('#data-table_filter'));
    //closed.buttons().container().appendTo( '#buttons-closed' );
    
    $('#agent-search').on( 'keyup click', function () {
        
        regex = '\\b' + this.value + '\\b';
        regex2 = '^(?=.*?(' + this.value + ')).*?';
        regex3 = this.value;
        
        transactions
        .column('7')
        .search(regex3, true, false, true )
        .draw();

        closed
        .column('12')
        .search(this.value, true, false, true )
        .draw();
    });
    
    $('#transaction-search').on( 'keyup click', function () {
        
        regex = '\\b' + this.value + '\\b';
        regex2 = '^(?=.*?(' + this.value + ')).*?';
        regex3 = this.value;
        
        transactions
        .column('8')
        .search(regex2, true, false, true )
        .draw();
    });
    
    $('#mt4login-search').on( 'keyup click', function () {
        
        regex = '\\b' + this.value + '\\b';
        regex2 = '^(?!' + this.value + '$).*$';
        regex3 = this.value;
        
        if ($('#exclude').is(':checked')) {
            transactions
            .column('1')
            .search(regex2, true, false, true )
            .draw();
            
            closed
            .column('1')
            .search(regex2, true, false, true )
            .draw();
            
            open
            .column('1')
            .search(regex2, true, false, true )
            .draw();
        } else {
            transactions
            .column('1')
            .search(this.value, true, false, true )
            .draw();
            
            closed
            .column('1')
            .search(this.value, true, false, true )
            .draw();
            
            open
            .column('1')
            .search(this.value, true, false, true )
            .draw();
        }
    });
    
    $('#ticket-search').on( 'keyup click', function () {
        
        regex = '\\b' + this.value + '\\b';
        regex2 = '^(?=.*?(' + this.value + ')).*?';
        regex3 = this.value;
        
        transactions
        .column('0')
        .search(regex2, true, false, true )
        .draw();
        
        closed
        .column('0')
        .search(this.value, true, false, true )
        .draw();
        
        open
        .column('0')
        .search(this.value, true, false, true )
        .draw();
    });
	
    
    setInterval( function () {
		closed.ajax.reload( null, false ); // user paging is not reset on reload
		$( "#TotalSwap" ).html(recordsTotalSWAP.toFixed(2));
		$( "#TotalProfit" ).html(recordsTotalPROFIT.toFixed(2));
		RawPL = parseFloat(recordsTotalPROFIT, 10) + parseFloat(recordsTotalSWAP, 10);
		$( "#TotalRawProfit" ).html(RawPL.toFixed(2));
	}, 10000 );
    
    var open = $('#data-table2').DataTable({  
        //"serverSide": true,
    	"ajax": {
            "url": "server_processing3.php?unit=<?php echo $unit; ?>&startTime=<?php echo $startTime; ?>&endTime=<?php echo $endTime; ?>",
            "dataSrc": function ( data ) {
                recordsTotalLOT2 = parseFloat(data.recordsTotalLOT, 10);
                recordsTotalPROFIT2 = parseFloat(data.recordsTotalPROFIT, 10);
                recordsTotalSWAP2 = parseFloat(data.recordsTotalSWAP, 10);
                return data.data;
            } 
        },
        "deferRender": true,
        "order": [ 7, 'desc' ],
        "columnDefs": [
            { 
                 "targets": [2],
                 "visible": false,
            },
        ],
		"responsive": true,
		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
		"search": {
            "regex": true
        },
		"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            totallot = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotallot = api
                .column( 4, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            // Total over all pages
            totalprofit = api
                .column( 9 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotalprofit = api
                .column( 9, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over all pages
            totalswap = api
                .column( 8 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotalswap = api
                .column( 8, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            pagePL = pageTotalprofit + pageTotalswap;
            PL = totalprofit + totalswap;
 
            // Update footer
            $( api.column( 0 ).footer() ).html(
                '<div class="row text-center"><div class="col-md-3">Filtered Traded Lot: '+pageTotallot.toFixed(2)+'</div><div class="col-md-3">Filtered Swap: $'+pageTotalswap.toFixed(2)+'</div><div class="col-md-3">Filtered Profit: $'+pageTotalprofit.toFixed(2)+'</div><div class="col-md-3">Filtered PL: $'+pagePL.toFixed(2)+'</div></div><hr><div class="row text-center"><div class="col-md-3">Total Traded Lot: '+ intVal(recordsTotalLOT2 / 100).toFixed(2)+'</div><div class="col-md-3"><?= $_L->T('Total_Swap','statistics') ?>: $'+ intVal(recordsTotalSWAP2).toFixed(2)+'</div><div class="col-md-3">Total Profit: $'+ intVal(recordsTotalPROFIT2).toFixed(2)+'</div><div class="col-md-3">Total PL: $'+ (intVal(recordsTotalPROFIT2)+recordsTotalSWAP2).toFixed(2)+'</div></div>'
            );
        }
    });
    
    setInterval( function () {
		open.ajax.reload( null, false ); // user paging is not reset on reload
		$( "#OTotalSwap" ).html(recordsTotalSWAP2.toFixed(2));
		$( "#OTotalProfit" ).html(recordsTotalPROFIT2.toFixed(2));
		RawPL2 = parseFloat(recordsTotalPROFIT2, 10) + parseFloat(recordsTotalSWAP2, 10);
		$( "#OTotalRawProfit" ).html(RawPL2.toFixed(2));
	}, 5000 );
<?php } ?>
	var exposure = $('#data-exposure').DataTable({  
        //"serverSide": true,
    	"ajax": "server_exposure.php",
        "order": [ 1, 'desc' ],
		"responsive": true,
		"lengthMenu": [ [ -1, 5, 10, 25, 50], ["All", 5, 10, 25, 50] ],
		"search": {
            "regex": true
        },
        "columnDefs": [ 
            {
				"targets": 0,
				render: function(data, type, full, meta) {
                    return '<a href="javascript:;" class="positions" data-symbol="'+full[0]+'">'+full[0]+'</a>' ;
                }
            },
            {
                "targets": 1,
    			"render": function ( data, type, row ) {
    			    var exposure;
    			    var direction;
    			    var buy = parseFloat(row[3]);
    			    var sell = parseFloat(row[4]);
                    if(buy > sell) {
                        exposure = buy - sell;
                        direction = "Buy";
                    } else if(buy < sell) {
                        exposure = sell - buy;
                        direction = "Sell";
                    } else {
                        exposure = 0;
                        direction = "";
                    }
                    return exposure.toFixed(2)+' '+direction;
                },
                "type": "numeric-comma"
            },
            {
				"targets": 5,
				"createdCell": function (td, cellData, rowData, row, col) {
					data = parseFloat(cellData);
					$(td).html(data.toFixed(5))
				}
			},
			{
				"targets": 6,
				"createdCell": function (td, cellData, rowData, row, col) {
					data = parseFloat(cellData);
					$(td).html(data.toFixed(5))
				}
			},
			
        ]
    });
    
    setInterval( function () {
		exposure.ajax.reload( null, false ); // user paging is not reset on reload
	}, 5000 );
	
	var symbolpositions = "";

    $("#data-exposure").on('click', '.positions', function(){
        var symbol = $(this).attr('data-symbol');
        $(".modal-title").html('Clients Positions: '+symbol);
        $('#myModal').modal({show:true});
        $('.my-modal-cont').html('<table id="symbol-positions" class="table table-bordered" width="100%"><thead><tr><th>Order</th><th>Account</th><th>Name</th><th>Type</th><th>Volume</th><th>Symbol</th><th>Stop Loss</th><th>Take Profit</th><th>Open Price</th><th>Open Time</th><th>Swap</th><th>Profit</th></tr></thead><tfoot><tr><th colspan="12"></th></tr></tfoot></table>');
        symbolpositions = $('#symbol-positions').DataTable({  
            //"serverSide": true,
        	"ajax": "symbol_positions.php?symbol="+symbol,
            "order": [ 1, 'desc' ],
    		"responsive": true,
    		"lengthMenu": [ [ 5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
    		"search": {
                "regex": true
            },
            "columnDefs": [ 
                
            ],
            "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            totallot = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotallot = api
                .column( 4, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            // Total over all pages
            totalprofit = api
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotalprofit = api
                .column( 11, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over all pages
            totalswap = api
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotalswap = api
                .column( 10, { search: 'applied'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            pagePL = pageTotalprofit + pageTotalswap;
            PL = totalprofit + totalswap;
 
            // Update footer
            $( api.column( 0 ).footer() ).html(
                '<div class="row text-center"><div class="col-md-3">Filtered Traded Lot: '+pageTotallot.toFixed(2)+'</div><div class="col-md-3">Filtered Swap: $'+pageTotalswap.toFixed(2)+'</div><div class="col-md-3">Filtered Profit: $'+pageTotalprofit.toFixed(2)+'</div><div class="col-md-3">Filtered PL: $'+pagePL.toFixed(2)+'</div></div>'
            );
        }
        });
        $('#myModal').on('hidden', function () {
            symbolpositions.destroy();
        });
    });
    
    
    
} );
</script>
<?php include('includes/script-bottom.php'); ?>

</body>

</html>