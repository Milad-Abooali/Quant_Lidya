<?php

    global $db;
    $resultSource = $db->select('marketing_report', 0, 'src', 0, 0, 'src');


?>

<div id="w-monthly" class="row">
    <div id="p-monthlyAmount" class="form-inline col">
        <select class="form-control col-md-4 mr-2 source" multiple>
            <option value="" disabled selected>Source</option>
            <?php foreach($resultSource as $source) { ?>
                <option value="<?= $source['src'] ?>"><?= $source['src'] ?></option>
            <?php } ?>
        </select>
        <select class="form-control mr-2 month" multiple>
            <option value="" disabled selected>Month</option>
            <option value="01">Jan</option>
            <option value="02">Feb</option>
            <option value="03">Mar</option>
            <option value="04">Apr</option>
            <option value="05">May</option>
            <option value="06">Jun</option>
            <option value="07">Jul</option>
            <option value="08">Aug</option>
            <option value="09">Sep</option>
            <option value="10">Oct</option>
            <option value="11">Nov</option>
            <option value="12">Dec</option>
        </select>
        <select class="form-control mr-2 year">
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
        <a id="doD-wg-marketing_month" download="ChartAmount.jpg" href="" class="doD-img btn-sm btn btn-light " title="Download Image">
            <i class="fa fa-download"></i> All
        </a>
        <a id="doD-monthlyAmount" download="ChartAmount.jpg" href="" class="doD-img btn btn-sm btn-light" title="Download Image">
            <i class="fa fa-download"></i> Amount
        </a>
        <a id="doD-monthlyCount" download="ChartCount.jpg" href="" class="doD-img btn btn-sm btn-light" title="Download Image">
            <i class="fa fa-download"></i> Count
        </a>
    </div>
    <div id="c-monthlySum" class="my-3 border-bottom border-top w-100 row mx-3">
        <div class="col">FTD: <strong id="ftd" class="text-success"></strong></div>
        <div class="col">RET: <strong id="ret" class="text-primary"></strong></div>
        <div class="col">FTD: <strong id="ftd_c" class="text-success"></strong></div>
        <div class="col">RET time(s): <strong id="ret_c_t" class="text-primary"></strong></div>
    </div>
    <div id="c-monthlyAmount" class="col-12"></div>
    <div id="c-monthlyCount" class="col-12"></div>
</div>
<script>
$(document).ready( function () {

    $('.doD-img,#c-monthlySum').hide();
    var doD_wg_marketing_month;
    $("body").on("click","#doD-wg-marketing_month", function(e) {
        $("#doD-wg-marketing_month").attr("href", doD_wg_marketing_month);
    });


    $("body").on("click","#p-monthlyAmount .doA-load", function(e) {

        let src = $('#p-monthlyAmount .source').val();
        let month = $('#p-monthlyAmount .month').val();
        let year = $('#p-monthlyAmount .year').val();

        let data = {
            s: src,
            m: month,
            y: year
        }
        console.log(data);
        let cid = 0;

        $("body").on("click","#doD-monthlyAmount", function(e) {
            let canvas = $("#monthlyAmount-"+cid);
            let url_base64jp = canvas[0].toDataURL('image/png');
            $(this).attr("href", url_base64jp);
        });

        $("body").on("click","#doD-monthlyCount", function(e) {
            let canvas = $("#monthlyCount-"+cid);
            let url_base64jp = canvas[0].toDataURL('image/png');
            $(this).attr("href", url_base64jp);
        });

        ajaxCall('marketing', 'reportMonth', data, function(Res){
            let obj = JSON.parse(Res);
            cid++;
            $('.doD-img').hide();

            $('#c-monthlySum #ftd').html('$ '+obj.total.ftd);
            $('#c-monthlySum #ret').html('$ '+obj.total.ret);
            $('#c-monthlySum #ftd_c').html(obj.total.ftd_c);
            $('#c-monthlySum #ret_c_t').html(obj.total.ret_c_t);

            let amountCanvasId = 'monthlyAmount-'+cid;
            $('#c-monthlyAmount').html('<canvas id="'+amountCanvasId+'" width="400" height="115"></canvas>');
            var monthlyAmount = document.getElementById(amountCanvasId).getContext('2d');
            var MonthAmount = new Chart(monthlyAmount, {
                type: 'line',
                data: {
                    labels: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
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

            let countCanvasId = 'monthlyCount-'+cid;
            $('#c-monthlyCount').html('<canvas id="'+countCanvasId+'" width="400" height="80"></canvas>');
            var monthlyCount = document.getElementById(countCanvasId).getContext('2d');
            var MonthCount = new Chart(monthlyCount, {
                type: 'line',
                data: {
                    labels: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
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
            $('#c-monthlySum').fadeIn();
            setTimeout(function(){
                let divp = $('#w-monthly')[0];
                $('#wg-marketing_month .btn').addClass('d-none');
                $('#wg-marketing_month #p-monthlyAmount').hide();
                html2canvas(divp).then(function(canvas) {
                    doD_wg_marketing_month = canvas.toDataURL('image/png');
                    $('#wg-marketing_month .btn').removeClass('d-none');
                });
                $('.doD-img').fadeIn();
                $('#wg-marketing_month #p-monthlyAmount').fadeIn();
            }, 500);
        });
    });

});
</script>