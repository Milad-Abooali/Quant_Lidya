<?php

    global $db;

    $brokers = $db->selectAll('brokers');
    $groups = $db->selectAll('ib_groups');

?>
<section class="<?= $href ?> px-2">

    <h6 class="text-center">Groups List</h6>

    <div class="container my-2">
        <table id="groups-list" class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Name</th>
                    <th>Broker</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody>
            <?php if($groups) foreach ($groups as $group){ ?>
                <tr>
                    <td><?= $group['id'] ?></td>
                    <td><?= $group['name'] ?></td>
                    <td><?= $brokers[$group['broker_id']]['title'] ?></td>
                    <td>
                        <button class="btn btn-secondary doA-copy" data-id="<?= $group['id'] ?>">Copy</button>
                        <button class="btn btn-info doA-edit" data-id="<?= $group['id'] ?>">Edit</button>
                        <button class="btn btn-danger doA-delete" data-id="<?= $group['id'] ?>">Delete</button>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>



</section>