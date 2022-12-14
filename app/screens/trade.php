<?php
/**
 * Trade
 * App - Screen Page
 * By Milad [m.abooali@hotmail.com]
 */
    global $APP;
    global $db;

    $screen_title   = 'Trade';
    $screen_id      = 'trade';

    if($APP->checkPermit($screen_id, 'view', 1)):

    $where = 'user_id='.$_SESSION['id'];
    $tp_accounts = $db->select('tp',$where);

?>

<!-- Home Screen -->
<div id="<?= $screen_id ?>" class="screen d-hide col-xs-12">
    <div class="screen-header">
        <h4><?= $screen_title ?></h4>
    </div>
    <div class="screen-body">

        <div class="row p-3">
            <div class="col-6">
                <button data-wizard-name="open-tp-demo" title="Open A New Trading Account" class="doM-wizard btn btn-sm btn-success">New Account</button>
            </div>
            <div class="col-6 text-end">
                <button data-form-name="trade_view_deposit" title="Deposit to wallet" class="doM-form btn btn-sm btn-outline-success">Deposit</button>
                <button data-form-name="trade_view_withdraw" title="Withdraw from wallet" class="doM-form btn-sm btn btn-outline-danger">Withdraw</button>
            </div>
        </div>
        <table class="table table-sm table-dark">
            <thead>
                <tr>
                    <th class="text-center text-white-50">Login</th>
                    <th class="text-center text-white-50">Manage</th>
                </tr>
            </thead>
            <tbody>
                <?php if($tp_accounts) { ?>
                    <?php foreach($tp_accounts as $tp_account) { ?>
                    <tr style="line-height: 65px;">
                        <td class="text-center text-white align-middle"><span class=""><?= $tp_account['login'] ?></span><sup class="ps-1 text-warning"><?= $tp_account['server'] ?></sup></td>
                        <td class="text-center text-white-50">

                            <?php
                                $mt5api = new mt5API();
                                $api_params['login'] = $tp_account['login'];
                                $mt5api->get('/api/user/group', $api_params);
                                $e = $mt5api->Error;
                                $api = $mt5api->Response;
                                $group = $api->answer->group;

                                GF::cLog($tp_account['login'].':'.$group);

                                $demo_groups = array('LidyaGOLD', 'LidyaSTD', 'LidyaVIP');
                                $is_demo = ($group != str_ireplace($demo_groups,"XX",$group))? true: false;
                            ?>
                            <span title="Login Type" class="doM-form btn btn-sm btn-outline-secondary disabled"><?= ($is_demo) ? 'Demo' : 'Real' ?></span>

                            <!--
                                <button data-form-params='{"login":"<?= $tp_account['login'] ?>"}' data-login="<?= $tp_account['login'] ?>" data-form-name="trade_update-login-password" title="Change <?= $tp_account['login'] ?> Passwords" class="doM-form btn btn-sm btn-outline-secondary"><i class="fa fa-key"></i></button>
                            -->
                            <button data-form-params='{"login":"<?= $tp_account['login'] ?>"}' data-login="<?= $tp_account['login'] ?>" data-form-name="trade_view_login" title="<?= $tp_account['login'] ?> Detail" class="doM-form btn btn-sm btn-primary"><i class="fa fa-info-circle"></i> Details</button>
                            <button data-screen="market" data-params='{"login":"<?= $tp_account['login'] ?>"}' class="show-screen btn-sm btn btn-warning m-3 col">View Market</button>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>

    </div>
    <div class="screen-footer">
        footer
    </div>
</div>

<?php endif; ?>
