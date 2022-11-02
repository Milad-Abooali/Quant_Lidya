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

function add() {
    $output = new stdClass();
    $output->e = !(($_POST['name']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->add($_POST['name'],$_POST['priority']);
    echo json_encode($output);
}

function copyFrom() {
    $output = new stdClass();
    $output->e = !(($_POST['name']) ?? false);
    $output->e = !(($_POST['gid']) ?? false);
    $groups = new groups();
    $perm = $_POST['perms'] ?? false;
    $users = $_POST['users'] ?? false;
    if (!$output->e) $output->res = $groups->copyFrom($_POST['name'],$_POST['priority'] , $_POST['gid'], $perm, $users);
    echo json_encode($output);
}

function update() {
    $output = new stdClass();
    $output->e = !(($_POST['gid']) ?? false);
    $output->e = !(($_POST['name']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->update($_POST['gid'],$_POST['name'],$_POST['priority']);
    echo json_encode($output);
}

function drop() {
    $output = new stdClass();
    $output->e = !(($_POST['gid']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->drop($_POST['gid']);
    echo json_encode($output);
}

function delete() {
    $output = new stdClass();
    $output->e = !(($_POST['gid']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->delete($_POST['gid']);
    echo json_encode($output);
}

function users() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->users($_POST['id']);
    echo json_encode($output);
}

function addUsers() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $output->e = !(($_POST['gids']) ?? false);
    $id_set = implode(',',$_POST['id']);
    $group_set = implode(',',$_POST['gids']);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->addUsers($id_set,$group_set);
    echo json_encode($output);
}

function removeUsers() {
    $output = new stdClass();
    $output->e = !(($_POST['ids']) ?? false);
    $output->e = !(($_POST['gids']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->removeUsers($_POST['ids'],$_POST['gids']);
    echo json_encode($output);
}


function addPerm() {
    $output = new stdClass();
    $output->e = !(($_POST['gid']) ?? false);
    $output->e = !(($_POST['pid']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->addPerm($_POST['gid'],$_POST['pid'],$_POST['view'],$_POST['new'],$_POST['edit'],$_POST['del']);
    echo json_encode($output);
}

function delPerm() {
    $output = new stdClass();
    $output->e = !(($_POST['gpid']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->delPerm($_POST['gpid']);
    echo json_encode($output);
}

function addUserPerm() {
    $output = new stdClass();
    $output->e = !(($_POST['uid']) ?? false);
    $output->e = !(($_POST['pid']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->addUserPerm($_POST['uid'],$_POST['pid'],$_POST['view'],$_POST['new'],$_POST['edit'],$_POST['del']);
    echo json_encode($output);
}

function delUserPerm() {
    $output = new stdClass();
    $output->e = !(($_POST['upid']) ?? false);
    $groups = new groups();
    if (!$output->e) $output->res = $groups->delUserPerm($_POST['upid']);
    echo json_encode($output);
}