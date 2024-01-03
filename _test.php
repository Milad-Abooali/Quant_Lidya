<?php
######################################################################
#  M | 9:38 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";
    GF::loadCSS('h','assets/css/bootstrap.min.css');
    GF::loadJS('h','assets/js/jquery.min.js',false);
    echo factory::header();
?>
<div class="container-fluid">
    <div class="row m-2">
        <!------------------ MeLog ------------------ -->
        <div class="col-md-6">
            <a href="lib/melog.php" target="_blank">Open In New Tab</a>
            <iframe src="lib/melog.php" title="MELog" style="width:100%;height:450px;"></iframe>
        </div>
        <!------------------ Test Pad -------------- -->
        <div class="col-md-6 alert alert-secondary">
            <h6 class="text-center">Test Pad</h6>
            <?php

                include 'gateways/paynet/main.php';
                $paynet = new paynet('./', 1);

                $order = [
                        'id' => 1500,
                        'amount' => 1.01,
                ];
                $result = $paynet->create_payment_link($order);

                GF::p($result);
            ?>


            <script>

                
            </script>

        </div>
    </div>
    <div class="row m-2">
        <!------------------ Session -------------- -->
        <div class="col-md-6 alert alert-warning">
            <h6 class="text-center">Session</h6>
            <small>
                <img src="<?= $_SESSION['captcha']['image_src'] ?>" >
                <br>
                Error: <?php GF::P($sess->ERROR); ?>
                <br>
                Is Login: <?php GF::P($sess->IS_LOGIN); ?>
                <?php GF::P($_SESSION); ?>
            </small>
        </div>
        <!------------------ Database -------------- -->
        <div class="col-md-6 alert alert-info">
            <h6 class="text-center">Database</h6>
            <small>
                <?php GF::P($db->log()); ?>
            </small>
        </div>
    </div>
</div>
<?= factory::footer() ?>