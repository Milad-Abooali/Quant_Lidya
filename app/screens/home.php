<?php
/**
 * Home
 * App - Screen Page
 * By Milad [m.abooali@hotmail.com]
 */
    global $APP;

    $screen_title   = 'Home';
    $screen_id      = 'home';

    if($APP->checkPermit($screen_id, 'view', 1)):
?>

<!-- Home Screen -->
<div id="<?= $screen_id ?>" class="screen <?= (APP_Dev_Mod) ? 'd-hide' : 'active' ?> col-xs-12">
    <div class="screen-header">
        <h4><?= $screen_title ?></h4>
    </div>
    <div class="screen-body">

        <div class="row">
            <?= blocks::widget('datetime') ?>
            <?= blocks::widget('crm-link') ?>
        </div>
        <div class="row">
            <?= blocks::widget('profile-documents') ?>
            <?= blocks::widget('profile-agreement') ?>
            <?= blocks::widget('profile-progress') ?>
        </div>
        <div class="row">
            <?= blocks::widget('wizard') ?>
            <?= blocks::widget('ruby') ?>
        </div>

    </div>
    <div class="screen-footer">
            footer
    </div>
</div>
<?php endif; ?>
