<?php

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
                                <h4 class="page-title"><?= $_L->T('My_Wallet','wallet') ?></h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        <?= $_L->T('Welcome_User_Broker','general', array(htmlspecialchars($_SESSION["username"]), Broker['title'])) ?>
                                    </li>
                                </ol>
                                <div class="state-information d-none d-sm-block">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-6">
                            <!-- Comparision Fixed - Widget -->
                            <?php factory::widget('wallet_balance', $_L->T('Wallet','wallet').' <small>'.$_L->T('Balance','trade').'</small>',12, 1) ?>
                            <?php factory::widget('wallet_transfer', $_L->T('Wallet','wallet').' <small>'.$_L->T('Transfer','wallet').'</small>',12, 1) ?>
                        </div>
                        <div class="col-6">
                            <div id="payment-info" class="card" style="overflow-y: scroll;max-height: 621px;">

                            </div>
                        </div>

                        <?php factory::widget('wallet_requests', $_L->T('Wallet','wallet').' <small>'.$_L->T('Requests','sidebar').'</small>',12, 1) ?>
                        <?php factory::widget('wallet_transactions', $_L->T('Wallet','wallet').' <small>'.$_L->T('Transactions','transactions').'</small>',12, 1) ?>

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

                ajaxCall("gateway", "paymentInfo", {'lang':'<?= $_L->T('_language_iso2','core') ?>'}, function(response) {
                    $('#payment-info').html(response);
                });

            });
        </script>

        <?php include('includes/script-bottom.php'); ?>
    </body>
    </html>