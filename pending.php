<?php
######################################################################
#  M | 11:20 AM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

    require_once "config.php";

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
    <?php include('includes/css.php'); ?>
<body>
    <div class="wrapper-page">

        <div class="card">
            <div class="card-body">

                <h3 class="text-center m-0">
                    <a href="index.php" class="logo logo-admin"><img src="assets/images/logo<?php echo $broker_logo;?>.png" height="50" alt="logo"></a>
                </h3>

                <div class="p-3">
                    <h2>Pending Confirmation</h2>
                    <p>
		                We sent an email to  <b><?php echo $_GET['email'] ?></b> to help you reset your account password.
		                </br></br>
		                Please follow the instruction given in the email.
	                </p>
	            </div>
            </div>
        </div>
    </div>
        
    <?php include('includes/script.php'); ?>
    <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
    <?php include('includes/script-bottom.php'); ?>
</body>
</html>