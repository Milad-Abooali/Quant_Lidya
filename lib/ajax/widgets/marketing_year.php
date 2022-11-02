<?php

global $db;
$resultSource = $db->select('marketing_report', 0, 'src', 0, 0, 'src');

?>

<div id="w-annual" class="row">
    <div id="p-annualAmount" class="form-inline col">
        <select class="form-control col-md-4 mr-2 source" multiple>
            <option value="" disabled selected>Source</option>
            <?php foreach($resultSource as $source) { ?>
                <option value="<?= $source['src'] ?>"><?= $source['src'] ?></option>
            <?php } ?>
        </select>
        <select class="form-control mr-2 year" multiple>
            <option value="" disabled selected>Year</option>
            <option value="2020">2019</option>
            <option value="2020">2020</option>
            <option value="2021">2021</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
        </select>
        <button class="doA-load btn btn-primary">Load</button>
    </div>
    <div class="col text-right p-5">
        <a id="doD-wg-marketing_year" download="ChartAmount.jpg" href="" class="doD-img btn-sm btn btn-light " title="Download Image">
            <i class="fa fa-download"></i> All
        </a>
        <a id="doD-annualAmount" download="ChartAmount.jpg" href="" class="doD-img btn btn-sm btn-light" title="Download Image">
            <i class="fa fa-download"></i> Amount
        </a>
        <a id="doD-annualCount" download="ChartCount.jpg" href="" class="doD-img btn btn-sm btn-light" title="Download Image">
            <i class="fa fa-download"></i> Count
        </a>
    </div>
    <div id="c-annualSum" class="my-3 border-bottom border-top w-100 row mx-3">
        <div class="col">FTD: <strong id="ftd" class="text-success"></strong></div>
        <div class="col">RET: <strong id="ret" class="text-primary"></strong></div>
        <div class="col">FTD Count: <strong id="ftd_c" class="text-success"></strong></div>
        <div class="col">RET time(s): <strong id="ret_c_t" class="text-primary"></strong></div>
    </div>
    <div id="c-annualAmount" class="col-12"></div>
    <div id="c-annualCount" class="col-12"></div>
</div>
<script>
    $(document).ready( function () {

        $('.doD-img,#c-annualSum').hide();
        var doD_wg_marketing_Year;
        $("body").on("click","#doD-wg-marketing_year", function(e) {
            $("#doD-wg-marketing_year").attr("href", doD_wg_marketing_Year);
        });


        $("body").on("click","#p-annualAmount .doA-load", function(e) {

            let src = $('#p-annualAmount .source').val();
            let Year = $('#p-annualAmount .Year').val();
            let year = $('#p-annualAmount .year').val();

            let data = {
                s: src,
                m: Year,
                y: year
            }
            console.log(data);
            let cid = 0;

            $("body").on("click","#doD-annualAmount", function(e) {
                let canvas = $("#annualAmount-"+cid);
                let url_base64jp = canvas[0].toDataURL('image/png');
                $(this).attr("href", url_base64jp);
            });

            $("body").on("click","#doD-annualCount", function(e) {
                let canvas = $("#annualCount-"+cid);
                let url_base64jp = canvas[0].toDataURL('image/png');
                $(this).attr("href", url_base64jp);
            });

            ajaxCall('marketing', 'reportYear', data, function(Res){
                let obj = JSON.parse(Res);
                cid++;
                $('.doD-img').hide();

                $('#c-annualSum #ftd').html('$ '+obj.total.ftd);
                $('#c-annualSum #ret').html('$ '+obj.total.ret);
                $('#c-annualSum #ftd_c').html(obj.total.ftd_c);
                $('#c-annualSum #ret_c_t').html(obj.total.ret_c_t);

                let amountCanvasId = 'annualAmount-'+cid;
                $('#c-annualAmount').html('<canvas id="'+amountCanvasId+'" width="400" height="115"></canvas>');
                var annualAmount = document.getElementById(amountCanvasId).getContext('2d');
                var YearAmount = new Chart(annualAmount, {
                    type: 'line',
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct", "Nov", "Dec"],
                        datasets: [{
                            label: 'FTD Amount',
                            data: obj.ftd,
                            backgroundColor: '#34f506',
                            borderColor: 'rgba(52,245,6,0.46)',
                            fill: false
                        },{
                            label: 'RET Amount',
                            data: obj.ret,
                            backgroundColor: '#4681f5',
                            borderColor: 'rgba(70,129,245,0.51)',
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
                                    labelString: 'Amount'
                                }
                            }]
                        }
                    }
                });

                let countCanvasId = 'annualCount-'+cid;
                $('#c-annualCount').html('<canvas id="'+countCanvasId+'" width="400" height="80"></canvas>');
                var annualCount = document.getElementById(countCanvasId).getContext('2d');
                var YearCount = new Chart(annualCount, {
                    type: 'line',
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct", "Nov", "Dec"],
                        datasets: [{
                            label: 'FTD Count',
                            data: obj.ftd_c,
                            backgroundColor: '#ddf506',
                            borderColor: 'rgba(229,245,6,0.46)',
                            fill: false
                        },{
                            label: 'RET Count',
                            data: obj.ret_c_t,
                            backgroundColor: '#46f5ef',
                            borderColor: 'rgba(70,230,245,0.51)',
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
                                    labelString: 'Count'
                                }
                            }]
                        }
                    }
                });
                $('#c-annualSum').fadeIn();
                setTimeout(function(){
                    let divp = $('#w-annual')[0];
                    $('#wg-marketing_year .btn').addClass('d-none');
                    $('#wg-marketing_year #p-annualAmount').hide();
                    html2canvas(divp).then(function(canvas) {
                        doD_wg_marketing_Year = canvas.toDataURL('image/png');
                        $('#wg-marketing_year .btn').removeClass('d-none');
                    });
                    $('.doD-img').fadeIn();
                    $('#wg-marketing_year #p-annualAmount').fadeIn();
                }, 500);
            });
        });

    });
</script>