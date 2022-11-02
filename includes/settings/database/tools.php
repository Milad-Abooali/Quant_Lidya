<?php

    global $db;
    $sql = "SELECT `TABLE_NAME`, `ENGINE`, `TABLE_ROWS`, `CREATE_TIME`, `UPDATE_TIME`, `CHECK_TIME`, `TABLE_COLLATION`,ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024) AS `Size` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".DB_admin['name']."'";
    $tables = $db->run($sql);

?>
<section class="<?= $href ?>">

    <h6 class="text-center">Database Tools</h6>
    <style>
        .db-console .db-console-head{
            background: black;
            color: #dcbc73;
        }
        .db-console .db-console-content > div{
            opacity: 0.3;
        }
        .db-console .db-console-content > div:first-of-type{
            opacity: 0.9;
            border: 1px solid rgb(255, 80, 6, 0.5);
        }
        .db-console .db-console-content > div:hover{
            opacity: 1;
        }

        .db-console .db-console-content{
            display: none;
            color: #fff;
            max-height: 540px;
        }
        .db-console{
            top: 90px;
            left: 430px;
            position: fixed;
            z-index: 999;
            background: #363638db;
            width: auto;
            min-width: 350px;
            max-width: 100%;
            max-height: 600px;
            overflow-y: hidden;
            border: 1px solid;
        }
    </style>
    <div id="db-console" class="db-console">
        <div class="db-console-head px-2 p-1">
            <i class="fas fa-arrows-alt"></i> DB Output
            <span class="db-console-mini float-right"><i class="fas fa-caret-down"></i></span>
        </div>
        <div class="db-console-content m-3 slimscroll">
        </div>
    </div>

    <div class="fix-tools row mb-3 ">
        <div class="col-md-6">

        </div>
        <div class="col-md-6 text-right">
            With Selected:
            <button id="do-check" class="btn btn-sm btn-outline-secondary"><i class="fab fa-envira"></i> Check</button>
            <button id="do-repair" class="btn btn-sm btn-outline-primary"><i class="fa fa-compass"></i> Repair</button>
            <button id="do-optimize" class="btn btn-sm btn-outline-success"><i class="fa fa-recycle"></i> Optimize</button>
        </div>
    </div>

    <div>
        <table id="tables" class="table table-sm dataTable table-hover">
            <thead>
                <tr>
                    <th><input class="select-table" type="checkbox" value="1" id="select-all"></th>
                    <th>Rows</th>
                    <th>Name</th>
                    <th>Size (KB)</th>
                    <th>Engine</th>
                    <th>Creat Time</th>
                    <th>Update Time</th>
                    <th>Check Time</th>
                    <th>Manage Table</th>
                </tr>
            </thead>
            <tbody>
            <?php if($tables) foreach ($tables as $table){ ?>
                <tr>
                    <td><input class="select-row" type="checkbox" value="1" data-table="<?= $table['TABLE_NAME'] ?>"></td>
                    <td><?= GF::nf($table['TABLE_ROWS']) ?></td>
                    <td><?= $table['TABLE_NAME'] ?></td>
                    <td><?= GF::nf($table['Size']) ?></td>
                    <td class="small"><?= $table['ENGINE'] ?> (<span class="text-muted"><?= $table['TABLE_COLLATION'] ?></span>)</td>
                    <td><?= $table['CREATE_TIME'] ?></td>
                    <td><?= $table['UPDATE_TIME'] ?></td>
                    <td><?= $table['CHECK_TIME'] ?></td>
                    <td>
                        <button data-table="<?= $table['TABLE_NAME'] ?>" class="doA-check btn btn-sm btn-outline-secondary" data-toggle="tooltip" data-placement="top" title="Check"><i class="fab fa-envira"></i></button>
                        <button data-table="<?= $table['TABLE_NAME'] ?>" class="doA-repair btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="top" title="Repair"><i class="fa fa-compass"></i></button>
                        <button data-table="<?= $table['TABLE_NAME'] ?>" class="doA-optimize btn btn-sm btn-outline-success" data-toggle="tooltip" data-placement="top" title="Optimize"><i class="fa fa-recycle"></i></button>
                        <button data-table="<?= $table['TABLE_NAME'] ?>" class="doM-history btn btn-sm btn-outline-info" data-toggle="tooltip" data-placement="top" title="History"><i class="fa fa-database"></i></button>
                        <button data-table="<?= $table['TABLE_NAME'] ?>" class="doA-export btn btn-sm btn-outline-warning" data-toggle="tooltip" data-placement="top" title="Export"><i class="fa fa-download"></i></button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="modal-progress" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
            <span class="fa fa-spinner fa-spin fa-3x"></span>
        </div>
    </div>

</section>