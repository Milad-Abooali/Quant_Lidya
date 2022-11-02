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
                                <h4 class="page-title">Dashboard</h4>
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


                    <div class="row" id="report-desk-all">
                        <?php
                        if($units) {
                            $data = json_encode(array(
                                'start' => $date_start,
                                'end' => $date_end
                            ));
                            factory::widget('report_desk_all', 'All <small>Units</small>',12,1, $data,'desk-card',0);
                        }
                        ?>
                    </div>
                    <div class="row" id="report-pad">
                        <div id="units-pad" class="col-12">
                            <div class="row">
                              <?php if($units) {
                                  foreach($units as $unit){
                                      $data = json_encode(array(
                                          'start'       => $date_start,
                                          'end'         => $date_end,
                                          'unit_name'   => $unit['name'],
                                          'unit_id'     => $unit['id']
                                      ));
                                      factory::widget('report_desk_card', $unit['name'],3,1, $data,'desk-card',0,0,0);
                                  }
                              } else { ?>
                                  There is not any unit in this broker.
                             <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>

        <?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
            $(document).ready( function () {

                /* Data Picker */
                $(function() {
                    var start = <?= $date_start; ?>;
                    var end = <?= $date_end; ?>;
                    $('#reportrange').daterangepicker({
                        startDate: start,
                        endDate: end,
                        opens: 'left',
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

                /**
                 *  Detail Slider - List Agents
                 */
                let detailPadStatus = false;
                $("body").on("click",'.doA-load-detail', function() {
                    if(detailPadStatus==false) {
                        detailPadStatus = true;
                        $('#units-pad>div>div').removeClass('col-xl-3').addClass('col-xl-12');
                        $('#units-pad').removeClass('col-12').addClass('col-3');
                        $('#report-pad').append('<div class="detail-pad col-md-9"></div>')
                    }
                    let unit_name = $(this).data('unit-name');
                    let unit_id = $(this).data('unit-id');
                    let meta_v = $(this).data('meta-v');
                    let detailVar = {
                        'start'       : '<?= $date_start ?>',
                        'end'         : '<?= $date_end ?>',
                        'unit_name'   : unit_name,
                        'unit_id'     : unit_id,
                        'meta_v'      : meta_v
                    }
                    detailPad = '<div class="card pmd-card"><div class="card-body"><div id="wg-report_desk_detail" data-autoload="0" data-wg="report_desk_detail.php" data-vars=\''+JSON.stringify(detailVar)+'\' data-widget-group="" class="widget row"><div class="widget-header col-12 d-flex justify-content-between"><h6 class="mt-0 m-b-30 header-title float-left">'+unit_name+'<small> Detail</small></h6><span class="float-right"><span class="datetime text-black-50" data-toggle="tooltip" data-placement="top" data-original-title="Updated Time">2021-6-8 3:14:04</span><span class="px-2">|</span><i class="reload fas fa-sync" data-toggle="tooltip" data-placement="top" data-original-title="Reload Widget"></i><i class="hide-detail pl-2 fa fa-window-close fa-1x pt-1 text-danger" data-toggle="tooltip" data-placement="top" data-original-title="Close Widget" style=""></i></span></div><div class="widget-body col-12"></div></div></div></div>';
                    $('.detail-pad').html(detailPad);
                    $('#wg-report_desk_detail .reload').trigger( "click" );
                });
                $("body").on("click",'.hide-detail', function() {
                    detailPadStatus = false;
                    $( ".detail-pad" ).remove();
                    $('#units-pad').removeClass('col-3').addClass('col-12');
                    $('#units-pad>div>div').removeClass('col-xl-12').addClass('col-xl-3');
                });

                /**
                 *  Detail Slider - List Logins
                 */
                $("body").on("click",'.doA-load-logins', function() {
                    let staff_id = $(this).data('staff-id');
                    let unit_name = $(this).data('unit-name');
                    let staff_name = $(this).data('staff-name');
                    let meta_v = $(this).data('meta-v');
                    let detailVar = {
                        'start'      : '<?= $date_start ?>',
                        'end'        : '<?= $date_end ?>',
                        'staff_id'   : staff_id,
                        'unit_name'  : unit_name,
                        'staff_name' : staff_name,
                        'meta_v'     : meta_v
                    }
                    let body =  '<div class="card pmd-card"><div class="card-body"><div id="wg-report_desk_detail_logins" data-autoload="0" data-wg="report_desk_detail_logins.php" data-vars=\''+JSON.stringify(detailVar)+'\' data-widget-group="" class="widget row"><div class="widget-header col-12 d-flex justify-content-between"><h6 class="mt-0 m-b-30 header-title float-left">'+staff_name+' <small>Agent</small></h6><span class="float-right"><span class="datetime text-black-50" data-toggle="tooltip" data-placement="top" data-original-title="Updated Time">2021-6-8 3:14:04</span><span class="px-2">|</span><i class="reload fas fa-sync" data-toggle="tooltip" data-placement="top" data-original-title="Reload Widget"></i><i class="hide-detail pl-2 fa fa-window-close fa-1x pt-1 text-danger" data-toggle="tooltip" data-placement="top" data-original-title="Close Widget" style=""></i></span></div><div class="widget-body col-12"></div></div></div></div>';

                    makeModal('Agent Clients Actions',body,'xl');
                    $('#wg-report_desk_detail_logins .reload').trigger( "click" );

                });
                
                $("body").on("change",'.card-switch', function(e) {
                    let test = e.target.checked;
                    if(test){
                        $(this).parent().find(".default-state").slideUp();
                        $(this).parent().find(".active-state").slideDown();
                    } else {
                        $(this).parent().find(".active-state").slideUp();
                        $(this).parent().find(".default-state").slideDown();
                    }
                });

            });
        </script>

        <?php include('includes/script-bottom.php'); ?>
    </body>
    </html>