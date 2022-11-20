<?php
    global $db;

    $mt5api = new mt5API();

    /* Get Group */
    $group = eFun::getLoginGroup($params['login']);
    if($group['name']) $is_demo = $group['is_demo'];

?>
<?php $form_name = 'trade-view-history'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>" data-login="<?= $params['login'] ?>">


    <div class="row text-secondary">
        <div class="col text-start">
            <label for="start-date">Start</label>
            <input id="start-date" class="form-control" type="date" value='<?php echo date('Y-m-d',strtotime("-1 days"));?>' />
        </div>
        <div class="col text-end">
            <label for="end-date">End</label>
            <input id="end-date" class="form-control" type="date" value='<?php echo date('Y-m-d',strtotime("+1 days"));?>' />
        </div>
    </div>
    <br>



    <h3 class="text-secondary">History</h3>
    <div class=" ">
        <table id="loginHistory" class="display table-sm" style="width:100%">
            <thead>
            <tr>
                <th>Deal</th>
                <th>Symbol</th>
                <th>Action</th>
                <th>Time</th>
                <th>Volume</th>
                <th>Price</th>
                <th>PriceSL</th>
                <th>PriceTP</th>
                <th>PriceCurrent</th>
                <th>Storage</th>
                <th>Profit</th>
            </tr>
            </thead>
        </table>
    </div>

    <div id="form-actions" class="text-end d-none">
        <button type="button" class="btn btn-outline-danger me-2" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
<script>
    var dtHistory = $('table#loginHistory').DataTable({
        stateSave: true,
        serverSide: false,
        deferRender: true,
        retrieve: true,
        paging: true,
        pageLength: 10,
        searching: false,
        responsive: false,
        columnDefs: [],
        data: [],
        columns: [
            { data: 'Deal' },
            { data: 'Symbol' },
            { data: 'Action' },
            { data: 'Time' },
            { data: 'Volume' },
            { data: 'Price' },
            { data: 'PriceSL' },
            { data: 'PriceTP' },
            { data: 'PriceCurrent' },
            { data: 'Storage' },
            { data: 'Profit' }
        ]
    });

    try{
        <?= str_replace('-','_', $form_name) ?>;
    }
    catch(e) {
        if(e.name == "ReferenceError") {
            <?= str_replace('-','_', $form_name) ?> = true;
            $.ajax({
                async: false,
                url: "app/assets/js/<?= $form_name ?>.js",
                dataType: "script"
            });
        }
    }
    let startDate=$('form#trade-view-history #start-date').val();
    let endDate=$('form#trade-view-history #end-date').val();
    updateHistory();
    selectedLogin = $('form#trade-view-history').data('login');
</script>