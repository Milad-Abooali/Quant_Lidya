<?php

    global $db;

    $brokers = $db->selectAll('brokers');
    $units = $db->selectAll('units');
    $brokers_units = array();
    if($units) foreach ($units as $unit) {
        $brokers_units[$unit['broker_id']][$unit['id']] = $unit;
    }

    $query['db']        = 'DB_admin';
    $query['table']     = 'wallet_types';
    $query['table_html']     = 'wallet_types';
    $query['key']       = 'id';

    $query['columns']   = array(
        array(
            'db' => 'id',
            'th' => '#',
            'dt' => 0
        ),
        array(
            'db' => 'title',
            'th' => 'Title',
            'dt' => 1
        ),
        array(
            'db' => 'sym',
            'th' => 'Symbol',
            'dt' => 2
        ),
        array(
            'db' => 'rate',
            'th' => 'Rate',
            'dt' => 3
        ),
        array(
            'db' => 'broker_id',
            'th' => 'Broker',
            'dt' => 4,
            'formatter' => true
        ),
        array(
            'db' => 'unit_id',
            'th' => 'Unit',
            'dt' => 5,
            'formatter' => true
        ),
        array(
            'db' => 'id',
            'th' => 'Manage',
            'dt' => 6,
            'formatter' => true

        )
    );
    $table_wallet_types = $factory::dataTableSimple(10, $query,null,false,'table-sm');


?>

<section class="<?= $href ?>">

    <h6 class="text-center">Wallet Types</h6>
    <div>

            <div class="row">
                <div class="col-md-6">
                    <?= $table_wallet_types ?>
                </div>
                <div class="col-md-6 border-left">
                    <form id="new-wallet-type">
                        <h5 class="text-center">Add New Wallet Type</h5>
                        <table class="table-sm table-hover w-100">
                            <tbody>
                            <tr>
                                <td>Title</td>
                                <td><input class="form-control" name="title" required></td>
                            </tr>
                            <tr>
                                <td>Symbol</td>
                                <td><input class="form-control" name="symbol" required></td>
                            </tr>
                            <tr>
                                <td>Rate</td>
                                <td><input class="form-control" name="rate" type="number" step="0.01" value="1.00" required></td>
                            </tr>
                            <tr>
                                <td><label for="broker">Broker</label></td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control col-6" name="broker" id="broker-id" autocomplete="false" required>
                                            <option value="0" disabled selected>Select Broker</option>
                                            <?php if($brokers) foreach ($brokers as $broker){ ?>
                                                <option value="<?= $broker['id'] ?>" ><?= $broker['title'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <select class="form-control col-6" name="unit" id="unit-id" autocomplete="false" required disabled>
                                            <option value="0" disabled selected>Select Unit</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div id="fRes" class="alert"></div>
                        <div class="text-center mt-3 row">
                            <button type="reset" class="btn btn-danger col-md-2 ml-3">Reset</button>
                            <button type="submit" class="btn btn-success col-md-4 ml-3">Add</button>
                        </div>
                    </form>

                    <form id="update-wallet-type">
                        <h5 class="text-center">Update Wallet Type</h5>
                        <table class="table-sm table-hover w-100">
                            <tbody>
                            <tr>
                                <td>Title</td>
                                <td><input class="form-control" name="title" required></td>
                            </tr>
                            <tr>
                                <td>Symbol</td>
                                <td><input class="form-control" name="symbol" required></td>
                            </tr>
                            <tr>
                                <td>Rate</td>
                                <td><input class="form-control" name="rate" type="number" step="0.01" value="1.00" required></td>
                            </tr>
                            <tr>
                                <td><label for="broker">Broker</label></td>
                                <td>
                                    <div class="input-group">
                                        <select class="form-control col-6" name="broker" id="broker-id" autocomplete="false" required>
                                            <option value="0" disabled selected>Select Broker</option>
                                            <?php if($brokers) foreach ($brokers as $broker){ ?>
                                                <option value="<?= $broker['id'] ?>" ><?= $broker['title'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <select class="form-control col-6" name="unit" id="unit-id" autocomplete="false" required disabled>
                                            <option value="0" disabled selected>Select Unit</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div id="fRes" class="alert"></div>
                        <div class="text-center mt-3 row">
                            <input type="hidden" class="form-control" name="id" value="0" required>
                            <button type="submit" class="btn btn-primary col-md-4 ml-3">Update</button>
                            <button id="add-new" type="button" class="btn btn-success col-md-4 ml-3">Add New Type</button>
                        </div>
                    </form>

                </div>

            </div>

    </div>

    <?php if($brokers_units) foreach ($brokers_units as $broker_id=>$brokers_unit){ ?>
        <div class="d-none" id="unit-list-<?= $broker_id ?>">
            <?php if($brokers_unit) foreach ($brokers_unit as $unit){ ?>
                <option value="<?= $unit['id'] ?>" ><?= $unit['name'] ?></option>
            <?php } ?>
        </div>
    <?php } ?>

</section>

<style>
    .da-delDoc{display: none;}
</style>