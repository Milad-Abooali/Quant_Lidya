<?php
/**
 * Debug
 * App - Screen Page
 * By Milad [m.abooali@hotmail.com]
 */
    global $APP;
    $screen_title   = 'App Status';
    $screen_id      = 'debug';

?>
<view class="hide-if-guest">
<!-- <?= $screen_id ?> Screen -->
<div id="<?= $screen_id ?>" class="screen <?= (!APP_Dev_Mod) ? 'd-hide' : 'active' ?> col-xs-12">
    <div class="screen-header">
        <h4><?= $screen_title ?></h4>
    </div>
    <div class="screen-body">
        <?php if( $APP->checkPermit($screen_id, 'view', 1) ): ?>

            <!--Service Monitors-->
            <div id="online-status" class="row text-center">
                <div class="col">
                    <small class="mr-2">Online</small><br>
                    <strong id="online-count" class="online-count text-success">0</strong>
                </div>
                <div class="col">
                    <small class="mr-2">Guest</small><br>
                    <strong id="guest-count" class="guest-count text-success">0</strong>
                </div>
                <div class="col">
                    <small class="mr-2">User</small><br>
                    <strong id="user-count" class="user-count text-success">0</strong>
                </div>
                <div class="col">
                    <small class="mr-2">Agent</small><br>
                    <strong id="agent-count" class="agent-count text-success">0</strong>
                </div>
                <div class="col">
                    <small class="mr-2">Admin</small><br>
                    <strong id="admin-count" class="admin-count text-success">0</strong>
                </div>
            </div>

            <!--Service Monitors-->
            <div id="app-status" class="row">
                <div class="col">
                    <span id="app-status" class="mdi mdi-checkbox-blank-circle mr-1 text-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Online"></span>
                    <small class="mr-2">App</small>
                </div>
                <div class="col">
                    <span id="socket-status" class="mdi mdi-checkbox-blank-circle mr-1 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title=""></span>
                    <small class="mr-2">Socket</small>
                </div>
                <div class="col">
                    <span id="crm-status" class="mdi mdi-checkbox-blank-circle text-muted mr-1 " data-bs-toggle="tooltip" data-bs-placement="top" title=""></span>
                    <small class="mr-2">CRM</small>
                </div>
                <div class="col">
                    <span id="redis-status" class="mdi mdi-checkbox-blank-circle text-muted mr-1 " data-bs-toggle="tooltip" data-bs-placement="top" title=""></span>
                    <small class="mr-2">Redis</small>
                </div>
                <div class="col">
                    <span id="aiml-status" class="mdi mdi-checkbox-blank-circle text-muted mr-1 " data-bs-toggle="tooltip" data-bs-placement="top" title=""></span>
                    <small class="mr-2">AIML</small>
                </div>
                <div class="col">
                    <span id="hi-status" class="mdi mdi-checkbox-blank-circle text-muted mr-1 " data-bs-toggle="tooltip" data-bs-placement="top" title=""></span>
                    <small class="mr-2">HI</small>
                </div>
                <div class="col">
                    <span id="ml-status" class="mdi mdi-checkbox-blank-circle text-muted mr-1 " data-bs-toggle="tooltip" data-bs-placement="top" title=""></span>
                    <small class="mr-2">ML</small>
                </div>
            </div>

            <!--Session-->
            <div id="session" class="row">
                <h5>Session</h5>
                <?php GF::p($_SESSION); ?>
            </div>

        <?php endif; ?>
    </div>
    <div class="screen-footer">
        <?php if($APP->checkPermit($screen_id, 'view')): ?>
            footer
        <?php endif; ?>
    </div>
</div>
</view>