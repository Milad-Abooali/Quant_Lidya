<?php
######################################################################
#  M | 10:27 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    include('config.php');

    if($_SESSION['id']) {
    $userManager = new userManager();

    // Live CUD
    $cid = $userManager->getCustom($_SESSION['id'],'cid')['cid'];

    // Session CID
    //$cid = $_SESSION['cid'];

    if (!in_array($cid, array(null,"0000-00-00 00:00:00"))) {
        $file = DOWNLOAD_LINK['MT5'];

        header('Content-type: application/octet-stream');
        header('Content-length: ' . filesize($file));
        readfile($file);

    } else {
        echo 'you cant have the file.';
    }

} else {
        echo 'Please Login.';

}