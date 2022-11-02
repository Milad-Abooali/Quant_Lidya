            <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                <div class="slimscroll-menu" id="remove-scroll">
                    <?php if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent") { ?>
                    <!--<ul class="list-inline menu-left mb-0">
                        <li class="dropdown notification-list">
                            <form id="searchForm" role="search" action="search.php" onsubmit="return validateSearchForm()" method="post" class="app-search form-inline">
                                <div class="form-group mb-0">
                                    <input type="number" class="form-control" placeholder="Search Any ..." name="search" id="search" autocomplete="off" style="width: 239px;padding-left: 105px;">
                                    <span class="btn btn-sm btn-light position-absolute disabled" style="margin: 0 0 0 5px;">
                                        <label for="filter" class="d-inline"><i class="fa fa-cogs"></i></label>
                                        <select class="alert-light ml-2" id="filter" name="filter">
                                            <option value="all" selected>All</option>
                                            <option value="tp">TP</option>
                                            <option value="phone">Phone</option>
                                            <option value="email">Email</option>
                                            <option value="name">Name</option>
                                        </select>
                                     </span>
                                    <button type="submit" ><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                        </li>
                    </ul>-->
                    <?php } ?>
                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu" id="side-menu">
                            <li class="menu-title">Main</li>
                            <?php if($_SESSION["type"] == "Admin"){ ?>
                            <li>
                                <a href="welcome2.php" class="waves-effect">
                                    <i class="mdi mdi-view-dashboard"></i><!--<span class="badge badge-primary badge-pill float-right">2</span>--> <span> <?= $_L->T('Dashboard','sidebar') ?> </span>
                                </a>
                            </li>
                            <li>
                                <a href="reports_agents.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Advanced_Reports','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="agentreport.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Agent_Report','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="totalreport.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Desk_Report','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="equity.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Equity_Report','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="marketing-report.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Marketing_Report','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="staffs.php" class="waves-effect"><i class="mdi mdi-account-box"></i><span> <?= $_L->T('Staffs','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="ib.php" class="waves-effect"><i class="mdi mdi-account-multiple"></i><span> <?= $_L->T('IBs','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="trader.php" class="waves-effect"><i class="mdi mdi-account-settings"></i><span> <?= $_L->T('Traders','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="leads.php" class="waves-effect"><i class="mdi mdi-account-card-details"></i><span> <?= $_L->T('Leads','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="scalp.php" class="waves-effect"><i class="mdi mdi-clock-fast"></i><span> <?= $_L->T('Scalpers','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="wl.php" class="waves-effect"><i class="mdi mdi-numeric"></i><span> <?= $_L->T('Winners_Losers','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="exposure.php" class="waves-effect"><i class="mdi mdi-bullhorn"></i><span> <?= $_L->T('Exposure','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="monitor.php" class="waves-effect"><i class="mdi mdi-pulse"></i><span> <?= $_L->T('Monitor','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="notes.php" class="waves-effect"><i class="mdi mdi-clipboard-text"></i><span> <?= $_L->T('Notes','note') ?> </span></a>
                            </li>
                            <li>
                                <a href="requests.php" class="waves-effect"><i class="mdi mdi-format-list-bulleted-type"></i><span> <?= $_L->T('Requests','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="devices-inventory.php" class="waves-effect"><i class="mdi mdi-cellphone-link"></i><span> <?= $_L->T('Devices_Inventory','sidebar') ?> </span></a>
                            </li>
                            <?php }
                            else if($_SESSION["type"] == "Lawyer"){ ?>
                            <li>
                                <a href="index.php" class="waves-effect">
                                    <i class="mdi mdi-view-dashboard"></i><!--<span class="badge badge-primary badge-pill float-right">2</span>--> <span> <?= $_L->T('Dashboard','sidebar') ?> </span>
                                </a>
                            </li>
                            <li>
                                <a href="totalreport.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Desk_Report','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="equity.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Equity_Report','sidebar') ?> </span></a>
                            </li>
                            <?php }
                            else if($_SESSION["type"] == "Manager"){ ?>
                            <li>
                                <a href="index.php" class="waves-effect">
                                    <i class="mdi mdi-view-dashboard"></i><!--<span class="badge badge-primary badge-pill float-right">2</span>--> <span> <?= $_L->T('Dashboard','sidebar') ?> </span>
                                </a>
                            </li>
                            <li>
                                <a href="agentreport.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Agent_Report','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="totalreport.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Desk_Report','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="marketing-report.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> <?= $_L->T('Marketing_Report','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="staffs.php" class="waves-effect"><i class="mdi mdi-account-box"></i><span> <?= $_L->T('Staffs','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="ib.php" class="waves-effect"><i class="mdi mdi-account-multiple"></i><span> <?= $_L->T('IBs','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="trader.php" class="waves-effect"><i class="mdi mdi-account-settings"></i><span> <?= $_L->T('Traders','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="leads.php" class="waves-effect"><i class="mdi mdi-account-card-details"></i><span> <?= $_L->T('Leads','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="scalp.php" class="waves-effect"><i class="mdi mdi-clock-fast"></i><span> <?= $_L->T('Scalpers','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="wl.php" class="waves-effect"><i class="mdi mdi-numeric"></i><span> <?= $_L->T('Winners_Losers','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="notes.php" class="waves-effect"><i class="mdi mdi-clipboard-text"></i><span> <?= $_L->T('Notes','note') ?> </span></a>
                            </li>
                            <li>
                                <a href="requests.php" class="waves-effect"><i class="mdi mdi-clipboard-text"></i><span> <?= $_L->T('Requests','sidebar') ?> </span></a>
                            </li>
                            <?php }
                            else if($_SESSION["type"] == "Retention Manager"){ ?>
                            <li>
                                <a href="index.php" class="waves-effect">
                                    <i class="mdi mdi-view-dashboard"></i><span> <?= $_L->T('Dashboard','sidebar') ?> </span>
                                </a>
                            </li>
                            <li>
                                <a href="download.php" class="waves-effect"><i class="mdi mdi-download"></i><span> <?= $_L->T('Download','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="stats.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> Stats </span></a>
                            </li>
                            <li>
                                <a href="trader.php" class="waves-effect"><i class="mdi mdi-account-settings"></i><span> <?= $_L->T('Traders','sidebar') ?> </span></a>
                            </li>
                            <?php }
                            else if($_SESSION["type"] == "Retention Agent"){ ?>
                            <li>
                                <a href="index.php" class="waves-effect">
                                    <i class="mdi mdi-view-dashboard"></i><span> <?= $_L->T('Dashboard','sidebar') ?> </span>
                                </a>
                            </li>
                            <li>
                                <a href="download.php" class="waves-effect"><i class="mdi mdi-download"></i><span> <?= $_L->T('Download','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="stats.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> Stats </span></a>
                            </li>
                            <li>
                                <a href="trader.php" class="waves-effect"><i class="mdi mdi-account-settings"></i><span> <?= $_L->T('Traders','sidebar') ?> </span></a>
                            </li>
                            <?php }
                            else if($_SESSION["type"] == "Sales Agent"){ ?>
                            <li>
                                <a href="index.php" class="waves-effect">
                                    <i class="mdi mdi-view-dashboard"></i><span> <?= $_L->T('Dashboard','sidebar') ?> </span>
                                </a>
                            </li>
                            <li>
                                <a href="download.php" class="waves-effect"><i class="mdi mdi-download"></i><span> <?= $_L->T('Download','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="stats.php" class="waves-effect"><i class="mdi mdi-chart-bar"></i><span> Stats </span></a>
                            </li>
                            <li>
                                <a href="leads.php" class="waves-effect"><i class="mdi mdi-account-card-details"></i><span> <?= $_L->T('Leads','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="notes.php" class="waves-effect"><i class="mdi mdi-clipboard-text"></i><span> <?= $_L->T('Notes','note') ?> </span></a>
                            </li>
                            <?php }
                            else if($_SESSION["type"] == "IB"){ ?>
                            <li>
                                <a href="index.php" class="waves-effect">
                                    <i class="mdi mdi-view-dashboard"></i><span> <?= $_L->T('Dashboard','sidebar') ?> </span>
                                </a>
                            </li>
                            <li>
                                <a href="profile.php" class="waves-effect"><i class="mdi mdi-account"></i><span> <?= $_L->T('Porofile','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="ib.php" class="waves-effect"><i class="mdi mdi-account-multiple"></i><span> <?= $_L->T('IBs','sidebar') ?> </span></a>
                            </li>
                            <li>
                                <a href="download.php" class="waves-effect"><i class="mdi mdi-download"></i><span> <?= $_L->T('Download','sidebar') ?> </span></a>
                            </li>
                            <?php }
                            else { ?>
                            <li>
                                <a href="index.php" class="waves-effect">
                                    <i class="mdi mdi-view-dashboard"></i><span> <?= $_L->T('Dashboard','sidebar') ?> </span>
                                </a>
                            </li>
                            <li>
                                <a href="profile.php" class="waves-effect"><i class="mdi mdi-account"></i><span> <?= $_L->T('Profile','profile') ?> </span></a>
                            </li>
                            <li>
                                <a href="download.php" class="waves-effect"><i class="mdi mdi-download"></i><span> <?= $_L->T('Download','sidebar') ?> </span></a>
                            </li>
                            <?php } ?>
                            <li>
                                <a href="wallet.php" class="waves-effect">
                                    <i class="mdi mdi-coin"></i><span> <?= $_L->T('My_Wallet','wallet') ?> </span>
                                    <sup class="text-warning">NEW</sup>
                                </a>
                            </li>
                            <li>
                                <a href="web-terminal.php" class="waves-effect">
                                    <i class="mdi mdi-chart-line"></i><span> <?= $_L->T('Web_Trader','sidebar') ?> </span>
                                    <sup class="text-warning">NEW</sup>
                                </a>
                            </li>
                        </ul>

                    </div>
                    <!-- Sidebar -->
                    <div class="clearfix"></div>
                    <hr>
                    <div class="contact">
                        <?php include_once "contact/contact_".$_L->T('_language_iso2', 'core').".php"; ?>
                    </div>

                </div>
                <!-- Sidebar -left -->

            </div>
            <!-- Left Sidebar End -->
