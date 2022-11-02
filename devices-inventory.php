<?php

include('includes/head.php');

?>
    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
<?php

include('includes/css.php');

global $db;
global $factory;
$where = 'broker_id='.Broker['id'];
$units = $db->select('units', $where);

$staff_list = $db->selectAll('staff_list');

$list_staff=array();
foreach($units as $unit){
    $where = "unit=".$unit['id'];
    $list_staff[$unit['name']] = $db->select('staff_list', $where);
}


$columns = "count(*) as recodes, type";
$types = $db->select('devices', 0, $columns,0,0,'`type`');


$query['db']            = 'DB_admin';
$query['table']         = 'devices';
$query['table_html']    = 'devices';
$query['key']           = 'id';
$query['columns']       = array(
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
        'db' => 'type',
        'th' => 'Type / Brand',
        'dt' => 2,
        'formatter'=> true
    ),
    array(
        'db' => 'manufacturer',
        'th' => 'Manufacturer',
        'dt' => 3
    ),
    array(
        'db' => 'model',
        'th' => 'Model / SN',
        'dt' => 4,
        'formatter'=> true
    ),
    array(
        'db' => 'serial_number',
        'th' => 'Serial Number',
        'dt' => 5
    ),
    array(
        'db' => 'cpu',
        'th' => 'Hardware',
        'dt' => 6,
        'formatter'=> true
    ),
    array(
        'db' => 'memory',
        'th' => 'Memory',
        'dt' => 7
    ),
    array(
        'db' => 'storage_size',
        'th' => 'Storage Size',
        'dt' => 8
    ),
    array(
        'db' => 'os',
        'th' => 'OS',
        'dt' => 9,
        'formatter'=> true
    ),
    array(
        'db' => 'os_license',
        'th' => 'OS License',
        'dt' => 10
    ),
    array(
        'db' => 'os_version',
        'th' => 'OS Version',
        'dt' => 11
    ),
    array(
        'db' => '(select username from users WHERE id=use_by)',
        'th' => 'Use By',
        'dt' => 12
    ),
    array(
        'db' => '(select username from users WHERE id=manage_by)',
        'th' => 'Manage By',
        'dt' => 13
    ),
    array(
        'db' => '(select username from users WHERE id=created_by)',
        'th' => 'Creat',
        'dt' => 14,
        'formatter'=> true
    ),
    array(
        'db' => 'created_at',
        'th' => 'Created At',
        'dt' => 15
    ),
    array(
        'db' => '(select username from users WHERE id=updated_by)',
        'th' => 'Update',
        'dt' => 16,
        'formatter'=> true
    ),
    array(
        'db' => 'updated_at',
        'th' => 'Updated At',
        'dt' => 17
    ),
    array(
        'db' => 'extra_info',
        'th' => 'Extra Info',
        'dt' => 18
    ),
    array(
        'db' => 'id',
        'th' => 'Manage',
        'dt' => 19,
        'formatter'=> true
    ),
    array(
        'db' => 'unit',
        'th' => 'Unit',
        'dt' => 20,
    )
);

$option = "
        'order': [[0, 'asc']],
        'columnDefs': [
            {
                'targets': 3,
                'orderable':false,
                'visible': false
            },
            {
                'targets': 5,
                'orderable':false,
                'visible': false
            },
            {
                'targets': 7,
                'orderable':false,
                'visible': false
            },
            {
                'targets': 8,
                'orderable':false,
                'visible': false
            },
            {
                'targets': 10,
                'orderable':false,
                'visible': false
            },
            {
                'targets': 11,
                'orderable':false,
                'visible': false
            },
            {
                'targets': 15,
                'orderable':false,
                'visible': false
            },
            {
                'targets': 17,
                'orderable':false,
                'visible': false
            }
        ]
        
    ";
$table_devices = $factory::dataTableSimple(25, $query, $option);

