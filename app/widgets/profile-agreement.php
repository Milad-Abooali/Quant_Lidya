<?php
/**
 * Profile Agreement
 * App - Widget
 * By Milad [m.abooali@hotmail.com]
 */
global $db;
global $APP;

$profile = main::getProfile();

if($APP->checkPermit('profile', 'view', 1)):
?>

<div id="w-profile-agreement" class="widget col-6 col-md-4">
    <div class="w-box">

        <h4>Agreement</h4>
        <div class="w-box-body h-100">
            <div class="px-2">
                <?php if($profile->Bill['verify'] && $profile->Agreement){ ?>
                    <div class="row">
                        <button class="btn btn-sm btn-outline-success col-sm-12 col-md-9 mb-2 mb-md-0" disabled>Accepted On <br><?= $profile->Agreement ?></button>
                        <button data-form-name="profile_edit_agreement" class="doM-form btn btn-sm btn-outline-secondary col-sm-12 col-md-3">View</button>
                    </div>
                <?php } else { ?>
                    <button class="btn btn-sm btn-info doM-form" data-form-name="profile_edit_agreement" title="Agreement">Need To Agree</button>
                <?php } ?>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>
