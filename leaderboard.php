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
    require_once "config.php";
    include('includes/topbar.php');
    include('includes/sidebar.php');
    
    if ($_SESSION["type"] == "Admin") {
        $sql = "SELECT CONCAT(fname,' ',lname) agent_name, user_id agent_id FROM `user_extra` WHERE type IN (9,6,8) AND status != 9";
    } else if ($_SESSION["type"] !== "Leads" && $_SESSION["type"] !== "IB" && $_SESSION["type"] !== "Trader") {
        $sql = "SELECT CONCAT(fname,' ',lname) agent_name, user_id agent_id FROM `user_extra` WHERE type IN (9,6,8) AND unit = '".$_SESSION["unitn"]."' AND status != 9";
    }
    
    $result = $DB_admin->query($sql);
?>
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <audio id="myAudio">
        <source src="assets/clap.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
    		<div class="row">
    			<div class="col-lg-12">
    				<div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Leader Board</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> to <?php echo Broker['title'];?>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                       <style>
                            #ranking-wrapper {
                            
                            }
                            .border-card {
                                position: absolute;
                                width: 98%;
                            }
                        </style>
                        <div id="ranking-wrapper" class="col-md-12">
                        
 
                            
                            <?php 
                                $i=1;
                                if($result) while ($rowResult = mysqli_fetch_array($result)) {
                            ?>
                                <div class="border-card" data-rank="<?= $i ?>" data-agent="<?= $rowResult['agent_id'] ?>">
                                    <div class="card-type-icon mr-3 text-center">
                                        <span class="display-5 rank">
                                        <?php
                                            if ($i===1) {
                                                echo '<i class="rank-1"></i>';
                                            } else if($i===2) {
                                                echo '<i class="rank-2"></i>';
                                            } else if($i===3) {
                                                echo '<i class="rank-3"></i>';
                                            } else {
                                                echo $i;
                                            }
                                            $i++;
                                        ?>
                                        </span>
                                    </div>
                                    <div class="content-wrapper row">
                                        <div class="label-group col-md-3">
                                            <p class="title agent_name"><?= $rowResult['agent_name'] ?></p>
                                            <p class="caption">Sales Agent</p>
                                        </div>
                                        <div class="label-group col-md-3">
                                            <p class="title count"><?= $rowResult['count'] ?></p>
                                            <p class="caption">FTD</p>
                                        </div>
                                        <div class="label-group col-md-3">
                                            <p class="title amount">$<?= $rowResult['amount'] ?></p>
                                            <p class="caption">FTD Amount</p>
                                        </div>

                                        <div class="label-group col-md-1 rank-angle">
                                            <small>-</small>
                                        </div>                                                                              
                                    </div>
                                </div>
                            <?php } ?>
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
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
    <?php include('includes/script-bottom.php'); ?>

    
    <script>


        var cardHeight = 68;

        /**
         * OnLoad Position
         */
        $( "div.border-card" ).each(function( index ) {
            let oldRank = $(this).data('rank');
            $('#ranking-wrapper').animate({height: '+='+cardHeight}, 1);
            let moveY = (cardHeight*oldRank) - cardHeight;
            $(this).animate({top: moveY+'px'}, 1);
            $(this).fadeIn();;
        });
        
        
        /**
         * Auto Call Ajax
         */
        let autoCall = setInterval(() => { updateRank(true);  },1000*8);
        
        /**
         * Func ...
         */
        updateRank();
        function updateRank(claping=false){
            ajaxCall('global','leaderBoardFTD', '', function(bridgeStatus){
                let resObj = JSON.parse(bridgeStatus);
                $( "div.border-card" ).each(function( index ) {
                    let agent = $(this).data('agent');
                    let newData = resObj.res[agent];
                    let oldRank = $(this).data('rank');
                    let change = newData.rank-oldRank;
                    if(oldRank < newData.rank) {
                        $(this).find(".rank-angle").html('<i class="fa fa-2x fa-caret-down text-danger"></i> <span style="color: #d6d6d6;"class="display-6">'+(change*(-1))+'</class>');
                        $(this).addClass('rank-down');
                        let moveY =(newData.rank-oldRank)*cardHeight;
                        $(this).animate({top: '+='+moveY}, moveY);
                    } else if (oldRank > newData.rank) {
                        if(claping){
                            var clapMusic = document.getElementById("myAudio"); 
                            clapMusic.play(); 
                            
                            var end = Date.now() + (10 * 1000);
                            // go Buckeyes!
                            var colors = ['#bb0000', '#ffffff', '#58db83', '#FBC02D'];
                            
                            (function frame() {
                              confetti({
                                particleCount: 4,
                                angle: 60,
                                spread: 55,
                                origin: { x: 0 },
                                colors: colors
                              });
                              confetti({
                                particleCount: 4,
                                angle: 120,
                                spread: 55,
                                origin: { x: 1 },
                                colors: colors
                              });
                            
                              if (Date.now() < end) {
                                requestAnimationFrame(frame);
                              }
                            }());
                        }
                        $(this).find(".rank-angle").html('<i class="fa fa-2x fa-caret-up text-success"></i> <span style="color: #d6d6d6;"class="display-6">'+(change*(-1))+'</class>');
                        $(this).addClass('rank-up');
                        let moveY =(oldRank-newData.rank)*cardHeight;
                        $(this).animate({top: '-='+moveY}, moveY);
                        
                    } else {
                        // $(this).find(".rank-angle").html('<small>-</small>');
                        $(this).addClass('rank-fix');
                    }
                    
                    if (newData.rank===1) {
                        $(this).find(".rank").html('<i class="rank-1"></i>');
                    } else if(newData.rank===2) {
                        $(this).find(".rank").html('<i class="rank-2"></i>');
                    } else if(newData.rank===3) {
                        $(this).find(".rank").html('<i class="rank-3"></i>');
                    } else {
                        $(this).find(".rank").html(newData.rank);
                    }
                    
                    
                    $(this).data('rank',newData.rank);
                    $(this).find(".agent_name").html(newData.agent_name);
                    $(this).find(".count").html(newData.count);
                    $(this).find(".amount").html('$'+newData.amount);
                    
                    setTimeout(() => {
                        $(this).removeClass('rank-down rank-up rank-fix');
                    }, 1800);

                   // clearInterval(autoCall);
                });
            });
        }
    </script>
</body>
</html>