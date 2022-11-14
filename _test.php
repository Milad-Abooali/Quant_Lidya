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
                global $db;
                $mt5api = new mt5API();
                $api_symbol['symbol'] = 'EURUSD';
                $mt5api->get('/api/tick/last', $api_symbol);
                $e = $mt5api->Error;
                $res = $mt5api->Response->answer;

                GF::p($e, 1);
                GF::p($res, 1);

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