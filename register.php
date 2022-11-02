<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

require_once "config.php";


// LangMan
global $_L;


    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    $affiliate = ($_GET["af"]) ?? (($_COOKIE['affiliate']) ?? null);
    setcookie('affiliate', $affiliate, time() + (86400 * 180), "/");

    $unit = ($_GET["lang"]) ?? Broker['def_unit'];
    $source = $_SERVER['HTTP_REFERER'];
    $campaign = htmlspecialchars($_GET["camp"]);
    //echo $source;

?>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title><?php echo Broker['title'];?> - <?= $_L->T('Feel_The_Difference','head') ?></title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <?php include('includes/css.php'); ?>
<!DOCTYPE html>
<html lang="en">
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
                        <p id="sta-t" class="text-muted text-center"><?= $_L->T('login_note','login') ?> <?= Broker['title'];?>.</p>
                    </div>
                <!-- Page Content -->
                <div class="container">
            		<div class="row">
                        <form id="register-form" class="was-validated w-100" novalidate>

                        <div class="col-lg-12">
                                <div>
                                    <div id="notifications">
                                        <div class="alert alert-danger alert-dismissible" id="delete" style="display:none;">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade show active" id="pills-1" role="tabpanel" aria-labelledby="pills-1-tab">
                                        <?php if (Broker['maintenance']): ?>
                                            <div class="alert alert-warning"><?= $_L->T('maintenance','login') ?></div>
                                        <?php else: ?>                                        
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <label for="fname"><?= $_L->T('First_Name','general') ?>:</label>
                                                    <input type="text" class="form-control form-control-sm" name="fname" id="fname" placeholder="<?= $_L->T('First_Name','general') ?>" required>
                                                    <div class="invalid-feedback">
                                                        <?= $_L->T('First_Name_note','login') ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="lname"><?= $_L->T('Last_Name','general') ?>:</label>
                                                    <input type="text" class="form-control form-control-sm" name="lname" id="lname" placeholder="<?= $_L->T('Last_Name','general') ?>" required>
                                                    <div class="invalid-feedback">
                                                        <?= $_L->T('Last_Name_note','login') ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="email"><?= $_L->T('Email','general') ?>:</label>
                                                    <input type="email" class="form-control form-control-sm" name="email" id="email" placeholder="<?= $_L->T('email_sample','login') ?>" required>
                                                    <div class="invalid-feedback">
                                                        <?= $_L->T('Email_note','login') ?>

                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="phone"><?= $_L->T('Phone','general') ?>:</label>
                                                    <input type="number" class="form-control form-control-sm" name="phone" id="phone" minlength="10" placeholder="<?= $_L->T('phone_sample','login') ?>"  required>
                                                    <div class="invalid-feedback">
                                                        <?= $_L->T('Phone_note','login') ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">    
                                                    <label for="country"><?= $_L->T('Country','general') ?>:</label>
                                                    <select class="custom-select custom-select-sm dbip-auto-select-country" style="font-size:100% !important;" name="country" id="country" placeholder="<?= $_L->T('Country_p_holder','login') ?>" required>
                                                    <?php
                                                        $sqlCU = 'SELECT country_name FROM countries';
                                                        $countries = $DB_admin->query($sqlCU);
                                                        while ($rowCU = mysqli_fetch_array($countries)) {
                                                            echo "<option value='".$rowCU['country_name']."'>".$rowCU['country_name']."</option>";
                                                        }
                                                    ?>
                                                    </select>
                                                </div>

                                                <?php if(Broker['captcha']) {
                                                    unset($_SESSION['captcha']);
                                                    include_once 'lib/captcha/captcha.php';
                                                    $_SESSION['captcha'] = simple_php_captcha();
                                                    if(DevMod) GF::cLog('Captcha: '.$_SESSION['captcha']['code']);
                                                ?>
                                                <div class="form-group pt-3 px-2 row">
                                                    <div class="col-md-6 pt-3">
                                                        <label for="phone"><?= $_L->T('Captcha','login') ?>:</label>
                                                        <span class="btn btn-default" id="doA-reCaptcha"><i class="fa fa-sync"></i></span>
                                                        <input type="text" class="form-control" name="captcha" id="captcha" required>
                                                    </div>
                                                    <div class="col-md-6 pt-3">
                                                        <img class="captcha-img" src="<?= $_SESSION['captcha']['image_src'] ?>" alt="CAPTCHA code">
                                                    </div>
                                                </div>
                                                <?php } ?>

                                            </div>
                                            <input type="hidden" id="timeoffset" name="timeoffset" class="btn btn-primary" value="0">
                                        <?php endif; ?>
                                    </div>

                                </div>
                                <hr>
                            <div id="act-div" class="text-center">
                                <button type="submit" class="btn btn-primary float-left" id="addUser" disabled><?= $_L->T('Register','login') ?></button>
                                <a type="button" class="btn btn-secondary float-right" href="login.php"><?= $_L->T('Back_to_Login','login') ?></a>
                            </div>

            		</div>
                        </form>
                        <div id="rsp" class=""> </div>
                    </div>
            </div>
        </div>
    </div>
