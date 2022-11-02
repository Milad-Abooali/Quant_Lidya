<?php
/**
 * Profile Documents
 * App - Widget
 * By Milad [m.abooali@hotmail.com]
 */
global $db;
global $APP;

$profile = main::getProfile();

if($APP->checkPermit('profile', 'view', 1)):
?>

<div id="w-profile-documents" class="widget col-xs-12 col-md-5">
    <div class="w-box">

        <h4>Authentication</h4>
        <div class="w-box-body h-md-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="">
                    <?php if($profile->IdCard['verify']): ?>
                        <i class="fa fa-check text-success me-1"></i>
                    <?php else: ?>
                        <i class="fas fa-minus-circle text-warning me-1"></i>
                    <?php endif; ?>
                    ID Card
                </div>


                <div class="">
                    <?php if($profile->Bill['verify']): ?>
                        <i class="fa fa-check text-success me-1"></i>
                    <?php else: ?>
                        <i class="fas fa-minus-circle text-warning me-1"></i>
                    <?php endif; ?>
                    Proof of Residence
                </div>

                <?php if($profile->Bill['verify'] && $profile->IdCard['verify']){ ?>
                    <button class="btn btn-sm btn-outline-success" disabled>IS DONE</button>
                <?php } else { ?>
                    <button class="btn btn-sm btn-outline-secondary show-screen" data-screen="profile">Upload <i class="fa fa-angle-right ms-1"></i></button>
                <?php } ?>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>