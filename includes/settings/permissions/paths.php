<?php

    $paths = new paths();
    $paths_list = $paths->list();

    $pStatus = array('<i class="fa fa-ban text-danger"></i>','<i class="fa fa-check-circle text-success"></i>');

?>


<section class="<?= $href ?>">


    <div class="row">

        <div class="col-md-4 px-3">
            <h6>Add New Path</h6>
            <form id="newPath" autocomplete="off">
                <div class="row">
                    <label for="nPath">Path</label>
                    <input class="form-control mb-2" id="nPath" name="path" placeholder="" required>
                </div>
                <div class="row py-3">
                    <div class="col">
                        <input class="form-check-input" type="checkbox" id="nView" name="view" value="1">
                        <label class="form-check-label" for="nView">View</label>
                    </div>
                    <div class="col">
                        <input class="form-check-input" type="checkbox" id="nNew" name="new" value="1">
                        <label class="form-check-label" for="nNew">New</label>
                    </div>
                    <div class="col">
                        <input class="form-check-input" type="checkbox" id="nEdit" name="edit" value="1">
                        <label class="form-check-label" for="nEdit">Edit</label>
                    </div>
                    <div class="col">
                        <input class="form-check-input" type="checkbox" id="nDel" name="del" value="1">
                        <label class="form-check-label" for="ndel">Del</label>
                    </div>
                </div>
                <button class="btn btn-success btn-block form-control" type="submit">Add New Path</button>
            </form>
        </div>

        <div class="col-md-4">
            <h6>Paths List</h6>
            <table id="pathsList" class="table table-striped table-bordered table-sm rounded">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Path</th>
                    <th>View</th>
                    <th>New</th>
                    <th>Edit</th>
                    <th>Del</th>
                    <th>Delete (A)</th>
                </tr>
                </thead>
                <tbody>
                <?php if($paths_list) foreach ($paths_list as $item) { ?>
                    <tr id="p-<?= $item['id']?>">
                        <td><?= $item['id']?></td>
                        <td><?= $item['path']?></td>
                        <td><?= $pStatus[$item['view']]; ?></td>
                        <td><?= $pStatus[$item['new']]; ?></td>
                        <td><?= $pStatus[$item['edit']]; ?></td>
                        <td><?= $pStatus[$item['del']]; ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm form-control doDelete" data-pid="<?= $item['id']?>"><i class="fa fa-times-circle"></i></button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-4 px-3">
            <h6>Edit Path</h6>
            <form id="editPath" autocomplete="off">
                <select class="form-control mb-2 plist" id="uPid" name="pid" required>
                    <option class="pn-0" disabled selected>Select Path</option>
                    <?php foreach ($paths->list() as $item) { ?>
                        <option class="pn-<?= $item['id']?>" value="<?= $item['id']?>" data-edit="<?= $item['edit']?>"data-del="<?= $item['del']?>"data-new="<?= $item['new']?>" data-view="<?= $item['view']?>"><?= $item['path']?></option>
                    <?php } ?>
                </select>
                <div id="editPath-data" class="row">
                    <div class="col">
                        <label for="uPath">Change Path</label>
                        <input class="form-control mb-2" id="uPath" name="path" placeholder="" required>
                    </div>
                    <div class="py-3">
                        <div class="col">
                            <input class="form-check-input" type="checkbox" id="uView" name="view" value="1">
                            <label class="form-check-label" for="uView">View</label>
                        </div>
                        <div class="col">
                            <input class="form-check-input" type="checkbox" id="uNew" name="new" value="1">
                            <label class="form-check-label" for="uNew">New</label>
                        </div>
                        <div class="col">
                            <input class="form-check-input" type="checkbox" id="uEdit" name="edit" value="1">
                            <label class="form-check-label" for="uEdit">Edit</label>
                        </div>
                        <div class="col">
                            <input class="form-check-input" type="checkbox" id="uDel" name="del" value="1">
                            <label class="form-check-label" for="udel">Del</label>
                        </div>
                    </div>
                    <button class="btn btn-success btn-block form-control" type="submit">Save Change</button>
                </div>
            </form>
        </div>

    </div>

</section>