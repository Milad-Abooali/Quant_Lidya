<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################
require_once "config.php";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();
    $sess->login($_POST['timeoffset'], $_POST['username'], $_POST['password'], $_POST['remember']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title><?php echo Broker['title'];?> - Feel The Difference</title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <?php include('includes/css.php'); ?>
<body>
        <!-- Begin page -->
        <div class="wrapper-page">

            <div class="card">
                <div class="card-body">

                    <h3 class="text-center m-0">
                        <a href="index.php" class="logo logo-admin"><img src="media/broker/<?= Broker['logo'];?>" height="50" alt="logo"></a>
                    </h3>

                    <div class="p-3">
                        <h4 class="text-muted font-18 m-b-5 text-center">Welcome Back !</h4>
                        <p class="text-muted text-center">Sign in to continue to <?php echo Broker['title'];?>.</p>
                        
                        <?= ($sess->ERROR) ? '<div class="alert alert-warning">'.$sess->ERROR.'</div>' : NULL ?>
                        <?php if (!Broker['maintenance']): ?>

                            <form class="form-horizontal m-t-30">
                                <?php  if($_waf->isBlacklistIP()) { ?>
                                    <h6 class="text-danger">Your IP Is Blocked . . !</h6>
                                <?php } else { ?>
                                    <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
                                    </div>

                                    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                        <label for="userpassword">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                                    </div>

                                    <div class="form-group row m-t-20">
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                                <label class="custom-control-label" for="remember">Remember me</label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-right">
                                            <input type="hidden" id="timeoffset" name="timeoffset" class="btn btn-primary" value="0">
                                            <input type="submit" class="btn btn-primary" value="Login">
                                        </div>
                                    </div>
                                <?php }  ?>

                                <div class="form-group m-t-10 mb-0 row">
                                    <div class="col-12 m-t-20">
                                        <small class="float-right text-muted">IP:
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

                        <?php else: ?>  
                        <form class="form-horizontal m-t-30" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"  method="post">
                            <?php  if($_waf->isBlacklistIP()) { ?>
                                <h6 class="text-danger">Your IP Is Blocked . . !</h6>
                            <?php } else { ?>
                            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
                            </div>

                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                <label for="userpassword">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                            </div>

                            <div class="form-group row m-t-20">
                                <div class="col-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                        <label class="custom-control-label" for="remember">Remember me</label>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                    <input type="hidden" id="timeoffset" name="timeoffset" class="btn btn-primary" value="0">
                                    <input type="submit" class="btn btn-primary" value="Login">
                                </div>
                            </div>
                            <?php }  ?>

                            <div class="form-group m-t-10 mb-0 row">
                                <div class="col-12 m-t-20">
                                    <small class="float-right text-muted">IP:
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
                        <hr>
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
</script>

</body>
</html>