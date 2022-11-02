<form action="" method="post" autocomplete="off">
    <div class="form-row">
        <div class="form-group col-md-2">

        </div>
        <div class="form-group col-md-3 date">
            <label for="inputstartTime">Start Time</label>
            <div class="input-group">
                <input type="text" class="form-control" id="startTime" name="startTime" value="" required="">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="form-group col-md-3 date">
            <label for="inputendTime">End Time</label><i class="mdi mdi-update"></i>
            <div class="input-group">
                <input type="text" class="form-control" id="endTime" name="endTime" value="" required="">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="form-group col-md-2">
            <label for="input">&nbsp;</label>
            <input class="btn btn-primary form-control" type="submit" name="submit" value="Submit">
        </div>
    </div>
</form>


<script>
    $(document).ready( function () {

        ajaxCall('marketing', 'leadsDeposit', '', function(Res){

        });

        $('#startTime').datepicker({
            uiLibrary: 'bootstrap',
            iconsLibrary: 'fontawesome',
            format: 'yyyy-mm-dd'
        });

        $('#endTime').datepicker({
            uiLibrary: 'bootstrap',
            iconsLibrary: 'fontawesome',
            format: 'yyyy-mm-dd'
        });
    });
</script>