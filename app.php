<?php
/**
 * Router
 * App - Main wrapper
 * By Milad [m.abooali@hotmail.com]
 */

require_once('config.php');
require_once('app/config-over.php');
global $db;
?>
<!doctype html><html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="generator" content="Quant CRM">
    <title><?= Broker['title'] ?></title>
    <!-- CSS -->
    <link href="app/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" >
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="app/assets/css/animate.min.css" rel="stylesheet" type="text/css">
    <link href="app/assets/css/breaking-news-ticker.css" rel="stylesheet" type="text/css">
    <link href="app/assets/css/progress.css" rel="stylesheet" type="text/css">
    <link href="app/assets/css/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="app/assets/css/main.css" rel="stylesheet" type="text/css">


    <!-- Favicons -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <meta name="theme-color" content="#212529">

    <!-- Script -->
    <script>
        /**
         * App Settings
         */
        let APP = {};
        APP.socket = 'wss://<?= Broker['crm_url'] ?>:3500/';
        APP.screen = 'home';
        APP.client = {
            id       : <?= $_SESSION['id'] ?? 0 ?>,
            sess     : '<?= session_id() ?>',
            token    : '<?= TOKEN ?>'
        };
        <?php if($_SESSION['app']['avatar']) { ?>
        APP.client.avatar = '<?= $_SESSION['app']['avatar'] ?>';
        <?php } ?>
    </script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="app/assets/js/popper.min.js"></script>
    <script src="app/assets/js/socket.io.js"></script>
    <script src="app/assets/js/breaking-news-ticker.min.js"></script>
    <script src="app/assets/js/circle-progress.min.js"></script>
    <script src="app/assets/js/bootstrap.bundle.min.js" defer></script>
    <script src="app/assets/js/dayjs.min.js" defer></script>


    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-core.min.js" type="text/javascript"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-stock.min.js" type="text/javascript"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-ui.min.js" type="text/javascript"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-exports.min.js?hcode=a0c21fc77e1449cc86299c5faa067dc4" type="text/javascript"></script>


</head>
<body>
<main>
    <?php include_once('app/menus/h-menu.php') ?>

    <?= blocks::screen('login') ?>

    <div id="screen-wrapper" class="container-fluid p-3">
        <div class="row">
            <?php if($_SESSION['id']) echo blocks::screen('home') ?>
            <?= blocks::screen('debug') ?>
        </div>
    </div>
    <view class="hide-if-guest">
        <?php include_once('app/menus/b-menu.php') ?>
    </view>
</main>

<!-- - Toast -->
<div class="toast-container position-fixed end-0 p-3">

    <div id="alertToast" class="toast fade">
        <div class="toast-header">
            <strong class="me-auto"> </strong>
            <small> </small>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body"> </div>
    </div>

</div>

<!-- - Modals -->
<div class="modal fade" id="app-main-modal" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-light">
                <h5 class="modal-title"> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-dark text-light"> </div>
            <div class="modal-footer bg-secondary text-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="app-frame-modal"  >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-light">
                <h5 class="modal-title"> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-dark text-light p-0"> </div>
            <div class="modal-footer bg-secondary text-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="over-screen" class="d-hide">
    <div>
        <button class="btn btn-danger" type="button" disabled>
            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
            Reconnecting ...
        </button>
        <div id="error-text" class="text-secondary"></div>
    </div>
</div>

<!-- JavaScript -->
<script src="app/assets/js/functions.js" defer></script>
<script src="app/assets/js/app-socket.js" defer></script>
<script src="app/assets/js/main.js" defer></script>
<script src="app/assets/js/ruby.js" defer></script>
<script src="app/assets/js/datatables.min.js" defer></script>

</body></html>
