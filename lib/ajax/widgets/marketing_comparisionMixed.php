<?php

global $db;
$resultSource = $db->select('marketing_report', 0, 'src', 0, 0, 'src');

?>

        <div id="ComparisionMixed">
            <div id="loading" class="text-center my-3 h4"><i class="fas fa-spinner fa-spin"></i> Loading Data</div>
            <form id="comparisionForm" action="" method="post" autocomplete="off">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="inputstartTime">Source</label>
                            <select id="source_leads" class="form-control" name="source" multiple required>
                                <option value="" disabled selected>Source</option>
                                <?php foreach($resultSource as $source) { ?>
                                    <option value="<?= strtolower($source['src']) ?>"><?= $source['src'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group date">
                            <label for="inputstartTime">Start Time</label>
                            <div class="input-group">
                                <input type="text" class="dateCustom form-control" id="startTime" name="startTime" value="<?= date("Y-m-d", strtotime("first day of this month")) ?>" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group date">
                            <label for="inputendTime">End Time</label><i class="mdi mdi-update"></i>
                            <div class="input-group">
                                <input type="text" class="dateCustom form-control" id="endTime" name="endTime" value="<?= date ("Y-m-d") ?>" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group text-center">
                            VS
                            <hr>
                            <select id="itemvs" class="form-control" name="itemvs" required>
                                <option value="ftd">FTD Amount</option>
                                <option value="ret">RET Amount</option>
                                <option value="ftd_c">FTD Count</option>
                                <option value="ret_c_t">RET Times</option>
                            </select>
                            <div class="form-group">
                                <label for="input">&nbsp;</label>
                                <input class="btn btn-primary form-control" type="submit" name="submit" value="Submit">
                            </div>
                            <div class="t_Count pt-4 row">
                                <div class="col-4"><strong id="totalL" class="text-success"></strong></div>
                                <div class="col-4 text-center">Total</div>
                                <div class="col-4 text-right"><strong id="totalR" class="text-success"></strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="inputstartTime">Source</label>
                            <select id="source_leadsR" class="form-control" name="sourceR" multiple required>
                                <option value="" disabled selected>Source</option>
                                <?php foreach($resultSource as $source) { ?>
                                    <option value="<?= strtolower($source['src']) ?>"><?= $source['src'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group date">
                            <label for="inputstartTime">Start Time</label>
                            <div class="input-group">
                                <input type="text" class="dateCustom form-control" id="startTimeR" name="startTimeR" value="<?= date("Y-m-d", strtotime("first day of previous month")) ?>" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group date">
                            <label for="inputendTime">End Time</label><i class="mdi mdi-update"></i>
                            <div class="input-group">
                                <input type="text" class="dateCustom form-control" id="endTimeR" name="endTimeR" value="<?= date("Y-m-d", strtotime("last day of previous month")) ?>" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <hr>
            <div id="c-ComparisionMixed" class="col-12"></div>
        </div>

<script>
    $(document).ready( function () {
        $('.t_Count,#loading').hide();

        let cmid = 0;
        $("body").on("submit","form#comparisionForm", function(e) {
            $('#ComparisionMixed #loading').show();
            let form = $(this);
            form.hide();
            e.preventDefault();
            let data = {
                startTime: $('#ComparisionMixed #startTime').val(),
                endTime: $('#ComparisionMixed #endTime').val(),
                source_leads: $('#ComparisionMixed #source_leads').val(),
                startTimeR: $('#ComparisionMixed #startTimeR').val(),
                endTimeR: $('#ComparisionMixed #endTimeR').val(),
                source_leadsR: $('#ComparisionMixed #source_leadsR').val(),
                itemvs: $('#ComparisionMixed #itemvs').val()
            }
            ajaxCall('marketing', 'comparisionMixed', data, function(Res){
                let obj = JSON.parse(Res);
                cmid++;
                $('.doD-img').hide();

                $('#ComparisionMixed #totalL').html(obj.total.L);
                $('#ComparisionMixed #totalR').html(obj.total.R);
                $('.t_Count').show();

                let mixCanvasId = 'ComparisionMixed-'+cmid;
                $('#c-ComparisionMixed').html('<canvas id="'+mixCanvasId+'" width="400" height="115"></canvas>');
                var ComparisionMixed = document.getElementById(mixCanvasId).getContext('2d');
                var ComparisionMix = new Chart(ComparisionMixed, {
                    type: 'line',
                    data: {
                        labels: obj.range,
                        datasets: [{
                            label: 'Left',
                            data: obj.L,
                            backgroundColor: '#f50ab1',
                            borderColor: 'rgba(235, 110, 199,0.46)',
                            fill: false
                        },{
                            label: 'Right',
                            data: obj.R,
                            backgroundColor: '#f59f0a',
                            borderColor: 'rgba(240, 213, 117,0.51)',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Day'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                }
                            }]
                        }
                    }
                });
                form.show();
                $('#ComparisionMixed #loading').hide();
            });
        });
    });
</script>