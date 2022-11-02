<?php

    global $db;
    global $userManager;

    $brokers = $db->selectAll('brokers');
    $groups = $db->selectAll('ib_groups');

    $id = $_GET['id'] ?? false;
    if($id) {
        $group = $db->selectID('ib_groups',$id);
        $where = 'group_id='.$group['id'];
        $group_ibs = $db->select('ibs_list', $where);
        $group_ibs_ids = ($group_ibs) ? implode(',', array_column($group_ibs,'id')) : 0;

        $where = 'id='.$group['broker_id'];
        $broker = $db->select('brokers', $where);

        $where = 'broker_id='.$group['broker_id'];
        $units = $db->select('units', $where);
        $units_id = ($units) ? implode("','", array_column($units,'name')) : 0;

        $where = "`type`='IB' AND`unit` IN ('$units_id') AND id NOT IN ($group_ibs_ids)";
        $units_ibs = $db->select('users', $where);
    }
?>
<section class="<?= $href ?> px-2">

    <h6 class="text-center">Groups Members</h6>

    <div class="container my-2">
        <?php if(!$id){ ?>
        <table id="groups-list" class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Name</th>
                    <th>Broker</th>
                    <th>Members</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody>
            <?php if($groups) foreach ($groups as $group){ ?>
                <tr>
                    <td><?= $group['id'] ?></td>
                    <td><?= $group['name'] ?></td>
                    <td><?= $brokers[$group['broker_id']]['title'] ?></td>
                    <td><?= $db->count('ibs_list', 'group_id='.$group['id']) ?></td>
                    <td>
                        <button class="btn btn-primary doA-select" data-id="<?= $group['id'] ?>">Select</button>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
        <?php } else { ?>
            <div class="row">
                <div class="col-md-6">
                    <h6>IBs <small><?= Broker['title']?></small></h6>
                    <div class="p-3">
                        <table id="list-ibs" class="table table-sm table-hover">
                            <thead>
                            <tr>
                                <th>IB</th>
                                <th>Unit</th>
                                <th>Add</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if($units_ibs) foreach ($units_ibs as $ib) {
                                $ib_custom = $userManager->getCustom($ib['id'], 'username, unit');
                                ?>
                                <tr>
                                    <td><?= $ib_custom['username'] ?></td>
                                    <td><?= $ib_custom['unit'] ?></td>
                                    <td>
                                        <i data-ib="<?= $ib['id'] ?>" data-group="<?= $group['id'] ?>" class="doA-add btn btn-outline-success fa fa-angle-right"></i>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6><?= ucfirst($group['name']) ?> <small>Members</small></h6>
                    <div class="p-3">
                        <table id="group-ibs" class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>IB</th>
                                    <th>Unit</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($group_ibs) foreach ($group_ibs as $ib) {
                                $ib_custom = $userManager->getCustom($ib['id'], 'username, unit');
                                ?>
                                <tr>
                                    <td><?= $ib_custom['username'] ?></td>
                                    <td><?= $ib_custom['unit'] ?></td>
                                    <td>
                                        <i data-ib="<?= $ib['id'] ?>" data-group="<?= $group['id'] ?>" class="doA-remove btn btn-outline-danger fa fa-times-circle"></i>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>



</section>