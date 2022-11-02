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

    $db = new iSQL(DB_admin);

    session_write_close();
?>
<div class="container-fluid">
    <div class="row m-2">
        <!------------------ Test Pad -------------- -->
        <div class="col-md-12 alert alert-secondary">
            <h6 class="text-center">Test Pad</h6>

            <?php
                $users_logins = $db->query('SELECT user_id, GROUP_CONCAT(login) logins FROM tp GROUP BY user_id');
                if($users_logins) foreach ($users_logins as $user){
                    $update['logins'] = $user['logins'];
                    $db->updateId('user_extra',$user['user_id'],$update);
                }
                echo '<hr>'.count($users_logins).'<br>';
            ?>

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