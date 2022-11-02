<?php

global $db;
$resultSource = $db->select('marketing_report', 0, 'src', 0, 0, 'src');
$resultCamps = $db->select('user_marketing', 0, 'lead_camp', 0, 0, 'lead_camp');
$resultUnit = $db->select('units');

?>

        <div id="DepositReportDeeply" >
            <div id="loading" class="text-center my-3 h4"><i class="fas fa-spinner fa-spin"></i> Loading Data</div>
            <form id="leadsDpositForm" action="" method="post" autocomplete="off">
                <div class="form-row">
                    <div class="form-group col-md-1">
                        <div class="custom-control custom-switch mb-1">
                            <input data-target="units" name="inc_units" id="inc_units" type="checkbox" class="custom-control-input enableFilter">
                            <label class="custom-control-label" for="inc_units">Units</label>
                        </div>
                        <select id="units" class="form-control form-select3" name="units" multiple>
                            <option value="**">* (All)</option>
                            <?php foreach($resultUnit as $item) { ?>
                                <option value="<?= $item['name'] ?>"><?= $item['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <div class="custom-control custom-switch mb-1">
                            <input data-target="sources" name="inc_source" id="inc_source" type="checkbox" class="custom-control-input enableFilter">
                            <label class="custom-control-label" for="inc_source">Sources</label>
                        </div>
                        <select id="sources" class="form-control form-select3" name="sources" multiple>
                            <option value="**">* (All)</option>
                            <?php foreach($resultSource as $item) { ?>
                                <option value="<?= strtolower($item['src']) ?>"><?= $item['src'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <div class="custom-control custom-switch mb-1">
                            <input data-target="campaigns" name="inc_camp" id="inc_camp" type="checkbox" class="custom-control-input enableFilter">
                            <label class="custom-control-label" for="inc_camp">Main Campaign </label>
                        </div>
                        <select id="campaigns" class="form-control form-select3" name="campaigns" multiple>
                            <option value="**">* (All)</option>
                            <?php foreach($resultCamps as $item) { ?>
                                <option value="<?= strtolower($item['lead_camp']) ?>"><?= $item['lead_camp'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <div class="custom-control custom-switch mb-1">
                            <input data-target="ex_campaigns" name="inc_excamp" id="inc_excamp" type="checkbox" class="custom-control-input enableFilter">
                            <label class="custom-control-label" for="inc_excamp">Extra Campaigns </label>
                        </div>
                        <div class="input-group">
                            <input class="form-control" id="ex_campaigns" name="ex_campaigns" placeholder="Extra Campaigns"><br>
                            <div class="input-group-append">
                                <span class="input-group-text" data-toggle="tooltip" data-placement="top" data-original-title="Comma Separated"><i class="mdi mdi-information-outline"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-2 date">
                        <label for="inputstartTime">Start Time</label>
                        <div class="input-group">
                            <input type="text" class="dateCustom form-control" id="startTime" name="startTime" value="<?= date("Y-m-d", strtotime("first day of this month")) ?>" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-2 date">
                        <label for="inputendTime">End Time</label><i class="mdi mdi-update"></i>
                        <div class="input-group">
                            <input type="text" class="dateCustom form-control" id="endTime" name="endTime" value="<?= date("Y-m-d", strtotime("last day of this month")) ?>" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-1">
                        <label for="input">&nbsp;</label>
                        <input class="btn btn-primary form-control" type="submit" name="submit" value="Submit">
                    </div>
                    <div class="t_Count form-group col text-right">
                        <div class="my-2">$ <strong id="ftd" class="text-success"></strong> FTD by <strong id="ftd_c" class="text-success"></strong> Clients</div>
                        <div class="">$ <strong id="ret" class="text-primary"></strong> RET in <strong id="ret_c_t" class="text-primary"></strong> time(s) by <strong id="ret_c" class="text-primary"></strong> Clients</div>
                    </div>
                </div>
            </form>
            <hr>
            <div id="p-leadstable"></div>
        </div>

<script>
    $(document).ready( function () {
        $('.form-select3').selectpicker({
            tickIcon: 'fas fa-check',
            liveSearch: true
        });
        let ctid=0;
        $('.t_Count,#loading').hide();

        $("body").on("change",".enableFilter", function(e) {
            let target = $(this).data('target');
            let required = $(this).is(":checked");
            $('#'+target).attr('required',required)
        });

        $("body").on("submit","form#leadsDpositForm", function(e) {
            $('#DepositReportDeeply #loading').show();
            let form = $(this);
            form.hide();
            ctid++;
            $('#DepositReportDeeply #p-leadstable').html('<table id="leadsDeposit-'+ctid+'" class="display table table-hover" style="width:100%"></table>');
            e.preventDefault();
            let data = {
                startTime: $('#DepositReportDeeply #startTime').val(),
                endTime: $('#DepositReportDeeply #endTime').val(),
                units: $('#DepositReportDeeply #units').val(),
                inc_units: $('#DepositReportDeeply #inc_units').is(":checked"),
                sources: $('#DepositReportDeeply #sources').val(),
                inc_source: $('#DepositReportDeeply #inc_source').is(":checked"),
                campaigns: $('#DepositReportDeeply #campaigns').val(),
                inc_camp: $('#DepositReportDeeply #inc_camp').is(":checked"),
                ex_campaigns: $('#DepositReportDeeply #ex_campaigns').val(),
                inc_excamp: $('#DepositReportDeeply #inc_excamp').is(":checked")
            }
            ajaxCall('marketing', 'leadsDeposit', data, function(Res){
                let resObj = JSON.parse(Res);
                var Dtable = $('#DepositReportDeeply #leadsDeposit-'+ctid).DataTable({
                    data: resObj.list,
                    columns: [
                        { title: "Login" },
                        { title: "Email" },
                        { title: "Unit" },
                        { title: "Source" },
                        { title: "Campaign" },
                        { title: "Campaign Extra" },
                        { title: "FTD Time" },
                        { title: "FTD Amount" },
                        { title: "RET Amount" },
                        { title: "RET Count" }
                    ],
                    buttons: [
                        'excelHtml5',
                        'copyHtml5'
                    ],
                    dom: 'Bfrtip',
                });
                Dtable.buttons().container().appendTo($('.dataTables_filter', Dtable.table().container()));
                $('#DepositReportDeeply .t_Count #ftd').html((resObj.sum.ftd || 0).toFixed(2));
                $('#DepositReportDeeply .t_Count #ftd_c').html(resObj.sum.ftd_c || 0);
                $('#DepositReportDeeply .t_Count #ret').html((resObj.sum.ret || 0).toFixed(2));
                $('#DepositReportDeeply .t_Count #ret_c').html(resObj.sum.ret_c || 0);
                $('#DepositReportDeeply .t_Count #ret_c_t').html(resObj.sum.ret_c_t || 0);
                $('#DepositReportDeeply .t_Count').fadeIn();
                form.fadeIn();
                $('#DepositReportDeeply #loading').fadeOut();
            });
        });

    });
</script>