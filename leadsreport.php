<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
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
    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    $sqlSource = 'SELECT lead_src FROM `user_marketing` WHERE lead_src != "" Group By lead_src';
    $resultSource = $DB_admin->query($sqlSource);
    
if ( isset( $_POST['submit'] ) ) {
    $source_leads = $_POST['source'];
    $startTime = $_POST['startTime'];
	$endTime = $_POST['endTime'];
    
    $sqlLeads = 'SELECT 
            Tp.login AS Login, 
            `Source`.lead_src AS `Source`,
            `Source`.lead_camp AS `Campaign`,
            Users.unit AS Unit,
            Users.username AS Email,
            (SELECT SUM(mt5_deals.Profit) FROM lidyapar_mt5.mt5_deals WHERE mt5_deals.Action = "2" AND mt5_deals.Login = Tp.Login AND mt5_deals.Time = Tp.ftd) AS FTD, 
            (SELECT SUM(mt5_deals.Profit) FROM lidyapar_mt5.mt5_deals WHERE mt5_deals.Action = "2" AND mt5_deals.Login = Tp.Login AND mt5_deals.Time != Tp.ftd AND mt5_deals.Profit > "0" AND mt5_deals.Comment Like "%Deposit%") AS RET 
            FROM `tp` AS Tp 
            LEFT JOIN user_marketing AS `Source` ON `Source`.user_id = Tp.user_id
            LEFT JOIN users AS Users ON Tp.user_id = Users.id
            WHERE Tp.ftd BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND `Source`.lead_src LIKE "%'.$source_leads.'%" AND Tp.group_id = "2"';
    //echo $sqlLeads;
    $resultLeads = $DB_admin->query($sqlLeads);
    
    $sqlLeadsT = 'SELECT 
            COUNT(lead_src) as Total
            FROM user_marketing
            WHERE created_at BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND lead_src LIKE "%'.$source_leads.'%"';
    //echo $sqlLeadsT;
    $resultLeadsT = $DB_admin->query($sqlLeadsT);
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
    			<div class="col-sm-12">
    			    <div class="card pmd-card">
    					<div class="card-body">
            			    <form action="" method="post" autocomplete="off">
                            	<div class="form-row">
                            		<div class="form-group col-md-2">
                            			<label for="inputuserId">Source</label>
                            			<select class="form-control" id="source" name="source" required>
                                			<?php
                                			    if($_SESSION["type"] == "Admin"){
                            					    if($resultSource) while ($rowSResult = mysqli_fetch_array($resultSource)) {
                            				?>
                            			        <option value="<?php echo $rowSResult['lead_src']; ?>" <?php if($source_leads == $rowSResult['lead_src']){ echo "selected"; }?>><?php echo $rowSResult['lead_src']; ?></option>
                            			    <?php
                            					    }
                                			    } else {
                                			        if($_SESSION["unitn"] == "1"){
                                			            $src = "TurkNew-FB";
                                			        } else if($_SESSION["unitn"] == "3"){
                                			            $src = "FarsiNew-FB";
                                			        } else if($_SESSION["unitn"] == "4"){
                                			            $src = "ArabicNew-FB";
                                			        } else if($_SESSION["unitn"] == "5"){
                                			            $src = "EnglishNew-FB";
                                			        } else if($_SESSION["unitn"] == "6"){
                                			            $src = "STPLNew-FB";
                                			        }
                                			?>
                            			        <option value="<?php echo $src; ?>" <?php if($source_leads == $src){ echo "selected"; }?>><?php echo $src; ?></option>
                            			    <?php        
                                			    }
                            				?>
                        				</select>
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
        				    <div class="row">
        				        <div class="col-md-12">
        				            <?php
                    					if($resultLeadsT) while ($rowResultT = mysqli_fetch_array($resultLeadsT)) {
                    				?>
                    				    <h6>Total Leads: <?php echo $rowResultT['Total']; ?></h6>
                    				<?php
                    					}
                    				?>   
        				        </div>
            				    <div class="col-md-12">
            				        <table id="data-table-leads" class="table table-hover" style="width: 100%;">  
                                        <thead>
                                            <tr>
                                                <th>Login</th>
                                                <th>Source</th>
                                                <th>Campaign</th>
                                                <th>Unit</th>
                                                <th>Email</th>
                                                <th>FTD</th>
                                                <th>RET</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                            					if($resultLeads) while ($rowResult = mysqli_fetch_array($resultLeads)) {
                            				?>
                                                <tr>
                                                    <td><?php echo $rowResult['Login']; ?></td>
                                                    <td><?php echo $rowResult['Source']; ?></td>
                                                    <td><?php if($rowResult['Campaign'] == ""){ echo "No Campaign"; } else { echo $rowResult['Campaign']; } ?></td>
                                                    <td><?php echo $rowResult['Unit']; ?></td>
                                                    <td><?php echo $rowResult['Email']; ?></td>
                                                    <td><?php if($rowResult['FTD'] == ""){ echo "0"; } else { echo $rowResult['FTD']; } ?></td>
                                                    <td><?php if($rowResult['RET'] == ""){ echo "0"; } else { echo $rowResult['RET']; } ?></td>
                                                </tr>
                                            <?php
                            					}
                            				?>
                    					</tbody>
                    					<tfoot>
                                            <tr>
                                                <th colspan="7"></th>
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
<?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
$(document).ready( function () {
	
	$('#data-table-leads').DataTable({  
		"responsive": true,
		"deferRender": true,
		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
		"order": [[ 4, "desc" ]],
		"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            var tretn = 1;
            var tftdn = 1;
            
            var tret = api
                .column( 6 )
                .data()
                .reduce(function(a,b) {
                    if (a > "0" && b > "0") {
                        tretn++;
                    }
                    return a + b*1; 
                }, 0);
                tret = $.fn.dataTable.render.number( ',', '.', 2).display( tret );
            
            var tftd = api
                .column( 5 )
                .data()
                .reduce(function(a,b) { 
                    if (a > "0" && b > "0") {
                        tftdn++;
                    }
                    return a + b*1; 
                }, 0);
                tftd = $.fn.dataTable.render.number( ',', '.', 2).display( tftd );
                
            // Update footer
            $( api.column( 0 ).footer() ).html(
                '<div class="row text-center"><div class="col-md-6">FTD: $'+tftd+'</div><div class="col-md-6">No: '+tftdn+'</div><div class="col-md-6">Ret: $'+tret+'</div><div class="col-md-6">No: '+tretn+'</div></div>'
            );
        },
    });
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
    
});
</script>

<?php include('includes/script-bottom.php'); ?>

</body>

</html>
<?php
    $DB_mt4->close();
?>