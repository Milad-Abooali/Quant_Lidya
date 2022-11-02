<?php

    global $db;
    $broker = $db->selectId('brokers',$_POST['id']);
    $broker['edit_email'] = explode(',',$broker['edit_email']);
    $broker['upload_docs'] = explode(',',$broker['upload_docs']);

    if ($_POST['edit']) {
?>
        <form id="edit-broker">
            <div class="row">
                <div class="col-md-6">
                    <table class="table-sm table-hover w-100">
                        <tbody>
                        <tr>
                            <td>ID</td>
                            <td><input class="form-control" name="id" value="<?= $broker['id'] ?>" readonly></td>
                        </tr>
                        <tr>
                            <td>Title</td>
                            <td><input class="form-control" name="title" value="<?= $broker['title'] ?>" required></td>

                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><input class="form-control" name="email" value="<?= $broker['email'] ?>" required></td>
                        </tr>
                        <tr>
                            <td>Web Site</td>
                            <td><input class="form-control" name="web_url" value="<?= $broker['web_url'] ?>" required></td>
                        </tr>
                        <tr>
                            <td>CRM</td>
                            <td><input class="form-control" name="crm_url" value="<?= $broker['crm_url'] ?>" required></td>
                        </tr>
                        <tr>
                            <td>PHP Session Path</td>
                            <td><input class="form-control" name="session_path" value="<?= $broker['session_path'] ?>" required></td>
                        </tr>
                        <tr>
                            <td>TOS File Path</td>
                            <td><input class="form-control" name="terms_file" value="<?= $broker['terms_file'] ?>" required></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table-sm table-hover w-100">
                        <tbody>
                        <tr>
                            <td>Defualt Language</td>
                            <td><input class="form-control" name="def_language" value="<?= $broker['def_language'] ?>" required></td>
                        </tr>
                        <tr>
                            <td>Defualt Unit ID</td>
                            <td><input class="form-control" name="def_unit" value="<?= $broker['def_unit'] ?>" required></td>
                        </tr>
                        <tr>
                            <td>Units Stes</td>
                            <td><input class="form-control" name="units" value="<?= $broker['units'] ?>" required></td>
                        </tr>
                        <tr>
                            <td>Captcha System</td>
                            <td>
                                <?=
                                ($broker['captcha']) ?
                                    '
                                        <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" name="captcha" value="1" id="rule-captcha" checked>
                                          <label class="custom-control-label" for="rule-captcha"> </label>
                                        </div>
                                    ' :
                                    '
                                        <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" name="captcha" value="1" id="rule-captcha">
                                          <label class="custom-control-label" for="rule-captcha"> </label>
                                        </div>
                                    ';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Pin Lock</td>
                            <td>
                                <?=
                                ($broker['pin_lock']) ?
                                    '
                                        <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" name="pin_lock" value="1" id="rule-pin_lock" checked>
                                          <label class="custom-control-label" for="rule-pin_lock"> </label>
                                        </div>
                                    ' :
                                    '
                                        <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" name="pin_lock" value="1" id="rule-pin_lock">
                                          <label class="custom-control-label" for="rule-pin_lock"> </label>
                                        </div>
                                    ';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Maintenance Mod</td>
                            <td>
                                <?=
                                ($broker['maintenance']) ?
                                    '
                                        <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" name="maintenance" value="1" id="rule-maintenance" checked>
                                          <label class="custom-control-label" for="rule-maintenance"> </label>
                                        </div>
                                    ' :
                                    '
                                        <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" name="maintenance" value="1" id="rule-maintenance">
                                          <label class="custom-control-label" for="rule-maintenance"> </label>
                                        </div>
                                    ';
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Edit Email</td>
                            <td>
                                <select class="form-control form-select3" id="edit_email" name="edit_email[]" multiple="multiple" required>
                                    <?php
                                    global $db;
                                    $types = $db->selectAll('type');
                                    if($types) foreach ($types as $type){
                                        $selected = ($type['name']==='Admin' || in_array($type['name'], $broker['edit_email']) ) ? 'selected' : '';
                                        echo "<option value='".$type['name']."' $selected>".$type['name']."</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        </tr>

                        <tr>
                            <td>Upload Documents</td>
                            <td>
                                <select class="form-control form-select3" id="upload_docs" name="upload_docs[]" multiple="multiple" required>
                                    <?php
                                    if($types) foreach ($types as $type){
                                        $selected = ($type['name']==='Admin' || in_array($type['name'], $broker['upload_docs']) ) ? 'selected' : '';
                                        echo "<option value='".$type['name']."' $selected>".$type['name']."</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <div class="col-md-12 mt-3">
                    <table class="table-sm table-bordered w-100">
                        <tbody>
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-9">
                                        <label for="logo">Logo</label>
                                        <div class="custom-file my-1">
                                            <input type="file" class="custom-file-input" id="logo" name="logo">
                                            <label class="custom-file-label" for="logo">Click to change</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <a href="media/broker/<?= $broker['logo'] ?>" target="_blank"><img class="mw-100" src="media/broker/<?= $broker['logo'] ?>"</a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="row">
                                    <div class="col-9">
                                        <label for="dark_logo">Dark</label>
                                        <div class="custom-file my-1">
                                            <input type="file" class="custom-file-input" id="dark_logo" name="dark_logo">
                                            <label class="custom-file-label" for="dark_logo">Click to change</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                      <a class="d-block bg-dark" href="media/broker/<?= $broker['dark_logo'] ?>" target="_blank"><img class="mw-100" src="media/broker/<?= $broker['dark_logo'] ?>"</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-9">
                                        <label for="mini_logo">Small</label>
                                        <div class="custom-file my-1">
                                            <input type="file" class="custom-file-input" id="mini_logo" name="mini_logo">
                                            <label class="custom-file-label" for="mini_logo">Click to change</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <a href="media/broker/<?= $broker['mini_logo'] ?>" target="_blank"><img class="mw-100" src="media/broker/<?= $broker['mini_logo'] ?>"</a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="row">
                                    <div class="col-9">
                                        <label for="favicon">Favicon</label>
                                        <div class="custom-file my-1">
                                           <input type="file" class="custom-file-input" id="favicon" name="favicon">
                                           <label class="custom-file-label" for="favicon">Click to change</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                      <a href="media/broker/<?= $broker['favicon'] ?>" target="_blank"><img class="mw-100" src="media/broker/<?= $broker['favicon'] ?>"</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="text-center mt-3">
              <div id="fRes" class="alert"></div>
              <button type="submit" class="btn btn-primary col-md-6">Save</button>
            </div>
        </form>

    <?php } else { ?>

    <div class="row">
        <div class="col-md-6">
            <table class="table-sm table-hover w-100">
                <tbody>
                    <tr>
                        <td class="alert-secondary">ID</td>
                        <td><?= $broker['id'] ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">Title</td>
                        <td><?= $broker['title'] ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">Email</td>
                        <td><?= $broker['email'] ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">Web Site</td>
                        <td><?= $broker['web_url'] ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">CRM</td>
                        <td><?= $broker['crm_url'] ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">PHP Session Path</td>
                        <td><?= $broker['session_path'] ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">TOS File Path</td>
                        <td><?= $broker['terms_file'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table-sm table-hover w-100">
                <tbody>
                    <tr>
                        <td class="alert-secondary">Defualt Language</td>
                        <td><?= $broker['def_language'] ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">Defualt Unit ID</td>
                        <td><?= $broker['def_unit'] ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">Unit Stes</td>
                        <td><?= $broker['units'] ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">Captcha System</td>
                        <td><?= ($broker['captcha']) ? '<span class="text-success">ON</span>' : '<span class="text-danger">OFF</span>' ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">Pin Lock</td>
                        <td><?= ($broker['pin_lock']) ? '<span class="text-success">ON</span>' : '<span class="text-danger">OFF</span>' ?></td>
                    </tr>
                    <tr>
                        <td class="alert-secondary">Maintenance Mod</td>
                        <td><?= ($broker['maintenance']) ? '<span class="text-success">ON</span>' : '<span class="text-danger">OFF</span>' ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-12 mt-3">
            <table class="table-sm table-hover w-100">
                <tbody>
                    <tr class="alert-secondary">
                        <td>Logo</td>
                        <td>Dark</td>
                        <td>Small</td>
                        <td>Favicon</td>
                    </tr>
                    <tr>
                        <td><a href="media/broker/<?= $broker['logo'] ?>" target="_blank"><img class="mw-100" src="media/broker/<?= $broker['logo'] ?>"</a></td>
                        <td class="bg-dark"><a href="media/broker/<?= $broker['dark_logo'] ?>" target="_blank"><img class="mw-100" src="media/broker/<?= $broker['dark_logo'] ?>"</a></td>
                        <td><a href="media/broker/<?= $broker['mini_logo'] ?>" target="_blank"><img class="mw-100" src="media/broker/<?= $broker['mini_logo'] ?>"</a></td>
                        <td><a href="media/broker/<?= $broker['favicon'] ?>" target="_blank"><img class="mw-100 img-thumbnail" src="media/broker/<?= $broker['favicon'] ?>"</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php } ?>