<?php
    $DB_admin->close();
?>
</body>
</html>
<?php include('includes/script.php'); ?>

<script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>

<?php include('includes/script-bottom.php'); ?>
<script>

$(document).ready( function () {

	
	$(":input").change(function () {
        $("#addUser").prop("disabled", false);
    });
    
    // $('#addUser').on('click', function() {
    $('#register-form').on('submit', function(e) {
        e.preventDefault();

        $("#addUser").attr("disabled", "disabled");
		var fname = $('#fname').val();
		var lname = $('#lname').val();
		var email = $('#email').val();
		var phone = $('#phone').val();
		var country = $('#country').val();
		var captcha = $('#captcha').val();

		var source = "<?php echo $source; ?>";
		var campaign = "<?php echo $campaign; ?>";
		var affiliate = "<?php echo $affiliate; ?>";
		var unit_id = "<?= $unit; ?>";

        function validateEmail(email)
        {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        }
		
		if(fname.length>2 && lname.length>1 && validateEmail(email) && phone.length>10){
            $('#rsp').removeClass();
            // AJAX CALL
            $('#captcha').val('');
            $('.captcha-img').fadeOut();
            let date = {
                fname: fname,
                lname: lname,
                email: email,
                phone: phone,
                country: country,
                source: source,
                captcha: captcha,
                campaign: campaign,
                affiliate: affiliate,
                unit_id: unit_id
            }
            ajaxCall ('users', 'register', date, function(response){
                let resObj = JSON.parse(response);
                if(resObj.e) {
                    $('#rsp').addClass('w-100 alert-danger alert').html(resObj.e);
                } else {
                    ajaxCall ('notify', 'register', {'unit_id':unit_id});
                    $("#pills-1").hide('fast');
                    $("#rsp").html('');
                    $("#act-div").html('<a href="login.php" class="col-md-6 btn btn-outline-success"><?= $_L->T('Login_Account','login') ?> <i class="fa fa-arrow-circle-right"></i> </a>');
                    $("#butsave").removeAttr("disabled");
                    $('#fupForm').find('input:text').val('');
                    $('#fupForm').find('input:text').html('Registration is done, ');
                    $('#sta-t').html('Registration is done ...');
                    $('#notifications').html('<div class="alert alert-success alert-dismissible" id="success"><?= $_L->T('Password_sent','login') ?></div>');
                }
            });


            $('#phone').removeClass('is-invalid');

        }
		else{
            if(phone.length<11) $('#phone').addClass('is-invalid');



			$('#rsp').addClass('w-100 alert-danger alert').html('<?= $_L->T('Please_fill_all','login') ?>');
		}
	});

    let dt = new Date();
    let ddd = dt.getTimezoneOffset();
    $("#timeoffset").val(ddd*(-1));

});
</script>
