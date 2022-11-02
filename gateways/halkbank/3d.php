<?php

    require_once  '../../config.php';
    require_once  'config.php';

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    if (! ($_POST['order_id'] ?? false)) die();
    $amount = $_POST['amount'];
    $oid = $_POST['order_id'];
    $clientId = $api_client;
    $okUrl = $failUrl = 'https://'.Broker['crm_url'].'/gateways/halkbank/callback.php?uid='.$_SESSION['id'].'&TOKEN='.TOKEN;

    $rnd = rand(1,999999);
    $storekey = $api_pass;
    $storetype = "3d";
    $hashstr = $clientId . $oid . $amount . $okUrl . $failUrl . $rnd  . $storekey;
    $hash = base64_encode(pack('H*',sha1($hashstr)));
?>
<!DOCTYPE html><html><body onload="document.forms[0].submit()"><form action="<?= $api_url_3d ?>" method="post">
    <input type="hidden" name="pan" size="20" value="<?= $_POST['card'] ?>"/>
    <input type="hidden" name="Ecom_Payment_Card_ExpDate_Year" value="<?= $_POST['exp_yy'] ?>"/>
    <input type="hidden" name="Ecom_Payment_Card_ExpDate_Month" value="<?= $_POST['exp_mm'] ?>"/>
    <input type="hidden" name="cardType" value="<?= $_POST['cardType'] ?>"/>
    <input type="hidden" name="clientid" value="<?= $clientId ?>">
    <input type="hidden" name="amount" value="<?= $amount ?>">
    <input type="hidden" name="oid" value="<?= $oid ?>">
    <input type="hidden" name="okUrl" value="<?= $okUrl ?>">
    <input type="hidden" name="failUrl" value="<?= $failUrl ?>">
    <input type="hidden" name="rnd" value="<?= $rnd ?>" >
    <input type="hidden" name="hash" value="<?= $hash ?>" >
    <input type="hidden" name="storetype" value="<?= $storetype ?>" >
    <input type="hidden" name="lang" value="en">
</form></body></html>