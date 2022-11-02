<?php


?>
<section class="<?= $href ?>">

    <h6 class="text-center">Creat New Theme</h6>
    <div class="row">

        <form id="newTheme" autocomplete="off">
            <label for="newName">Name</label>
            <input class="form-control mb-2" id="newName" type="text" name="name" placeholder="Theme Name" required>
            <label for="newCat">Cat</label>
            <input class="form-control mb-2" id="newCat"  type="text" name="cat" placeholder="Theme Cat" required>
            <button class="btn btn-success btn-block form-control" type="submit">Add New Theme</button>
        </form>

    </div>

</section>