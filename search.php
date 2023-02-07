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

        $search = ($_POST['search']) ?? $_GET['s'];
        $search = trim($search);
        $search = preg_replace('/[\x00-\x1F\x7F]/u', '', $search);

        $filter = ($_POST['filter']) ?? $_GET['f'];
        $_SESSION['M']['search_filter'] = $filter;

        if($_SESSION["type"] == "Manager") {
            $perm_where = ' AND ue.unit = "'.$_SESSION["unitn"].'"';
        } else if($_SESSION["type"] == "Sales Agent") {
            $perm_where = ' AND ue.conversion = "'.$_SESSION["id"].'" AND ue.type = "1"';
        } else if($_SESSION["type"] == "Retention Agent") {
            $perm_where = ' AND ue.retention = "'.$_SESSION["id"].'" AND ue.type IN ("2","3")';
        } else if($_SESSION["type"] == "Admin") {
            $perm_where = null;
        }


        switch ($filter) {
            case 'tp':
                $sql ='
                        SELECT GROUP_CONCAT(tp.login) login,
                            u.email as email,
                            ue.type as type,
                            ue.fname as fname,
                            ue.lname as lname,
                            ue.phone as phone,
                            ue.user_id as id
                        FROM user_extra ue
                        JOIN users u ON u.id = ue.user_id
                        JOIN tp ON tp.user_id = ue.user_id
                        WHERE 1 '.$perm_where.'
                        GROUP BY u.email,
                            ue.type,
                            ue.fname,
                            ue.lname,
                            ue.phone,
                            ue.user_id
                        HAVING login like "%'.$search.'%"
                    ';
                break;
            case 'phone':
                $sql ='
                        SELECT GROUP_CONCAT(tp.login) login,
                            u.email as email,
                            ue.type as type,
                            ue.fname as fname,
                            ue.lname as lname,
                            ue.phone as phone,
                            ue.user_id as id
                        FROM user_extra ue
                        JOIN users u ON u.id = ue.user_id
                        LEFT JOIN tp ON tp.user_id = ue.user_id
                        WHERE ue.phone like "%'.$search.'%" '.$perm_where.'
                        GROUP BY u.email,
                            ue.type,
                            ue.fname,
                            ue.lname,
                            ue.phone,
                            ue.user_id
                    ';
                break;
            case 'email':
                $sql ='
                        SELECT GROUP_CONCAT(tp.login) login,
                            u.email as email,
                            ue.type as type,
                            ue.fname as fname,
                            ue.lname as lname,
                            ue.phone as phone,
                            ue.user_id as id
                        FROM user_extra ue
                        JOIN users u ON u.id = ue.user_id
                        LEFT JOIN tp ON tp.user_id = ue.user_id
                        WHERE u.email like "%'.$search.'%" '.$perm_where.'
                        GROUP BY u.email,
                            ue.type,
                            ue.fname,
                            ue.lname,
                            ue.phone,
                            ue.user_id
                    ';
                break;
            case 'name':
                $sql ='                
                        SELECT GROUP_CONCAT(tp.login) login,
                            u.email as email,
                            ue.type as type,
                            ue.fname as fname,
                            ue.lname as lname,
                            ue.phone as phone,
                            ue.user_id as id
                        FROM user_extra ue
                        JOIN users u ON u.id = ue.user_id
                        LEFT JOIN tp ON tp.user_id = ue.user_id
                        WHERE CONCAT(ue.fname, " ", ue.lname) like "%'.$search.'%" '.$perm_where.'
                        GROUP BY u.email,
                            ue.type,
                            ue.fname,
                            ue.lname,
                            ue.phone,
                            ue.user_id
                        ';
                break;
            case 'all':
                $sql = '
                        SELECT GROUP_CONCAT(tp.login) login,
                            u.email as email,
                            ue.type as type,
                            ue.fname as fname,
                            ue.lname as lname,
                            ue.phone as phone,
                            ue.user_id as id
                        FROM user_extra ue
                        JOIN users u ON u.id = ue.user_id
                        LEFT JOIN tp ON tp.user_id = ue.user_id
                        WHERE u.email like "%'.$search.'%" '.$perm_where.'
                        GROUP BY u.email,
                            ue.type,
                            ue.fname,
                            ue.lname,
                            ue.phone,
                            ue.user_id
                        UNION DISTINCT
                        SELECT GROUP_CONCAT(tp.login) login,
                            u.email as email,
                            ue.type as type,
                            ue.fname as fname,
                            ue.lname as lname,
                            ue.phone as phone,
                            ue.user_id as id
                        FROM user_extra ue
                        JOIN users u ON u.id = ue.user_id
                        LEFT JOIN tp ON tp.user_id = ue.user_id
                        WHERE CONCAT(ue.fname, " ", ue.lname) like "%'.$search.'%" '.$perm_where.'
                        GROUP BY u.email,
                            ue.type,
                            ue.fname,
                            ue.lname,
                            ue.phone,
                            ue.user_id
                        UNION DISTINCT
                        SELECT GROUP_CONCAT(tp.login) login,
                            u.email as email,
                            ue.type as type,
                            ue.fname as fname,
                            ue.lname as lname,
                            ue.phone as phone,
                            ue.user_id as id
                        FROM user_extra ue
                        JOIN users u ON u.id = ue.user_id
                        LEFT JOIN tp ON tp.user_id = ue.user_id
                        WHERE ue.phone like "%'.$search.'%" '.$perm_where.'
                        GROUP BY u.email,
                            ue.type,
                            ue.fname,
                            ue.lname,
                            ue.phone,
                            ue.user_id
                        UNION DISTINCT
                        SELECT GROUP_CONCAT(tp.login) login,
                            u.email as email,
                            ue.type as type,
                            ue.fname as fname,
                            ue.lname as lname,
                            ue.phone as phone,
                            ue.user_id as id
                        FROM user_extra ue
                        JOIN users u ON u.id = ue.user_id
                        JOIN tp ON tp.user_id = ue.user_id
                        WHERE 1 '.$perm_where.'
                        GROUP BY u.email,
                            ue.type,
                            ue.fname,
                            ue.lname,
                            ue.phone,
                            ue.user_id
                        HAVING login like "%'.$search.'%"
                    ';
                break;
        }

        if($sql) $result = $DB_admin->query($sql);
        if($result) $count = mysqli_num_rows ($result);
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

                                    <div class="row">

                                        <div class="col-md-4">
                                            <span class="bold size-20">Search Results for "<span role="button" class="text-primary cursor-pointer" onclick="location.reload();"><?= $search ?></span>"  <?= ($filter!='all') ? "In ".strtoupper($filter) : null ?>: <span class="text-success"><?= ($count) ?? 0 ?></span> </span>

                                        <br><small id="FilterDesc" class="alert-light text-danger"></small>
                                        </div>

                                        <div class="col-md-4 text-right">
                                            <label>Filter Type</label><br>
                                            <button class="filterType btn btn-sm btn-light fa fa-angle-right" data-filter="t-All"> ALL</button>
                                            <button class="filterType btn btn-sm btn-dark" data-filter="t-Staff"> Staff</button>
                                            <button class="filterType btn btn-sm btn-info" data-filter="t-IB"> IB</button>
                                            <button class="filterType btn btn-sm btn-secondary" data-filter="t-Leads"> Leads</button>
                                            <button class="filterType btn btn-sm btn-warning" data-filter="t-Trader"> Trader</button>
                                        </div>

                                        <?php if($_SESSION["type"] == "Admin") { ?>
                                            <div class="col-md-4 text-right">
                                                <label>Filter Unit</label><br>
                                                <button class="filterUnit btn btn-sm btn-light fa fa-angle-right" data-filter="u-All"> ALL</button>
                                                <button class="filterUnit btn btn-sm btn-outline-secondary" data-filter="u-Arabic"> Arabic</button>
                                                <button class="filterUnit btn btn-sm btn-outline-secondary" data-filter="u-English"> English</button>
                                                <button class="filterUnit btn btn-sm btn-outline-secondary" data-filter="u-Farsi"> Farsi</button>
                                                <button class="filterUnit btn btn-sm btn-outline-secondary" data-filter="u-Farsi2"> Farsi2</button>
                                                <button class="filterUnit btn btn-sm btn-outline-secondary" data-filter="u-STPL"> STPL</button>
                                                <button class="filterUnit btn btn-sm btn-outline-secondary" data-filter="u-Turkish"> Turkish</button>
                                            </div>
                                        <?php } ?>

                                    </div>
                                    <hr>
                                    <div id="resp" class="row">

                                        <?php
                                        $countF = array();
                                        global $userManager;
                                        if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent"  ) {
                                            if($result) while ($rowResult = mysqli_fetch_array($result)) {
                                                $unit = factory::getCustomDataByID ($rowResult['id'], 'unit')['unit'];
                                                $status_id = $userManager->getCustom ($rowResult['id'], 'status')['extra']['status'];
                                                $sql = "SELECT status FROM status WHERE id = ".$status_id;
                                                if($sql) $result_status = $DB_admin->query($sql);
                                                if($result_status) while ($rowResultStatus = mysqli_fetch_array($result_status)) { 
                                                    $status = $rowResultStatus['status'];
                                                }
                                                $countF['Unit'][$unit]++;
                                                if($rowResult['type'] == "1"){
                                                    $Leads_count++;
                                                    $type = "Leads";
                                                    $card = "text-white bg-secondary";
                                                } else if($rowResult['type'] == "2"){
                                                    $Trader_count++;
                                                    $type = "Trader";
                                                    $card = "text-white bg-warning";
                                                } else if($rowResult['type'] == "3"){
                                                    $IB_count++;
                                                    $type = "IB";
                                                    $card = "text-white bg-info";
                                                } else {
                                                    $Staff_count++;
                                                    $type = "Staff";
                                                    $card = "text-white bg-dark";
                                                }
                                                $countF['Type'][$type]++;

                                                ?>
                                                <div class="col-lg-3 itemW col-md-6 t-All t-<?= $type ?> u-All u-<?= $unit; ?>">

                                                    <div class="card <?php echo $card; ?> text-center item">
                                                        <div class="card-header">
                                                            <?php if(isset($_GET['duplicate'])){ ?>
                                                            <style>
                                                                #resp .item {height: 350px!important;}
                                                                #resp .selected{box-shadow: inset -1px 1px 20px 20px;opacity: 0.5;}
                                                            </style>
                                                                <button data-id="<?= $rowResult['id'] ?>" class="doA-select-keep btn btn-outline-dark">Select to Keep</button>
                                                            <hr>
                                                            <?php } ?>
                                                            <?php echo $type; ?>
                                                            <span class="float-right badge-pill bg-dark text-success">
                                                                <?= $unit; ?>
                                                            </span>
                                                            <span class="float-left badge-pill bg-dark text-white">
                                                                <small><?= $status; ?></small>
                                                            </span>
                                                        </div>
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?php echo $rowResult['fname']." ".$rowResult['lname']; ?></h5>
                                                            <div class="card-text"><?php echo $rowResult['email']; ?></div>
                                                            <div class="card-text">Phone: <?php echo $rowResult['phone']; ?></div>
                                                            <hr>
                                                            <?php echo "<span class='badge badge-primary'>".str_replace(",", "</span> <span class='badge badge-danger'>", $rowResult['login'])."</span>"; ?>
                                                        </div>
                                                        <div class="card-footer text-muted row mx-2">
                                                            <div class="admin-tools col text-left">
                                                            <?php if($_SESSION['type']=='Admin'){ ?>
                                                                <button data-id="<?= $rowResult['id'] ?>" class="btn btn-sm bg-gradient-danger text-white doA-deleteUser mx-1">Delete</button>
                                                                <a href="javascript:;" class="doA-addMerge btn bg-gradient-light btn-sm" data-userid="<?php echo $rowResult['id']; ?>">Merge</a>
                                                            <?php } ?>
                                                            </div>
                                                            <div class="col text-right">
                                                                <a href="javascript:;" class="btn btn-primary btn-sm detail" data-user="<?php echo $rowResult['id']; ?>"><i class="fas fa-user-check"></i> View</a>
                                                                <a href="javascript:;" class="btn btn-primary btn-sm edit" data-user="<?php echo $rowResult['id']; ?>"><i class="fas fa-user-edit"></i> Edit</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
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

                    <?php if(isset($_GET['duplicate'])){ ?>
                        $("body").on('click', '.doA-select-keep', function(){
                            $('.card').addClass('selected');
                            $(this).closest('.card').removeClass('selected');

                            $('.doA-merge-duplicates').addClass('doA-select-keep btn-outline-dark');
                            $('.doA-merge-duplicates').html('Select to Keep');
                            $('.doA-merge-duplicates').removeClass('doA-merge-duplicates btn-success');

                            $(this).removeClass('doA-select-keep btn-outline-dark');
                            $(this).addClass('doA-merge-duplicates btn-success');
                            $(this).html('Start Merge ');
                        });
                    $("body").on('click', '.doA-merge-duplicates', function(){
                        let data = {
                            id: $(this).data('id'),
                            target: '<?= $_GET['s'] ?>',
                            type: '<?= $_GET['f'] ?>'
                        }
                        const r = confirm("Are you sure ti merge (notes) for these clients and delete them?");
                        if (r === true) {
                            ajaxCall('users', 'mergeDuplicates', data, function (response) {
                                let resObj = JSON.parse(response);
                                if (resObj.e) {
                                    toastr.error("Error on request !");
                                } else if (resObj.res) {
                                    toastr.success("Your request has been done.");
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1950);
                                }
                            });
                        }
                    });




                    <?php } ?>

                    /* Filter Counter */
                    $('button[data-filter="u-Turkish"]').append(' (<?= $countF['Unit']['Turkish'] ?? 0 ?>)');
                    $('button[data-filter="u-STPL"]').append(' (<?= $countF['Unit']['STPL'] ?? 0 ?>)');
                    $('button[data-filter="u-Farsi2"]').append(' (<?= $countF['Unit']['Farsi2'] ?? 0 ?>)');
                    $('button[data-filter="u-Farsi"]').append(' (<?= $countF['Unit']['Farsi'] ?? 0 ?>)');
                    $('button[data-filter="u-English"]').append(' (<?= $countF['Unit']['English'] ?? 0 ?>)');
                    $('button[data-filter="u-Arabic"]').append(' (<?= $countF['Unit']['Arabic'] ?? 0 ?>)');
                    $('button[data-filter="t-Staff"]').append(' (<?= $countF['Type']['Staff'] ?? 0 ?>)');
                    $('button[data-filter="t-IB"]').append(' (<?= $countF['Type']['IB'] ?? 0 ?>)');
                    $('button[data-filter="t-Leads"]').append(' (<?= $countF['Type']['Leads'] ?? 0 ?>)');
                    $('button[data-filter="t-Trader"]').append(' (<?= $countF['Type']['Trader'] ?? 0 ?>)');

                    /* Filter RESP */
                    var filterType = 't-All';
                    $("body").on('click', '.filterType', function(){
                        $('.filterType').removeClass('fa fa-angle-right');
                        $(this).addClass('fa fa-angle-right');
                        filterType = $(this).data('filter');
                        filterRESP(filterType,filterUnit);
                    });
                    var filterUnit = 'u-All';
                    $("body").on('click', '.filterUnit', function(){
                        $('.filterUnit').removeClass('fa fa-angle-right');
                        $(this).addClass('fa fa-angle-right');
                        filterUnit = $(this).data('filter');
                        filterRESP(filterType,filterUnit);
                    });
                    function filterRESP(type,unit) {
                        $('#resp .itemW').fadeOut();
                        $('#resp .'+type+'.'+unit).fadeIn('fast');
                        <?php if($_SESSION["type"] == "Admin") { ?>
                        if (type.substring(2) != unit.substring(2)) {
                            $('#FilterDesc').html('Filter "'+unit.substring(2)+'" units And "'+type.substring(2)+'"');
                        } else {
                            $('#FilterDesc').html(' ');
                        }
                        <?php } else { ?>
                        if (type != unit) $('#FilterDesc').html('Filter "'+type.substring(2)+'"');
                        <?php } ?>
                    }


                    $(function () {
                        $('[data-toggle="tooltip"]').tooltip()
                    });

                    $("#resp").on('click', '.detail', function(){
                        var val = $(this).attr('data-user');
                        var valuser = '<?php echo htmlspecialchars($_SESSION["username"]); ?>';
                        var url = "user-details.php?code="+val;
                        $('.my-modal-cont').load(url,function(result){
                            $(".modal-title").html('Leads Details');
                            $('#myModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                            $('#myModal').modal({show:true});
                        });
                    });
                    $("#resp").on('click', '.edit', function(){
                        var val2 = $(this).attr('data-user');
                        var valuser2 = '<?php echo htmlspecialchars($_SESSION["username"]); ?>';
                        var url = "user-edit.php?code="+val2;
                        $('.my-modal-cont').load(url,function(result){
                            $(".modal-title").html('Are You Sure? '+valuser2);
                            $('#myModal .modal-footer').html('<button type="button" class="btn btn-primary" id="saveUser" disabled>Save</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                            $('#myModal').modal({show:true});
                        });
                    });
                    
                    
                    $("body").on("click touchstart", '#media-table .mverify', function () {
                		let id =  $(this).data('id');
                		let status = $(this).data('status');
                		$.ajax({
                			url: "verify_media.php",
                			type: "POST",
                			data: {
                				id: id,
                				status: status
                			},
                			cache: false,
                			success: function(dataResult){
                			    //console.log(dataResult);
                				toastr.success("Status has been updated.");
                			}
                		});
                	});
                	

                
                    $("body").on("click touchstart", '#adminTools .doA-agree', function() {
                        let data = {
                            id: $(this).data('id'),
                            status: $(this).data('status')
                        }
                        const r = confirm("Change Agreement Status?");
                        if (r === true) {
                            ajaxCall('global', 'agreeSet', data, function (response) {
                                let resObj = JSON.parse(response);
                                if (resObj.e) {
                                    toastr.error("Error on request !");
                                } else if (resObj.res) {
                                    toastr.success("Status has been update.");
                                }
                            });
                        }
                    });
                    
                    $("body").on("click touchstart", '#adminTools .doA-allow', function() {
                        let data = {
                            id: $(this).data('id'),
                            status: $(this).data('status')
                        }
                        const r = confirm("Allow E-Book Download?");
                        if (r === true) {
                            ajaxCall('global', 'allowSet', data, function (response) {
                                let resObj = JSON.parse(response);
                                if (resObj.e) {
                                    toastr.error("Error on request !");
                                } else if (resObj.res) {
                                    toastr.success("Status has been update.");
                                }
                            });
                        }
                    });
                    
                } );
            </script>
            <?php include('includes/script-bottom.php'); ?>

    </body>

    </html>
<?php
$DB_admin->close();
?>