<?php
    global $db;
    $profile = main::getProfile();

    $job_categories = $db->selectAll('job_category');
    $incomes = $db->selectAll('income');
    $investments = $db->selectAll('investment');
?>
<?php $form_name = 'profile-edit-extra'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>">

    <table class="table table-sm table-dark">
        <thead>
            <tr>
                <th colspan="2" class="text-center text-white-50">Extra Information</th>
            </tr>
        </thead>
        <?php $item = 'city' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">City</label>
            </td>
            <td>
                <input type="text" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->Extra[$item] ?>" placeholder="your <?= $item ?>">
            </td>
        </tr>
        <?php $item = 'address' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">Address</label>
            </td>
            <td>
                <input type="text" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->Extra[$item] ?>" placeholder="your <?= $item ?>">
            </td>
        </tr>
        <?php $item = 'interests' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">Interests</label>
            </td>
            <td>
                <input type="text" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->Extra[$item] ?>" placeholder="your <?= $item ?>">
            </td>
        </tr>
        <?php $item = 'hobbies' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">Hobbies</label>
            </td>
            <td>
                <input type="text" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->Extra[$item] ?>" placeholder="your <?= $item ?>">
            </td>
        </tr>
        <?php $item = 'job_cat' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">Job Category</label>
            </td>
            <td>
                <select class="form-select" name="<?= $item ?>" id="f-<?= $item ?>" >
                    <?php if($job_categories) foreach ($job_categories as $job_category) { ?>
                        <option value="<?= $job_category['id'] ?>" <?= ($profile->Extra[$item]['id']==$job_category['id']) ? 'selected' : ''; ?> ><?= $job_category['name'] ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <?php $item = 'job_title' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">Job Title</label>
            </td>
            <td>
                <input type="text" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->Extra[$item] ?>" placeholder="your <?= $item ?>">
            </td>
        </tr>
        <?php $item = 'exp_fx_year' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">FX Experience</label>
            </td>
            <td>
                <input type="number" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->Extra[$item] ?>" placeholder="your <?= $item ?>">
            </td>
        </tr>
        <?php $item = 'exp_cfd_year' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">CFD Experience</label>
            </td>
            <td>
                <input type="number" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->Extra[$item] ?>" placeholder="your <?= $item ?>">
            </td>
        </tr>
        <?php $item = 'income' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">Income</label>
            </td>
            <td>
                <select class="form-select" name="<?= $item ?>" id="f-<?= $item ?>" >
                    <?php if($incomes) foreach ($incomes as $income) { ?>
                        <option value="<?= $income['id'] ?>" <?= ($profile->Extra[$item]['id']==$income['id']) ? 'selected' : ''; ?> ><?= $income['name'] ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <?php $item = 'investment' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">Investment Amount</label>
            </td>
            <td>
                <select class="form-select" name="<?= $item ?>" id="f-<?= $item ?>" >
                    <?php if($investments) foreach ($investments as $investment) { ?>
                        <option value="<?= $investment['id'] ?>" <?= ($profile->Extra[$item]['id']==$investment['id']) ? 'selected' : ''; ?> ><?= $investment['name'] ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <?php $item = 'strategy' ?>
        <tr data-item="<?= $item ?>" class="item-row">
            <td>
                <label for="f-<?= $item ?>" class="form-label">Trading Strategy</label>
            </td>
            <td>
                <input type="text" class="form-control" name="<?= $item ?>" id="f-<?= $item ?>" value="<?= $profile->Extra[$item] ?>" placeholder="your <?= $item ?>">
            </td>
        </tr>

    </table>



    <div id="form-actions" class="text-end">
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