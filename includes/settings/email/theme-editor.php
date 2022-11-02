<?php

    $load = ($_GET['theme']) ?? false;
    if(!$load) {

        $query['db']             = 'DB_admin';
        $query['table']          = 'email_themes';
        $query['table_html']     = 'email-them-list';
        $query['key']            = 'id';
        $query['columns']        = array(
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
                'db' => 'cat',
                'th' => 'Cat',
                'dt' => 2
            ),
            array(
                'db' => 'update_time',
                'th' => 'Last Update',
                'dt' => 3
            ),
            array(
                'db' => 'update_by',
                'th' => 'Last Editor',
                'dt' => 4
            ),
            array(
                'db' => 'id',
                'th' => 'Manage',
                'dt' => 5,
                'formatter' => true
            )
        );
        $table_email_themes = $factory::dataTableSimple(10, $query);

    } else {

        $Email_Theme = new Email_Theme();
        $theme = $Email_Theme->load($load);

        GF::loadCSS('f', "https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css");
        GF::loadJS('f', "https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js");
        GF::makeJS('f',"
                
            $('#eContent').summernote({
                placeholder: 'Hello Bootstrap 4',
                tabsize: 2,
                height: 500,
                toolbar: [
                  ['style', ['style']],
                  ['font', ['bold', 'underline', 'clear']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['table', ['table']],
                  ['insert', ['link', 'picture', 'video']],
                  ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        
        ");


    }

?>
<section class="<?= $href ?>">

    <h6 class="text-center">Theme Editor</h6>
    <div>
        <?php if(!$load): echo $table_email_themes; else: ?>
            <form id="editTheme" autocomplete="off">

                <input class="form-control mb-2" type="hidden" name="id" value="<?= $theme['data']['id'] ?>" readonly>

                <label for="eName">Name</label>
                <input class="form-control mb-2" id="eName" name="name" placeholder="Theme Name" value="<?= $theme['data']['name'] ?>" required>
                <label for="eCat">Cat</label>
                <input class="form-control mb-2" id="eCat" name="cat" placeholder="Theme Cat" value="<?= $theme['data']['cat'] ?>"  required>

                <button class="btn btn-success btn-block form-control" type="submit">Update The Theme</button>
                <hr>
                <label for="eContent">Theme Content</label>
                <p class="alert-warning p-2">
                    User {~~varname~~} to place dynamic data;
                </p>
                <textarea id="eContent" name="content">
                    <?= $theme['content'] ?>
                </textarea>
            </form>
        <?php endif; ?>
    </div>

</section>