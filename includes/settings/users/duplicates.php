<?php
    global $db;

$unit = array();
if($_SESSION["type"] == "Manager")
    $unit[] = $_SESSION["unitn"];
else if(isset($_GET['units']))
    $unit = explode(',', $_GET['units']);
if($unit)
    $unit_s = implode(',', $unit);

$client = array();
if(isset($_GET['leads']) || isset($_GET['trader']) || isset($_GET['IB'])){
    if(isset($_GET['leads']))   $client [] = 1;
    if(isset($_GET['trader']))  $client [] = 2;
    if(isset($_GET['IB']))      $client [] = 3;
} else {
    $client = array(1,2,3);
}
$client_s = implode(',', $client);
    $where = " WHERE type IN($client_s) ";

if($unit_s)
    $where .= "And unit IN ($unit_s)";


if(isset($_GET['skip_archived']))
    $where .=' And status != 16';

$sql ="SELECT GROUP_CONCAT(user_id) as targets, SUBSTR(phone, -10, 10) l10, COUNT(*) d_count FROM user_extra $where GROUP BY SUBSTR(phone, -10, 10) HAVING d_count > 1";
$duplicate_phones = $db->query($sql);

$sql ="SELECT GROUP_CONCAT(id) as targets, email, COUNT(*) d_count FROM useres $where GROUP BY email HAVING d_count > 1";
$duplicate_emails = $db->query($sql);

$units = $db->select('units','broker_id='.Broker['id']);
?>
<section class="<?= $href ?>">

    <h6 class="text-center">Duplicate Finder</h6>
    <div class="row">
        <div class="col-12 row">
            <div class="col-4">

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="leads" <?= (in_array('1',$client)) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="leads">
                        Leads
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="trader" <?= (in_array('2',$client)) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="trader">
                        Trader
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="IB" <?= (in_array('3',$client)) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="IB">
                        IB
                    </label>
                </div>

            </div>
            <div class="col-4">
                <label for="filterStatus">Units</label>
                <button type="button" class="do-clear-filters btn btn-sm" data-target="filterUnit"><i class="fa fa-times-circle"></i></button>
                <select class="form-control form-select3" name="unit" id="filterUnit" multiple="multiple">
                    <?php if($units) foreach($units as $item) { ?>
                        <option value="<?= $item['id'] ?>" <?= (in_array($item['id'], $unit)) ? 'selected' : '' ?> > <?= $item['name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="skip_archived" <?= (isset($_GET['skip_archived'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="skip_archived">
                        Skip Archives
                    </label>
                </div>
                <br>
                <button class="do-filter btn btn-info">Filter Result</button>
            </div>
        </div>
        <div class="col-12">
            <hr>
        </div>
        <div class="col-6">
            <h6 class="text-center">Duplicate Based Email</h6>
            <table id="duplicates_email" class="table table-sm table-striped table-border table-hover" width="100%">
                <thead>
                <tr>
                    <th>Email</th>
                    <th>Count</th>
                    <th>Detail</th>
                </tr>
                </thead>
                <tbody>
                <?php if($duplicate_emails) foreach($duplicate_emails as $item) { ?>
                    <tr>
                        <td><?= $item['email'] ?></td>
                        <td><?= $item['d_count'] ?></td>
                        <td><a target="_blank"  class="btn btn-sm btn-primary" href="search.php?q=<?= $item['email'] ?>">View</a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <h6 class="text-center">Duplicate Based Phone</h6>
            <table id="duplicates_phone" class="table table-sm table-striped table-border table-hover" width="100%">
                <thead>
                <tr>
                    <th>Last 10 Phone Digits</th>
                    <th>Count</th>
                    <th>Detail</th>
                </tr>
                </thead>
                <tbody>
                <?php if($duplicate_phones) foreach($duplicate_phones as $item) { ?>
                    <tr>
                        <td><?= $item['l10'] ?></td>
                        <td><?= $item['d_count'] ?></td>
                        <td><a target="_blank"  class="btn btn-sm btn-primary" href="search.php?q=<?= $item['l10'] ?>">View</a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>


</section>
