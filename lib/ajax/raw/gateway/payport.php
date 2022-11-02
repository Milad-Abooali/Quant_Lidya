<?php
    global $_L;
?>
<div id="payport" class="gateway-info">
    <p class="col-sm-12 mb-3">
        <span class="text-danger">*</span> <?= $_L->T('Pay_Next_Step','gateway') ?>
        <input type="hidden" name="payment" value="payport">
    </p>
</div>
