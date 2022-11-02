<?php

?>
<?php $form_name = 'profile-edit-agreement'; ?>
<form class="screen-wrapper" name="<?= $form_name ?>" id="<?= $form_name ?>">
    <p>
        <?php include '../'.Broker['terms_file']; ?>
    </p>
    <div id="form-actions" class="text-end">
        <button type="submit" class="btn btn-primary">I Agree</button>
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