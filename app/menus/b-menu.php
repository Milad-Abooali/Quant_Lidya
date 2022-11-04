<?php

?>
<footer>
    <div class="b-menu">
        <div class="d-flex justify-content-between">
            <div class="b-menu-item" data-screen="trade">
                <a class="nav-link" href="#">
                    <i class="fas fa-donate"></i>
                    <span>Trade</span></a>
            </div>
            <div class="b-menu-item" data-screen="deposit">
                <a class="nav-link" href="#">
                    <i class="fas fa-level-down-alt"></i>
                    <span>Deposit</span></a>
            </div>
            <div class="b-menu-logo active" data-screen="home">
                <a class="nav-link" href="#">
                    <img class="broker-logo" src="media/broker/<?= Broker['mini_logo'] ?>" alt="broker-logo">
                </a>
            </div>
            <div class="b-menu-item" data-screen="withdraw">
                <a class="nav-link" href="#">
                    <i class="fas fa-level-up-alt"></i>
                    <span>Withdraw</span></a>
            </div>
            <div class="b-menu-item" data-screen="wallet">
                <a class="nav-link" href="#">
                    <i class="fas fa-wallet"></i>
                    <span>Wallet</span></a>
            </div>
        </div>
    </div>
</footer>
