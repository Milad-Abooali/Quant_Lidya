<?php

    $groups = new groups();

?>


<section id="t3v1">


    <div class="row">
        <div class="col-md-4">
            <h6>Groups List</h6>

            <table id="groupsList" class="table table-striped table-bordered table-sm rounded">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Group Name</th>
                    <th>Users</th>
                    <th>Drop Users</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($groups->list() as $item) { ?>
                    <tr id="g-<?= $item['id']?>">
                        <td><?= $item['id']?></td>
                        <td class="gn-<?= $item['id']?>" ><?= $item['name']?></td>
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
            <h6>Change Group Name</h6>
            <form id="editGroup" autocomplete="off">
                <label for="uGid">Select Group</label>
                <select class="form-control mb-2 glist" id="uGid" name="gid" required>
                <?php foreach ($groups->list() as $item) { ?>
                    <option class="gn-<?= $item['id']?>" value="<?= $item['id']?>"><?= $item['name']?></option>
                <?php } ?>
                </select>
                <label for="changeName">New name</label>
                <input class="form-control mb-2" id="changeName" name="name" placeholder="" required>
                <button class="btn btn-success btn-block form-control" type="submit">Save Change</button>
            </form>
        </div>
        <div class="col-md-4 px-3">
            <h6>Add New Group</h6>
            <form id="copyGroup" autocomplete="off">
                <label for="cGid">Copy From</label>
                <select class="form-control mb-2 glist" id="cGid" name="gid" required>
                    <?php foreach ($groups->list() as $item) { ?>
                        <option class="gn-<?= $item['id']?>"  value="<?= $item['id']?>"><?= $item['name']?></option>
                    <?php } ?>
                </select>
                <label for="newcname">Name</label>
                <input class="form-control mb-2" id="newcname" name="name" placeholder="" required>
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
            <hr>
            <form id="newGroup" autocomplete="off">
                <label for="newName">Name</label>
                <input class="form-control mb-2" id="newName" name="name" placeholder="" required>
                <button class="btn btn-success btn-block form-control" type="submit">Add New Group</button>
            </form>
        </div>
    </div>

    <hr>

    <div class="row">
        <h6> Select Groups</h6>
        <div class="col-md-12 row">
            <?php foreach ($groups->list() as $item) { ?>
                <div class="col">
                    <input class="form-check-input" type="checkbox" id="<?= $item['name']?>" name="<?= $item['name']?>" value="<?= $item['id']?>">
                    <label class="form-check-label" for="<?= $item['name']?>"><?= $item['name']?></label>
                </div>
            <?php } ?>
        </div>
        <h6> Select Users</h6>
        <div class="col-md-12">

        </div>
    </div>


    <button id="TestYKEY" class="btn btn-success btn-block form-control my-2" >TestYKEY</button>

</section>