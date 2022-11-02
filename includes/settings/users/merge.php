<?php
    $called_users = explode(',',$_GET['u'] ?? '');
    if($called_users) foreach ($called_users AS $item)
        $_SESSION['M']['mergeUsers'][$item] = $item;
    if($_SESSION['M']['mergeUsers']) $called_users = $_SESSION['M']['mergeUsers'];
    unset($called_users['']);
    $users = array();
    if(count($called_users)>1){
        global $userManager;
        if($called_users) foreach ($called_users as $user){
            $get_user = $userManager->get($user);
            if($get_user){
                unset(
                    $get_user['password'],
                    $get_user['created_at'],
                    $get_user['created_by'],
                    $get_user['updated_at'],
                    $get_user['updated_by'],

                    $get_user['user_extra']['id'],
                    $get_user['user_extra']['user_id'],
                    $get_user['user_extra']['unit'],
                    $get_user['user_extra']['type'],
                    $get_user['user_extra']['created_at'],
                    $get_user['user_extra']['created_by'],
                    $get_user['user_extra']['updated_at'],
                    $get_user['user_extra']['updated_by'],

                    $get_user['marketing']['user_id'],
                    $get_user['marketing']['id'],
                    $get_user['marketing']['created_at'],
                    $get_user['marketing']['created_by'],
                    $get_user['marketing']['updated_at'],
                    $get_user['marketing']['updated_by'],

                    $get_user['gi']['user_id'],
                    $get_user['gi']['id'],
                    $get_user['gi']['created_at'],
                    $get_user['gi']['created_by'],
                    $get_user['gi']['updated_at'],
                    $get_user['gi']['updated_by'],

                    $get_user['fx']['user_id'],
                    $get_user['fx']['id'],
                    $get_user['fx']['created_at'],
                    $get_user['fx']['created_by'],
                    $get_user['fx']['updated_at'],
                    $get_user['fx']['updated_by']
                );
                if($get_user['tp']) foreach ($get_user['tp'] as $key=>$tp){
                    $get_user['tp'][$key] = [
                        "login"  => $get_user['tp'][$key]['login'],
                        "server" => $get_user['tp'][$key]['server']
                    ];
                }
                if(empty($get_user['marketing'])) unset($get_user['marketing']);
                if(empty($get_user['gi'])) unset($get_user['gi']);
                if(empty($get_user['fx'])) unset($get_user['fx']);
                if(empty($get_user['user_extra'])) unset($get_user['user_extra']);
                if(empty($get_user['tp'])) unset($get_user['tp']);
                if($get_user){
                    $users[$user] = $get_user;
                }
            }
        }
    }

?>
<style>
    td{
        height: 30px;
    }
    .content-page,#wrapper {
        overflow: unset;
    }
</style>

