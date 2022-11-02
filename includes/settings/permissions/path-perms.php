<?php

$paths = new paths();
$paths_list = $paths->list();

$pStatus = array('<i class="fa fa-ban text-danger"></i>','<i class="fa fa-check-circle text-success"></i>');

$path_id = ($_GET['id']) ?? false;

if($path_id) {

    $query['db']             = 'DB_admin';
    $query['table']          = 'perm_groups';
    $query['table_html']     = 'group-perms';
    $query['where']     = "path_id=$path_id";
    $query['key']       = 'id';
    $query['columns']   = array(
        array(
            'db' => 'id',
            'dt' => 0,
            'th' => '#',
            'formatter' => true
        ),
        array(
            'db' => '(SELECT name FROM user_groups WHERE id = group_id)',
            'dt' => 1,
            'th' => 'Group'
        ),
        array(
            'db' => 'view',
            'dt' => 2,
            'th' => 'View',
            'formatter' => true
        ),
        array(
            'db' => 'new',
            'dt' => 3,
            'th' => 'New',
            'formatter' => true
        ),
        array(
            'db' => 'edit',
            'dt' => 4,
            'th' => 'Edit',
            'formatter' => true
        ),
        array(
            'db' => 'del',
            'dt' => 5,
            'th' => 'Del',
            'formatter' => true
        ),
        array(
            'db' => 'id',
            'dt' => 6,
            'th' => 'Remove',
            'formatter' => true
        )
    );
    $option = '
            "columnDefs": [
               {"orderable":false, "targets":0}
            ],
            "order": [[0, "asc"]]
        ';
    $group_perms = $factory::dataTableSimple(10,$query,$option);


    $query['db']             = 'DB_admin';
    $query['table']          = 'perm_users';
    $query['table_html']     = 'user-perms';
    $query['where']     = "path_id=$path_id";
    $query['key']       = 'id';
    $query['columns']   = array(
        array(
            'db' => 'id',
            'dt' => 0,
            'th' => '#',
            'formatter' => true
        ),
        array(
            'db' => '(SELECT username FROM users WHERE id = user_id)',
            'dt' => 1,
            'th' => 'User'
        ),
        array(
            'db' => 'view',
            'dt' => 2,
            'th' => 'View',
            'formatter' => true
        ),
        array(
            'db' => 'new',
            'dt' => 3,
            'th' => 'New',
            'formatter' => true
        ),
        array(
            'db' => 'edit',
            'dt' => 4,
            'th' => 'Edit',
            'formatter' => true
        ),
        array(
            'db' => 'del',
            'dt' => 5,
            'th' => 'Del',
            'formatter' => true
        ),
        array(
            'db' => 'id',
            'dt' => 6,
            'th' => 'Remove',
            'formatter' => true
        )
    );
    $option = '
            "columnDefs": [
               {"orderable":false, "targets":0}
            ],
            "order": [[0, "asc"]]
        ';
    $user_perms = $factory::dataTableSimple(10,$query,$option);

}
?>


<section class="<?= $href ?>">


    <div class="row">
        <div class="col-md-12 px-4">
            <h6>Select Path</h6>
            <div class="row">
                <select class="form-control mb-2" id="vPid" name="pid" required>
                    <option <?php if(!$path_id) echo 'selected'; ?> disabled>Please Select Path</option>
                    <?php foreach ($paths_list as $item) { ?>
                        <option value="<?= $item['id']?>" <?php if($path_id==$item['id']) echo 'selected'; ?>><?= $item['path']?></option>
                        <?php if($path_id==$item['id']) $c_path =  $item; ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <?php if($path_id): ?>
            <div class="col-md-12">
                <h6>Default Perms on <small class="text-primary"><?= $c_path['path']; ?></small></h6>
                <table id="path-perms" class="table table-striped table-hover dataTable no-footer">
                    <thead>
                        <tr role="row">
                            <th>View</th>
                            <th>New</th>
                            <th>Edit</th>
                            <th>Del</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row" class="odd">
                            <td><?= $pStatus[$c_path['view']]; ?></td>
                            <td><?= $pStatus[$c_path['new']]; ?></td>
                            <td><?= $pStatus[$c_path['edit']]; ?></td>
                            <td><?= $pStatus[$c_path['del']]; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <div class="col-md-12">
                <h6>Group Perms on <small class="text-primary"><?= $c_path['path']; ?></small></h6>
                <?= $group_perms ?>
            </div>

            <div class="col-md-12">
                <h6>Users Perms on <small class="text-primary"><?= $c_path['path']; ?></small></h6>
                <?= $user_perms ?>
            </div>
        <?php endif; ?>
    </div>

</section>