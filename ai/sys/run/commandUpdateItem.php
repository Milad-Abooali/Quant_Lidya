<?php

/**
 * Command Login
 */

$page['title'] = 'Command Login';

$_AJAX_ON = true;
$_API_ON  = false;

$username = $_REQUEST['username'] ?? false;
$error = null;

$class   = $_REQUEST['class'];
$block   = $_REQUEST['block'];
$item    = $_REQUEST['item'];
$val     = $_REQUEST['val'];

if(!$sess->IS_LOGIN) {
    $error = 'You need login first!';
} else {
    if ($class == 'classUser') {
        $userManager = new userManager();
        $data=array();
        if(in_array($block, array('extra','gi','fx','marketing'))) {
            $data['user_'.$block][$item] = $val;
        } else {
            $data[$block][$item] = $val;
        }
        $_RUN->res = $userManager->setCustom($_SESSION['id'], $data);
        $_RUN->d = $data;
    } else if ($class == 'classMeta') { $error = 'dev'; }
}

$_RUN->e = $error;