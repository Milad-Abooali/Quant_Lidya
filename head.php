<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

if(($is_include_head) ?? false) {

} else {
$is_include_head = true;

require_once "config.php";

$media = 'SELECT media FROM media WHERE user_id = '.$_SESSION["id"].' AND type = "avatar"';
$result2 = $DB_admin->query($media);

if(mysqli_num_rows($result2) > 0){
    while ($avatar = mysqli_fetch_array($result2)) {
        $avatars = $avatar['media'];
    }
} else {
    $avatars = "logo-sm.png";
}

$media2 = 'SELECT media,verify FROM media WHERE user_id = '.$_SESSION["id"].' AND type = "ID"';
$result3 = $DB_admin->query($media2);

if(mysqli_num_rows($result3) > 0){
    while ($ids = mysqli_fetch_array($result3)) {
        $id = $ids['media'];
        $id_verify = $ids['verify'];
    }
} else {
    $id = "logo-sm.png";
    $id_verify = "3";
}

$media3 = 'SELECT media,verify FROM media WHERE user_id = '.$_SESSION["id"].' AND type = "Bill"';
$result4 = $DB_admin->query($media3);

if(mysqli_num_rows($result4) > 0){
    while ($poas = mysqli_fetch_array($result4)) {
        $poa = $poas['media'];
        $poa_verify = $poas['verify'];
    }
} else {
    $poa = "logo-sm.png";
    $poa_verify = "3";
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title><?php echo Broker['title']; ?> - Feel The Difference</title>
        <meta content="Admin Dashboard" name="description" />
        <meta content="Themesbrand" name="author" />
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <?= factory::header() ?>
        
<?php 
    // Version Changer
    $_version = ($_GET['_vch']) ?? false;
    if ($_version) {
        GF::makeJS('f','console.log("VCH is active: v '.$_version.'")');
        $_vch_file = "_vch/$_version/$_path";
        if(file_exists($_vch_file)) {
            GF::makeJS('f','console.log("VCH file loaded.")');

            $_vch_file = file_get_contents($_vch_file);
            eval('?> '.$_vch_file.' ');
            die();
        }
        die('VCH file is not exists');

    }




}
?>

