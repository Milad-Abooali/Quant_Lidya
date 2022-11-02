<?php

    global $db;
    $units = $db->selectAll('units');

?>

<div id="w-monthly" class="row">
    <div class="form-report_topten form-inline col-auto">
        <select class="form-control mr-2 unit" multiple>
            <option value="" disabled selected>Target</option>
            <?php foreach($units as $unit) { ?>
                <option value="<?= $unit['name'] ?>"><?= $unit['name'] ?></option>
            <?php } ?>
        </select>
        <select class="form-control mr-2 month" multiple>
            <option value="" disabled>Month</option>
            <option value="01" <?= (date('m')=='01') ? 'selected' : null ?>>Jan</option>
            <option value="02" <?= (date('m')=='02') ? 'selected' : null ?>>Feb</option>
            <option value="03" <?= (date('m')=='03') ? 'selected' : null ?>>Mar</option>
            <option value="04" <?= (date('m')=='04') ? 'selected' : null ?>>Apr</option>
            <option value="05" <?= (date('m')=='05') ? 'selected' : null ?>>May</option>
            <option value="06" <?= (date('m')=='06') ? 'selected' : null ?>>Jun</option>
            <option value="07" <?= (date('m')=='07') ? 'selected' : null ?>>Jul</option>
            <option value="08" <?= (date('m')=='08') ? 'selected' : null ?>>Aug</option>
            <option value="09" <?= (date('m')=='09') ? 'selected' : null ?>>Sep</option>
            <option value="10" <?= (date('m')==10) ? 'selected' : null ?>>Oct</option>
            <option value="11" <?= (date('m')==11) ? 'selected' : null ?>>Nov</option>
            <option value="12" <?= (date('m')==12) ? 'selected' : null ?>>Dec</option>
        </select>
        <select class="form-control mr-2 year" multiple>
            <option value="" disabled>Year</option>
            <?= (date('Y')==2019) ? 'selected' : null ?>
            <option value="2019" <?= (date('Y')==2019) ? 'selected' : null ?>>2019</option>
            <option value="2020" <?= (date('Y')==2020) ? 'selected' : null ?>>2020</option>
            <option value="2021" <?= (date('Y')==2021) ? 'selected' : null ?>>2021</option>
            <option value="2022" <?= (date('Y')==2022) ? 'selected' : null ?>>2022</option>
            <option value="2023" <?= (date('Y')==2023) ? 'selected' : null ?>>2023</option>
            <option value="2024" <?= (date('Y')==2024) ? 'selected' : null ?>>2024</option>
            <option value="2025" <?= (date('Y')==2025) ? 'selected' : null ?>>2025</option>
        </select>
    </div>
    <div class="form-report_topten col-auto">

      <button class="doA-load btn btn-primary mt-2">Load</button>
    </div>

    <div class="col text-right p-3">
        <a id="doD-wg-report_topten" download="top=10.jpg" href="" class="doD-img btn-sm btn btn-light " title="Download Image">
            <i class="fa fa-download"></i> Snapshot
        </a>
        </a>
    </div>
    <div id="c-topTen" class="col-12"></div>
</div>
<script>
$(document).ready( function () {

    $('.doD-img,#c-monthlySum').hide();
    /* Screenshot Chart */
    var doD_wg_report_topten;
    $("body").on("click","#doD-wg-report_topten", function(e) {
        $("#doD-wg-report_topten").attr("href", doD_wg_report_topten);
    });


    $("body").on("click",".form-report_topten .doA-load", function(e) {

        let unit = $('.form-report_topten .unit').val();
        let month = $('.form-report_topten .month').val();
        let year = $('.form-report_topten .year').val();

        let data = {
            u: unit,
            m: month,
            y: year
        }
        let cid = 0;

        ajaxCall('reports', 'topTen', data, function(Res){
            let obj = JSON.parse(Res);
            cid++;
            $('.doD-img').hide();

            let amountCanvasId = 'topTen-'+cid;
            $('#c-topTen').html('<canvas id="'+amountCanvasId+'" width="400" height="200"></canvas>');
            var topTen = document.getElementById(amountCanvasId).getContext('2d');
            var topTenScatter = new Chart(topTen, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'FTD',
                        data: obj.res.c,
                        backgroundColor: '#34f506',
                        borderColor: 'rgba(52,245,6,0.46)',
                        fill: false
                    },{
                        label: 'Retention',
                        data: obj.res.r,
                        backgroundColor: '#4681f5',
                        borderColor: 'rgba(70,129,245,0.51)',
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom'
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].label || '';
                                return label + ': (' + tooltipItem.xLabel + ', ' + tooltipItem.yLabel + ')';
                            }
                        }
                    }
                }
            });

            $('#c-monthlySum').fadeIn();
            setTimeout(function(){
                let divp = $('#w-monthly')[0];
                $('#wg-report_topten .btn').addClass('d-none');
                $('#wg-report_topten .form-report_topten').hide();
                html2canvas(divp).then(function(canvas) {
                    doD_wg_report_topten = canvas.toDataURL('image/png');
                    $('#wg-report_topten .btn').removeClass('d-none');
                });
                $('.doD-img').fadeIn();
                $('#wg-report_topten .form-report_topten').fadeIn();
            }, 500);
        });
    });

});
</script>