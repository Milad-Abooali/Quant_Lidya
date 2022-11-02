<?php
/**
 * Profile
 * App - Screen Page
 * By Milad [m.abooali@hotmail.com]
 */
    global $db;
    global $APP;

    $screen_title   = 'Profile';
    $screen_id      = 'profile';

    $profile = main::getProfile();

    if($APP->checkPermit($screen_id, 'view', 1)):
?>

<!-- Home Screen -->
<div id="<?= $screen_id ?>" class="screen d-hide col-xs-12">
    <div class="screen-header">
        <h4><?= $screen_title ?></h4>
    </div>
    <div class="screen-body">
        <div id="profile-avatar" class="text-center">
            <img src="<?= $profile->avatar ?>" alt="avatar" class="avatar">
            <button type="button" class="doA-upload-avatar btn btn-outline-secondary"><i class="fa fa-edit"></i> Edit</button>
            <input type="file" name="avatar-file" id="avatar-file" class="d-hide">
        </div>

        <div class="accordion accordion-flush my-5" id="accordionProfile">
            <div class="accordion-item">
                <h2 class="accordion-header" id="general-details-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#general-details" aria-expanded="false" aria-controls="general-details">
                        <i class="fas fa-user-alt text-secondary me-2"></i> General Details
                    </button>
                </h2>
                <div id="general-details" class="accordion-collapse collapse" aria-labelledby="general-details" data-bs-parent="#accordionProfile">
                    <div class="accordion-body">
                        <table class="table table-sm table-dark">
                            <tbody>
                                <tr>
                                    <td>First Name</td>
                                    <td class="table-active"><span class="c-fname"><?= $profile->General['fname'] ?? '-' ?></span></td>
                                </tr>
                                <tr>
                                    <td>Last Name</td>
                                    <td class="table-active"><span class="c-lname"><?= $profile->General['lname'] ?? '-' ?></span></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td class="table-active"><span class="c-email"><?= $profile->General['email'] ?? '-' ?></span></td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td class="table-active"><span class="c-phone">+<?= $profile->General['phone'] ?? '-' ?></span></td>
                                </tr>
                                <tr>
                                    <td>Location</td>
                                    <td class="table-active"><span class="c-country"><?= $profile->General['country'] ?? '-' ?></span></td>
                                </tr>
                                <tr>
                                    <td>Business Unit</td>
                                    <td class="table-active"><span class="c-unit"><?= $profile->General['unit'] ?? '-' ?></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="table-active text-center">
                                        <button type="button" data-form-name="profile_edit_general" title="Edit Profile" class="doM-form btn btn-dark col-8"><i class="fa fa-edit"></i> Edit</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="docs-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#docs" aria-expanded="false" aria-controls="docs">
                        <i class="fas fa-id-card-alt text-secondary me-2"></i> Documents
                    </button>
                </h2>
                <div id="docs" class="accordion-collapse collapse small" aria-labelledby="docs" data-bs-parent="#accordionProfile">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-6">
                                <div>
                                    <span class="c-doc-id-status">
                                        <?php if($profile->IdCard['verify']): ?>
                                            <i class="fa fa-check text-success"></i>
                                        <?php else: ?>
                                            <i class="fas fa-minus-circle text-warning"></i>
                                        <?php endif; ?>
                                    </span>
                                    <small class="text-secondary ms-1">ID Card</small>
                                </div>
                                <div class="btn-group my-2" role="group">
                                    <?php if( $profile->IdCard['src'] ): ?>
                                        <a href="<?= $profile->IdCard['src'] ?>" target="_blank" role="button" class="btn btn-sm btn-outline-primary"><i class="fa fa-download"></i> Old</a>
                                    <?php endif; ?>
                                    <button type="button" class="doA-upload-idcard btn btn-sm btn-primary"><i class="fa fa-upload"></i> New</button>
                                </div>
                                <input type="file" name="idcard-file" id="idcard-file" class="d-hide">
                            </div>
                            <div class="col-6">
                                <div>
                                    <span class="c-doc-id-status">
                                        <?php if($profile->Bill['verify']): ?>
                                            <i class="fa fa-check text-success"></i>
                                        <?php else: ?>
                                            <i class="fas fa-minus-circle text-warning"></i>
                                        <?php endif; ?>
                                    </span>
                                    <small class="text-secondary ms-1">Proof of Residence</small>
                                </div>
                                <div class="btn-group my-2" role="group">
                                    <?php if( $profile->Bill['src'] ): ?>
                                        <a href="<?= $profile->Bill['src'] ?>" target="_blank" role="button" class="btn btn-sm btn-outline-primary"><i class="fa fa-download"></i> Old</a>
                                    <?php endif; ?>
                                    <button type="button" class="doA-upload-bill btn btn-sm btn-primary"><i class="fa fa-upload"></i> New</button>
                                </div>
                                <input type="file" name="bill-file" id="bill-file" class="d-hide">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="extra-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#extra" aria-expanded="false" aria-controls="extra">
                        <i class="fas fa-id-card-alt text-secondary me-2"></i> Extra Information
                    </button>
                </h2>
                <div id="extra" class="accordion-collapse collapse" aria-labelledby="extra" data-bs-parent="#accordionProfile">
                    <div class="accordion-body">
                        <table class="table table-sm table-dark">
                            <tbody>
                            <tr>
                                <td>City</td>
                                <td class="table-active"><span class="c-city"><?= $profile->Extra['city'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td class="table-active"><span class="c-address"><?= $profile->Extra['address'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>Interests</td>
                                <td class="table-active"><span class="c-interests"><?= $profile->Extra['interests'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>Hobbies</td>
                                <td class="table-active"><span class="c-hobbies"><?= $profile->Extra['hobbies'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>Job Category</td>
                                <td class="table-active"><span class="c-job-category"><?= $profile->Extra['job_cat']['name'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>Job Title</td>
                                <td class="table-active"><span class="c-job-title"><?= $profile->Extra['job_title'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>FX Experience</td>
                                <td class="table-active"><span class="c-exp_fx"><?= $profile->Extra['exp_fx_year'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>CFD Experience</td>
                                <td class="table-active"><span class="c-exp_cfd"><?= $profile->Extra['exp_cfd_year'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>Income</td>
                                <td class="table-active"><span class="c-income"><?= $profile->Extra['income']['name'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>Investment Amount</td>
                                <td class="table-active"><span class="c-investment"><?= $profile->Extra['investment']['name'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td>Trading Strategy</td>
                                <td class="table-active"><span class="c-strategy"><?= $profile->Extra['strategy'] ?? '-' ?></span></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="table-active text-center">
                                    <button type="button" data-form-name="profile_edit_extra"  title="Edit Profile" class="doM-form btn btn-dark col-8"><i class="fa fa-edit"></i> Edit</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="screen-footer">

    </div>
</div>
<?php endif; ?>
