<?php include('includes/head.php'); ?>

    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">

<?php include('includes/css.php'); ?>

<body>

    <!-- Begin page -->
    <div id="wrapper">


<?php 
    require_once "config.php";
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
                                <h4 class="page-title">Chat</h4>
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
                            <div class="col-xl-3 col-lg-4">
                                <div class="card chat-list-card mb-xl-0">
                                    <div class="card-body">
                                        
                                        <div class="media">
                                            <div class="mr-2 align-self-center">
                                                <img src="assets/images/users/user-1.jpg" alt="" class="rounded-circle avatar-sm">
                                            </div>
                                            <div class="media-body">
                                                <h5 class="mt-0 mb-1">Nowak Helme</h5>
                                                <p class="font-13 text-muted mb-0">Admin Head</p>
                                            </div>
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop font-20" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-3">

                                        <ul class="nav nav-pills nav-justified my-3" id="tradingTab" role="tablist">
                                            <li class="nav-item">
                                                <a href="#accountsTab" id="accounts-tab" data-toggle="tab" role="tab" aria-controls="accountsTab" aria-selected="true" class="nav-link active">
                                                    <i class="bx bx-chat font-size-20 d-sm-none"></i>
                                                    <span class="d-none d-sm-block">Accounts</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#watchlistTab" id="watchlist-tab" data-toggle="tab" role="tab" aria-controls="watchlistTab" aria-selected="false" class="nav-link">
                                                    <i class="bx bx-group font-size-20 d-sm-none"></i>
                                                    <span class="d-none d-sm-block">Watch List</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#positionsTab" id="positions-tab" data-toggle="tab" role="tab" aria-controls="positionsTab" aria-selected="false" class="nav-link">
                                                    <i class="bx bx-book-content font-size-20 d-sm-none"></i>
                                                    <span class="d-none d-sm-block">Positions</span>
                                                </a>
                                            </li>
                                        </ul>
                                        
                                        <div class="tab-content" id="tradingTabContent">
                                            <div class="tab-pane fade show active" id="accountsTab" role="tabpanel" aria-labelledby="accountsTab">
                                                <ul class="list-unstyled slimscroll mb-0" style="max-height: 370px;">
                                                    <li class="alert alert-success">
                                                        <a href="#">
                                                            <div class="media">
                                                                <div class="media-body overflow-hidden">
                                                                    <h5 class="font-16 mt-0 mb-1 text-muted">#100001</h5>
                                                                    <div class="row">
                                                                        <p class="mb-0 col-md-6 text-muted">Balance</p>
                                                                        <p class="mb-0 col-md-6 text-muted">Equity</p>
                                                                        <p class="mb-0 col-md-6 bold text-muted">$100,000</p>
                                                                        <p class="mb-0 col-md-6 bold text-muted">$102,500</p>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="row">
                                                                        <p class="mb-0 col-md-6 text-muted">Margin Level</p>
                                                                        <p class="mb-0 col-md-6 text-muted">Free Margin</p>
                                                                        <p class="mb-0 col-md-6 bold text-muted">1000%</p>
                                                                        <p class="mb-0 col-md-6 bold text-muted">$50000</p>
                                                                    </div>
                                                                </div>
                                                                <div class="font-11 border border-success px-2 text-muted" style="position: absolute;right: 10px;">Real</div>
                                                                <div class="font-11 border border-success px-2 py-1 text-success" style="position: absolute;right: 0;bottom: 0;border-bottom: 0px !important;border-right: 0px !important;border-radius: 5px 0 0 0;background: #fff;border: 1px transparent !important;"><i class="fas fa-check"></i></div>
                                                            </div>
                                                        </a>
                                                    </li>
                                                    
                                                    <li class="alert alert-secondary">
                                                        <a href="#">
                                                            <div class="media">
                                                                <div class="media-body overflow-hidden">
                                                                    <h5 class="font-16 mt-0 mb-1 text-muted">#100001</h5>
                                                                    <div class="row">
                                                                        <p class="mb-0 col-md-6 text-muted">Balance</p>
                                                                        <p class="mb-0 col-md-6 text-muted">Equity</p>
                                                                        <p class="mb-0 col-md-6 bold text-muted">$100,000</p>
                                                                        <p class="mb-0 col-md-6 bold text-muted">$102,500</p>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="row">
                                                                        <p class="mb-0 col-md-6 text-muted">Margin Level</p>
                                                                        <p class="mb-0 col-md-6 text-muted">Free Margin</p>
                                                                        <p class="mb-0 col-md-6 bold text-muted">1000%</p>
                                                                        <p class="mb-0 col-md-6 bold text-muted">$50000</p>
                                                                    </div>
                                                                </div>
                                                                <div class="font-11 border border-secondary px-2 text-muted" style="position: absolute;right: 10px;">Demo</div>
                                                                <div class="font-11 border border-success px-2 py-1 text-muted" style="position: absolute;right: 0;bottom: 0;border-bottom: 0px !important;border-right: 0px !important;border-radius: 5px 0 0 0;background: #fff;border: 1px transparent !important;"><i class="fas fa-check"></i></div>
                                                            </div>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <div>
                                                    <b>Note:</b> Please selcet an account before you make the detail requests. 
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="watchlistTab" role="tabpanel" aria-labelledby="watchlistTab">
                                                ....
                                            </div>
                                            <div class="tab-pane" id="positionsTab" role="tabpanel" aria-labelledby="positionsTab">
                                                ....
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-8">
                                <div class="conversation-list-card card">
                                    <div class="card-body">
                                        <div class="media">
                                            <div class="media-body">
                                                <h5 class="mt-0 mb-1 text-truncate">Margaret Clayton</h5>
                                                <p class="font-13 text-muted mb-0"><i class="mdi mdi-checkbox-blank-circle text-success mr-1 font-11"></i> Active</p>
                                            </div>
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop font-20" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                                    <div class="dropdown-divider"></div>
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-3">

                                        <div>
                                            <ul class="conversation-list slimscroll" style="max-height: 410px;">
                                                <li>
                                                    <div class="chat-day-title">
                                                        <span class="title">Today</span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="message-list">
                                                        <div class="chat-avatar">
                                                            <img src="assets/images/users/user-2.jpg" alt="">
                                                        </div>
                                                        <div class="conversation-text">
                                                            <div class="ctext-wrap">
                                                                <span class="user-name">Margaret Clayton</span>
                                                                <p>
                                                                    Hello!
                                                                </p>
                                                            </div>
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle arrow-none card-drop font-20" data-toggle="dropdown" aria-expanded="false">
                                                                    <i class="mdi mdi-dots-vertical"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                                                                </div>
                                                            </div>
                                                            <span class="time">10:00</span>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li class="odd">
                                                    <div class="message-list">
                                                        <div class="chat-avatar">
                                                            <img src="assets/images/users/user-1.jpg" alt="">
                                                        </div>
                                                        <div class="conversation-text">
                                                            <div class="ctext-wrap">
                                                                <span class="user-name">Nowak Helme</span>
                                                                <p>
                                                                    Hi, How are you? What about our next meeting?
                                                                </p>
                                                            </div>
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle arrow-none card-drop font-20" data-toggle="dropdown" aria-expanded="false">
                                                                    <i class="mdi mdi-dots-vertical"></i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                                                                </div>
                                                            </div>
                                                            <span class="time">10:01</span>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li>
                                                    <div class="message-list">
                                                        <div class="chat-avatar">
                                                            <img src="assets/images/users/user-2.jpg" alt="">
                                                            
                                                        </div>
                                                        <div class="conversation-text">
                                                            <div class="ctext-wrap">
                                                                <span class="user-name">Margaret Clayton</span>
                                                                <p>
                                                                    Yeah everything is fine
                                                                </p>
                                                            </div>
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle arrow-none card-drop font-20" data-toggle="dropdown" aria-expanded="false">
                                                                    <i class="mdi mdi-dots-vertical"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                                                                </div>
                                                            </div>
                                                            <span class="time">10:03</span>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="message-list">
                                                        <div class="chat-avatar">
                                                            <img src="assets/images/users/user-2.jpg" alt="male">
                                                            
                                                        </div>
                                                        <div class="conversation-text">
                                                            <div class="ctext-wrap">
                                                                <span class="user-name">Margaret Clayton</span>
                                                                <p>
                                                                    & Next meeting tomorrow 10.00AM
                                                                </p>
                                                            </div>
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle arrow-none card-drop font-20" data-toggle="dropdown" aria-expanded="false">
                                                                    <i class="mdi mdi-dots-vertical"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                                                                </div>
                                                            </div>
                                                            <span class="time">10:03</span>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li class="odd">
                                                    <div class="message-list">
                                                        <div class="chat-avatar">
                                                            <img src="assets/images/users/user-1.jpg" alt="">
                                                        </div>
                                                        <div class="conversation-text">
                                                            <div class="ctext-wrap">
                                                                <span class="user-name">Nowak Helme</span>
                                                                <p>
                                                                    Wow that's great
                                                                </p>
                                                            </div>
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle arrow-none card-drop font-20" data-toggle="dropdown" aria-expanded="false">
                                                                    <i class="mdi mdi-dots-vertical"></i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <!-- item-->
                                                                    <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                                                                </div>
                                                            </div>
                                                            <span class="time">10:04</span>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="p-3 conversation-input border-top">
                                        <div class="row">
                                            <div class="col">
                                                <div>
                                                    <input type="text" class="form-control" placeholder="Enter Message...">
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-primary chat-send width-md waves-effect waves-light"><span class="d-none d-sm-inline-block mr-2">Send</span> <i class="mdi mdi-send"></i></button>
                                            </div>
                                        </div>
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