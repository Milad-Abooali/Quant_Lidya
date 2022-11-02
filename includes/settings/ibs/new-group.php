<?php

    global $db;

    $symbols_mt5 = $db->select('lidyapar_mt5.mt5_symbols',null,'`Symbol`,`Description`');
    $symbols_mt4 = $db->select('lidyapar_mt4.MT4_PRICES',null,'`SYMBOL`');

    $brokers = $db->selectAll('brokers');

    $action = $_GET['a'] ?? false;
    $id = $_GET['id'] ?? false;
?>
<section class="<?= $href ?> px-2">

    <?php if($action=='copy' || $action=='edit'){

        $group = $db->selectId('ib_groups', $id);
        $rebates = json_decode($group['rebates'],1);
    ?>

        <h6 class="text-center"><?php echo ucfirst($action) ?> IB Group</h6>
        <div class="container mt-2">
            <form id="<?= ($action=='edit') ? 'edit' : 'new' ?>-ib-group" class="">
                <div class="row">
                    <div class="col-md-4">
                        <label for="group_name">Name</label>
                        <input class="form-control <?= ($action=='copy') ? 'border-danger' : null ?>" type="text" id="group_name" name="group_name" value="<?= ($action=='edit') ? $group['name'] : null ?>" autocomplete="group_name" placeholder="New Group Name" required>
                    </div>
                    <div class="col-md-3">
                        <label for="broker">Broker</label>
                        <select class="form-control" name="broker" autocomplete="false">
                            <?php if($brokers) foreach ($brokers as $broker){ ?>
                                <option value="<?= $broker['id'] ?>" <?= ($group['broker_id']==$broker['id']) ? 'selected':  null ?>><?= $broker['title'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <?php if($action=='copy') {?>
                            <button class="form-control btn btn-primary mt-4" type="submit">Add Group</button>
                        <?php } else {?>
                            <input class="form-control" type="hidden" name="group_id" value="<?= $id ?>" required>
                            <button class="form-control btn btn-primary mt-4" type="submit">Edit Group</button>
                        <?php } ?>
                    </div>
                    <div class="col-md-2">
                        <button class="-control btn btn-outline-danger mt-4" type="button" id="doW-go-back">Go Back</button>
                    </div>
                </div>
                <div class="row my-5">
                    <div class="col-md-6 border-right">
                        <h6 class="text-primary mt-4 text-center">MT4 Symbols</h6>
                        <div class="row" id="symbols-rate-mt4">
                            <?php if($rebates['mt4']) foreach ($rebates['mt4'] as $item){?>
                                <div class="col-md-3 mb-2">
                                    <label><?= $item[0] ?></label>
                                    <input class="form-control" type="number" step="0.01" name="mt4_v[]" value="<?= $item[1] ?>" required></div>
                                    <input class="form-control" type="hidden" name="mt4_s[]" value="<?= $item[0] ?>" required>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mt-4 text-center">MT5 Symbols</h6>
                        <div class="row" id="symbols-rate-mt5">
                            <?php if($rebates['mt5']) foreach ($rebates['mt4'] as $item){?>
                                <div class="col-md-3 mb-2">
                                    <label><?= $item[0] ?></label>
                                    <input class="form-control" type="number" step="0.01" name="mt5_v[]" value="<?= $item[1] ?>" required></div>
                                    <input class="form-control" type="hidden" name="mt5_s[]" value="<?= $item[0] ?>" required>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    <?php } else { ?>
    <h6 class="text-center">New IB Group</h6>
    <div id="prepare-form" class="row mt-5">
            <div class="col-md-3">
                <label for="group_name">Name</label>
                <input class="form-control" type="text" id="group_name_t" autocomplete="group_name_t">
            </div>
            <div class="col-md-2">
                <label for="mt4_list">MT4 Symbols</label>
                <select class="form-control select2 select2-multiple" id="mt4_list" autocomplete="false" multiple>
                    <?php if($symbols_mt4) foreach ($symbols_mt4 as $symbols){ ?>
                        <option value="<?= $symbols['SYMBOL'] ?>"><?= $symbols['SYMBOL'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="mt5_list">MT5 Symbols</label>
                <select class="form-control select2 select2-multiple" id="mt5_list" autocomplete="false" multiple>
                    <?php if($symbols_mt5) foreach ($symbols_mt5 as $symbols){ ?>
                        <option value="<?= $symbols['Symbol'] ?>"><?= $symbols['Symbol'] ?> - <?= $symbols['Description'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="form-control btn btn-primary mt-4" type="button" id="doV-form">Creat</button>
            </div>
    </div>

    <div class="container mt-2">
        <form id="new-ib-group" class="needShow">
            <div class="row">
                <div class="col-md-4">
                    <label for="group_name">Name</label>
                    <input class="form-control" type="text" id="group_name" name="group_name" autocomplete="group_name" required>
                </div>
                <div class="col-md-3">
                    <label for="broker">Broker</label>
                    <select class="form-control" name="broker" autocomplete="false">
                    <?php if($brokers) foreach ($brokers as $broker){ ?>
                       <option value="<?= $broker['id'] ?>"><?= $broker['title'] ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="form-control btn btn-primary mt-4" type="submit">Add Group</button>
                </div>
                <div class="col-md-2">
                    <button class="form-control btn btn-outline-danger mt-4" type="button" id="doW-go-back">Go Back</button>
                </div>
            </div>
            <div class="row my-5">
                <div class="col-md-6 border-right">
                    <h6 class="text-primary mt-4 text-center">MT4 Symbols</h6>
                    <div class="row" id="symbols-rate-mt4"></div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary mt-4 text-center">MT5 Symbols</h6>
                    <div class="row" id="symbols-rate-mt5"></div>
                </div>
            </div>
        </form>
    </div>
    <?php } ?>

</section>