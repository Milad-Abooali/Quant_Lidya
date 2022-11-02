<?php

/**
 * Paths Functions Class
 * 11:13 AM Wednesday, November 19, 2020 | M.Abooali
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

function add() {
    $output = new stdClass();
    $output->e = !(($_POST['path']) ?? false);
    $paths = new paths();
    if (!$output->e) $output->res = $paths->add($_POST['path'],$_POST['view'],$_POST['new'],$_POST['edit'],$_POST['del']);
    echo json_encode($output);
}

function update() {
    $output = new stdClass();
    $output->e = !(($_POST['pid']) ?? false);
    $output->e = !(($_POST['path']) ?? false);
    $paths = new paths();
    if (!$output->e) $output->res = $paths->update($_POST['pid'],$_POST['path'],$_POST['view'],$_POST['new'],$_POST['edit'],$_POST['del']);
    echo json_encode($output);
}

function delete() {
    $output = new stdClass();
    $output->e = !(($_POST['pid']) ?? false);
    $paths = new paths();
    if (!$output->e) $output->res = $paths->delete($_POST['pid']);
    echo json_encode($output);
}
