<?php
/**
 * Wizard
 * App - Screen Page
 * By Milad [m.abooali@hotmail.com]
 */
    global $APP;

    $screen_title   = 'AI Wizards';
    $screen_id      = 'wizard';

    if($APP->checkPermit($screen_id, 'view', 1)):
?>

<!-- Wizard Screen -->
<div id="<?= $screen_id ?>" class="screen d-hide col-xs-12">
    <div class="screen-header">
        <h4><?= $screen_title ?></h4>
    </div>
    <div class="screen-body">
        <h4 class="text-primary">What do you want to do?</h4>
        <p>Our AI Wizard designed to help you based on artificial intelligence.
            Just follow the steps to reach your goals.</p>

        <hr><h5>Account
            <button data-screen="profile" class="show-screen btn btn-sm btn-outline-secondary float-end">My Profile</button>
        </h5><hr>
        <button data-wizard-name="authentication" title="Authentication" class="doM-wizard btn btn-light m-3">Authentication <span class="badge bg-dark">3</span></button>
        <button data-wizard-name="complete-profile" title="Complete My Profile" class="doM-wizard btn btn-light m-3">Complete My Profile <span class="badge bg-dark">4</span></button>
        <button data-wizard-name="refer-friend" title="Refer a Friend" class="doM-wizard btn btn-primary m-3">Refer a Friend (Affiliate Program) <span class="badge bg-dark">2</span></button>

        <hr><h5>Wallet
            <button data-screen="wallets" class="show-screen btn btn-sm btn-outline-secondary float-end">My Wallet</button>
        </h5><hr>
        <button class="btn btn-success m-3">Creat A New Wallet</button>
        <button class="btn btn-light m-3">Deposit</button>
        <button class="btn btn-light m-3">Withdrawal</button>
        <button class="btn btn-light m-3">Transfer To Other Wallet</button>
        <button class="btn btn-light m-3">View My Transactions</button>

        <hr><h5>Trading Platform
            <button data-screen="tps" class="show-screen btn btn-sm btn-outline-secondary float-end">My TP Accounts</button>
        </h5><hr>
        <button data-wizard-name="open-tp-demo" title="Open A Trading Account (DEMO)" class="doM-wizard btn btn-success m-3">Open A Trading Account (DEMO)</button>
        <button class="btn btn-primary m-3">Trade (Buy/Sell)</button>
        <button class="btn btn-danger m-3">Manage Open Positions</button>
        <button class="btn btn-light m-3">View History</button>
        <button class="btn btn-light m-3">Download Apps</button>

        <hr><h5>Market
            <button data-screen="market" class="show-screen btn btn-sm btn-outline-secondary float-end">View Market</button>
        </h5><hr>
        <button class="btn btn-info m-3">Announcements</button>
        <button class="btn btn-light m-3">Check News</button>
        <button class="btn btn-warning m-3">Trad Signals</button>


    </div>
    <div class="screen-footer">
        footer
    </div>
</div>

    <script>

    </script>

<?php endif; ?>