<section class="<?= $href ?>">
    <h6 class="text-center">Merge Users</h6>

        <?php if(count($users)>1) { ?>
        <div class="sticky-top py-3 px-5 col-md-7">
            <button id="doA-mergeUsers" class="btn btn-outline-warning">Merge All Users</button>
            <span class="ml-3">Move</span>
            <button id="doA-moveComments" class="btn btn-outline-info">Comments</button>
            <button id="doA-moveEmails" class="btn btn-outline-info">Emails</button>
            <button id="doA-moveLogs" class="btn btn-outline-secondary">Loges</button>
            <button id="doA-sessionsArchive" class="btn btn-outline-secondary">Session Archive</button>
        </div>
        <div class="row">
            <?php foreach ($users as $user) { ?>
            <div class="col-auto">

                <h4 id="u-<?= $user['id'] ?>" data-uid="<?= $user['id'] ?>" class="main-user-select p-2" style="transform: rotateX(0deg);">
                    User <?= $user['id'] ?>
                    <button data-id="<?= $user['id'] ?>" data-toggle="tooltip" data-placement="top" title="Remove from list" class="doA-remove btn btn-sm btn-outline-danger float-right ">X</button>
                </h4>
                <div class="border border-light p-1">
                    <table id="u-item-<?= $user['id'] ?>" class="user-items table table-sm" style="table-layout:fixed;width:250px;transform: rotateX(0deg);">
                        <tr><th><strong>General</strong></th></tr>
                        <?php
                            foreach ($user as $key=>$item) {
                            if(is_array($item)) continue;
                            if($item==null) continue;
                        ?>
                        <tr style="transform: rotateX(0deg);" id="tr-<?= $user['id'] ?>-<?= $key ?>" data-uid="<?= $user['id'] ?>" data-table="users" data-item="<?= $key ?>" class="items item-<?= $key ?>" data-toggle="tooltip" data-placement="top" title="<?= $key ?>">
                            <td>
                                <small id="v-<?= $user['id'] ?>-<?= $key ?>"><?php echo (is_array($item)) ? json_encode($item) : $item; ?></small>
                            </td>
                        </tr>
                        <?php } ?>

                        <tr><td><strong>Extra</strong></td></tr>
                        <?php if($user['user_extra']) foreach ($user['user_extra'] as $key=>$item) { ?>
                        <tr style="transform: rotateX(0deg);" id="tr-<?= $user['id'] ?>-<?= $key ?>" data-uid="<?= $user['id'] ?>" data-table="user_extra" data-item='<?= $key ?>' class="items item-<?= $key ?>" data-toggle="tooltip" data-placement="top" title="Extra <?= $key ?>">
                            <td>
                                <small id="v-<?= $user['id'] ?>-<?= $key ?>"><?php echo (is_array($item)) ? json_encode($item) : $item; ?></small>
                            </td>
                        </tr>
                        <?php } ?>

                        <tr><td><strong>Marketing</strong></td></tr>
                        <?php if($user['marketing']) foreach ($user['marketing'] as $key=>$item) { ?>
                        <tr style="transform: rotateX(0deg);" id="tr-<?= $user['id'] ?>-<?= $key ?>" data-uid="<?= $user['id'] ?>" data-table="marketing" data-item="<?= $key ?>" class="items item-<?= $key ?>" data-toggle="tooltip" data-placement="top" title="Marketing <?= $key ?>">
                            <td>
                                <small id="v-<?= $user['id'] ?>-<?= $key ?>"><?php echo (is_array($item)) ? json_encode($item) : $item; ?></small>
                            </td>
                        </tr>
                        <?php } ?>

                        <tr><td><strong>GI</strong></td></tr>
                        <?php if($user['gi']) foreach ($user['gi'] as $key=>$item) { ?>
                        <tr style="transform: rotateX(0deg);" id="tr-<?= $user['id'] ?>-<?= $key ?>" data-uid="<?= $user['id'] ?>" data-table="gi" data-item="<?= $key ?>" class="items item-<?= $key ?>" data-toggle="tooltip" data-placement="top" title="GI <?= $key ?>">
                            <td>
                                <small id="v-<?= $user['id'] ?>-<?= $key ?>"><?php echo (is_array($item)) ? json_encode($item) : $item; ?></small>
                            </td>
                        </tr>
                        <?php } ?>

                        <tr><td><strong>FX</strong></td></tr>
                        <?php if($user['fx']) foreach ($user['fx'] as $key=>$item) { ?>
                        <tr style="transform: rotateX(0deg);" id="tr-<?= $user['id'] ?>-<?= $key ?>" data-uid="<?= $user['id'] ?>" data-table="fx" data-item="<?= $key ?>" class="items item-<?= $key ?>" data-toggle="tooltip" data-placement="top" title="FX <?= $key ?>">
                            <td>
                                <small id="v-<?= $user['id'] ?>-<?= $key ?>"><?php echo (is_array($item)) ? json_encode($item) : $item; ?></small>
                            </td>
                        </tr>
                        <?php } ?>

                        <tr><td><strong>TP</strong></td></tr>
                        <?php if($user['tp']) foreach ($user['tp'] as $key=>$item) { ?>
                        <tr style="transform: rotateX(0deg);" id="tr-<?= $user['id'] ?>-<?= $key ?>" data-uid="<?= $user['id'] ?>"  data-table="tp" data-item="<?= $item['login'] ?>" class="items item-<?= $key ?>" data-toggle="tooltip" data-placement="top" title="TP <?= $key ?>">
                            <td>
                                <small id="v-<?= $user['id'] ?>-<?= $key ?>"><?php echo (is_array($item)) ? json_encode($item) : $item; ?></small>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>

                </div>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div id="select-users">
            Select more then 1 users to merge<br>
            Current Selected: <?php if($_SESSION['M']['mergeUsers']) foreach($_SESSION['M']['mergeUsers'] as $user) echo $user.'<br>'; ?>
            <p>
                <button class="doA-empty btn btn-outline-danger">Empty List</button>
            </p>
        </div>
    <?php } ?>
</section>

