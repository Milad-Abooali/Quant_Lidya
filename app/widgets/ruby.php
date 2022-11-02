<?php
/**
 * AI Assistance - Ruby
 * App - Widget
 * By Milad [m.abooali@hotmail.com]
 */
global $db;
global $APP;

$profile = main::getProfile();

if($APP->checkPermit('ruby', 'view', 1)):
    ?>

    <div id="w-ruby" class="widget col-xs-12 col-md-6">
        <div class="w-box">

            <h4>AI Assistance</h4>
            <div class="w-box-body">
                <div class="p-3 rounded-1 bg-bg-wizard">
                    <h6 class="text-primary">Chat with the AI bot</h6>
                    <p class="text-light">
                        We have an artificial intelligence bot named Ruby.
                        <br>Ruby will improve day by day thanks to machine learning technology.
                    </p>
                    <button class="doM-show-ruby btn btn-outline-success w-100">Start Chat</button>
                </div>
            </div>

        </div>
    </div>
<!--
    <script src="app/assets/js/ruby.js" defer></script>
-->
<?php endif; ?>