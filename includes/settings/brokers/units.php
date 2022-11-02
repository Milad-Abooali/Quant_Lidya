<?php

    $query['db']        = 'DB_admin';
    $query['table']     = 'units';
    $query['table_html']     = 'units_list';
    $query['key']       = 'id';
    $query['columns']   = array(
                                array(
                                    'db' => 'id',
                                    'th' => '#',
                                    'dt' => 0
                                ),
                                array(
                                    'db' => 'name',
                                    'th' => 'Name',
                                    'dt' => 1
                                ),
                                array(
                                    'db' => '(Select title from brokers where id=broker_id)',
                                    'th' => 'Broker',
                                    'dt' => 2
                                ),
                                array(
                                    'db' => 'id',
                                    'th' => 'Manage',
                                    'dt' => 3,
                                    'formatter' => true
                                )
                            );
    $option = '
          		"responsive": true,
                "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
        		"order": [ 0, "asc" ]
    ';
    $table_units_list = $factory::dataTableSimple(10, $query,$option);

?>
<section class="<?= $href ?>">

    <div class="row">
        <div class="col-md-8">
            <h6 class="text-center">Units List</h6>
            <?php echo $table_units_list; ?>
        </div>
        <div class="col-md-4">
            <h6 class="text-center">New Unit</h6>
            <form id="new-unit">
                <table class="table-sm table-hover w-100">
                    <tbody>
                    <tr>
                        <td>Name</td>
                        <td><input class="form-control" name="name" required></td>
                    </tr>
                    <tr>
                        <td>Broker</td>
                        <td>
                            <select class="form-control" name="broker_id" required>
                                <?php
                                    $brokers = $db->selectAll('brokers');
                                    if($brokers) foreach ($brokers as $broker) {
                                ?>
                                        <option value="<?= $broker['id'] ?>"><?= $broker['title'] ?></option>
                                <?php
                                    }
                                ?>

                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div id="fRes" class="alert"></div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success col-md-4">Save</button>
                </div>
            </form>

        </div>
    </div>

</section>

<style>
    .da-delDoc{display: none;}
</style>