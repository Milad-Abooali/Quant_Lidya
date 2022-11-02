<?php
/**
 * Profile Progress
 * App - Widget
 * By Milad [m.abooali@hotmail.com]
 */
global $db;
global $APP;

if($APP->checkPermit('profile', 'view', 1)):
?>

<div id="w-profile-progress" class="col-6 col-md-3">
    <div class="w-box">

        <h4>Profile</h4>
        <div class="w-box-body h-100">
            <div class="row text-center px-3">
                <div id="profile-progress" class="col-sm-12 col-md-5 mb-2 mb-md-0" data-val="<?= main::profileProgress(); ?>" data-color="blue"></div>
                <button data-screen="profile" class="show-screen btn btn-sm btn-outline-secondary col-sm-12 col-md-7">Update</button>
            </div>
        </div>

    </div>
</div>

<script src="app/assets/js/profile-progress.js" defer></script>

<?php endif; ?>
