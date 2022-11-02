<?php
/**
 * Withdrew
 * App - Screen Page
 * By Milad [m.abooali@hotmail.com]
 */
    global $APP;

    $screen_title   = 'Withdrew';
    $screen_id      = 'withdrew';

?>

<!-- Home Screen -->
<div id="<?= $screen_id ?>" class="screen d-hide col-xs-12">
    <div class="screen-header">
        <h4><?= $screen_title ?></h4>
    </div>
    <div class="screen-body">
        <?php if($APP->checkPermit($screen_id, 'view', 1)): ?>
        body
        <?php endif; ?>
    </div>
    <div class="screen-footer">
        <?php if($APP->checkPermit($screen_id, 'view')): ?>
        footer
        <?php endif; ?>
    </div>
</div>
