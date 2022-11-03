<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";


// LangMan
global $_L;

if(($_SESSION["id"]) ?? false) {
    if($_SESSION["tye"] = "Admin"){
        header("location: welcome2.php");
        exit;
    } else {
        header("location: welcome.php");
        exit;
    }
}

// Processing form data when form is submitted
global $sess;
if($_SERVER["REQUEST_METHOD"] == "POST") {

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    $sess->login($_POST['timeoffset'], $_POST['username'], $_POST['password'], $_POST['remember'], true, $_POST['target']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title><?php echo Broker['title'];?> - <?= $_L->T('Feel_The_Difference','head') ?></title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <?php include('includes/css.php'); ?>
<body>
        <!-- Begin page -->
        <div class="wrapper-page">

            <div class="container text-right">
                <a class="nav-link dropdown-toggle arrow-none waves-effect text-capitalize" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="flag-icon flag-icon-<?= $_language ?> "></span> <?= $_language ?> </span> <span class="mdi mdi-chevron-down "> </span>
                </a>
                <div class="dropdown-menu dropdown-menu-left dropdown-menu-sm">
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
            </div>
            <div class="card">
                <div class="card-body">

                    <h3 class="text-center m-0">
                        <a href="index.php" class="logo logo-admin"><img src="media/broker/<?= Broker['logo'];?>" height="50" alt="logo"></a>
                    </h3>

                    <div class="p-3">
                        <h4 class="text-muted font-18 m-b-5 text-center"><?= $_L->T('Welcome_Back','login') ?></h4>
                        <p class="text-muted text-center"><?= $_L->T('login_note','login') ?> <?= Broker['title'];?>.</p>
                        
                        <?= ($sess->ERROR) ? '<div class="alert alert-warning">'.$sess->ERROR.'</div>' : NULL ?>
                        <?php if (Broker['maintenance']): ?>
                            <div class="alert alert-warning"><?= $_L->T('maintenance','login') ?></div>
                        <?php else: ?>  
                        <form id="login" class="form-horizontal m-t-30" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"  method="post">
                            <?php  if($_waf->isBlacklistIP()) { ?>
                                <h6 class="text-danger"><?= $_L->T('IP_Blocked','login') ?></h6>
                            <?php } else { ?>
                            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                <label for="username"><?= $_L->T('Username','general') ?></label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="<?= $_L->T('Enter_username','login') ?>">
                            </div>

                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                <label for="userpassword"><?= $_L->T('Password','login') ?></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="<?= $_L->T('Enter_password','login') ?>">
                            </div>

                            <div class="form-group row m-t-20">
                                <div class="col-12">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                        <label class="custom-control-label" for="remember"><?= $_L->T('Remember_me','login') ?></label>
                                    </div>
                                </div>
                                <div class="col-12 text-right">
                                    <input type="hidden" id="timeoffset" name="timeoffset" class="btn btn-primary" value="0">
                                    <input type="hidden" class="form-control" id="target" name="target" value="">

                                    <button type="button" id="web-trader" class="btn btn-outline-primary"><i class="mdi mdi-chart-line"></i> <?= $_L->T('Web_Trader','sidebar') ?> <?= $_L->T('Login','login') ?></button>
                                    <input type="submit" class="btn btn-primary" value="<?= $_L->T('Login','login') ?>">
                                </div>
                            </div>
                            <?php }  ?>

                            <div class="form-group m-t-10 mb-0 row">
                                <div class="col-12 m-t-20">
                                    <a href="forget-password.php" class="text-muted"><i class="mdi mdi-lock"></i> <?= $_L->T('Forgot_password','login') ?> </a>
                                    <small class="float-right text-muted"><?= $_L->T('IP','general') ?>:
                                        <?php if ($_waf->isBlacklistIP()) { ?>
                                            <span class="text-danger"><?= GF::getIP() ?></span>
                                        <?php } else if($_waf->isWhitelistIP()) { ?>
                                            <span class="text-success"><?= GF::getIP() ?></span>
                                        <?php } else { ?>
                                            <span class="text-warning"><?= GF::getIP() ?></span>
                                        <?php } ?>
                                    </small>
                                </div>
                            </div>
                        </form>
                        <?php endif; ?>
                        <hr></hr>
                            <div class="form-group m-t-10 mb-0 row">
                                <div class="col-12 m-t-20">
                                    <i class="mdi mdi-account-plus"></i> <?= $_L->T('register_note','login') ?> <a href="register.php" class="text-muted"><?= $_L->T('Register','login') ?></a>.
                                </div>
                            </div>
                    </div>

                </div>
            </div>

        </div>
        

<?php include('includes/script.php'); ?>

        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>

<?php include('includes/script-bottom.php'); ?>

<script>
    let dt = new Date();
    let ddd = dt.getTimezoneOffset();
    $("#timeoffset").val(ddd*(-1));

    $('body').on('click','#web-trader', function(){
        $('input#target').val('<?= REDIRECT_TO['web_trader'] ?>');
        $('form#login').trigger('submit');
    });

</script>

</body>
</html>