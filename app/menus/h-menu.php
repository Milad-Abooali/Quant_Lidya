<header class="h-menu py-3 mb-1">
    <div class="fluid row">
        <div class="col-7 text-start">
            <button class="btn btn-key doO-l-side-menu text-start ms-2"><i class="fas fa-bars h-menu-ico"></i></button>
            <button id="doS-support-test" class="do-test btn btn-sm btn-dark text-center ms-2 px-2">
                <small>
                <span class="mdi mdi-checkbox-blank-circle mr-1 font-11 text-warning"></span>
                Test</small>
            </button>
            <button id="doM-show-ruby" class="doM-show-ruby btn btn-sm btn-dark text-center ms-2 px-2">
                <small>
                <span class="mdi mdi-checkbox-blank-circle mr-1 font-11 text-success"></span>
                Support</small>
            </button>
            <button id="doS-debug" class="btn btn-key doS-debug text-center show-if-admin d-hide"><i class="fa fa-bug h-menu-ico"></i></button>
        </div>
        <div class="col-5 text-end">
            <button class="btn btn-key doO-search-menu text-end"><i class="fas fa-search h-menu-ico"></i></button>
            <button data-screen="notifications" class="show-screen btn btn-key text-center"><i class="far fa-bell h-menu-ico"></i></button>
            <button class="btn btn-key doO-r-side-menu text-center me-2"><img src="app/assets/img/avatar.png" alt="avatar" class="avatar"></button>
        </div>
    </div>

    <form id="h-jump" class="text-center d-hide">
        <input id="jump-input" type="search" list="jumping" class="form-control" placeholder="Jump to..." aria-label="Jump TO" autocomplete="off">
        <button id="doS-jump" class="btn btn-sm btn-outline-primary d-hide">GO</button>
        <button id="doO-search-menu" class="btn doO-search-menu text-center"><i class="fa fa-angle-up"></i></button>
    </form>
    <datalist id="jumping">
        <option value="home">
        <option value="trade">
        <option value="profile">
    </datalist>

    <?php include_once('l-menu.php') ?>

    <?php include_once('r-menu.php') ?>

</header>
