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


function updateModuleStatus() {
    $output = new stdClass();
    $waf = new waf();
    if (!$output->e) $output->res = $waf->updateModuleStatus($_POST['m'],$_POST['status']);
    echo json_encode($output);
}

function addIP() {
    $output = new stdClass();
    $output->e = !(($_POST['ip']) ?? false);
    $waf = new waf();
    if (!$output->e) $output->res = $waf->addIP($_POST['ip'], $_POST['ip_info'], $_POST['status']);
    echo json_encode($output);
}

function updateIP() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $waf = new waf();
    if (!$output->e) $output->res = $waf->updateIP($_POST['id'],$_POST['status']);
    echo json_encode($output);
}

function deleteIP() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $waf = new waf();
    if (!$output->e) $output->res = $waf->deleteIP($_POST['id']);
    echo json_encode($output);
}


function addFilter() {
    $output = new stdClass();
    $output->e = !(($_POST['col']) ?? false);
    $output->e = !(($_POST['cond']) ?? false);
    $output->e = !(($_POST['val']) ?? false);
    $waf = new waf();
    if (!$output->e) $output->res = $waf->addFilter($_POST['col'], $_POST['cond'], $_POST['val'], $_POST['status']);
    echo json_encode($output);
}

function updateFilter() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $waf = new waf();
    if (!$output->e) $output->res = $waf->updateFilter($_POST['id'],$_POST['status']);
    echo json_encode($output);
}

function deleteFilter() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $waf = new waf();
    if (!$output->e) $output->res = $waf->deleteFilter($_POST['id']);
    echo json_encode($output);
}

function addException() {
    $output = new stdClass();
    $output->e = !(($_POST['col']) ?? false);
    $output->e = !(($_POST['cond']) ?? false);
    $output->e = !(($_POST['val']) ?? false);
    $output->e = !(($_POST['expire']) ?? false);
    $waf = new waf();
    if (!$output->e) $output->res = $waf->addException($_POST['col'], $_POST['cond'], $_POST['val'], $_POST['expire']);
    echo json_encode($output);
}

function updateSetting() {
    $output = new stdClass();
    $output->e = !(($_POST['module']) ?? false);
    $waf = new waf();
    $module = $_POST['module'];
    unset($_POST['module']);
    $data = (json_encode($_POST)) ?? null;
    if (!$output->e) $output->res = $waf->updateModuleSettings($module, $data);
    echo json_encode($output);
}

function endException() {
    $output = new stdClass();
    $output->e = !(($_POST['id']) ?? false);
    $waf = new waf();
    if (!$output->e) $output->res = $waf->endException($_POST['id']);
    echo json_encode($output);
}

function endSess() {
    $output = new stdClass();
    $output->e = !(($_POST['sessid']) ?? false);
    $waf = new waf();
    if (!$output->e) $output->res = $waf->endSess($_POST['sessid']);
    echo json_encode($output);
}

function endSEN() {
    $output = new stdClass();
    $output->e = !(($_POST['sen']) ?? false);
    $waf = new waf();
    $sess_id = hex2bin($_POST['sen']);
    if (!$output->e) $output->res = $waf->endSEN($sess_id);
    echo json_encode($output);
}

function endAllSess() {
    $output = new stdClass();
    $output->e = false;
    $waf = new waf();
    if (!$output->e) $output->res = $waf->endAllSess();
    echo json_encode($output);
}