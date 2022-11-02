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

    $transaction_id = $_GET["id"] ?? false;
    $db = new iSQL(DB_admin);

    $table='user_extra';
    $where = "type=5";
    $group_users = $db->select($table, $where);

    require_once "lib/autoload/transaction.php";

    $userManager = new userManager();
    $Transaction = new Transaction();
    $t = $Transaction->loadTransactionByID($transaction_id);

    $g_token = $_GET["k"] ?? false;
    $o_token = ($_GET["id"]*7) + 4;
    if (!$t || ($g_token != $o_token)) {
      $error = 'Transaction is not exist';
      $expired = true;
    } else {

      $expired =($t['finance_verify'] || ($t['status']!="Pending")) ?? false;
      $pinCode = $_POST["pincode"] ?? false;

      $docs = $Transaction->loadDocs($t['id']);

      if(!$expired) {
          if($pinCode) {
                $staff_id = ($_SESSION["id"]) ?? (($_POST["staff_id"]) ?? $_GET["staff_id"]);
                $pin_verify = $sess->pinLogin($staff_id,$pinCode);

                if($pin_verify){

                    $name = $userManager->getCustom($t['user_id'],'fname,lname');
                    $full_name = $name['extra']['fname'].' '.$name['extra']['lname'];

                    $name = $userManager->getCustom($staff_id,'fname,lname');
                    $staff_name = $name['extra']['fname'].' '.$name['extra']['lname'];

                    // Verify
                    $output = new stdClass();
                    $output->res = $Transaction->verify($t['id'], $staff_id, 'finance_verify');

                    if ($t['type']=='Deposit') {
                        $text = "USD ".$t['amount']." from ".$full_name." has been received by ".$staff_name;

                        // Treasury
                        $db = new iSQL(DB_admin);
                        $where = "type IN ('Backoffice','Admin')";
                        $agents = $db->select('users', $where, 'id');
                        foreach ($agents as $agent) $ids[] = $agent['id'];
                        $receivers = implode(",",$ids);
                        global $notify; $notify->addMulti('User '.$_SESSION["id"],3, $_POST['transaction_id'], $receivers);

                    } else if ($t['type']=='Withdraw') {
                        $text = "USD ".$t['amount']." from ".$full_name." has been sent by ".$staff_name;

                        // Desk Manager
                        $db = new iSQL(DB_admin);
                        $user_unit = $db->selectId('users', $t['user_id'], 'unit')['unit'];
                        $where = "unit ='$user_unit' AND type='Manager'";
                        $agents = $db->select('users', $where, 'id');
                        foreach ($agents as $agent) $ids[] = $agent['id'];
                        $receivers = implode(",",$ids);
                        global $notify; $notify->addMulti('User '.$_POST['user_id'],4,$_POST['transaction_id'], $receivers);
                        // User
                        $notify->add('User '.$_POST['user_id'],4,$_POST['transaction_id'], $t['user_id']);
                        $Transaction->done($t['id']);

                    }

                    $url = 'https://api.telegram.org/bot1453786628:AAFvUx8bzZqxOxA8n5cF2ot_zaDrF7eXC0s/sendMessage?chat_id=-1001478321844&parse_mode=html&text='.urlencode($text);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $output_t = curl_exec($ch);
                    curl_close($ch);

                    global $actLog; $actLog->add('Verify',(($_POST['transaction_id']) ?? null),(($output->res) ? 1 : 0),'{"Transaction Id":"'.$transaction_id.'","Amount":"'.$t['amount'].'"}');

                    $done = 'You accepted the request.';

                } else {
                $error = 'You Entered Wrong Pin!';
                }
          }
      } else {
          $error = 'This transaction has been done or canceled.';
      }
    }


    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>PIN Access</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 850px; padding: 20px; }
    </style>
    <?php include('includes/css.php'); ?>
    <body>
    <div class="d-flex justify-content-center">

            <div class="card px-5">
                <div class="card-body">

                    <h3 class="text-center m-0">
                        <a href="index.php" class="logo logo-admin"><img src="media/broker/<?= Broker['logo'];?>" height="50" alt="logo"></a>
                    </h3>
                    <?php if(!$expired && !$done) { ?>
                        <div class="row">
                    <div class="p-5 col-sm-12 mb-3">
                        <form id="tUpdate" name="tUpdate" method="post" enctype="multipart/form-data">
                            <div class="col-sm-12 mb-3" id="receipt">
                                <label for="doc">New Document /<small>Up to 3 file</small></label>
                                <div class="custom-file my-1">
                                    <input type="file" class="custom-file-input" id="doc" name="doc[]">
                                    <label class="custom-file-label" for="doc">Choose your Receipt</label>
                                </div>
                                <span style="display: none" data-max="2" class="da-addDoc small btn btn-sm btn-light"><i class="fa fa-plus text-success"></i> Add more</span>
                            </div>
                            <div class="col-sm-12 mb-3" id="comment">
                                <label for="comment">Comment /<small>Optional</small></label>
                                <textarea class="form-control" type="text" id="comment" name="comment"></textarea>
                            </div>
                            <div class="col-sm-12">
                                <input type="hidden" name="transaction_id" value="<?= $t['id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $_SESSION['id'] ?>">
                                <?php if(!$_SESSION["id"]) { ?>
                                    <select name="user_id" id="user" class="form-control">
                                        <?php foreach($group_users as $user) { ?>
                                            <option value="<?= $user['user_id'] ?>"><?= $user['fname'].' '.$user['lname'] ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <input type="text" name="staff" class="form-control" value="<?= $_SESSION["username"] ?>" readonly>
                                    <input type="hidden" name="user_id" class="form-control" value="<?= $_SESSION["id"] ?>" readonly>
                                <?php } ?>
                                <button class="btn btn-primary"  type="submit">Submit</button>
                                <div id="fRes" class="mt-3 alert" style="display: none;"></div>
                            </div>
                        </form>
                    </div>
                    <div class="p-3 col-sm-12 ">
                        <h2>Verify by PIN Code</h2>
                        <p>Please fill out this form by your PIN Code</p>
                        <form class="login-form" action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>User</label>
                          <?php if(!$_SESSION["id"]) { ?>
                                <select id="staff_id" name="staff_id" id="user" class="form-control">
                                  <?php foreach($group_users as $user) { ?>
                                    <option value="<?= $user['user_id'] ?>"><?= $user['fname'].' '.$user['lname'] ?></option>
                                  <?php } ?>
                                </select>
                          <?php } else { ?>
                                <input type="text" name="staff" class="form-control" value="<?= $_SESSION["username"] ?>" readonly>
                                <input type="hidden" id="staff_id" name="staff_id" class="form-control" value="<?= $_SESSION["id"] ?>" readonly>
                          <?php } ?>
                                <label>Pin</label>
                                <input type="number" name="pincode" class="form-control">
                            </div>
                            <div class="form-group">
                                <?php if($error ?? false) { ?>
                                    <div class="alert alert-danger"><?= $error ?></div>
                                <?php } ?>
                                    <div id="tCheck" class="text-left py-2">
                                        <input class="form-check-input tCheck" type="checkbox" id="tallcheck">
                                        <label for="tallcheck" class="ml-4 pl-2"> Confirm All Information</label>
                                    </div>
                                    <button type="submit" name="verify" class="btn btn-success" id="do-verify" disabled>Accept</button>
                            </div>
                        </form>
                    </div>
                        </div>
                        <hr>

                            <div class="page-header">
                                <h3><?= $t['type'] ?> <small class="text-info"><?= $t['status'] ?></small></h3>
                            </div>

                            <div class="timeline">

                                <div class="alert-light">
                                    <div class="line text-mutedmt-2"></div>
                                    <div class="separator text-muted">
                                        <time><?= $t['created_at'] ?></time>
                                    </div>
                                </div>


                                <div class="panel-body">
                                    <div class="rounded-circle alert-primary ml-n4" style="width: 50px;height: 50px;padding:12px 0 0 15px">
                                        <i class="fa fa-2x fa-child"></i>
                                    </div>
                                </div>
                                <article class="panel panel-danger panel-outline mt-n5 mb-3">
                                    <strong class="border-bottom">START</strong>
                                    <blockquote class="quote-card">
                                        <cite class="rounded-top  alert-primary  px-2 py-1"><?= $userManager->getCustom($t['created_by'],'username')['username'] ?></cite>
                                        <div id="tActive" class="card mini-stat border-secondary">
                                            <div class="card-body mini-stat-img">
                                                <div class="row">
                                                    <div class="col">
                                                        <h6 class="text-uppercase mb-3 text-secondary">Type</h6>
                                                        <h4 class="mb-4 text-primary"><?= $t['type'] ?></h4>
                                                    </div>
                                                    <div class="col">
                                                        <h6 class="text-uppercase mb-3 text-secondary">Amount</h6>
                                                        <h4 class="mb-4">$ <?= $t['amount'] ?></h4>
                                                    </div>
                                                    <div class="col">
                                                        <h6 class="text-uppercase mb-3 text-secondary">Status</h6>
                                                        <h4 class="mb-4 text-warning"><?= $t['status'] ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </blockquote>
                                </article>
                                <hr>
                                <div id="attachments" class="card border-secondary py-2">
                                    <small class="mx-auto"><?= ($docs) ? count($docs) : 'No' ?> Attachment</small>
                                    <div class="card-deck overflow-auto row" style="max-height: 150px;">
                                        <?php if ($docs) foreach ($docs as $doc) { ?>
                                            <div class="col-6">
                                                <div class="mt-1 card-body text-center">
                                                    <span><?= $doc['created_at'] ?></span>
                                                    <br>
                                                    <span>By: <?= $userManager->getCustom($doc['created_by'],'username')['username'] ?></span>
                                                    <br>
                                                    <a href="media/transaction/<?= $doc['filename'] ?>" target="_blank"><img src="media/transaction/<?= $doc['filename'] ?>" class="img-thumbnail w-25"></a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>

                    <?php } else if ($done) { ?>
                        <div class="alert alert-success"><?= $done ?></div>
                    <?php } else { ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php } ?>

                </div>
            </div>

        </div>

    <?php include('includes/script.php'); ?>
    <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
    <?php include('includes/script-bottom.php'); ?>

    <script>
        $(document).ready( function () {


                $('#do-verify').prop('disabled',true);
            <?php if ($t['type']=='Withdraw') { ?>
                $('#do-verify').hide();
                /* Update Request */
                $('form#tUpdate .custom-file').addClass('border-top border-danger');
             <?php } ?>


            // Check Marks
            $("body").on("change","#tallcheck", function() {
                (this.checked) ? $('#do-verify').prop('disabled',false) : $('#do-verify').prop('disabled',true);
            });

            /* Update Request */
            $("body").on("submit","form#tUpdate", function(e) {
                e.preventDefault();
                let commentTU = $('form#tUpdate textarea#comment').val();
                let docTU = $('form#tUpdate #doc').val();
                const fResp = $("#tUpdate #fRes");
                if (commentTU.length >1 || docTU) {
                    let formData = new FormData(this);
                    ajaxForm ('transaction', 'update', formData, function(response){
                        let resObj = JSON.parse(response);
                        if (resObj.e) {
                            fResp.removeClass('alert-success').addClass('alert-warning');
                            fResp.fadeIn();
                            fResp.html('Error, Please Check Inputs!');
                        }
                        if (resObj.res) {
                            fResp.removeClass('alert-warning').addClass('alert-success');
                            fResp.fadeIn();
                            fResp.html('Your Request Added.');
                            document.getElementById("tUpdate").reset();
                            setTimeout(function(){
                                $('#do-verify').fadeIn();
                                fResp.fadeOut();
                                $("#attachments").load(" #attachments");
                            }, 1500);
                        }
                    });
                } else {
                    fResp.removeClass('alert-success').addClass('alert-warning');
                    fResp.fadeIn();
                    fResp.html('Error, Please Check Inputs!');
                }
            });

        });
    </script>

    </body>
    </html>