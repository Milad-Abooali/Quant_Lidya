<?php

    $paths = new paths();

?>


<section id="t3v2">


    <div class="row">
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
                <?php foreach ($paths->list() as $item) { ?>
                    <tr id="p-<?= $item['id']?>">
                        <td><?= $item['id']?></td>
                        <td><?= $item['path']?></td>
                        <td><?= $item['view']?></td>
                        <td><?= $item['new']?></td>
                        <td><?= $item['edit']?></td>
                        <td><?= $item['del']?></td>
                        <td>
                            <button class="btn btn-danger btn-sm form-control doDelete" data-pid="<?= $item['id']?>"><i class="fa fa-times-circle"></i></button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-4 px-3">

        </div>
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
    </div>

    <hr>

    <div class="row">
        <h6> Select Groups</h6>
        <div class="col-md-12 row">
            <?php foreach ($paths->list() as $item) { ?>
                <div class="col">
                    <input class="form-check-input" type="checkbox" id="<?= $item['path']?>" name="<?= $item['path']?>" value="<?= $item['id']?>">
                    <label class="form-check-label" for="<?= $item['path']?>"><?= $item['path']?></label>
                </div>
            <?php } ?>
        </div>
        <h6> Select Users</h6>
        <div class="col-md-12">

        </div>
    </div>


    <button id="TestYKEY" class="btn btn-success btn-block form-control my-2" >TestYKEY</button>

</section>