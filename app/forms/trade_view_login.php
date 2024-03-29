<?php
    global $db;

    $mt5api = new mt5API();

    /* Get Group */
    $group = eFun::getLoginGroup($params['login']);
    if($group['name']) $is_demo = $group['is_demo'];

    /* Get Login Detail */
    $api_account['login'] = $params['login'];
    $mt5api->get('/api/user/account/get', $api_account);
    $e = $mt5api->Error;
    $api_account = $mt5api->Response;
    $number_digit = $api_account->answer->CurrencyDigits;
?>
<?php $form_name = 'trade-view-login'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>" data-login="<?= $params['login'] ?>">

    <div class="row text-secondary">
        <div class="col text-start">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="intervalResLoginPositions" checked>
                <label class="form-check-label" for="intervalResLoginPositions">Live</label>
            </div>
        </div>
        <div class="col text-end">
            <span class="text-opacity-25">Last Update: </span><br>
            <span id="update-time" class="text-light"></span>
        </div>
    </div>
    <br>

    <div class="row text-secondary">
        <div class="col text-end">
            <button data-form-params='{"login":"<?= $params['login'] ?>"}' data-login="<?= $params['login'] ?>" data-form-name="trade_view_history" title="<?= $params['login'] ?> History" class="doM-form ms-2 btn btn-secondary"><i class="fa fa-history"></i> History </button>
            <button data-form-params='{"login":"<?= $params['login'] ?>"}' data-login="<?= $params['login'] ?>" data-form-name="trade_view_pending" title="<?= $params['login'] ?> Pending Orders" class="doM-form ms-2 btn btn-secondary"><i class="fa fa-clock"></i> Pending </button>
        </div>

    </div>


    <h3 class="text-secondary">Statistics</h3>
    <table id="loginStatistics" class="table table-sm table-dark">
        <tbody>
            <tr class="item-row">
                <td> Login Type </td>
                <td data-lable="login-type"> <?= ($is_demo) ? 'Demo' : 'Real' ?></td>
            </tr>
            <tr class="item-row">
                <td> Balance </td>
                <td> $ <span data-lable="balance"><?= GF::nf($api_account->answer->Balance, $number_digit) ?></span></td>
            </tr>
            <tr class="item-row">
                <td> Equity </td>
                <td> $ <span data-lable="equity"><?= GF::nf($api_account->answer->Equity, $number_digit) ?></span></td>
            </tr>
            <tr class="item-row">
                <td> Margin </td>
                <td> $ <span data-lable="margin"><?= GF::nf($api_account->answer->Margin, $number_digit) ?></span></td>
            </tr>
            <tr class="item-row">
                <td> Margin Level </td>
                <td> % <span data-lable="margin-level"><?= GF::nf($api_account->answer->MarginLevel, $number_digit) ?></span></td>
            </tr>
            <tr class="item-row">
                <td> Free Margin </td>
                <td> $ <span data-lable="free-margin"><?= GF::nf($api_account->answer->MarginFree, $number_digit) ?></span></td>
            </tr>
            <tr class="item-row">
                <td> Leverage </td>
                <td> <span data-lable="margin-leverage"><?= GF::nf($api_account->answer->MarginLeverage, $number_digit) ?></span></td>
            </tr>
            <tr class="item-row">
                <td> Profit </td>
                <td> <span data-lable="profit"><?= GF::nf($api_account->answer->Profit, $number_digit) ?></span></td>
            </tr>
        </tbody>
    </table>

    <h3 class="text-secondary">Positions</h3>
    <div class="row mb-2 text-end">
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                Column
            </button>
            <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenuButton1">
                <li class="ps-3" data-column="3">
                    <div class="form-check">
                        <input class="table-hide-column form-check-input" type="checkbox" id="col-3" data-column="3" checked>
                        <label class="form-check-label" for="col-3">
                            Action
                        </label>
                    </div>
                </li>
                <li class="ps-3" data-column="4">
                    <div class="form-check">
                        <input class="table-hide-column form-check-input" type="checkbox" id="col-4" data-column="4">
                        <label class="form-check-label" for="col-4">
                            Time Create
                        </label>
                    </div>
                </li>
                <li class="ps-3" data-column="5">
                    <div class="form-check">
                        <input class="table-hide-column form-check-input" type="checkbox" id="col-5" data-column="5" checked>
                        <label class="form-check-label" for="col-5">
                            Volume
                        </label>
                    </div>
                </li>
                <li class="ps-3" data-column="6">
                    <div class="form-check">
                        <input class="table-hide-column form-check-input" type="checkbox" id="col-6" data-column="6" checked>
                        <label class="form-check-label" for="col-6">
                            Price Open
                        </label>
                    </div>
                </li>
                <li class="ps-3" data-column="7">
                    <div class="form-check">
                        <input class="table-hide-column form-check-input" type="checkbox" id="col-7" data-column="7">
                        <label class="form-check-label" for="col-7">
                            Price SL
                        </label>
                    </div>
                </li>
                <li class="ps-3" data-column="8">
                    <div class="form-check">
                        <input class="table-hide-column form-check-input" type="checkbox" id="col-8" data-column="8">
                        <label class="form-check-label" for="col-8">
                            Price TP
                        </label>
                    </div>
                </li>
                <li class="ps-3" data-column="9">
                    <div class="form-check">
                        <input class="table-hide-column form-check-input" type="checkbox" id="col-9" data-column="9" checked>
                        <label class="form-check-label" for="col-9">
                            Price Current
                        </label>
                    </div>
                </li>
                <li class="ps-3" data-column="10">
                    <div class="form-check">
                        <input class="table-hide-column form-check-input" type="checkbox" id="col-10" data-column="10">
                        <label class="form-check-label" for="col-10">
                            Storage
                        </label>
                    </div>
                </li>
                <li class="ps-3" data-column="11">
                    <div class="form-check">
                        <input class="table-hide-column form-check-input" type="checkbox" id="col-11" data-column="11" checked>
                        <label class="form-check-label" for="col-11">
                            Profit
                        </label>
                    </div>
                </li>
        </div>
    </div>
    <div class="wrapper-dt">
        <table id="loginPositions" class="display table-sm" style="width:100%">
            <thead>
            <tr>
                <th>Close</th>
                <th>Position</th>
                <th>Symbol</th>
                <th>Action</th>
                <th>TimeCreate</th>
                <th>Volume</th>
                <th>PriceOpen</th>
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
    var dtPosition = $('table#loginPositions').DataTable({
        stateSave: true,
        serverSide: false,
        deferRender: true,
        retrieve: true,
        paging: false,
        searching: false,
        responsive: false,
        columnDefs: [],
        data: [],
        columns: [
            { data: 'Close' },
            { data: 'Position' },
            { data: 'Symbol' },
            { data: 'Action' },
            { data: 'TimeCreate' },
            { data: 'Volume' },
            { data: 'PriceOpen' },
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
    $("form#trade-view-login #update-time").text(rDT().dateTime);
    selectedLogin = $('form#trade-view-login').data('login');
    intervalLoginPositions();
    $("form#trade-view-login .table-hide-column").each(function( index ) {
        const column = parseInt($(this).attr('data-column'));
        $(this).prop("checked", hiddenColumn[column]);
        $(this).attr("checked", hiddenColumn[column]);
    });
    updateStatistics();
    updatePositions();
</script>