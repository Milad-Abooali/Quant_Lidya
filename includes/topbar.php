<?php

// LangMan
global $_L;

?>
<!-- MODAL Main -->
<div id="modalMain" class="modal mt-5"   role="dialog">
    <div class="modal-dialog mt-5" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> </h5>
                <button type="button" class="close close-right" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $_L->T('Close','general') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Window content -->
<div class="modal fade" id="myModal"  role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 1400;">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="my-modal-cont"></div>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>

            <audio id="alert_sound" src="assets/alert.mp3" preload="auto"></audio>
            <!-- Top Bar Start -->
            <div class="topbar">

                <!-- LOGO -->
                <div class="topbar-left">
                    <a href="index.php" class="logo">
                        <span>
                            <img src="media/broker/<?= Broker['dark_logo'];?>" alt="" height="40">
                        </span>
                        <i>
                            <img src="media/broker/<?= Broker['mini_logo'];?>" alt="" height="22">
                        </i>
                    </a>
                </div>

                <nav class="navbar-custom">

                    <ul class="navbar-right d-flex list-inline float-right mb-0">

                        <?php if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent") { ?>
                        <li class="dropdown notification-list d-none d-sm-block">
                            <form id="searchForm" role="search" action="search.php" onsubmit="return validateSearchForm()" method="post" class="app-search form-inline">
                                <div class="form-group mb-0">
                                    <input type="number" class="form-control" placeholder="Search Any ..." name="search" id="search" autocomplete="off" style="width: 280px;padding-left: 105px;">
                                    <span class="btn btn-sm btn-light position-absolute disabled" style="margin: 0 0 0 5px;">
                                        <label for="filter" class="d-inline"><i class="fa fa-cogs"></i></label>
                                        <select class="alert-light ml-2" id="filter" name="filter">
                                            <option value="all" selected>All</option>
                                            <option value="tp"><?= $_L->T('TP','trade') ?></option>
                                            <option value="tp"><?= $_L->T('Phone','general') ?></option>
                                            <option value="tp"><?= $_L->T('Email','general') ?></option>
                                            <option value="tp"><?= $_L->T('Name','general') ?></option>
                                        </select>
                                     </span>
                                    <button type="submit" ><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                        </li>
                        <?php } ?>
                        
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none waves-effect text-capitalize" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <span class="flag-icon flag-icon-<?= $_language ?> "></span> <?= $_language ?> </span> <span class="mdi mdi-chevron-down "> </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-sm">
                                <?php
                                    $languages = scandir('./languages');
                                    unset($languages[0]);
                                    unset($languages[1]);
                                    foreach ($languages as $lang) {
                                        $lang = str_replace('.ini','',$lang);
                                ?>
                                        <a href="?language=<?= $lang ?>" class="dropdown-item-text btn-outline-light my-1 py-1">
                                            <span class="flag-icon flag-icon-<?= $lang ?>"></span> <span class="align-middle text-capitalize"> <?= $lang ?> </span>
                                        </a>
                                <?php } ?>
                            </div>
                        </li>
                        <li class="dropdown notification-list">
                            <a class="nav-link right-bar-toggle arrow-none waves-effect" href="javascript:;">
                                <i class="ti-bell noti-icon"></i>
                                <span class="badge badge-pill badge-danger noti-icon-badge">0</span>
                            </a>
                        </li>
                        <!--<li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="ti-bell noti-icon"></i>
                                <span class="badge badge-pill badge-danger noti-icon-badge">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
                                <h6 class="dropdown-item-text">Notifications</h6>
                                <hr>

                                <h6 class="dropdown-item-text">
                                    <button type="button" class="btn btn-outline-dark btn-sm" id="tagGeneral">General</button>
                                    <button type="button" class="btn btn-outline-success btn-sm" id="tagTransactions">Transactions</button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="tagFollow-Up">Follow-Up</button>
                                </h6>
                                <div class="slimscroll notification-item-list">
                                    
                                </div>

                                <a href="notifications.php" class="dropdown-item text-center text-primary">
                                    View all <i class="fi-arrow-right"></i>
                                </a>
                            </div>        
                        </li>-->
                        <li class="dropdown notification-list">
                            <div class="dropdown notification-list nav-pro-img">
                                <a class="dropdown-toggle nav-link arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <img src="media/<?php global $avatars; echo $avatars; ?>" alt="user" class="avatar_img rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                    <!-- item-->
                                    <!--<a class="dropdown-item" href="javascript:;"> <?php echo htmlspecialchars($_SESSION["username"]); ?></a>
                                    <div class="dropdown-divider"></div>-->
                                    <a class="dropdown-item" href="profile.php"><i class="mdi mdi-account-circle m-r-5"></i> <?= $_L->T('Profile','profile') ?></a>
                                    <!--<a class="dropdown-item" href="#"><i class="mdi mdi-wallet m-r-5"></i> My Wallet</a>
                                    <a class="dropdown-item d-block" href="#"><span class="badge badge-success float-right">11</span><i class="mdi mdi-settings m-r-5"></i> Settings</a>-->
                                    <a class="dropdown-item" href="forget-password.php"><i class="mdi mdi-lock-open-outline m-r-5"></i> <?= $_L->T('Reset_Password','login') ?></a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="logout.php"><i class="mdi mdi-power text-danger"></i> <?= $_L->T('Logout','login') ?></a>
                                </div>                                                                    
                            </div>
                        </li>

                    </ul>

                    <ul class="list-inline menu-left mb-0">
                        <li class="float-left">
                            <button class="button-menu-mobile open-left waves-effect">
                                <i class="mdi mdi-menu"></i>
                            </button>
                        </li>
                        <li class="dropdown notification-list float-left" id="fullscreen">
                            <a href="javascript:;" class="nav-link arrow-none waves-effect noti-icon requestfullscreen" onclick="openFullscreen()"><i class="mdi mdi-fullscreen"></i></a>
                            <a href="javascript:;" class="nav-link arrow-none waves-effect noti-icon exitfullscreen" onclick="closeFullscreen()" style="display: none"><i class="mdi mdi-fullscreen-exit"></i></a>
                        </li>
                    <!--<li class="d-none d-sm-block">
                            <div class="dropdown pt-3 d-inline-block">
                                <a class="btn btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Create
                                </a>
                                
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Separated link</a>
                                </div>
                            </div>
                        </li>-->
                    </ul>

                </nav>

            </div>
            <!-- Top Bar End -->
            <div class="right-bar notification-list" id="right-bar">
                <div class="rightbar-title align-items-center px-3 py-4 mb-3">
                    <h5 class="m-0 me-2 float-left"><?= $_L->T('Notifications','head') ?></h5>

                    <a href="javascript:void(0);" class="right-bar-toggle ms-auto float-right">
                        <i class="mdi mdi-close noti-icon text-light"></i>
                    </a>
                </div>
                <hr>
                <!-- item-->
                <div>
                    <h6 class="dropdown-item-text float-left"><?= $_L->T('General','general') ?></h6>
                    <a href="javascript:void(0);" class="dropdown-item-text float-right pt-3">
                        <?= $_L->T('Mark_Read','general') ?>
                    </a>
                </div>
                <div class="clearfix"></div>
                <div class="slimscroll notification-item-list" id="tagGeneral" style='height:100px;'>
                    
                </div>
                <hr class="m-0">
                <div>
                    <h6 class="dropdown-item-text float-left"><?= $_L->T('Transactions','transactions') ?></h6>
                    <a href="javascript:void(0);" class="dropdown-item-text float-right pt-3">
                        <?= $_L->T('Mark_Read','general') ?>
                    </a>
                </div>
                <div class="clearfix"></div>
                <div class="slimscroll notification-item-list" id="tagTransactions">
                    
                </div>
                <hr class="m-0">
                <div>
                    <h6 class="dropdown-item-text float-left"><?= $_L->T('Follow_Up','general') ?></h6>
                    <a href="javascript:void(0);" class="dropdown-item-text float-right pt-3">
                        <?= $_L->T('Mark_Read','general') ?>
                    </a>
                </div>
                <div class="clearfix"></div>
                <div class="slimscroll notification-item-list" id="tagFollow-Up">
                    
                </div>
                <hr class="mt-0">
                <!-- View All-->
                <div class="col-md-12 mb-5 text-center">
                    <a href="notifications.php" class="btn btn-primary"><?= $_L->T('View_All_Notifications','head') ?> <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
            <!-- /Right-bar -->

            <!-- Right bar overlay-->
            <div class="rightbar-overlay"></div>
                