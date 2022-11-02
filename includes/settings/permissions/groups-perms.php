<?php

    $groups = new groups();
    $groups_list = ($groups->list()) ?? array();

    $group_id = ($_GET['id']) ?? false;

    if($group_id) {

        $paths = new paths();
        $paths_list = ($paths->list()) ?? array();

        $query['db']             = 'DB_admin';
        $query['table']          = 'perm_groups';
        $query['table_html']     = 'group-perms';
        $query['where']     = "group_id=$group_id";
        $query['key']       = 'id';
        $query['columns']   = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'th' => '#',
                'formatter' => true
            ),
            array(
                'db' => '(SELECT path FROM perm_paths WHERE id = path_id)',
                'dt' => 1,
                'th' => 'Path'
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

    }
?>


<section class="<?= $href ?>">


    <div class="row">
        <div class="col-md-12 px-4">
            <h6>Select Group</h6>
            <div class="row">
                <select class="form-control mb-2 glist" id="uGid" name="gid" required>
                    <option <?php if(!$group_id) echo 'selected'; ?> disabled>Please Select Group</option>
                    <?php foreach ($groups_list as $item) { ?>
                        <option class="gn-<?= $item['id']?>" value="<?= $item['id']?>" <?php if($group_id==$item['id']) echo 'selected'; ?>><?= $item['name']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <?php if($group_id): ?>
        <div class="col-md-12 py-3">
            <h6>Add Perm for Group on Path</h6>
            <form id="newPerm" autocomplete="off" class="row">
                <div class="col-md-6">
                    <input class="form-check-input" type="hidden" id="gid" name="gid" value="<?= $_GET['id']?>">

                    <select class="form-control mb-2 plist" id="uPid" name="pid" required>
                        <?php foreach ($paths_list as $item) { ?>
                            <option class="pn-<?= $item['id']?>" value="<?= $item['id']?>"><?= $item['path']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="py-3">
                    <div class="col">
                        <input class="form-check-input" type="checkbox" id="gpView" name="view" value="1">
                        <label class="form-check-label" for="gpView">View</label>
                    </div>
                    <div class="col">
                        <input class="form-check-input" type="checkbox" id="gpNew" name="new" value="1">
                        <label class="form-check-label" for="gpNew">New</label>
                    </div>
                    <div class="col">
                        <input class="form-check-input" type="checkbox" id="gpEdit" name="edit" value="1">
                        <label class="form-check-label" for="gpEdit">Edit</label>
                    </div>
                    <div class="col">
                        <input class="form-check-input" type="checkbox" id="gpDel" name="del" value="1">
                        <label class="form-check-label" for="gpDel">Del</label>
                    </div>
                </div>
                <button class="btn btn-success btn-block form-control" type="submit">Add Perm</button>
            </form>
        </div>
        <div class="col-md-12">
            <h6>Group Perms</h6>
            <?= $group_perms ?>
        </div>
        <?php endif; ?>
    </div>

</section>