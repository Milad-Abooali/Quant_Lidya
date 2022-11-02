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

    function delete($id) {
        $isql = new iSQL(DB_admin);
        $isql->deleteId('users',$id);
        $where = 'user_id='.$id;
        $act['data'] = $isql->selectRow('user_extra',$where);
        $isql->deleteAny('user_session',$where);
        $isql->deleteAny('user_marketing',$where);
        $isql->deleteAny('user_gi',$where);
        $isql->deleteAny('user_fx',$where);
        $isql->deleteAny('user_extra',$where);
        // Add actLog
        $act['act'] = "delete";
        global $actLog; $actLog->add('User',$id,1,json_encode($act));
        unset($isql);
        return true;
    }

?>
<div class="container-fluid">
    <div class="row m-2">
        <!------------------ Test Pad -------------- -->
        <div class="col-md-12 alert alert-secondary">
            <h6 class="text-center">Test Pad</h6>

            <?php
                $db = new iSQL(DB_admin);

                $list = [1872,
                        41421,
                        44456,
                        57566,
                        57912,
                        62195,
                        68078,
                        72255,
                        72580,
                        73180,
                        81551,
                        92690,
                        103209,
                        104354,
                        110551,
                        144345,
                        151385,
                        153288,
                        153779,
                        155758,
                        200293,
                        200494,
                        201878,
                        301473,
                        331201,
                        331257,
                        331322,
                        331944,
                        332602,
                        332692,
                        333100,
                        337917,
                        348875,
                        351649,
                        352544,
                        352630,
                        352658,
                        390590,
                        391070,
                        392524,
                        2882383,
                        2883998];

                foreach ($list as $user) delete($user);
                
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