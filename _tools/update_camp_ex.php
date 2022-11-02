<?php
######################################################################
#          M | 2:58 PM Friday, December 31, 2021
#          Fix Campaign
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

                $users = [
                    [
                    'aboukaddour@hotmail.com','25٪ منحة'
                    ],[
                    'ahmad.helo@hotmail.com','25٪ منحة'
                    ],[
                    'alialican310@gmail.com','%25 Bonus'
                    ],[
                    'aliassri05@gmail.com','25٪ منحة'
                    ],[
                    'aryaazad44@gmail.com','50$ بونوس'
                    ],[
                    'av.dilekoksuz@gmail.com','%25 Bonus'
                    ],[
                    'ayhanuzun1964@hotmail.com','%25 Bonus'
                    ],[
                    'bach.chaaya@hotmail.com','50 دولار اضافية'
                    ],[
                    'bahtiyarbozkurt8@gmail.com','%25 Bonus'
                    ],[
                    'binnazyaprakuprak@gmail.com','50$ Ekstra'
                    ],[
                    'bkorkmaz@moneyss.com','%25 Bonus'
                    ],[
                    'bkorkmazz@moneyss.com','%35 Bonus'
                    ],[
                    'buyukfiratm@gmail.com','%25 Bonus'
                    ],[
                    'canbarim1967@gmail.com','50$ Ekstra'
                    ],[
                    'canhib@gmail.com','50$ Ekstra'
                    ],[
                    'carbogamustafa718@gmail.com','%25 Bonus'
                    ],[
                    'cemilbaba.cs@gmail.com','%25 Bonus'
                    ],[
                    'chianian4@gmail.com','%25 بونوس'
                    ],[
                    'chianianjery@gmail.com','%25 بونوس'
                    ],[
                    'drkenanmehmetali@gmail.com','50$ Ekstra'
                    ],[
                    'emadmasri75@outlook.com','50 دولار اضافية'
                    ],[
                    'faresalhallab@gmail.com','25٪ منحة'
                    ],[
                    'fatih_teke59@hotmail.com','%25 Bonus'
                    ],[
                    'ferdi07@outlook.com','50$ Ekstra'
                    ],[
                    'ffff122456090@gmail.com','25٪ منحة'
                    ],[
                    'fkrtgln@gamil.com','%25 Bonus'
                    ],[
                    'gizemleather07@hotmail.com','%25 Bonus'
                    ],[
                    'goksu_se@hotmail.com','50$ Ekstra'
                    ],[
                    'guzel@gmail.com','50$ Ekstra'
                    ],[
                    'h.i.kurt@hotmail.com','%35 Bonus'
                    ],[
                    'hassan.serhan@icloud.com','30٪ منحة'
                    ],[
                    'hibayar16@gmail.com','%40 Bonus'
                    ],[
                    'holtaismail@gmail.com','%25 Bonus'
                    ],[
                    'i.gul67@hotmail.com','%40 Bonus'
                    ],[
                    'ibo803434@gmail.com','%25 Bonus'
                    ],[
                    'ibr.hsn@hotmail.com','25٪ منحة'
                    ],[
                    'ilyaskahramanoglu1993@gmail.com','%35 Bonus'
                    ],[
                    'imad.farhat85@gmail.com','25٪ منحة'
                    ],[
                    'info@kalimanews.com','25٪ منحة'
                    ],[
                    'jomjom_flames@hotmail.com','25٪ منحة'
                    ],[
                    'kam7504@yahoo.com','%25 بونوس'
                    ],[
                    'kurkcu@gmail.com','%25 Bonus'
                    ],[
                    'kyanyj@gmail.com','%25 بونوس'
                    ],[
                    'kyanyj@gmail.com','%25 Bonus'
                    ],[
                    'meral_gorur@hotmail.com','%25 Bonus'
                    ],[
                    'mert.enerji76@gmail.com','%25 Bonus'
                    ],[
                    'mhamadameer95@gmail.com','25٪ منحة'
                    ],[
                    'mucahit0744@gmail.com','%40 Bonus'
                    ],[
                    'muratavanos@hotmail.com','%25 Bonus'
                    ],[
                    'muraterdogmus45@gmail.com','%45 Bonus'
                    ],[
                    'onderbuz@hotmail.com','%25 Bonus'
                    ],[
                    'ozgurcakir67744@gmail.com','50$ Ekstra'
                    ],[
                    'ram_love_85@hotmail.com','25٪ منحة'
                    ],[
                    'sabanates@hotmail.com','%25 Bonus'
                    ],[
                    'sabanatesw@hotmail.com','%25 Bonus'
                    ],[
                    'sametbeykonak@gmail.com','%25 Bonus'
                    ],[
                    'samicanayan@gmail.com','50$ Ekstra'
                    ],[
                    'selam-82@hotmail.com','%25 Bonus'
                    ],[
                    'semanuraslan4545@gmail.com','%25 Bonus'
                    ],[
                    'seyyedi2006@gmail.com','%25 بونوس'
                    ],[
                    'sohachem@gmail.com','35٪ منحة'
                    ],[
                    'tlgylmazzz@gmail.com','%25 Bonus'
                    ],[
                    'tosa1tosa@yahoo.com','25٪ منحة'
                    ],[
                    'viyaboat@gmail.com','%25 Bonus'
                    ],[
                    'ykp.dmr.02@gmail.com','%25 Bonus'
                    ],[
                    'youssefamer98@outlook.com','25٪ منحة'
                    ],[
                    'zein43215@gmail.com','25٪ منحة'
                    ]
                ];
                global $db;
                $userManager = new userManager();
                
                foreach($users as $user){
                        $email = $user[0];
                        $where = "username='$email' ";
                        $user_id = $db->selectRow('users', $where)['id'];
                        if($user_id ){
                            $main_camp = $userManager->getCustom($user_id, 'lead_camp')['marketing']['lead_camp'] ?? false;
                            $ex_camp = $userManager->getCustom($user_id, 'campaign_extra')['marketing']['campaign_extra'];
                            $ex_camp = explode(",", $ex_camp);
                            $camp = 'Wheel - '.$user[1];
                            if($main_camp){
                                if(!in_array($camp, $ex_camp)){
                                    $sql = "UPDATE user_marketing SET campaign_extra=CONCAT(campaign_extra,',','$camp') WHERE user_id=$user_id;";
                                }else{
                                    $sql = 'select 1';
                                }
                            } else {
                               $sql =  "UPDATE user_marketing SET lead_camp ='$camp' WHERE  user_id=$user_id;";
                            }
                             // $db->run($sql);
                             echo $user_id.'|'.$user[0].' > Main ? '.boolval($main_camp).'<br>'.$sql.'<hr>';
                        }
                }
            
            
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