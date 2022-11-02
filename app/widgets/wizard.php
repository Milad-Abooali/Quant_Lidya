<?php
/**
 * Wizard
 * App - Widget
 * By Milad [m.abooali@hotmail.com]
 */
global $db;
global $APP;

$profile = main::getProfile();

if($APP->checkPermit('wizard', 'view', 1)):
    ?>

    <div id="w-wizard" class="widget col-xs-12 col-md-6">
        <div class="w-box">

            <h4>AI Wizards</h4>
            <div class="w-box-body">
                <div class="p-3 rounded-1 bg-bg-wizard">
                    <h6 class="text-primary">Do you need help?</h6>
                    <p class="text-light">
                        Our AI Wizard designed to help you based on artificial intelligence.
                        <br>Just follow the steps to reach your goals.
                    </p>
                    <button data-screen="wizard" class="show-screen btn btn-outline-success w-100">Try It ...</button>
                </div>
            </div>

        </div>
    </div>
<?php endif; ?>


