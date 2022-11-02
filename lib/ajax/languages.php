<?php

/**
 * Group Functions Class
 * 11:13 AM Wednesday, November 18, 2020 | M.Abooali
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

function update() {
    $output = new stdClass();
    $output->e = !(($_POST['__lang']) ?? false);
    global $_L;
    if (!$output->e) {
        $lang = $_POST['__lang'];
        unset($_POST['__lang']);
        $output->res = $_L->update($lang, $_POST);
    }
    echo json_encode($output);
}

function select() {
    $output = new stdClass();
    $output->e = !(($_POST['lang']) ?? false);
    global $_L;
    if (!$output->e) {
        // unset($_POST['lang']);
        $_SESSION['language'] = $_POST['lang'];
        $output->res = $_L->set($_POST['lang']);
    }
    echo json_encode($output);
}