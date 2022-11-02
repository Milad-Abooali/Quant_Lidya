<?php

global $db;
$resultSource = $db->select('marketing_report', 0, 'src', 0, 0, 'src');

?>

        <div id="ComparisionFixed">
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
                        <div class="form-group t_Count">
                            <label for="inputstartTime">End Time</label>
                            <input type="text" class="form-control" id="endL" value="" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group text-center">
                            VS
                            <hr>
                            <div class="row">

                                <div class="col">
                                    <label for="range">Range Days</label>
                                </div>
                                <div class="col">
                                    <input class="form-control" name="range" id="range" type="number" value="<?= date("d", strtotime("last day of previous month")) ?>">

                                </div>
                                <div class="col">
                                    <input class="btn btn-primary form-control" type="submit" name="submit" value="Submit">
                                </div>
                            </div>
                            <div class="t_Count pt-4 row">
                                <div class="col-4 text-left">
                                    FTD: $ <strong id="totalLftd" class="text-success"></strong> (<strong id="totalLftd_c" class="text-success"></strong>)<br>
                                    RET: $ <strong id="totalLret" class="text-success"></strong> (<strong id="totalLret_c" class="text-success"></strong>)
                                </div>
                                <div class="col-4 text-center">Total</div>
                                <div class="col-4 text-left">
                                    FTD: $ <strong id="totalRftd" class="text-success"></strong> (<strong id="totalRftd_c" class="text-success"></strong>)<br>
                                    RET: $ <strong id="totalRret" class="text-success"></strong> (<strong id="totalRret_c" class="text-success"></strong>)
                                </div>
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
                        <div class="form-group t_Count">
                            <label for="inputstartTime">End Time</label>
                            <input type="text" class="form-control" id="endR" value="" readonly>
                        </div>
                    </div>
                </div>
            </form>
            <hr>
            <div class="row">
                <div id="c-ComparisionFixedFTD" class="col-6"></div>
                <div id="c-ComparisionFixedRET" class="col-6"></div>
                <div id="c-ComparisionFixedFTDC" class="col-6"></div>
                <div id="c-ComparisionFixedRETC" class="col-6"></div>
            </div>
        </div>

<script>
    $(document).ready( function () {
        $('.t_Count,#loading').hide();

        let cmid = 0;
        $("body").on("submit","form#comparisionForm", function(e) {
            $('#ComparisionFixed #loading').show();
            let form = $(this);
            form.hide();
            e.preventDefault();
            let data = {
                startTime: $('#ComparisionFixed #startTime').val(),
                source_leads: $('#ComparisionFixed #source_leads').val(),
                startTimeR: $('#ComparisionFixed #startTimeR').val(),
                source_leadsR: $('#ComparisionFixed #source_leadsR').val(),
                itemvs: $('#ComparisionFixed #itemvs').val(),
                range: $('#ComparisionFixed #range').val()
            }
            ajaxCall('marketing', 'comparisionFixed', data, function(Res){
                let obj = JSON.parse(Res);
                cmid++;
                $('.doD-img').hide();

                $('#ComparisionFixed #totalLftd').html(obj.total.L['ftd']);
                $('#ComparisionFixed #totalLret').html(obj.total.L['ret']);
                $('#ComparisionFixed #totalLftd_c').html(obj.total.L['ftd_c']);
                $('#ComparisionFixed #totalLret_c').html(obj.total.L['ret_c_t']);
                $('#ComparisionFixed #totalRftd').html(obj.total.R['ftd']);
                $('#ComparisionFixed #totalRret').html(obj.total.R['ret']);
                $('#ComparisionFixed #totalRftd_c').html(obj.total.R['ftd_c']);
                $('#ComparisionFixed #totalRret_c').html(obj.total.R['ret_c_t']);

                $('#ComparisionFixed #endL').val(obj.endl);
                $('#ComparisionFixed #endR').val(obj.endr);

                $('.t_Count').show();

                let fixFTDCanvasId = 'ComparisionFixedFTD-'+cmid;
                $('#c-ComparisionFixedFTD').html('<canvas id="'+fixFTDCanvasId+'" width="400" height="115"></canvas>');
                var ComparisionFixedFTD = document.getElementById(fixFTDCanvasId).getContext('2d');
                var ComparisionFixFTD = new Chart(ComparisionFixedFTD, {
                    type: 'line',
                    data: {
                        labels: obj.range,
                        datasets: [{
                            label: 'Left',
                            data: obj.L['ftd'],
                            backgroundColor: '#f50ab1',
                            borderColor: 'rgba(235, 110, 199,0.46)',
                            fill: false
                        },{
                            label: 'Right',
                            data: obj.R['ftd'],
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
                                    labelString: 'FTD.Amouunt'
                                }
                            }]
                        }
                    }
                });

                let fixRETCanvasId = 'ComparisionFixedRET-'+cmid;
                $('#c-ComparisionFixedRET').html('<canvas id="'+fixRETCanvasId+'" width="400" height="115"></canvas>');
                var ComparisionFixedRET = document.getElementById(fixRETCanvasId).getContext('2d');
                var ComparisionFixRET = new Chart(ComparisionFixedRET, {
                    type: 'line',
                    data: {
                        labels: obj.range,
                        datasets: [{
                            label: 'Left',
                            data: obj.L['ret'],
                            backgroundColor: '#f50ab1',
                            borderColor: 'rgba(235, 110, 199,0.46)',
                            fill: false
                        },{
                            label: 'Right',
                            data: obj.R['ret'],
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
                                    labelString: 'RET.Amount'
                                }
                            }]
                        }
                    }
                });

                let fixRETCCanvasId = 'ComparisionFixedRETC-'+cmid;
                $('#c-ComparisionFixedRETC').html('<canvas id="'+fixRETCCanvasId+'" width="400" height="115"></canvas>');
                var ComparisionFixedRETC = document.getElementById(fixRETCCanvasId).getContext('2d');
                var ComparisionFixRETC = new Chart(ComparisionFixedRETC, {
                    type: 'line',
                    data: {
                        labels: obj.range,
                        datasets: [{
                            label: 'Left',
                            data: obj.L['ret_c_t'],
                            backgroundColor: '#f50ab1',
                            borderColor: 'rgba(235, 110, 199,0.46)',
                            fill: false
                        },{
                            label: 'Right',
                            data: obj.R['ret_c_t'],
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
                                    labelString: 'RET.Count'
                                }
                            }]
                        }
                    }
                });

                let fixFTDCCanvasId = 'ComparisionFixedFTDC-'+cmid;
                $('#c-ComparisionFixedFTDC').html('<canvas id="'+fixFTDCCanvasId+'" width="400" height="115"></canvas>');
                var ComparisionFixedFTDC = document.getElementById(fixFTDCCanvasId).getContext('2d');
                var ComparisionFixFTDC = new Chart(ComparisionFixedFTDC, {
                    type: 'line',
                    data: {
                        labels: obj.range,
                        datasets: [{
                            label: 'Left',
                            data: obj.L['ftd_c'],
                            backgroundColor: '#f50ab1',
                            borderColor: 'rgba(235, 110, 199,0.46)',
                            fill: false
                        },{
                            label: 'Right',
                            data: obj.R['ftd_c'],
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
                                    labelString: 'RET.Count'
                                }
                            }]
                        }
                    }
                });
                form.show();
                $('#ComparisionFixed #loading').hide();
            });
        });
    });
</script>