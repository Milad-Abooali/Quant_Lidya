<?php
    global $_L;
?>

<div id="wire_bank" class="gateway-info">
    <div class="col-sm-12 mb-3" id="bank-detail">
        <ul class="nav nav-tabs" id="bank-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="usd-tab" data-toggle="tab" href="#bank-usd" role="tab" aria-controls="bank-usd" aria-selected="true">USD</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="eur-tab" data-toggle="tab" href="#bank-eur" role="tab" aria-controls="bank-eur" aria-selected="false">EUR</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="rub-tab" data-toggle="tab" href="#bank-rub" role="tab" aria-controls="bank-rub" aria-selected="false">RUB</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="cny-tab" data-toggle="tab" href="#bank-cny" role="tab" aria-controls="bank-cny" aria-selected="false">CNY</a>
            </li>
        </ul>
        <div class="tab-content" id="bank-tabs-content">
            <div class="tab-pane fade active" id="bank-usd" role="tabpanel" aria-labelledby="bank-tab">
                <div data-currency="$" class="bank-detail">
                    <h6 class="text-warning"><?= $_L->T('Account_In','wallet') ?> USD</h6>
                    <pre>
    JSC "BSB Bank"
    <?= $_L->T('Reg_No','bank') ?>: 807000069
    IBAN: BY38UNBS30122350700000000840
    SWIFT: UNBSBY2X
    <?= $_L->T('Bank_Address','bank') ?>: 220004, Minsk, Pobediteley Ave., 23, bldg. 4, Republic of Belarus

    <?= $_L->T('Correspondent_Bank','bank') ?>
    RAIFFEISEN BANK INTERNATIONAL AG,
    Vienna, Austria
    SWIFT: RZBAATWW
    <?= $_L->T('Account_Number','bank') ?>: 70-55.082.960
                            </pre>
                </div>
            </div>
            <div class="tab-pane fade" id="bank-eur" role="tabpanel" aria-labelledby="bank-tab">
                <div data-currency="€" class="bank-detail">
                    <h6 class="text-warning"><?= $_L->T('Account_In','wallet') ?> EUR</h6>
                    <pre>
    JSC "BSB Bank"
    <?= $_L->T('Reg_No','bank') ?>: 807000069
    IBAN: BY95UNBS30122350700000000978
    SWIFT: UNBSBY2X
    <?= $_L->T('Bank_Address','bank') ?>: 220004, Minsk, Pobediteley Ave., 23, bldg. 4, Republic of Belarus

    <?= $_L->T('Correspondent_Bank','bank') ?>
    RAIFFEISEN BANK INTERNATIONAL AG,
    Vienna, Austria
    SWIFT: RZBAATWW
    <?= $_L->T('Account_Number','bank') ?>: 1-55.082.960
                            </pre>
                </div>
            </div>
            <div class="tab-pane fade" id="bank-rub" role="tabpanel" aria-labelledby="bank-tab">
                <div data-currency="₽" class="bank-detail">
                    <h6 class="text-warning"><?= $_L->T('Account_In','wallet') ?> RUB</h6>
                    <pre>
    JSC "BSB Bank"
    <?= $_L->T('Reg_No','bank') ?>: 807000069
    IBAN: BY22UNBS30122350700000000643
    SWIFT: UNBSBY2X
    <?= $_L->T('Bank_Address','bank') ?>: 220004, Minsk, Pobediteley Ave., 23, bldg. 4, Republic of Belarus

    <?= $_L->T('Correspondent_Bank','bank') ?>
    PUBLIC JOINT STOCK COMPANY SBERBANK OF RUSSIA
    Moscow, RF
    BIK 044525225, TIN 7707083893
    SWIFT: SABRRUMM
    <?= $_L->T('Account_Number','bank') ?>: 30111810300000000764
                            </pre>
                </div>
            </div>
            <div class="tab-pane fade" id="bank-cny" role="tabpanel" aria-labelledby="bank-tab">
                <div data-currency="¥" class="bank-detail">
                    <h6 class="text-warning"><?= $_L->T('Account_In','wallet') ?> CNY</h6>
                    <pre>
    JSC "BSB Bank"
    <?= $_L->T('Reg_No','bank') ?>: 807000069
    IBAN: BY76UNBS30122350700000000156
    SWIFT: UNBSBY2X
    <?= $_L->T('Bank_Address','bank') ?>: 220004, Minsk, Pobediteley Ave., 23, bldg. 4, Republic of Belarus
                            </pre>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 mb-3" id="receipt">
        <label for="doc"><span class="text-danger">*</span> <?= $_L->T('Document','doc') ?> /<small><?= $_L->T('up_to_5_file','doc') ?></small></label>
        <div class="custom-file my-1">
            <input type="file" class="custom-file-input" id="doc" name="doc[]" required="">
            <label class="custom-file-label" for="doc"><?= $_L->T('Choose_Your_Receipt','doc') ?></label>
        </div>
        <span style="display: none" data-max="4" class="da-addDoc small btn btn-sm btn-light">
            <i class="fa fa-plus text-success"></i> <?= $_L->T('Add_more','doc') ?>
        </span>
    </div>
</div>
<script>
    $('#wire_bank .tab-content .active').addClass('show');
</script>