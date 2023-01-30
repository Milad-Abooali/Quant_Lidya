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
        <!------------------ Test Pad ---------------->
        <div class="col-md-12 alert alert-secondary">
            <h6 class="text-center">Test Pad</h6>
            <?php

            $mt4_count = $mt5_count = 0;
            $mt4_skip = $mt5_skip = 0;
            $userManager = new userManager();

            // date("Y-m-d")
            $day = $_GET['day'] ?? false;


            $uid = $_GET['uid'] ?? false;

            if($day) {
                $c_date = date('Y-m-d', strtotime("-$day day"));

                /**
                 * MT4 Database
                 */

                /* Swap */
                $_db_mt4_ps = new iSQL(DB_mt4);
                $query = "SELECT LOGIN, SUM(PROFIT), SUM(SWAPS), count(LOGIN) FROM lidyapar_mt4.MT4_TRADES WHERE CMD IN (0,1) And DATE(`CLOSE_TIME`) = '$c_date' GROUP BY LOGIN";
                $_db_mt4_ps->LINK->query($query, MYSQLI_ASYNC);

                /* Bonus In */
                $_db_mt4_bins = new iSQL(DB_mt4);
                $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                          AND COMMENT NOT IN (
                        	'Deposit Wire Transfer',
                			'Deposit Credit Card',
                			'Deposit',
                			'zeroutbonus',
                			'zerooutbonus',
                			'Zerout') AND PROFIT>0 AND DATE(`CLOSE_TIME`) = '$c_date' GROUP BY LOGIN";
                $_db_mt4_bins->LINK->query($query, MYSQLI_ASYNC);

                /* Bonus Out */
                $_db_mt4_bout = new iSQL(DB_mt4);
                $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                          AND COMMENT NOT IN (
                        	'Withdrawal Wire Transfer',
                			'Withdrawal Credit Card',
                			'Withdrawal',
                			'Account Transfer') AND PROFIT<0 AND DATE(`CLOSE_TIME`) = '$c_date' GROUP BY LOGIN";
                $_db_mt4_bout->LINK->query($query, MYSQLI_ASYNC);

                /* Withdrawal */
                $_db_mt4_w = new iSQL(DB_mt4);
                $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                          AND COMMENT IN (
                        	'Withdrawal Wire Transfer',
                			'Withdrawal Credit Card',
                			'Withdrawal',
                			'Account Transfer') AND PROFIT<0 AND DATE(`CLOSE_TIME`) = '$c_date' GROUP BY LOGIN";
                $_db_mt4_w->LINK->query($query, MYSQLI_ASYNC);

                /* Deposit */
                $_db_mt4_d = new iSQL(DB_mt4);
                $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                          AND COMMENT IN (
                        	'Deposit Wire Transfer',
                			'Deposit Credit Card',
                			'Deposit') AND PROFIT>0 AND DATE(`CLOSE_TIME`) = '$c_date' GROUP BY LOGIN";
                $_db_mt4_d->LINK->query($query, MYSQLI_ASYNC);

                /* Zeroing */
                $_db_mt4_z = new iSQL(DB_mt4);
                $query = "SELECT LOGIN, SUM(PROFIT), count(LOGIN) FROM MT4_TRADES WHERE CMD=6 
                          AND COMMENT IN (
                        	'zeroutbonus',
                			'zerooutbonus',
                			'Zerout') AND PROFIT>0 AND DATE(`CLOSE_TIME`) = '$c_date' GROUP BY LOGIN";
                $_db_mt4_z->LINK->query($query, MYSQLI_ASYNC);

                /* Login Groups */
                $_db_mt4_groups = new iSQL(DB_mt4);
                $query = "SELECT `LOGIN`,`GROUP` FROM MT4_USERS WHERE `AGENT_ACCOUNT`!=1";
                $_db_mt4_groups->LINK->query($query, MYSQLI_ASYNC);

                /* Res - Profit Swap */
                $_db_mt4_ps_res = $_db_mt4_ps->LINK->reap_async_query();

                /* Res - Bonus In */
                $_db_mt4_bins_res = $_db_mt4_bins->LINK->reap_async_query();

                /* Res - Bonus Out */
                $_db_mt4_bout_res = $_db_mt4_bout->LINK->reap_async_query();

                /* Res - Withdrawal */
                $_db_mt4_w_res = $_db_mt4_w->LINK->reap_async_query();

                /* Res - Deposit */
                $_db_mt4_d_res = $_db_mt4_d->LINK->reap_async_query();

                /* Res - Zeroing */
                $_db_mt4_z_res = $_db_mt4_z->LINK->reap_async_query();

                /* Res - Login Groups  */
                $_db_mt4_groups_res = $_db_mt4_groups->LINK->reap_async_query();

                $max_row_mt4 = max(array(
                    $_db_mt4_ps_res->num_rows,
                    $_db_mt4_bins_res->num_rows,
                    $_db_mt4_bout_res->num_rows,
                    $_db_mt4_w_res->num_rows,
                    $_db_mt4_d_res->num_rows,
                    $_db_mt4_z_res->num_rows
                ));


                $tps_mt4=array();
                for($i=1; $i<=$max_row_mt4; $i++) {

                    /* Profit & Swap */
                    $ps = $_db_mt4_ps_res->fetch_row();
                    if($ps[0]){
                        $tps_mt4[$ps[0]]['profit'] = $ps[1];
                        $tps_mt4[$ps[0]]['swap'] = $ps[2];
                        $tps_mt4[$ps[0]]['trades_count'] = $ps[3];
                    }

                    /* Bonus In */
                    $bin = $_db_mt4_bins_res->fetch_row();
                    if($bin[0]) {
                        $tps_mt4[$bin[0]]['bonus_in'] = $bin[1];
                        $tps_mt4[$bin[0]]['bonus_in_count'] = $bin[2];
                    }

                    /* Bonus Out */
                    $bout = $_db_mt4_bout_res->fetch_row();
                    if($bout[0]) {
                        $tps_mt4[$bout[0]]['bonus_out'] = $bout[1];
                        $tps_mt4[$bout[0]]['bonus_out_count'] = $bout[2];
                    }

                    /* Withdrawal */
                    $w = $_db_mt4_w_res->fetch_row();
                    if($w[0]) {
                        $tps_mt4[$w[0]]['withdrawal'] = $w[1];
                        $tps_mt4[$w[0]]['withdrawal_count'] = $w[2];
                    }

                    /* Deposit */
                    $d = $_db_mt4_d_res->fetch_row();
                    if($d[0]) {
                        $tps_mt4[$d[0]]['deposit'] = $d[1];
                        $tps_mt4[$d[0]]['deposit_count'] = $d[2];
                    }

                    /* Zeroing */
                    $z = $_db_mt4_z_res->fetch_row();
                    if($z[0]) {
                        $tps_mt4[$z[0]]['zeroing'] = $z[1];
                        $tps_mt4[$z[0]]['zeroing_count'] = $z[2];
                    }

                }

                $list_login_mt4 = implode(',', array_keys($tps_mt4));
                if($list_login_mt4 ?? false){
                    $where = 'group_id=2 AND login IN ('.$list_login_mt4.')';
                    $user_tps_mt4 = $db->select('tp', $where);
                }
                if($_db_mt4_groups_res) $login_groups_mt4 = array_column($_db_mt4_groups_res->fetch_all(),1,0);

                if($user_tps_mt4 ?? false) foreach($user_tps_mt4 as $user_tp){

                    if($uid){
                        if($user_tp['user_id']==$uid) {

                            $tps_mt4[$user_tp['login']]['retention_id'] = $user_tp['retention'];
                            $tps_mt4[$user_tp['login']]['conversion_id'] = $user_tp['conversion'];

                            if(date("Y-m-d",strtotime($user_tp['ftd'])) == $c_date ) {
                                $tps_mt4[$user_tp['login']]['ret_amount'] = $tps_mt4[$user_tp['login']]['deposit'] - $user_tp['ftd_amount'];
                                $tps_mt4[$user_tp['login']]['ftd_amount'] = $user_tp['ftd_amount'];
                            } else {
                                $tps_mt4[$user_tp['login']]['ret_amount'] = $tps_mt4[$user_tp['login']]['deposit'];
                                $tps_mt4[$user_tp['login']]['ftd_amount'] = 0;
                            }

                            $tps_mt4[$user_tp['login']]['ib_id'] = $user_tp['ib'];

                            $tps_mt4[$user_tp['login']]['user_id'] = $user_tp['user_id'];
                            $user_data = $userManager->getCustom($user_tp['user_id'],'email,unit');
                            $tps_mt4[$user_tp['login']]['email'] = $user_data['email'];
                            $tps_mt4[$user_tp['login']]['unit'] = $user_data['unit'];
                            $tps_mt4[$user_tp['login']]['login'] = $user_tp['login'];
                            $tps_mt4[$user_tp['login']]['day'] = $c_date;

                            /*
                             *  ALTER TABLE `tp_report_mt4` ADD UNIQUE `login_day`(`login`, `day`);
                             */
                            if($login_groups_mt4[$user_tp['login']] ?? false){
                                $tps_mt4[$user_tp['login']]['mt4_group'] = $login_groups_mt4[$user_tp['login']];
                                $db->insert('tp_report_mt4', $tps_mt4[$user_tp['login']], 1);
                                $mt4_count++;
                            }

                        } else {
                            $mt4_skip++;
                        }
                    } else {

                        $tps_mt4[$user_tp['login']]['retention_id'] = $user_tp['retention'];
                        $tps_mt4[$user_tp['login']]['conversion_id'] = $user_tp['conversion'];

                        if(date("Y-m-d",strtotime($user_tp['ftd'])) == $c_date ) {
                            $tps_mt4[$user_tp['login']]['ret_amount'] = $tps_mt4[$user_tp['login']]['deposit'] - $user_tp['ftd_amount'];
                            $tps_mt4[$user_tp['login']]['ftd_amount'] = $user_tp['ftd_amount'];
                        } else {
                            $tps_mt4[$user_tp['login']]['ret_amount'] = $tps_mt4[$user_tp['login']]['deposit'];
                            $tps_mt4[$user_tp['login']]['ftd_amount'] = 0;
                        }

                        $tps_mt4[$user_tp['login']]['ib_id'] = $user_tp['ib'];

                        $tps_mt4[$user_tp['login']]['user_id'] = $user_tp['user_id'];
                        $user_data = $userManager->getCustom($user_tp['user_id'],'email,unit');
                        $tps_mt4[$user_tp['login']]['email'] = $user_data['email'];
                        $tps_mt4[$user_tp['login']]['unit'] = $user_data['unit'];
                        $tps_mt4[$user_tp['login']]['login'] = $user_tp['login'];
                        $tps_mt4[$user_tp['login']]['day'] = $c_date;

                        /*
                         *  ALTER TABLE `tp_report_mt4` ADD UNIQUE `login_day`(`login`, `day`);
                         */
                        if($login_groups_mt4[$user_tp['login']] ?? false){
                            $tps_mt4[$user_tp['login']]['mt4_group'] = $login_groups_mt4[$user_tp['login']];
                            $db->insert('tp_report_mt4', $tps_mt4[$user_tp['login']], 1);
                            $mt4_count++;
                        }

                    }

                }


                /**
                 * MT5 Database
                 */
                $where = 'type=2';
                $crm_groups = $db->select('mt_groups',$where,'name');
                $crm_groups = array_column($crm_groups,'name');
                array_walk($crm_groups, function (&$value, $key) {
                    $value='real\\'.$value;
                });
                if(empty($crm_groups)) { exit('Empty MT_Groups in CRM !');}

                /* Profit Swap */
                $_db_mt5_ps = new iSQL(DB_mt5);
                $query = "SELECT Login, SUM(Profit), SUM(Storage), count(Login) FROM mt5_deals WHERE Action IN (0,1) AND Entry IN (1,3) AND DATE(`Time`) = '$c_date' GROUP BY Login";
                $_db_mt5_ps->LINK->query($query, MYSQLI_ASYNC);

                /* Bonus In */
                $_db_mt5_bins = new iSQL(DB_mt5);
                $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=6 AND Profit>0 AND DATE(`Time`) = '$c_date' GROUP BY Login";
                $_db_mt5_bins->LINK->query($query, MYSQLI_ASYNC);

                /* Bonus Out */
                $_db_mt5_bout = new iSQL(DB_mt5);
                $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=6 AND Profit<0 AND DATE(`Time`) = '$c_date' GROUP BY Login";
                $_db_mt5_bout->LINK->query($query, MYSQLI_ASYNC);

                /* Withdrawal */
                $_db_mt5_w = new iSQL(DB_mt5);
                $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=2 AND Profit<0 AND Comment!='Zeroing' AND DATE(`Time`) = '$c_date' GROUP BY Login";
                $_db_mt5_w->LINK->query($query, MYSQLI_ASYNC);

                /* Deposit */
                $_db_mt5_d = new iSQL(DB_mt5);
                $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=2 AND Profit>0 AND Comment!='Zeroing' AND DATE(`Time`) = '$c_date' GROUP BY Login";
                $_db_mt5_d->LINK->query($query, MYSQLI_ASYNC);

                /* Correction */
                $_db_mt5_c = new iSQL(DB_mt5);
                $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=5 AND Comment!='Zeroing' AND DATE(`Time`) = '$c_date' GROUP BY Login";
                $_db_mt5_c->LINK->query($query, MYSQLI_ASYNC);

                /* Zeroing */
                $_db_mt5_z = new iSQL(DB_mt5);
                $query = "SELECT Login, SUM(Profit), count(Login) FROM mt5_deals WHERE Action=2 AND Comment='Zeroing' AND DATE(`Time`) = '$c_date' GROUP BY Login";
                $_db_mt5_z->LINK->query($query, MYSQLI_ASYNC);

                /* Login Groups */
                $_db_mt5_groups = new iSQL(DB_mt5);
                $query = "SELECT `Login`,`Group` FROM `mt5_users` WHERE `Group` LIKE 'real%'";
                $_db_mt5_groups->LINK->query($query, MYSQLI_ASYNC);


                /* Res - Profit Swap */
                $_db_mt5_ps_res = $_db_mt5_ps->LINK->reap_async_query();

                /* Res - Bonus In */
                $_db_mt5_bins_res = $_db_mt5_bins->LINK->reap_async_query();

                /* Res - Bonus Out */
                $_db_mt5_bout_res = $_db_mt5_bout->LINK->reap_async_query();

                /* Res - Withdrawal */
                $_db_mt5_w_res = $_db_mt5_w->LINK->reap_async_query();

                /* Res - Deposit */
                $_db_mt5_d_res = $_db_mt5_d->LINK->reap_async_query();

                /* Res - Correction */
                $_db_mt5_c_res = $_db_mt5_c->LINK->reap_async_query();

                /* Res - Zeroing */
                $_db_mt5_z_res = $_db_mt5_z->LINK->reap_async_query();

                /* Res - Login Groups  */
                $_db_mt5_groups_res = $_db_mt5_groups->LINK->reap_async_query();

                $max_row_mt5 = max(array(
                    $_db_mt5_ps_res->num_rows,
                    $_db_mt5_bins_res->num_rows,
                    $_db_mt5_bout_res->num_rows,
                    $_db_mt5_w_res->num_rows,
                    $_db_mt5_d_res->num_rows,
                    $_db_mt5_c_res->num_rows,
                    $_db_mt5_z_res->num_rows
                ));

                $tps_mt5=array();
                for ($i=1; $i<=$max_row_mt5; $i++) {

                    /* Profit & Swap */
                    $ps = $_db_mt5_ps_res->fetch_row();
                    if($ps[0]){
                        $tps_mt5[$ps[0]]['profit'] = $ps[1];
                        $tps_mt5[$ps[0]]['swap'] = $ps[2];
                        $tps_mt5[$ps[0]]['trades_count'] = $ps[3];
                    }

                    /* Bonus In */
                    $bin = $_db_mt5_bins_res->fetch_row();
                    if($bin[0]) {
                        $tps_mt5[$bin[0]]['bonus_in'] = $bin[1];
                        $tps_mt5[$bin[0]]['bonus_in_count'] = $bin[2];
                    }

                    /* Bonus Out */
                    $bout = $_db_mt5_bout_res->fetch_row();
                    if($bout[0]) {
                        $tps_mt5[$bout[0]]['bonus_out'] = $bout[1];
                        $tps_mt5[$bout[0]]['bonus_out_count'] = $bout[2];
                    }

                    /* Withdrawal */
                    $w = $_db_mt5_w_res->fetch_row();
                    if($w[0]) {
                        $tps_mt5[$w[0]]['withdrawal'] = $w[1];
                        $tps_mt5[$w[0]]['withdrawal_count'] = $w[2];
                    }

                    /* Deposit */
                    $d = $_db_mt5_d_res->fetch_row();
                    if($d[0]) {
                        $tps_mt5[$d[0]]['deposit'] = $d[1];
                        $tps_mt5[$d[0]]['deposit_count'] = $d[2];
                    }

                    /* Correction */
                    $c = $_db_mt5_c_res->fetch_row();
                    if($c[0]) {
                        $tps_mt5[$c[0]]['correction'] = $c[1];
                        $tps_mt5[$c[0]]['correction_count'] = $c[2];
                    }

                    /* Zeroing */
                    $z = $_db_mt5_z_res->fetch_row();
                    if($z[0]) {
                        $tps_mt5[$z[0]]['zeroing'] = $z[1];
                        $tps_mt5[$z[0]]['zeroing_count'] = $z[2];
                    }

                }

                $list_login_mt5 = implode(',', array_keys($tps_mt5));
                if($list_login_mt5 ?? false){
                    $where = 'group_id=2 AND login IN ('.$list_login_mt5.')';
                    $user_tps_mt5 = $db->select('tp', $where);
                }
                $login_groups_mt5 = array_column($_db_mt5_groups_res->fetch_all(),1,0);

                if($user_tps_mt5 ?? false) foreach($user_tps_mt5 as $user_tp){

                    if($uid){
                        if($user_tp['user_id']==$uid) {

                            $tps_mt5[$user_tp['login']]['retention_id'] = $user_tp['retention'];
                            $tps_mt5[$user_tp['login']]['conversion_id'] = $user_tp['conversion'];

                            if(date("Y-m-d",strtotime($user_tp['ftd'])) == $c_date ) {
                                $tps_mt5[$user_tp['login']]['ret_amount'] = $tps_mt5[$user_tp['login']]['deposit'] - $user_tp['ftd_amount'];
                                $tps_mt5[$user_tp['login']]['ftd_amount'] = $user_tp['ftd_amount'];
                            } else {
                                $tps_mt5[$user_tp['login']]['ret_amount'] = $tps_mt5[$user_tp['login']]['deposit'];
                                $tps_mt5[$user_tp['login']]['ftd_amount'] = 0;
                            }

                            $tps_mt5[$user_tp['login']]['ib_id'] = $user_tp['ib'];

                            $tps_mt5[$user_tp['login']]['user_id'] = $user_tp['user_id'];
                            $user_data = $userManager->getCustom($user_tp['user_id'],'email,unit');
                            $tps_mt5[$user_tp['login']]['email'] = $user_data['email'];
                            $tps_mt5[$user_tp['login']]['unit'] = $user_data['unit'];
                            $tps_mt5[$user_tp['login']]['login'] = $user_tp['login'];
                            $tps_mt5[$user_tp['login']]['day'] = $c_date;

                            /*
                             *  ALTER TABLE `tp_report_mt5` ADD UNIQUE `login_day`(`login`, `day`);
                             */
                            if($login_groups_mt5[$user_tp['login']] ?? false){
                                $tps_mt5[$user_tp['login']]['mt5_group'] = $login_groups_mt5[$user_tp['login']];
                                $db->insert('tp_report_mt5', $tps_mt5[$user_tp['login']], 1);
                                $mt5_count++;
                            }

                        } else {
                            $mt5_skip++;
                        }
                    } else {
                        $tps_mt5[$user_tp['login']]['retention_id'] = $user_tp['retention'];
                        $tps_mt5[$user_tp['login']]['conversion_id'] = $user_tp['conversion'];

                        if(date("Y-m-d",strtotime($user_tp['ftd'])) == $c_date ) {
                            $tps_mt5[$user_tp['login']]['ret_amount'] = $tps_mt5[$user_tp['login']]['deposit'] - $user_tp['ftd_amount'];
                            $tps_mt5[$user_tp['login']]['ftd_amount'] = $user_tp['ftd_amount'];
                        } else {
                            $tps_mt5[$user_tp['login']]['ret_amount'] = $tps_mt5[$user_tp['login']]['deposit'];
                            $tps_mt5[$user_tp['login']]['ftd_amount'] = 0;
                        }

                        $tps_mt5[$user_tp['login']]['ib_id'] = $user_tp['ib'];

                        $tps_mt5[$user_tp['login']]['user_id'] = $user_tp['user_id'];
                        $user_data = $userManager->getCustom($user_tp['user_id'],'email,unit');
                        $tps_mt5[$user_tp['login']]['email'] = $user_data['email'];
                        $tps_mt5[$user_tp['login']]['unit'] = $user_data['unit'];
                        $tps_mt5[$user_tp['login']]['login'] = $user_tp['login'];
                        $tps_mt5[$user_tp['login']]['day'] = $c_date;

                        /*
                         *  ALTER TABLE `tp_report_mt5` ADD UNIQUE `login_day`(`login`, `day`);
                         */
                        if($login_groups_mt5[$user_tp['login']] ?? false){

                            if( in_array($login_groups_mt5[$user_tp['login']], $crm_groups) ){
                                $tps_mt5[$user_tp['login']]['mt5_group'] = $login_groups_mt5[$user_tp['login']];
                                $db->insert('tp_report_mt5', $tps_mt5[$user_tp['login']], 1);
                                $mt5_count++;
                            } else {
                                $mt5_skip++;
                                GF::cLog($login_groups_mt5[$user_tp['login']] . ' Skipped.');
                            }
                        }
                    }
                }

            }
            echo $_GET['day'] ?? false;
            echo ' | '.$c_date;
            $js_sleep = (($mt4_count+$mt5_count+$mt4_skip+$mt5_skip)>0) ? 500 : 10;
            if($uid) $js_sleep += 5000;
            ?>
            <h5>Active Login</h5>
            MT4: <strong class="text-success"><?= $mt4_count; ?></strong> | Skipped: <strong class="text-danger"><?= $mt4_skip; ?></strong>
            <br>
            MT5: <strong class="text-success"><?= $mt5_count; ?></strong> | Skipped: <strong class="text-danger"><?= $mt5_skip; ?></strong>
            <script>
                <?php if($_GET['day'] ?? false) {
                if($uid) { ?>
                    setTimeout(() => {
                        window.location = "tp_report.php?day=<?= $_GET['day']-1 ?>&uid=<?= $uid ?>";
                    }, <?= $js_sleep ?>);
                <?php } else { ?>
                    setTimeout(() => {
                        window.location = "tp_report.php?day=<?= $_GET['day']-1 ?>";
                    }, <?= $js_sleep ?>);
                <?php
                    }
                } ?>
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