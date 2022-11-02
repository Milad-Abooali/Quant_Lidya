<?php
    global $db;
    $where = 'login='.$params['login'];
    $tp_account = $db->selectRow('tp', $where);
?>
<?php $form_name = 'trade-update-login-password'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>">
    <p>
        Fill the Password you want to change:
    </p>
    <table class="table table-sm table-dark">
        <tbody>
            <?php if($tp_account) { ?>
                <tr class="item-row">
                    <td>
                        <label for="f-main-pass" class="form-label">Main Password</label>
                    </td>
                    <td data-lable="investor-pass">
                        <input type="password" class="form-control" name="main-pass" id="f-main-pass" value="">
                    </td>
                </tr>
            <tr class="item-row">
                <td>
                    <label for="f-investor-pass" class="form-label">Investor Password</label>
                </td>
                <td data-lable="investor-pass">
                    <input type="password" class="form-control" name="investor-pass" id="f-investor-pass" value="">
                </td>
            </tr>
            <?php } else {?>
            <tr class="item-row">
                <th colspan="2" class="text-center"> Account Not Found!</th>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="form-actions" class="text-end">
        <input type="hidden" class="form-control" name="login" id="f-login" value="<?= $params['login'] ?>">
        <button type="button" class="btn btn-outline-danger me-2" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>

<script>

    try{
        <?= str_replace('-','_', $form_name) ?>;
    }
    catch(e) {
        if(e.name == "ReferenceError") {
            <?= str_replace('-','_', $form_name) ?> = true;
            $.ajax({
                async: false,
                url: "app/assets/js/<?= $form_name ?>.js",
                dataType: "script"
            });
        }
    }
</script>