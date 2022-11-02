<?php
/**
 * Date Time
 * App - Widget
 * By Milad [m.abooali@hotmail.com]
 */
global $db;
global $APP;

$profile = main::getProfile();

if($APP->checkPermit('home', 'view', 1)):
?>

<div id="w-datetime" class="widget col-12 col-md-12">
    <div class="breaking-news-ticker" id="zone-times">
        <div class="bn-label"> <i class="text-muted fa fa-clock"></i></div>
        <div class="bn-news">
            <ul>
                <li class="px-3" data-zone="local">
                    <div class="zone data-local-zone float-start"></div>
                    <div class="time data-local-time float-end"></div>
                </li>
                <li class="px-3" data-zone="America/New_York">
                    <div class="zone float-start">New York</div>
                    <div class="time float-end"></div>
                </li>
                <li class="px-3" data-zone="Europe/London">
                    <div class="zone float-start">London</div>
                    <div class="time float-end"></div>
                </li>
                <li class="px-3" data-zone="Europe/Berlin">
                    <div class="zone float-start">Frankfurt</div>
                    <div class="time float-end"></div>
                </li>
                <li class="px-3" data-zone="Asia/Tokyo">
                    <div class="zone float-start">Tokyo</div>
                    <div class="time float-end"></div>
                </li>
                <li class="px-3" data-zone="Asia/Hong_Kong">
                    <div class="zone float-start">Hong Kong</div>
                    <div class="time float-end"></div>
                </li>
                <li class="px-3" data-zone="Australia/Sydney">
                    <div class="zone float-start">Sydney</div>
                    <div class="time float-end"></div>
                </li>
            </ul>
        </div>
    </div>
    <div id="server-time">
        <div class="zone float-start">Server Time (<?php echo date_default_timezone_get(); ?>)</div>
        <div class="time float-end"></div>
    </div>
</div>
    <script>
        try{
            datetime_js;
        }
        catch(e) {
            if(e.name == "ReferenceError") {
                datetime_js = true;
                $.ajax({
                    async: false,
                    url: "app/assets/js/datetime.js",
                    dataType: "script"
                });
            }
        }
    </script>

<?php endif; ?>
