<?php

    $groups = new groups();
    $groups_list = $groups->list();
?>


<section class="<?= $href ?>">


    <div class="row">

        <div class="col-md-3 px-3">
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                <h6>Add New Group</h6>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse in">
                        <form id="newGroup" autocomplete="off">
                            <label for="newName">Name</label>
                            <input class="form-control mb-2" id="newName" name="name" placeholder="" required>
                            <label for="newPriority">Priority</label>
                            <input class="form-control mb-2" id="newPriority" name="priority" type="number" placeholder="" required>
                            <button class="btn btn-success btn-block form-control" type="submit">Add New Group</button>
                        </form>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                <h6>Copy Group</h6>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse">
                        <form id="copyGroup" autocomplete="off">
                            <label for="cGid">Copy From</label>
                            <select class="form-control mb-2 glist" id="cGid" name="gid" required>
                                <?php if($groups_list) foreach ($groups_list as $item) { ?>
                                    <option class="gn-<?= $item['id']?>"  value="<?= $item['id']?>"><?= $item['name']?></option>
                                <?php } ?>
                            </select>
                            <label for="cgName">Name</label>
                            <input class="form-control mb-2" id="cgName" name="name" placeholder="" required>
                            <label for="cgPriority">Priority</label>
                            <input class="form-control mb-2" id="cgPriority" name="priority" type="number" placeholder="" required>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="cgPerms" name="perms" value="1">
                                <label class="form-check-label" for="cgPerms">Permissions</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="cgUsers" name="users" value="1">
                                <label class="form-check-label" for="cgUsers">Users</label>
                            </div>
                            <button class="btn btn-success btn-block form-control my-2" type="submit">Copy The Group</button>
                        </form>
                        <small>* After created group refresh the page.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <h6>Groups List</h6>
            <table id="groupsList" class="table table-striped table-bordered table-sm rounded">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Group Name</th>
                    <th>Priority </th>
                    <th>Users</th>
                    <th>Drop Users</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($groups_list) foreach ($groups_list as $item) { ?>
                    <tr id="g-<?= $item['id']?>">
                        <td><?= $item['id']?></td>
                        <td class="gn-<?= $item['id']?>" ><?= $item['name']?></td>
                        <td><?= $item['priority']?></td>
                        <td><?= $item['users']?></td>
                        <td>
                            <button class="btn btn-warning btn-sm form-control doDrop" data-gid="<?= $item['id']?>"><i class="fa fa-user-alt-slash"></i></button>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm form-control doDelete" data-gid="<?= $item['id']?>"><i class="fa fa-times-circle"></i></button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-4 px-3">
            <h6>Update Group</h6>
            <form id="editGroup" autocomplete="off">
                <label for="uGid"></label>
                <select class="form-control mb-2 glist" id="uGid" name="gid" required>
                    <option class="0" selected disabled>Not Selected</option>
                    <?php if($groups_list) foreach ($groups_list as $item) { ?>
                    <option class="gn-<?= $item['id']?>" value="<?= $item['id']?>"><?= $item['name']?></option>
                <?php } ?>
                </select>
                <label for="changeName">New name</label>
                <input class="form-control mb-2" id="changeName" name="name" placeholder="" required>
                <label for="changePriority">Priority</label>
                <input class="form-control mb-2" id="changePriority" name="priority" type="number" placeholder="" required>
                <button class="btn btn-success btn-block form-control" type="submit">Save Change</button>
            </form>
        </div>

    </div>
</section>