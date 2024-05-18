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

$token = ($_GET['token']) ?? false;
global $sess;
if ($token || $sess->forceChangePasswod($_SESSION['id'])) {

    if ($token) {
        $where = "token='$token' AND unit IN (" . Broker['units'] . ")";
        $user = $db->selectRow('users', $where);
    }

    if ($sess->forceChangePasswod($_SESSION['id'])) {
        $user = $db->selectId('users', $_SESSION['id']);
    }


    if($user) {

        // Define variables and initialize with empty values
        $new_password = $confirm_password = $new_password_err = $confirm_password_err = "";

        // Processing form data when form is submitted
        if($_SERVER["REQUEST_METHOD"] == "POST") {

            // Validate new password
            if(empty(trim($_POST["new_password"]))){
                $new_password_err = "Please enter the new password.";
            } elseif(strlen(trim($_POST["new_password"])) < 6){
                $new_password_err = "Password must have atleast 6 characters.";
            } else{
                $new_password = trim($_POST["new_password"]);
            }

            // Validate confirm password
            if(empty(trim($_POST["confirm_password"]))){
                $confirm_password_err = "Please confirm the password.";
            } else{
                $confirm_password = trim($_POST["confirm_password"]);
                if(empty($new_password_err) && ($new_password != $confirm_password)){
                    $confirm_password_err = "Password did not match.";
                }
            }

            // Check input errors before updating the database
            if(empty($new_password_err) && empty($confirm_password_err)){

                $update['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                $update['pa'] = GF::encodeAm($new_password);

                if ($db->updateId('users', $user['id'], $update)) {

                        $db->updateId('users', $user['id'], array('token'=>null));
                    $db->updateId('users', $user['id'], array('fchange_pass' => 0));

                        // Send Email
                        global $_Email_M;
                        $receivers[] = array (
                            'id'    =>  $user['id'],
                            'email' =>  $user['email'],
                            'data'  =>  array(
                                'email'         =>  $user['email'],
                                'new_password'  =>  $new_password
                            )
                        );
                        $subject = $theme = 'CRM_Password_Changed';
                        $_Email_M->send($receivers, $theme, $subject);

                        // Add actLog
                        global $actLog; $actLog->add('Change Pass', $user['id'],1,'{"new_password":"'.$new_password.'","email":"'.$user['email'].'"}');

                        header("location: login.php");
                        exit();
                    } else {
                        $error = 'Oops! Something went wrong. Please try again later.';
                    }


                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        $error = 'Oops! We cant find your account.';
    }
} else {
    $error = 'This link is expired!';
}

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
                        <a href="index.php" class="logo logo-admin"><img src="media/broker/<?= Broker['logo'] ?>"
                                                                         height="50" alt="logo"></a>
                    </h3>
                    <?php if($error) { ?>
                    <div class="p-3">
                        <div class="w-100 alert alert-danger"><?= $error ?></div>
                    </div>
                    <?php } else { ?>
                    <div class="p-3">
                        <h2>Reset Password</h2>
                        <p>Please fill out this form to reset your password.</p>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?token=".$token; ?>" method="post"> 
                            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                                <span class="help-block"><?php echo $new_password_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control">
                                <span class="help-block"><?php echo $confirm_password_err; ?></span>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Submit">
                                <a class="btn btn-link" href="welcome.php">Cancel</a>
                            </div>
                        </form>
                    </div>
                    <?php }?>
                </div>
            </div>

        </div>
        

<?php include('includes/script.php'); ?>

        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>

<?php include('includes/script-bottom.php'); ?>

</body>
</html>