?>
    <body>

    <!-- Begin page -->
    <div id="wrapper">


        <?php
        include('includes/topbar.php');
        include('includes/sidebar.php');

        ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <!-- Start content -->
            <div class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Devices Inventory</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">
                                        Devices Manger
                                    </li>
                                </ol>
                                <div class="state-information d-none d-sm-block">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card pmd-card">
                        <div class="card-body">
                            <div id=" " class="row card-body">
                                <div class="form-group col-md-12">
                                    <?php if($types) foreach($types as $item) { ?>
                                        <button class="filterDTX btn btn-sm btn-outline-info small mb-2" data-tableid="DT_devices" data-col="2" data-filter="<?= $item['type'] ?>">&nbsp;
                                            <img class="fa-fw" src="/assets/icons/devices/<?= str_replace(' ','-',$item['type']) ?>.png">
                                            <?= $item['type'] ?> (<?= $item['recodes'] ?>)</button>
                                    <?php  } ?><br>
                                    <button class="filterDTX btn btn-sm btn-secondary" data-tableid="DT_devices" data-col="2" data-filter="">&nbsp; Remove Type Filter (Show All)</button>
                                    <button type="button" class="doA-add btn btn-sm btn-success">Add New Device</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card pmd-card">
                        <div class="card-body">
                            <div id=" " class="card-body">
                                <?php echo $table_devices; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <div id="m-form-device" class="d-none">
                <input type="hidden" class="form-control" name="id" id="id">
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="fname">Name:</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lname">Manufacturer:</label>
                        <input type="text" class="form-control" name="manufacturer" id="manufacturer">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="country">Type:</label>
                        <select class="form-control" name="type" id="type" required>
                            <option>Cell Phone</option>
                            <option>SIM card</option>
                            <option>Laptop</option>
                            <option>Tablet</option>
                            <option>PC</option>
                            <option>All In One</option>
                            <option>Printer / Scanner</option>
                            <option>Wave Audio</option>
                            <option>Display</option>
                            <option>Projector</option>
                            <option>Cable</option>
                            <option>Connector</option>
                            <option>Network Devices</option>
                            <option>Mouse / Keyboard</option>
                            <option>Storage</option>
                            <option>Others</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="phone">Model:</label>
                        <input type="text" class="form-control" name="model" id="model">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="city">Serial Number:</label>
                        <input type="text" class="form-control" name="serial_number" id="serial_number">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="address">CPU:</label>
                        <input type="text" class="form-control" name="cpu" id="cpu">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="interests">Memory:</label>
                        <input type="text" class="form-control" name="memory" id="memory">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="hobbies">Storage Size:</label>
                        <input type="text" class="form-control" name="storage_size" id="storage_size">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="retention">OS:</label>
                        <input type="text" class="form-control" name="os" id="os">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="conversion">OS License:</label>
                        <input type="text" class="form-control" name="os_license" id="os_license">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="conversion">OS Version:</label>
                        <input type="text" class="form-control" name="os_license" id="os_license">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-5 mb-3">
                        <label for="conversion">Use By:</label>
                        <select class="form-control" id="use_by" name="use_by" required>
                            <option>? ? ?</option>
                            <?php foreach($list_staff as $unit=>$staffs) { ?>
                                <optgroup label="<?= $unit ?>">
                                    <?php foreach($staffs as $staff) { ?>
                                        <option><?= $staff['email'] ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="followup">Manage By:</label>
                        <select class="form-control" id="manage_by" name="manage_by" required>
                            <option>? ? ?</option>
                            <?php foreach($list_staff as $unit=>$staffs) { ?>
                                <optgroup label="<?= $unit ?>">
                                    <?php foreach($staffs as $staff) { ?>
                                        <option><?= $staff['email'] ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="followup">Unit:</label>
                        <select class="form-control" id="unit" name="unit" required>
                            <option>? ? ?</option>
                            <?php foreach($units as $unit) { ?>
                                <option><?= $unit['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="ip">Extra Info:</label>
                        <textarea id="extra_info" name="extra_info"  class="form-control" rows="4" cols="50" ></textarea>
                    </div>
                </div>
        </div>
        <?php include('includes/script.php'); ?>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>
            $(document).ready( function () {
                $('.form-select3').selectpicker({
                    tickIcon: 'fas fa-check',
                    liveSearch: true
                });

                // Delete Device
                $("body").on("click", ".doA-delete", function(event){
                    event.preventDefault();
                    let data = {
                        id: $(this).data('id')
                    }
                    const r = confirm("Delete Device?");
                    if (r === true) {
                        ajaxCall('devices', 'deleteDevice', data, function (response) {
                            let resObj = JSON.parse(response);
                            if (resObj.e) {
                                toastr.error("Error on request !");
                            } else if (resObj.res) {
                                toastr.success("Device deleted.");
                                DT_devices.ajax.reload();
                            }
                        });
                    }
                });

                let deviceForm = $('#m-form-device').html();
                // Add Device
                $("body").on("click", ".doA-add", function(e){
                    const footer = '<button type="submit" form="form-device-add" class="btn btn-success">Add</button>';
                    makeModal('Add Device', '<form class="m-form-device" id="form-device-add" accept-charset="utf-8">'+deviceForm+'</form>', 'lg',footer);
                    $('#form-device-add select').addClass('form-select3');
                });
                $("body").on("submit", "#form-device-add", function(e){
                    e.preventDefault();
                    let formData = new FormData(this);
                    ajaxForm ('devices', 'addDevice', formData, function(response){
                        let resObj = JSON.parse(response);
                        if(resObj.e) {
                            toastr.error("Error on request !");
                        } else if (resObj.res) {
                            toastr.success("Device added.");
                            DT_devices.ajax.reload();
                            setTimeout(() => {
                                $('.modal').modal('hide');
                            }, 150);
                        }
                    });
                });

                // Update Device
                $("body").on("click", ".doA-edit", function(e){
                    const id = $(this).data('id');
                    ajaxCall('devices', 'getDevice',{id:id}, function(response) {
                        let resObj = JSON.parse(response);
                        if (resObj.e) {
                            console.log(resObj.e);
                        } else if (resObj.res) {
                            const footer = '<button type="submit" form="form-device-update" class="btn btn-success">Update</button>';
                            makeModal('Update Device', '<form class="m-form-device" id="form-device-update" accept-charset="utf-8">'+deviceForm+'</form>', 'lg',footer);
                            $('#form-device-update input#id').val(id);
                            $('#form-device-update input#cpu').val(resObj.res.cpu);
                            $('#form-device-update input#extra_info').val(resObj.res.extra_info);
                            $('#form-device-update input#manufacturer').val(resObj.res.manufacturer);
                            $('#form-device-update input#memory').val(resObj.res.memory);
                            $('#form-device-update input#model').val(resObj.res.model);
                            $('#form-device-update input#name').val(resObj.res.name);
                            $('#form-device-update input#os').val(resObj.res.os);
                            $('#form-device-update input#os_license').val(resObj.res.os_license);
                            $('#form-device-update input#os_version').val(resObj.res.os_version);
                            $('#form-device-update input#serial_number').val(resObj.res.serial_number);
                            $('#form-device-update input#storage_size').val(resObj.res.storage_size);
                            $('#form-device-update #use_by option:contains('+resObj.res.use_by+')').prop('selected', true)
                            $('#form-device-update #manage_by option:contains('+resObj.res.manage_by+')').prop('selected', true)
                            $('#form-device-update #unit option:contains('+resObj.res.unit+')').prop('selected', true)
                            $('#form-device-update #type option:contains('+resObj.res.type+')').prop('selected', true)
                        }
                    });
                });
                $("body").on("submit", "#form-device-update", function(e){
                    e.preventDefault();
                    let formData = new FormData(this);
                    ajaxForm ('devices', 'updateDevice', formData, function(response){
                        let resObj = JSON.parse(response);
                        if(resObj.e) {
                            toastr.error("Error on request !");
                        } else if (resObj.res) {
                            toastr.success("Device updated.");
                            DT_devices.ajax.reload();
                            setTimeout(() => {
                                $('.modal').modal('hide');
                            }, 150);
                        }
                    });
                });

            });

        </script>

        <?php include('includes/script-bottom.php'); ?>
    </body>
    </html>