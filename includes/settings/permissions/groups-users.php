<?php

    $groups = new groups();
    $groups_list = $groups->list();


    $query['db']             = 'DB_admin';
    $query['table_html']     = 'user-list';
    $query['query']     = "`users` AS `users` LEFT JOIN `user_extra` AS `user_extra` ON `users`.`id` = `user_extra`.`user_id` LEFT JOIN `status` AS `status` ON `user_extra`.`status` = `status`.`id` LEFT JOIN `user_marketing` ON `user_marketing`.`user_id` = `user_extra`.`user_id`";
    $query['key']       = '`users`.`id`';
    $query['columns']   = array(
        array(
                'db' => 'users.id',
                'dt' => 0,
                'th' => '<input type="checkbox" class="select-all">',
                'formatter' => true
            ),
        array(
                'db' => 'user_extra.fname',
                'dt' => 1,
                'th' => 'First Name'
            ),
        array(
                'db' => 'user_extra.lname',
                'dt' => 2,
                'th' => 'Last Name'
            ),
        array(
                'db' => 'users.username',
                'dt' => 3,
                'th' => 'Username'
            ),
        array(
                'db' => 'users.email',
                'dt' => 4,
                'th' => 'Email'),
        array(
                'db' => '(SELECT username FROM users WHERE user_extra.conversion = users.id)',
                'dt' => 5,
                'th' => 'Agent'
        ),
        array(
                'db' => 'status.status',
                'dt' => 6,
                'th' => 'Status'
            ),
        array(
                'db' => 'user_marketing.lead_src',
                'dt' => 7,
                'th' => 'Source'
            ),
        array(
                'db' => 'user_extra.country',
                'dt' => 8,
                'th' => 'Country'
            )
    );
    $option = '
        "columnDefs": [
           {"orderable":false, "targets":0}
        ],
        "order": [[0, "asc"]]
    ';
    $users_list = $factory::dataTableComplex(10,$query,$option);

?>


<section class="<?= $href ?>">


    <div class="row">
        <form id="usersGroups" autocomplete="off">
            <div class="col-md-12 px-4">
                <h6>Group List</h6>
                <div class="row">
                <?php if($groups_list) foreach ($groups_list as $item) { ?>
                    <div class="col">
                        <input class="form-check-input" type="checkbox" id="<?= $item['name'] ?>-<?= $item['id'] ?>" name="gids[]" value="<?= $item['id'] ?>">
                        <label class="form-check-label" for="<?= $item['name'] ?>-<?= $item['id'] ?>"><?= $item['name'] ?></label>
                    </div>
                <?php } ?>
                    <div class="col">
                        <button class="btn btn-success btn-block form-control" type="submit">Save Change</button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="col-md-12">
                <h6>User List</h6>
                <?= $users_list ?>
            </div>
        </form>
    </div>

</section>