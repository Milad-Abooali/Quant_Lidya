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
                                <h4 class="page-title">Download</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?= Broker['title'] ?>
                                    </li>
                                </ol>
                                <!--<div class="state-information d-none d-sm-block">
                                    <div class="state-graph">
                                        <div id="header-chart-1"></div>
                                        <div class="info">Leads 1500</div>
                                    </div>
                                    <div class="state-graph">
                                        <div id="header-chart-2"></div>
                                        <div class="info">Converted 40</div>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                    </div>
                	<div class="card pmd-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
            					        <div class="col-sm-12">
            					           <img alt="MetaTrader 5" src="media/mt5-logo.png" class="img-responsive">
            					           <h2>About MetaTrader 5</h2>
            					       </div>
            					       <div class="col-sm-12">
            					           <p>MetaTrader 5, widely known as MT5, is a stand-alone online trading platform developed by MetaQuotes Software. Trading on MT5 via <?= Broker['title'] ?> provides access to a range of markets and hundreds of different financial instruments, including foreign exchange, commodities, CFDs and indices. It’s completely free to download and provides you with everything you need to both analyse the markets and manage your trades.</p>
            					           <p>The MetaTrader 5 platform offers easy-to-read, interactive charts that allow you to monitor and analyse the markets in real-time. You’ll also have access to more than 30 technical indicators which can help you identify market trends and signals for entry and exit points.</p>
            					           <p>Other benefits of MT5 include its powerful security system and multi-device functionality, which enable you to trade with complete confidence and at your convenience.</p>
            					           <p>Discover better trading today when you combine this cutting-edge platform with <?= Broker['title'] ?>’s outstanding products and services.</p>
            					       </div>
            					       <div class="col-sm-12 mt40">
            					           <a class="btn bg-gradient-primary text-white" href="<?= DOWNLOAD_LINK['MT5'] ?>"><i class="fab fa-windows"></i>&nbsp;&nbsp;Download <?= Broker['title'] ?> MT5</a>
            					       </div>
            					</div>
                                <div id="accordion" class="col-md-6">
                                    <div class="card mb-1 shadow-none">
                                        <div class="card-header p-3" id="headingOne">
                                            <h6 class="m-0 font-size-14">
                                                <a href="#collapseOne" class="text-dark" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne">
                                                    <i class="fab fa-windows"></i>&nbsp;&nbsp;MetaTrader 5 for PC
                                                </a>
                                            </h6>
                                        </div>
    
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-12">
                                                        <p>Download MetaTrader 5 for PC to receive the full technical analysis toolkit. Whether you’re trading forex, commodities or indices, the MT5 platform offers you both back-office reporting functions and front-end terminals. Fast, intuitive and user-friendly, MT5 has everything you need to explore the markets in style.</p>
                                                        <p>Benefits of MT5 for PC include:</p>
                                                        <ul>
                                                            <li>Pioneering Automated Trading system complete with Expert Advisors</li>
                                                            <li>Trading Signals</li>
                                                            <li>Multiple timeframes</li>
                                                            <li>Over 30 technical indicators</li>
                                                            <li>Dynamic security system</li>
                                                            <li>Multi-currency/language support</li>
                                                            <li>MQL4</li>
                                                        </ul>
                                                        <div class="system-req android">
                                                            <a href="<?= DOWNLOAD_LINK['MT5'] ?>" class="btn bg-gradient-primary text-white"><i class="fab fa-windows float-left" style="font-size: 37px;"></i>&nbsp;&nbsp;&nbsp;<strong class="float-right">System Requirements:<br>Windows Vista/7/8</strong></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 d-none d-lg-block">
                                                        <img alt="MetaTrader 5 App for Android" src="media/mt4-pc.png" class="img-responsive">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-1 shadow-none">
                                        <div class="card-header p-3" id="headingTwo">
                                            <h6 class="m-0 font-size-14">
                                                <a href="#collapseTwo" class="text-dark collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="collapseTwo">
                                                    <i class="fab fa-android"></i>&nbsp;&nbsp; MetaTrader 5 for Android
                                                </a>
                                            </h6>
                                        </div>
    
                                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-12">
                                                        <p>Download MT5 on the popular Android operating system to enjoy unlimited market access wherever you are and whatever you’re doing. Available on both <strong>mobile and tablet,</strong> MT5 for Android makes it more convenient than ever to trade.</p>
                                                        <p>Just a few of its features include:</p>
                                                        <ul>
                                                            <li>Instant real-time quotes</li>
                                                            <li>Multiple timeframes</li>
                                                            <li>3 chart types</li>
                                                            <li>Real-time interactive charts</li>
                                                            <li>All trade orders available</li>
                                                            <li>All execution modes available</li>
                                                            <li>Over 30 technical indicators</li>
                                                            <li>Trading history</li>
                                                        </ul>
                                                        <div class="system-req android">
                                                            <a href="https://download.mql5.com/cdn/mobile/mt5/android" class="btn bg-gradient-primary text-white"><i class="fab fa-android float-left" style="font-size: 37px;"></i>&nbsp;&nbsp;&nbsp;<strong class="float-right">System Requirements:<br>Android 2.1 or later, 3G/Wi-Fi</strong></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 d-none d-lg-block">
                                                        <img alt="MetaTrader 5 App for Android" src="media/mt4-app-for-android.png" class="img-responsive">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-1 shadow-none">
                                        <div class="card-header p-3" id="headingThree">
                                            <h6 class="m-0 font-size-14">
                                                <a href="#collapseThree" class="text-dark collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="collapseThree">
                                                    <i class="fab fa-apple"></i>&nbsp;&nbsp;MetaTrader 5 for iOS
                                                </a>
                                            </h6>
                                        </div>
                                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion" style="">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-12">
                                                        <p>Prefer to trade from your iPhone or iPad? Download MT5 for iOS straight from the Apple App Store and let your market journey begin! Access the financial markets at a time and place to suit you and <strong>manage your trading activity with complete ease.</strong></p>
                                                        <p>MT5’s features include:</p>
                                                        <ul>
                                                            <li>Customisable graphics</li>
                                                            <li>All trade orders available</li>
                                                            <li>All execution modes available</li>
                                                            <li>Real-time quotes</li>
                                                            <li>Superior security system</li>
                                                            <li>3 chart types</li>
                                                            <li>More than 30 technical indicators</li>
                                                        </ul>
                                                        <div class="system-req apple">
                                                            <a href="https://apps.apple.com/us/app/metatrader-5/id413251709" class="btn bg-gradient-primary text-white"><i class="fab fa-apple float-left" style="font-size: 37px;"></i>&nbsp;&nbsp;&nbsp;<strong class="float-right">System Requirements:<br>iOS 4.0 or later, 3G/Wi-Fi</strong></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 d-none d-lg-block">
                                                        <img alt="MetaTrader 5 for iOS" src="media/mt4-app-for-ios.png" class="img-responsive">
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

        } );
        </script>
    <?php include('includes/script-bottom.php'); ?>

</body>

</html>