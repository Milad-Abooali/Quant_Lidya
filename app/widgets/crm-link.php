<?php
/**
 * CRM Link
 * App - Widget
 * By Milad [m.abooali@hotmail.com]
 */
global $db;
global $APP;

if($APP->checkPermit('crm', 'view', 1)):
?>

<div id="w-profile-progress" class="widget col-12 col-md-12">
    <div class="">
        <a href="https://<?= Broker['web_url'] ?>" class="btn btn-xl btn-dark d-block"><i class="fas fa-external-link-alt"></i> View Client Zone</a>
    </div>
</div>

<?php endif; ?>
