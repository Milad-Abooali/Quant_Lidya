<?php

include('includes/head.php');

?>
    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.css">
<?php

include('includes/css.php');

global $db;
$where = 'broker_id='.Broker['id'];
$units = $db->select('units', $where);

$DateTime =  new DateTime('first day of this month');
$date_start = $_REQUEST['startTime'] ?? $DateTime->format('Y-m-d');
$date_end = $_REQUEST['endTime'] ?? date('Y-m-d');


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
                                <h4 class="page-title">Test Pad</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?php echo Broker['title'];?>
                                    </li>
                                </ol>
                                <div class="state-information d-none d-sm-block">
                                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span><?php echo $date_start." - ".$date_end; ?></span> <i class="fa fa-caret-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="#taskBox" class="row">

                        <?php
                        factory::widget('tasks', '<span id="widgetScroll" data-size="400"></span>Tasks <small>manager</small> / <span class="badge badge-sm bg-gradient-primary align-middle font-size-60p pl-1 text-white" id="new_task">New Task</span>',6,1);
                        ?>

                    </div>
                </div>
            </div>
        </div>
        <?= factory::footer() ?>
        <?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script src="assets/plugins/summernote/summernote-bs4.min.js"></script>
        <script>
            $(document).ready( function () {

                /* Data Picker */
                $(function() {
                    var start = <?= $date_start; ?>;
                    var end = <?= $date_end; ?>;
                    $('#reportrange').daterangepicker({
                        startDate: start,
                        endDate: end,
                        "showDropdowns": true,
                        "timePicker": true,
                        ranges: {
                            'Today': [moment(), moment().add(1, 'days')],
                            'Yesterday': [moment().subtract(1, 'days'), moment()],
                            'Last 7 Days': [moment().subtract(8, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(31, 'days'), moment()],
                            'Last 90 Days': [moment().subtract(91, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month').add(1, 'days')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month').add(1, 'days')],
                            'Last 3 Month': [moment().subtract(3, 'month').startOf('month'), moment().endOf('month').add(1, 'days')],
                            'Total': [moment().subtract(300, 'month').startOf('month'), moment().endOf('month').add(1, 'days')]
                        }
                    }, function(start, end, label){
                        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                        window.location.href = "reports_agents.php?startTime="+start.format('YYYY-MM-DD')+"&endTime="+end.format('YYYY-MM-DD');
                    });
                    
                });
            });

        </script>

        <?php include('includes/script-bottom.php'); ?>
    </body>
    </html>