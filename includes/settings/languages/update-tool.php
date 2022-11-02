<?php
    $lang = $_GET['lang'] ?? false;
    global $_L;
    if ($lang) $phrases = $_L->get($lang);
?>
<section class="<?= $href ?>">

    <h6 class="text-center">Update Tool</h6>
    <?php
    if($phrases) {
        $direction= $phrases['core']['_direction'];
        $language_name= $phrases['core']['_language_name'];
    ?>
    <form id="lang-update">
        <!-- Accordion -->
        <div id="update-tools" class="accordion ">
            <?php foreach ($phrases as $section => $phrase) { ?>
            <!-- Accordion item 1 -->
            <div class="card">
                <div id="headingOne" class="px-3 bg-white shadow-sm border-0">
                    <strong class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse" data-target="#sec-<?= $section ?>" aria-expanded="false" aria-controls="collapseOne" class="d-block position-relative collapsed text-dark collapsible-link py-2"><?= $section ?></a></strong>
                </div>
                <div id="sec-<?= $section ?>" aria-labelledby="headingOne" data-parent="#update-tools" class="collapse">
                    <div class="card-body p-5">
                        <table class="table table-sm table-hover">
                            <thead>
                            <tr>
                                <th>Phrase</th>
                                <th>Translation</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($phrase as $k => $v) { ?>
                                <tr>
                                    <td><label for="<?= $k ?>"> <?= $k ?> </label></td>
                                    <td><input data-old="<?= $v ?>" dir="<?= $direction ?? 'ltr' ?>"class="form-control d-block" type="text" id="<?= $k ?>" name="<?= $section ?>[<?= $k ?>]" value="<?= $v ?>"></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php } ?>
            <input class="form-control d-block" type="hidden" name="__lang"  data-old="<?= $lang ?>"  value="<?= $lang ?>">
            <span class="">Changed Phrases: <span id="countChange" class="alert-warning px-2 mx-3 text-muted">0</span>
            <button class="btn btn-success ml-5 my-4">Update Language File</button>
        </div>
    </form>
    <?php } else {
        $langs = scandir('./languages');
        unset($langs[0]);
        unset($langs[1]);
        ?>
        <div class="p-5">
            <p>Please Select Lang File to Edit: </p>
            <?php if($langs) foreach ($langs as $lang) echo " <a class='btn btn-outline-secondary m-2' href='sys_settings.php?section=languages_update-tool&lang=".basename ($lang, ".ini")."'>$lang</a>"; ?>
        </div>
    <?php } ?>

</section>