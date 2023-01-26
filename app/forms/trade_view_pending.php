<?php

?>
<?php $form_name = 'trade-view-pending'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>" data-login="<?= $params['login'] ?>">

    <h3 class="text-secondary">Pending</h3>
    <div class=" ">
        <table id="loginPending" class="display table-sm" style="width:100%">
            <thead>
            <tr>
                <th>Order</th>
                <th>Symbol</th>
                <th>Type</th>
                <th>Volume</th>
                <th>PriceOrder</th>
                <th>PriceSL</th>
                <th>PriceTP</th>
                <th>PriceCurrent</th>
                <th>PriceTrigger</th>
                <th>TypeTime</th>
                <th>TimeExpiration</th>
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
    var dtPending = $('table#loginPending').DataTable({
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
            { data: 'Order' },
            { data: 'Symbol' },
            { data: 'Type' },
            { data: 'Volume' },
            { data: 'PriceOrder' },
            { data: 'PriceSL' },
            { data: 'PriceTP' },
            { data: 'PriceCurrent' },
            { data: 'PriceTrigger' },
            { data: 'TypeTime' },
            { data: 'TimeExpiration' }
        ]
    });

    selectedLogin = <?= $params['login'] ?>;

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
    updatePending();
</script>