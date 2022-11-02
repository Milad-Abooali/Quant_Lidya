<?php

    global $factory;

    $query['db']            = 'DB_ib_contracts';
    $query['table']         = 'ib_contracts';
    $query['table_html']    = 'ib_contracts';
    $query['where']         = "user_id=".$_POST['id'];
    $query['key']           = 'id';
    $query['columns']       = array(
        array(
            'db' => 'id',
            'th' => '#',
            'dt' => 0
        ),
        array(
            'db' => '(select username from users WHERE id=user_id)',
            'th' => 'User',
            'dt' => 1,
            'formatter' => true
        ),
        array(
            'db' => 'filename',
            'th' => 'File',
            'dt' => 2,
            'formatter' => true
        ),
        array(
            'db' => 'has_signed',
            'th' => 'Signed',
            'dt' => 3
        ),
        array(
            'db' => 'status',
            'th' => 'Status',
            'dt' => 4,
            'formatter' => true
        ),
        array(
            'db' => 'comment',
            'th' => 'Comment',
            'dt' => 5
        ),
        array(
            'db' => 'created_at',
            'th' => 'Created At',
            'dt' => 6,
            'formatter' => true
        ),
        array(
            'db' => '(select username from users WHERE id=created_by)',
            'th' => 'Created',
            'dt' => 7
        ),
        array(
            'db' => 'updated_at',
            'th' => 'Updated',
            'dt' => 8,
            'formatter' => true
        ),
        array(
            'db' => '(select username from users WHERE id=updated_by)',
            'th' => 'Update By',
            'dt' => 9
        ),
        array(
            'db' => 'id',
            'th' => 'Manage',
            'dt' => 10,
            'formatter' => true
        )
    );

    $option = '
          		"responsive": true,
        		"order": [ 0, "desc" ],
                "columnDefs": [
                    { "visible": false, "targets": 3 },
                    { "visible": false, "targets": 7 },
                    { "visible": false, "targets": 9 },
                ]
    ';
    $table_ib_contracts = $factory::dataTableSimple(25, $query, $option);

?>
<style>
    .da-delDoc{display: none;}
</style>

<form method="post" action="" enctype="multipart/form-data" id="contractForm">
    <div id="new-contract" class="row">
            <div class="col-md-6">
                <p>Upload the contract file <small class="text-muted">(PDF, DOC, DOCX, PNG, JPG)</small></p>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFile">Upload</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="contract" name="contract" required>
                        <label class="custom-file-label" for="contract">Choose file</label>
                    </div>
                </div>

                <div class="py-3">
                    <input type="checkbox" class=" " value="1" id="has_signed" name="has_signed">
                    <label for="has_signed">Signed Version</label>
                </div>
            </div>
            <div class="col-md-6">
                <label for="comment">Comment</label>
                <textarea class="form-control" name="comment" id="comment"></textarea>
                <input type="hidden" id="uid" name="uid" value="<?= $_POST['id'] ?>" required>
                <button class="m-2 float-right btn btn-primary">Upload</button>
            </div>
            <div class="col-md-12">
                <div id="fRes" class="mt-3 alert" style="display: none;"></div>
            </div>
    </div>
</form>

<hr>
<?= $table_ib_contracts ?>

<?= factory::footer() ?>

<script>

    // Submit Update
    $("body").on("submit","form#contractForm", function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        ajaxForm ('ib', 'newContract', formData, function(response){
            let resObj = JSON.parse(response);
            const fResp = $("#new-contract #fRes");
            if (resObj.e) {
                fResp.addClass('alert-warning');
                fResp.fadeIn();
                fResp.html('Error, Please Check Inputs!');
            }
            if (resObj.res) {
                fResp.addClass('alert-success');
                fResp.fadeIn();
                fResp.html('Your file Added.');
                DT_ib_contracts.ajax.reload();
            }
        });
    });


</script>