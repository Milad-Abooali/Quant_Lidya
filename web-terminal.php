<?php
include('includes/head.php');
global $_L;
?>
    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
<?php

include('includes/css.php');

global $db;
$where = 'broker_id='.Broker['id'];
$units = $db->select('units', $where);

$DateTime =  new DateTime('first day of this month');
$date_start = $_REQUEST['startTime'] ?? $DateTime->format('Y-m-d');
$date_end = $_REQUEST['endTime'] ?? date('Y-m-d');

function checkIfMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

$login_tp = $_REQUEST['login'] ?? '';
$isMobile = 0;
if( isset($_REQUEST['ui']) ) {
    if($_GET['ui']==='mobile') {
        $isMobile = 1;
    }
    else if($_GET['ui']==='desktop') {
        $isMobile = 0;
    }
} else {
    $isMobile = (checkIfMobile())?1:0;
}

?>
    <body>

    <!-- Begin page -->
    <div id="wrapper">

        <?php
        // include('includes/topbar.php');
        // include('includes/sidebar.php');

        /**
         * Escape User Input Values POST & GET
         */
        GF::escapeReq();
        ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class=" ">
            <!-- Start content -->
            <div class=" ">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="page-title-box">
                                <h4 class="page-title">Web Trader</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        <?= $_L->T('Welcome_User_Broker','general', array(htmlspecialchars($_SESSION["username"]), Broker['title'])) ?>
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-sm-6 text-right py-4">
                            <?php if(!checkIfMobile()) { ?>
                                    <a class="btn <?= ($isMobile) ? 'btn-secondary' : 'btn-outline-dark disabled' ?>" href="web-terminal.php?ui=desktop"><i class="fas fa-desktop pr-2"></i> <?= $_L->T('Desktop','device') ?></a>
                                    <a class="btn <?= ($isMobile) ? 'btn-outline-dark disabled' : 'btn-secondary' ?>" href="web-terminal.php?ui=mobile"><i class="fas fa-mobile-alt pr-2"></i> <?= $_L->T('Mobile','device') ?></a>
                                    <span>|</span>
                            <?php } ?>
                                    <a class="btn btn-primary" href="/"><i class="fa fa-home pr-2"></i> <?= $_L->T('Dashboard','sidebar') ?></a>
                                    <a class="btn btn-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> <?= $_L->T('Logout','login') ?></a>
                        </div>
                    </div>


                    <div id=" " class="row">
                 
<div id="webterminal" style="width:100%;height:600px;"></div>
<script type="text/javascript" src="https://trade.mql5.com/trade/widget.js"></script>
<script type="text/javascript"> new MetaTraderWebTerminal( "webterminal", {
     version: 5, 
     <?= ($login_tp) ? ('login: "'. $login_tp.'",') : '' ?>
     servers: ["TradeClanIntLtd-Server"], 
     server: "TradeClanIntLtd-Server", 
     utmCampaign: "", 
     utmSource: "www.tradeclan.by", 
     startMode: "login",
     <?= ($isMobile) ? ('mobile: "'. $isMobile.'",') : '' ?>
    language: "<?= $_L->T('_language_iso2','core') ?>",
     colorScheme: "black_on_white" 
 }
  ); </script>

                    </div>
                </div>
            </div>
        </div>
        <?= factory::footer() ?>
        <?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
            $(document).ready( function () {
 
            });

        </script>

        <?php include('includes/script-bottom.php'); ?>
    </body>
    </html>