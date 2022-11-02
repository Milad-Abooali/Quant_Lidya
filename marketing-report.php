<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

include('includes/head.php');

$resultSource = $db->select('marketing_report', 0, 'src', 0, 0, 'src');

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

                        <!-- Deposit Monthly - Widget -->
                        <?php factory::widget('marketing_month', 'Deposit <small>Monthly</small>') ?>

                        <!-- Deposit Annual - Widget -->
                        <?php factory::widget('marketing_year', 'Deposit <small>Annual</small>') ?>

                        <!-- Comparision Fixed - Widget -->
                        <?php factory::widget('marketing_comparisionFixed', 'Comparision <small>Fixed</small>',12) ?>

                        <!-- Comparision Mixed - Widget -->
                        <?php factory::widget('marketing_comparisionMixed', 'Comparision <small>Mixed</small>',12) ?>

                        <!-- Table Deeply - Widget -->
                        <?php factory::widget('marketing_tableDeep', 'Deposit Report <small>Deeply</small>',12) ?>

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