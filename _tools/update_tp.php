<?php
######################################################################
#  M | 9:38 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "../config.php";
    GF::loadCSS('h','../assets/css/bootstrap.min.css');
    GF::loadJS('h','../assets/js/jquery.min.js',false);
    if($_sys_header) echo $_sys_header;
?>
<div class="container-fluid">
    <div class="row m-2">
        <!------------------ Test Pad -------------- -->
        <div class="col-md-12 alert alert-secondary">
            <h6 class="text-center">Test Pad</h6>
            <?php

            $id = $_GET['id'] ?? false;
            global $db;

            if($id){
                $start_id = $id-25;
                $where = "`id` BETWEEN $start_id AND $id";
                $tps = $db->select('tp', $where);
                if($tps) {
                    foreach ($tps as $tp) {
                        $userManager = new userManager();
                        $data = $userManager->getCustom($tp['user_id'],'retention,conversion,affiliate');
                        GF::p($data);
                        GF::updateFtd($tp['user_id'], $data['extra']['retention'], $data['extra']['conversion'], null, null, $data['marketing']['affiliate']);
                    }
                    $js_sleep = 50;
                } else {
                    $js_sleep = 10;
                }
            } else {
                $tp_last_id = $db->selectRow('tp',null,'`id` DESC')['id'];
                echo $tp_last_id;
                $js_sleep = 75;
            }

            ?>
            <script>
                setTimeout(() => {
                    window.location = "update_tp.php?id=<?= $start_id ?? $tp_last_id+1 ?>";
                }, <?= $js_sleep ?>);
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
<?php if($_sys_footer) echo $_sys_footer; ?>