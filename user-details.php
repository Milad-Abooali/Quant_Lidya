<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
#  A | 10:23 AM Wednesday, July 21, 2021
#          Fix Date Range
######################################################################

require_once "config.php";


    // LangMan
    global $_L;

    /**
     * Escape User Input Values POST & GET
     */
    GF::escapeReq();

    if (isset($_GET['startTime']) )
    {
        $startTime = $_GET['startTime'];
        $endTime = $_GET['endTime'];
    } else {
        $startTime = date('Y-m-01');
        $endTime = date('Y-m-t');
    }
    
    $userID = intval($_GET['code']);


    /**
     * Notes Table
     */
    $query['db']            = 'DB_admin';
    $query['table_html']    = 'notes_user';
    $query['query']         = "`notes` WHERE user_id=$userID";
    $query['key']           = '`notes`.`id`';
    $query['columns']       = array(
        array(
            'db' => '`notes`.`note`',
            'dt' => 0,
            'th' => 'Note'
        ),
        array(
            'db' => '`notes`.`note_type`',
            'dt' => 1,
            'th' => 'Type'
        ),
        array(
            'db' => '`notes`.`id`',
            'dt' => 2,
            'th' => 'id'
        ),
        array(
            'db' => '(SELECT username from users WHERE id=`notes`.`created_by`)',
            'dt' => 3,
            'th' => 'Created By'
        ),
        array(
            'db' => '`notes`.`created_at`',
            'dt' => 4,
            'th' => 'Created At'
        ),
        array(
            'db' => '(SELECT username from users WHERE id=`notes`.`updated_by`)',
            'dt' => 5,
            'th' => 'Updated By'
        ),
        array(
            'db' => '`notes`.`updated_at`',
            'dt' => 6,
            'th' => 'Updated At',
            'formatter' => true
        )
    );
    $option = "
            'responsive': false,
            'deferRender': true,
            'lengthMenu': [ [5, 10, 25, 50, -1], [5, 10, 25, 50, 'All'] ],
            'order': [[ 2, 'desc' ]],
            'columnDefs': [
                {
                    'targets': 2,
                    'visible': false
                }
            ]
        ";
    global $factory;
    $table_notes_user = $factory::dataTableComplex(5,$query,$option);


/**
 * Expenses Table
 */
    $query['db']            = 'DB_admin';
    $query['table_html']    = 'user_expenses';
    $query['query']         = "`users_expenses` WHERE user_id=$userID";
    $query['key']           = '`users_expenses`.`id`';
    $query['columns']       = array(
        array(
            'db' => '`users_expenses`.`id`',
            'dt' => 0,
            'th' => '#'
        ),
        array(
            'db' => '`users_expenses`.`amount`',
            'dt' => 1,
            'th' => 'Amount'
        ),
        array(
            'db' => '`users_expenses`.`type`',
            'dt' => 2,
            'th' => 'Type',
            'formatter' => true
        ),
        array(
            'db' => '`users_expenses`.`o_type`',
            'dt' => 3,
            'th' => 'o_type'
        ),
        array(
            'db' => '`users_expenses`.`payee`',
            'dt' => 4,
            'th' => 'Payee'
        ),
        array(
            'db' => '`users_expenses`.`comment`',
            'dt' => 5,
            'th' => 'Comment'
        ),
        array(
            'db' => '(SELECT username from users WHERE id=`users_expenses`.`created_by`)',
            'dt' => 6,
            'th' => 'By'
        ),
        array(
            'db' => '`users_expenses`.`created_at`',
            'dt' => 7,
            'th' => 'Created At',
            'formatter' => true
        )
    );
    $option = "
                'responsive': false,
                'deferRender': true,
                'lengthMenu': [ [5, 10, 25, 50, -1], [5, 10, 25, 50, 'All'] ],
                'order': [[ 2, 'desc' ]],
                'columnDefs': [
                    {
                        'targets': 3,
                        'visible': false
                    }
                ]
            ";
    $table_user_expenses = $factory::dataTableComplex(5, $query, $option);

    $sqlGD = 'SELECT user_extra.*, users.unit as unitn, users.username, users.email FROM user_extra LEFT JOIN users ON user_extra.user_id = users.id WHERE user_id = "'.$userID.'"';
    $gd = $DB_admin->query($sqlGD);
    
    $sqlGD2 = 'SELECT * FROM user_extra WHERE user_id = "'.$userID.'"';
    $gd2 = $DB_admin->query($sqlGD2);
    
    $sqlEXP = 'SELECT * FROM user_fx WHERE user_id = "'.$userID.'"';
    $exp = $DB_admin->query($sqlEXP);
    
    $sqlSM = 'SELECT * FROM user_gi WHERE user_id = "'.$userID.'"';
    $sm = $DB_admin->query($sqlSM);
    
    $sqlMT = 'SELECT * FROM user_marketing WHERE user_id = "'.$userID.'"';
    $mt = $DB_admin->query($sqlMT);
    
    if($_GET["type"] == "ib"){
        $sqlMT4 = 'SELECT * FROM tp WHERE ib = "'.$userID.'" AND group_id != "1"';
        //$sqlMT4 = 'SELECT * FROM tp WHERE user_id = "'.$userID.'"';
        //$sqlGDIB = 'SELECT GROUP_CONCAT(user_id) as clients FROM user_marketing WHERE affiliate = "'.$userID.'"';
        //$gdIB = $DB_admin->query($sqlGDIB);
        //while ($rowGDIB = mysqli_fetch_array($gdIB)) {
        //    $array2 = str_replace(",", '","', $rowGDIB['clients']);
        //    $sqlMT4 = 'SELECT * FROM tp WHERE user_id IN ("'.$array2.'") AND group_id != "1"';
        //}
    } else if ($_GET["type"] == "profile") {
        $sqlMT4 = 'SELECT * FROM tp WHERE user_id = "'.$userID.'"';
    } else {
        if($_SESSION["type"] !== "IB"){
            $sqlMT4 = 'SELECT * FROM tp WHERE user_id = "'.$userID.'"';
        } else {
            $sqlGDIB = 'SELECT GROUP_CONCAT(user_id) as clients FROM user_marketing WHERE affiliate = "'.$userID.'"';
            $gdIB = $DB_admin->query($sqlGDIB);
            while ($rowGDIB = mysqli_fetch_array($gdIB)) {
                $array2 = str_replace(",", '","', $rowGDIB['clients']);
                $sqlMT4 = 'SELECT * FROM tp WHERE user_id IN ("'.$array2.'")';
            }
        }
    }
    
    $mt4 = $DB_admin->query($sqlMT4);
    
    if($_GET["type"] == "ib"){
        //$sqlMT41 = 'SELECT * FROM tp WHERE user_id = "'.$userID.'"';
        $sqlGDIB1 = 'SELECT GROUP_CONCAT(user_id) as clients FROM user_marketing WHERE affiliate = "'.$userID.'"';
        $gdIB1 = $DB_admin->query($sqlGDIB1);
        while ($rowGDIB1 = mysqli_fetch_array($gdIB1)) {
            $array3 = str_replace(",", '","', $rowGDIB1['clients']);
            $sqlMT41 = 'SELECT * FROM tp WHERE user_id IN ("'.$array3.'") AND group_id != "1"';
        }
    } else if ($_GET["type"] == "profile") {
        $sqlMT41 = 'SELECT * FROM tp WHERE user_id = "'.$userID.'"';
    } else {
        if($_SESSION["type"] !== "IB"){
            $sqlMT41 = 'SELECT * FROM tp WHERE user_id = "'.$userID.'" AND group_id != "1"';
        } else {
            $sqlGDIB1 = 'SELECT GROUP_CONCAT(user_id) as clients FROM user_marketing WHERE affiliate = "'.$userID.'"';
            $gdIB1 = $DB_admin->query($sqlGDIB1);
            while ($rowGDIB1 = mysqli_fetch_array($gdIB1)) {
                $array3 = str_replace(",", '","', $rowGDIB1['clients']);
                $sqlMT41 = 'SELECT * FROM tp WHERE user_id IN ("'.$array3.'") AND group_id != "1"';
            }
        }
    }
    
    $mt41 = $DB_admin->query($sqlMT41);

    $usunit = "";
    
?>
 
<!DOCTYPE html>
<html lang="en">
<body>
    <!-- Page Content -->
    <div class="container">
		<div class="row">
			<div class="col-lg-12">
				<?php
					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent" OR $_SESSION["type"] == "Leads" OR $_SESSION["type"] == "Trader" OR $_SESSION["type"] == "IB"){
				?>
				    <div class="row">
    				    <div class="col-md-6">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-1-tab" data-toggle="tab" href="#pills-1" role="tab" aria-controls="pills-1" aria-selected="true"><?= $_L->T('General_Details','user_modal') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-2-tab" data-toggle="tab" href="#pills-2" role="tab" aria-controls="pills-2" aria-selected="false"><?= $_L->T('Extra_Details','user_modal') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-3-tab" data-toggle="tab" href="#pills-3" role="tab" aria-controls="pills-3" aria-selected="false"><?= $_L->T('Comments','note') ?></a>
                                </li>
                                <?php if(in_array($_SESSION['type'], ['Admin', "Manager"])): ?>
                                <li class="nav-item">
                                        <a class="nav-link" id="pills-6-tab" data-toggle="tab" href="#pills-5" role="tab" aria-controls="pills-5" aria-selected="false"><?= $_L->T('Expenses','user_modal') ?></a>
                                </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-4-tab" data-toggle="tab" href="#pills-4" role="tab" aria-controls="pills-4" aria-selected="false"><?= $_L->T('Trading_Accounts','trade') ?></a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <?php
                                if(
                                        $_SESSION["type"] == "Admin"
                                    OR $_SESSION["type"] == "Manager"
                                    OR $_SESSION["type"] == "Retention Manager"
                                    OR $_SESSION["type"] == "Retention Agent"
                                    OR $_SESSION["type"] == "Sales Agent"
                                    OR $userID==$_SESSION['id']
                                ){
                            ?>
                            <span class="bold size-30 float-right ml-1"><button type="button" class="btn bg-gradient-primary text-white btn-sm show-newaccount"><i class="fas fa-plus"></i></button></span>
                                <?php if($userID!=$_SESSION['id']) { ?>
                                    <span class="bold size-30 float-right ml-1"><button type="button" class="btn bg-gradient-primary text-white btn-sm show-existingaccount"><i class="fas fa-plus"></i> <?= $_L->T('Add_Existing_TP','user_modal') ?></button></span>
                                <?php } ?>
                            <?php
                                } 
                            ?>
                            <div id="reportrange" class="bold size-30 float-right ml-1">
                                <button type="button" class="btn bg-gradient-primary text-white btn-sm">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span><?php echo $startTime." - ".$endTime; ?></span> <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-0">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        </div>
                        <div class="alert alert-danger alert-dismissible" id="delete" style="display:none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        </div>
                        <div class="tab-pane fade show active" id="pills-1" role="tabpanel" aria-labelledby="pills-1-tab">
                            <?php
                                while ($rowGD = mysqli_fetch_array($gd)) {
                                    $usunit = $rowGD['unit'];
                                    $usname = $rowGD['fname'].' '.$rowGD['lname'];
                                    $us_name = $rowGD['fname'];
                                    $us_sname = $rowGD['lname'];
                                    $usemail = $rowGD['email'];
                                    $uscountry = $rowGD['country'];
                                    if($rowGD['retention'] == "" || $rowGD['conversion'] == ""){
                                        $retention = 0;
                                        $conversion = 0;
                                    } else {
                                        $retention = $rowGD['retention'];
                                        $conversion = $rowGD['conversion'];
                                    }
                                    
                                    $duplicate_username = 'SELECT COUNT(*) As count FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.username = "'.$usemail.'" AND user_extra.status != 16';
                                    $result_duplicate_username = $DB_admin->query($duplicate_username);
                                    while ($rowDuplicate = mysqli_fetch_array($result_duplicate_username)) {
                                        $duplicates = $rowDuplicate['count'];
                                    }
                                    
                                    $phonetos = substr($rowGD['phone'], -10);
                                    $duplicate_phone = 'SELECT COUNT(*) As count FROM user_extra WHERE phone LIKE "%'.$phonetos.'%" AND status != 16';
                                    $result_duplicate_phone = $DB_admin->query($duplicate_phone);
                                    while ($rowDuplicateP = mysqli_fetch_array($result_duplicate_phone)) {
                                        $duplicatesP = $rowDuplicateP['count'];
                                    }
                            ?>
                            <?php
                                if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent"){
                            ?>
                                <div class="float-right" style="Margin-top: -30px;">
                                    <button type="button" class="btn bg-gradient-danger text-white btn-sm"><?php if ($duplicates > 1) {echo $duplicates;} else { echo "0";} ?> <?= $_L->T('Duplicate_Email','user_modal') ?></button>
                                    <button type="button" class="btn bg-gradient-danger text-white btn-sm"><?php if ($duplicatesP > 1) {echo $duplicatesP;} else { echo "0";} ?> <?= $_L->T('Duplicate_Phone','user_modal') ?></button>
                                </div>
                            <?php 
                                } 
                            ?>
                                <div class="alert bg-gradient-info text-white" role="alert">
                                    <?= $_L->T('Your_Affiliate_Link','user_modal') ?>: <input type="text" class="form-control form-control-sm" name="affiliatelink" id="affiliatelink" Value="<?php echo Broker['crm_url'];?>/register.php?af=<?php echo $rowGD['user_id']; ?>&lang=<?php echo $rowGD['unit']; ?>" disabled>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
                                       <h4><?php echo $rowGD['fname']?> <?php echo $rowGD['lname']?></h4>
                                       <span><?php echo $usemail; ?> - <?= $_L->T('Last_Updated','user_modal') ?>: <?php echo $rowGD['updated_at']?></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Phone','general') ?>:</strong>
                                        <?php echo $rowGD['phone']?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Location','user_modal') ?>:</strong> <?php echo $rowGD['city']?>, <?php echo $rowGD['country']?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Address','user_modal') ?>:</strong> <?php echo $rowGD['address']?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Interests','user_modal') ?>:</strong> <?php echo $rowGD['interests']?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Hobbies','user_modal') ?>:</strong> <?php echo $rowGD['hobbies']?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <?php
                                            $sqlUSERS = 'SELECT name FROM units WHERE id = "'.$rowGD['unit'].'"';
                                            $users = $DB_admin->query($sqlUSERS);
                                            while ($rowUSERS = mysqli_fetch_array($users)) {
                                                echo "<strong>". $_L->T('Business_Unit', 'user_modal') .":</strong> ".$rowUSERS['name']."</td>";
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <?php
                                            $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowGD['retention'].'"';
                                            $users = $DB_admin->query($sqlUSERS);
                                            while ($rowUSERS = mysqli_fetch_array($users)) {
                                                $username_user = $rowUSERS['username'];
                                                echo "<strong>". $_L->T('Retention', 'general') .":</strong> ".$rowUSERS['username']."</td>";
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <?php
                                            $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowGD['conversion'].'"';
                                            $users = $DB_admin->query($sqlUSERS);
                                            while ($rowUSERS = mysqli_fetch_array($users)) {
                                                echo "<strong>". $_L->T('Conversion', 'general') .":</strong> ".$rowUSERS['username']."</td>";
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <?php
                                            if($rowGD['type'] == "Leads"){
                                                $sqlSTATUS = 'SELECT status FROM status WHERE id = "'.$rowGD['status'].'" AND cat = "Leads"';
                                            } else {
                                                $sqlSTATUS = 'SELECT status FROM status WHERE id = "'.$rowGD['status'].'" AND cat = "Trader"';
                                            }
                                            $status = $DB_admin->query($sqlSTATUS);
                                            while ($rowSTATUS = mysqli_fetch_array($status)) {
                                                echo "<strong>". $_L->T('Status', 'general') .":</strong> ".$rowSTATUS['status']."</td>";
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <?php
                                            $sqlTYPE = 'SELECT name FROM type WHERE id = "'.$rowGD['type'].'"';
                                            $types = $DB_admin->query($sqlTYPE);
                                            while ($rowTYPE = mysqli_fetch_array($types)) {
                                                $type = $rowTYPE['name'];
                                            }
                                        ?>
                                        <strong><?= $_L->T('Follow_Up','general') ?>:</strong> <?= $type ?>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Follow_Up','general') ?>:</strong> <?php echo $rowGD['followup']?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('IP','general') ?>:</strong> <?php echo $rowGD['ip']?>
                                    </div>
                                </div>
                                <hr>

                                <?php if($type == 'IB') { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5><?= $_L->T('IB_Contract','user_modal') ?></h5>
                                        <p><button data-dismiss="modal" data-uid="<?= $userID ?>" type="button" class="doM-contract btn btn-sm btn-info"><?= $_L->T('Show_Contracts','user_modal') ?></button></p>
                                    </div>
                                </div>
                                <hr>
                                <?php } ?>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12" id="media-table">
                                        <strong><?= $_L->T('Documents','doc') ?>:</strong>
                                        <?php
                                            //echo $rowGD['user_id'];
                                            //$sqlDOCS = 'SELECT * FROM media WHERE user_id = "'.$rowGD['user_id'].'"';
                                            $where = 'user_id = "'.$rowGD['user_id'].'"';
                                            $docs = $db->select("media",$where);
                                            //var_dump($docs);
                                            if($docs) foreach ($docs as $rowMedia) {
                                                    echo "<a href='javascript:;' class='pop m-2'><img class='rounded-circle' src='media/".$rowMedia['media']."' style='width: 40px; height: 40px' /></a>";
                                                    if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager"){
                                                        if($rowMedia['verify'] == 1){
                                                        ?>
                                                            <td class="text-center">
                                                                <a href="javascript:;" data-id="<?php echo $rowMedia['id']; ?>" data-status="0" class="mverify">
                                                                    <i class="far fa-check-circle text-success"></i>
                                                                </a>
                                                            </td>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <td class="text-center">
                                                                <a href="javascript:;" data-id="<?php echo $rowMedia['id']; ?>" data-status="1" class="mverify">
                                                                    <i class="far fa-times-circle text-danger"></i>
                                                                </a>
                                                            </td>
                                                        <?php
                                                        }
                                                    } else {
                                                        if($rowMedia['verify'] == 1){
                                                        ?>
                                                            <td class="text-center">
                                                                <a href="javascript:;" data-id="<?php echo $rowMedia['id']; ?>">
                                                                    <i class="far fa-check-circle text-success"></i>
                                                                </a>
                                                            </td>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <td class="text-center">
                                                                <a href="javascript:;" data-id="<?php echo $rowMedia['id']; ?>">
                                                                    <i class="far fa-times-circle text-danger"></i>
                                                                </a>
                                                            </td>
                                                        <?php
                                                        }
                                                    }
                                                }
                                            else echo "<?= $_L->T('No_documents_uploaded','doc') ?>";
                                            
                                        ?>
                                    </div>
                                </div>
                            <?php
                                }
                            ?>
                            <?php if($_SESSION["type"] == "Admin" || $_SESSION["type"] == "Manager"){ ?>
                                <hr>
                                <div id="adminTools" class="admin-tools">
                                    <?php if ($_SESSION["type"] == "Admin") { ?>
                                    <button data-id="<?= $userID ?>" class="btn btn-sm bg-gradient-danger text-white doA-deleteUser mx-1"><?= $_L->T('Delete_User','user_modal') ?></button>
                                    <a href="javascript:;" class="doA-addMerge btn btn-outline-light btn-sm" data-userid="<?= $userID ?>"><i class="fas fa-user-ninja"></i> <?= $_L->T('Merge','user_modal') ?></a>
                                    <br><br>
                                    <a class="btn btn-sm bg-gradient-info text-white mx-1" target="_blank" href='sys_settings.php?section=waf_session-user&search=<?= $userID ?>'> <?= $_L->T('Sessions','user_modal') ?></a>
                                    <a class="btn btn-sm bg-gradient-info text-white mx-1" target="_blank" href='sys_settings.php?section=email_logs&dt={"table":"DT_email_log","regex":"1","cols":{"1":"<?= $userID ?>"}}'> <?= $_L->T('Emails','user_modal') ?></a>
                                    <a class="btn btn-sm bg-gradient-info text-white mx-1" target="_blank" href='sys_settings.php?section=system_actlog&dt={"table":"DT_actlog_user","regex":"1","cols":{"2":"<?= $db->selectID('users',$userID,'username')['username'] ?>"}}'><?= $_L->T('ActLogs','user_modal') ?></a>
                                    <?php } else if ($_SESSION["type"] == "Manager") { ?>
                                        <br><br>
                                        <a class="btn btn-sm bg-gradient-info text-white mx-1" target="_blank" href='manager_panel.php?section=email_logs&dt={"table":"DT_email_log","regex":"1","cols":{"1":"<?= $userID ?>"}}'> <?= $_L->T('Emails','user_modal') ?></a>
                                    <?php } ?>
                                    <br><br>

                                    <?= $_L->T('Change_Agreement_Status','user_modal') ?>:
                                    <?php
                                    $where = 'user_id='.$userID;
                                    $agree = $db->select('user_extra',$where,'date_approve',1)[0]['date_approve'];
                                    if ($agree == null || $agree == '0000-00-00 00:00:00') { ?>
                                        <button data-id="<?= $userID ?>" data-status='1' class="btn btn-sm btn-outline-success doA-agree mx-1"><?= $_L->T('Agree','general') ?></button>
                                    <?php } else { ?>
                                        (<?= $agree ?>)
                                        <button data-id="<?= $userID ?>" data-status='0' class="btn btn-sm btn-outline-warning doA-agree mx-1"><?= $_L->T('Need_Agree','general') ?></button>
                                    <?php } ?>
                                    <br><br>
                                    <?= $_L->T('EBook_Download','user_modal') ?>:
                                    <?php
                                    $where = 'id='.$userID;
                                    $allow = $db->select('users',$where,'cid',1)[0]['cid'];
                                    if ($allow == null || $allow == '0000-00-00 00:00:00') { ?>
                                        <button data-id="<?= $userID ?>" data-status='1' class="btn btn-sm btn-outline-success doA-allow mx-1"><?= $_L->T('Allow','user_modal') ?></button>
                                    <?php } else { ?>
                                        (<?= $allow ?>)
                                        <button data-id="<?= $userID ?>" data-status='0' class="btn btn-sm btn-outline-warning doA-allow mx-1"><?= $_L->T('Not_Allow','user_modal') ?></button>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="tab-pane fade" id="pills-2" role="tabpanel" aria-labelledby="pills-2-tab">
                            <?php
                                while ($rowEXP = mysqli_fetch_array($exp)) {
                            ?>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
                                       <h4><?= $_L->T('Past_Experiences','user_modal') ?></h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <?php
                                            $sqlJOBS = 'SELECT name FROM job_category WHERE id = "'.$rowEXP['job_cat'].'"';
                                            $jobs = $DB_admin->query($sqlJOBS);
                                            while ($rowJOBS = mysqli_fetch_array($jobs)) {
                                                echo "<strong>". $_L->T('Job_Category', 'user_modal') .":</strong> ".$rowJOBS['name']."</td>";
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Job_Title','user_modal') ?>:</strong> <?php echo $rowEXP['job_title']?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Experience_in_FX','user_modal') ?>:</strong> <?php if($rowEXP['exp_fx'] == "1") {echo "Yes";} else {echo "No";}?> (<?php echo $rowEXP['exp_fx_year']?> <?= $_L->T('Years','user_modal') ?>)
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Experience_in_CFD','user_modal') ?>:</strong> <?php if($rowEXP['exp_cfd'] == "1") {echo "Yes";} else {echo "No";}?> (<?php echo $rowEXP['exp_cfd_year']?> <?= $_L->T('Years','user_modal') ?>)
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <?php
                                            $sqlINCOME = 'SELECT name FROM income WHERE id = "'.$rowEXP['income'].'"';
                                            $income = $DB_admin->query($sqlINCOME);
                                            while ($rowINCOME = mysqli_fetch_array($income)) {
                                                echo "<strong>". $_L->T('Income', 'user_modal') .":</strong> ".$rowINCOME['name']."</td>";
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <?php
                                            $sqlINVEST = 'SELECT name FROM investment WHERE id = "'.$rowEXP['investment'].'"';
                                            $invest = $DB_admin->query($sqlINVEST);
                                            while ($rowINVEST = mysqli_fetch_array($invest)) {
                                                echo "<strong>". $_L->T('Investment', 'user_modal') .":</strong> ".$rowINVEST['name']."</td>";
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Strategy','user_modal') ?>:</strong> <?php echo $rowEXP['strategy']?>
                                    </div>
                                </div>
                            <?php
                                }
                            ?>
                            <hr>
                            <?php
                                while ($rowSM = mysqli_fetch_array($sm)) {
                            ?>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
                                       <h4><?= $_L->T('Social_Media','user_modal') ?></h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Birth_Date','user_modal') ?>:</strong> <?php echo $rowSM['bd']?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Whatsapp','user_modal') ?>:</strong> <?php echo $rowSM['whatsapp']?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Telegram','user_modal') ?>:</strong> <?php echo $rowSM['telegram']?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Facebook','user_modal') ?>:</strong> <?php echo $rowSM['facebook']?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Instagram','user_modal') ?>:</strong> <?php echo $rowSM['instagram']?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Twitter','user_modal') ?>:</strong> <?php echo $rowSM['twitter']?>
                                    </div>
                                </div>
                            <?php
                                }
                            ?>
                            <hr>
                            <?php
                                while ($rowMT = mysqli_fetch_array($mt)) {
                                $leadcheck = $rowMT['lead_src'];
                            ?>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
                                       <h4><?= $_L->T('Marketing','marketing') ?></h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Source','marketing') ?>:</strong> <?php echo $rowMT['lead_src']?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <strong><?= $_L->T('Campaign','marketing') ?>:</strong> <?php echo $rowMT['lead_camp']?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 mb-3">
                                        <?php
                                            $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowMT['affiliate'].'"';
                                            $users = $DB_admin->query($sqlUSERS);
                                            while ($rowUSERS = mysqli_fetch_array($users)) {
                                                echo "<strong>". $_L->T('Affiliate', 'user_modal') .":</strong> ".$rowUSERS['username']."</td>";
                                            }
                                        ?>
                                    </div>
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                        <div class="tab-pane fade" id="pills-3" role="tabpanel" aria-labelledby="pills-3-tab">
                            <form id="fupForm" name="form1" method="post">
                                <div class="form-row">
                                    <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
                            			<label for="type"><?= $_L->T('Type','general') ?>:</label>
                            			<select name="note_type" id="note_type" class="form-control">
                            				<option value=""><?= $_L->T('Select','user_modal') ?></option>
                            				<?php if($_SESSION["type"] == "Backoffice"){ ?>
                            				<option value="Light Scalper"><?= $_L->T('Light_Scalper','user_modal') ?></option>
                            				<option value="Heavy Scalper"><?= $_L->T('Heavy_Scalper','user_modal') ?></option>
                            				<option value="Latency User"><?= $_L->T('Latency_User','user_modal') ?></option>
                            				<option value="A-Book Potential"><?= $_L->T('ABook_Potential','user_modal') ?></option>
                            				<option value="On Watch List"><?= $_L->T('On_Watch_List','user_modal') ?></option>
                            				<option value="Black Listed"><?= $_L->T('Black_Listed','user_modal') ?></option>
                            				<?php } else if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads" OR $_SESSION["type"] == "IB"){ ?>
                            				<option value="Support"><?= $_L->T('Support_Request','user_modal') ?></option>
                            				<option value="Finance"><?= $_L->T('Financial_Request','user_modal') ?></option>
                            				<option value="Other"><?= $_L->T('Other','user_modal') ?></option>
                            				<?php } else { ?>
                            				<option value="Busy – Call Back Later"><?= $_L->T('Busy_Call_Back','user_modal') ?></option>
                            				<option value="Interested – Call Back Later"><?= $_L->T('Interested_Later','user_modal') ?></option>
                            				<option value="Interested – Send Information"><?= $_L->T('Interested_Information','user_modal') ?></option>
                            				<option value="No Interest – Reason Given"><?= $_L->T('No_Interest','user_modal') ?></option>
                            				<option value="Incorect Information"><?= $_L->T('Incorrect_Information','user_modal') ?></option>
                            				<option value="Follow-Up Scheduled"><?= $_L->T('Follow_Up_Scheduled','user_modal') ?></option>
                            				<option value="Fully Interested"><?= $_L->T('Fully_Interested','user_modal') ?></option>
                            				<option value="Whatsapp"><?= $_L->T('Whatsapp','user_modal') ?></option>
                            				<option value="Other"><?= $_L->T('Other','user_modal') ?></option>
                            				<?php } ?>
                            			</select>
                            		</div>
                            		<div class="col-md-4 col-sm-12 col-xs-12 mb-3">
                            			<label for="note"><?= $_L->T('Note','note') ?>:</label>
                            			<textarea class="form-control" id="note" placeholder="<?= $_L->T('Note','note') ?>" name="note" autocomplete="off"></textarea>
                            		</div>
                            		<div class="col-md-4 col-sm-12 col-xs-12 mb-3">
                            		    <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?= $userID ?>" data-id="<?= $userID ?>">
                            		    <label for="save">&nbsp;</label>
                            		    <input type="button" name="save" class="form-control btn bg-gradient-primary text-white" value="<?= $_L->T('Save','general') ?>" id="note-save">
                            		</div>
                                </div>
                        	</form>
                        	<?php if($_SESSION["type"] !== "Trader" AND $_SESSION["type"] !== "Leads" AND $_SESSION["type"] !== "IB"){ ?>
                        	<hr>
                                <?= $table_notes_user ?>
                            <?php } ?>
                        </div>
                        <div class="tab-pane fade" id="pills-4" role="tabpanel" aria-labelledby="pills-4-tab">
                            <?php
                                while ($rowGD2 = mysqli_fetch_array($gd2)) {
                            ?>
                            <table id="data-table7" class="table table-hover table-sm" style="width: 100%">
                                <thead> 
                                    <tr>
                                        <th><?= $_L->T('TP_Login','trade') ?></th>
                                        <?php if($rowGD2['type'] == "3"){ ?>
                                            <th><?= $_L->T('TP_Login','trade') ?></th>
                                        <?php } else { ?>
                                            <th><?= $_L->T('Type','general') ?></th>
                                        <?php } ?>
                                        <th><?= $_L->T('Equity','trade') ?></th>
                                        <th><?= $_L->T('Balance','trade') ?></th>
                                        <th><?= $_L->T('Active','user_modal') ?></th>
                                        <!--<th>Created By</th>
                                        <th>Created At</th>-->
                                        <th><?= $_L->T('Updated_By','general') ?></th>
                                        <th><?= $_L->T('Updated_At','general') ?></th>
                                        <th><?= $_L->T('Action','trade') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        
                                        while ($rowMT4 = mysqli_fetch_array($mt4)) {
                                            echo "<tr>";
                                            echo "<td>".($rowMT4['login'] ?? '-')."</td>";
                                            
                                            if($rowMT4['server'] == "MT4"){
                                                $sqlEQUITY = 'SELECT NAME, EQUITY, BALANCE FROM MT4_USERS WHERE LOGIN = "'.$rowMT4['login'].'"';
                                            } else {
                                                $sqlEQUITY = 'SELECT CONCAT(clients.FirstName," ",clients.LastName) as NAME,accounts.Equity as EQUITY, accounts.Balance as BALANCE FROM mt5_accounts as accounts LEFT JOIN mt5_users as clients ON accounts.Login = clients.Login WHERE accounts.Login = "'.$rowMT4['login'].'"';
                                            }
                                            
                                            if($rowMT4['group_id'] == "2"){
                                                $mt_type = "mt4";
                                            } else if($rowMT4['group_id'] == "1" AND $rowMT4['server'] == "MT5") {
                                                $mt_type = "mt4";
                                            } else {
                                                $mt_type = "mt4_demo";
                                            }
                                            if($rowMT4['group_id'] == "2"){
                                                if($rowMT4['server'] == "MT4"){
                                                    $equity = $DB_mt4->query($sqlEQUITY);
                                                } else {
                                                    $equity = $DB_mt5->query($sqlEQUITY);
                                                }
                                            } else {
                                                if($rowMT4['server'] == "MT4"){
                                                    $equity = $DB_mt4_demo->query($sqlEQUITY);
                                                } else {
                                                    $equity = $DB_mt5->query($sqlEQUITY);
                                                }
                                            }
                                            
                                            if(mysqli_num_rows($equity) > 0){
                                                while ($rowEQUITY = mysqli_fetch_array($equity)) {
                                                if($rowGD2['type'] == "3"){
                                                    $name_type = $rowEQUITY['NAME'];
                                                } else {
                                                    $sqlGROUPS = 'SELECT name FROM groups WHERE id = "'.$rowMT4['group_id'].'"';
                                                    $groups = $DB_admin->query($sqlGROUPS);
                                                    while ($rowGROUPS = mysqli_fetch_array($groups)) {
                                                        $name_type = $rowGROUPS['name'];
                                                    }
                                                }
                                                    echo "<td>".($name_type ?? '-')."</td>";
                                                    echo "<td>".(number_format( $rowEQUITY['EQUITY'], 2, '.', ',') ?? '-')."</td>";
                                                    echo "<td>".(number_format( $rowEQUITY['BALANCE'], 2, '.', ',') ?? '-')."</td>";
                                                    $sqlACTIVE = 'SELECT 
                                                                        MT4_TRADES.LOGIN as LOGIN 
                                                                    FROM    lidyapar_mt4.MT4_TRADES
                                                                    WHERE   MT4_TRADES.LOGIN = "'.$rowMT4['login'].'"
                                                                            AND MT4_TRADES.CMD IN ("0","1","6")
                                                                            AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
                                                                    UNION ALL 
                                                                    SELECT 
                                                                        mt5_deals.Login as LOGIN
                                                                    FROM lidyapar_mt5.mt5_deals 
                                                                    WHERE   mt5_deals.Login = "'.$rowMT4['login'].'"
                                                                            AND mt5_deals.Action IN ("0","1","2","6")
                                                                            AND mt5_deals.Time BETWEEN "'.$startTime.'" AND "'.$endTime.'"
                                                                    LIMIT 2';
                                                    //echo $sqlACTIVE;
                                                    $genTime->start($rowMT4['login']);
                                                    $active = $DB_mt4->query($sqlACTIVE);
                                                    $genTime->end($rowMT4['login']);
                                                    $statusActive = "No Activity";
                                                    while ($rowACTIVE = mysqli_fetch_array($active)) { 
                                                        if($rowACTIVE['LOGIN'] > 0){
                                                            $statusActive = "Active";    
                                                        } else {
                                                            $statusActive = "No Activity";
                                                        }
                                                    }
                                                    echo "<td>".($statusActive ?? '-')."</td>";
                                                    
                                                }
                                            } else {
                                                $sqlGROUPS = 'SELECT name FROM groups WHERE id = "'.$rowMT4['group_id'].'"';
                                                $groups = $DB_admin->query($sqlGROUPS);
                                                while ($rowGROUPS = mysqli_fetch_array($groups)) {
                                                    $name = $rowGROUPS['name'] ?? '-';
                                                }
                                                echo "<td>".$name."</td>";
                                                echo '<td>-</td><td>-</td><td>-</td>';
                                            }
                                            //$sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowMT4['created_by'].'"';
                                            //$users = $DB_admin->query($sqlUSERS);
                                            //while ($rowUSERS = mysqli_fetch_array($users)) {
                                            //    echo "<td>".$rowUSERS['username']."</td>";
                                            //}
                                            //echo "<td>".$rowMT4['created_at']."</td>";
                                            $sqlUSERS = 'SELECT username FROM users WHERE id = "'.$rowMT4['updated_by'].'"';
                                            $users = $DB_admin->query($sqlUSERS);
                                            if($users) while ($rowUSERS = mysqli_fetch_array($users)) {
                                                $update_by='';
                                                $update_by=$rowUSERS['username'];
                                            }
                                            echo "<td>".($update_by ?? '-')."</td>";

                                            echo "<td>". ($rowMT4['updated_at'] ?? '-')."</td>";
                                            $copy_btn = '<div class="mx-2 btn btn-outline-secondary btn-sm cb-copy-data" data-cb-copy="'.$rowMT4['password'].'" ><i class="fa fa-key"></i> > <i class="fa fa-copy"></i></div>';
                                                echo "<td align='center'>
                                                        <a href='javascript:;' data-id=".$rowMT4['login']." data-server=".$rowMT4['server']." data-user-type = ".$rowMT4['user_id']." data-tp-type=".$mt_type." class='view-tp-details tp-".$rowMT4['login']."'><i class='fas fa-eye'></i></a>
                                                        <a href='javascript:;' data-toggle='popover' title='Password'  data-content='".$rowMT4['password']."' tabindex='0' role='button' class='popupover' data-trigger='focus' data-html='true'><i class='fas fa-key'></i></a>
                                                        $copy_btn
                                                    </td>";
                                            echo "</tr>";
                                        }
                                    ?>
            					</tbody>
                            </table>
                            <?php } ?>
                            <hr>
                            <div id="mt4">
                                <?php

                                    while( $rowLogin = mysqli_fetch_array($mt41)){
                                        $tradermt4[] = $rowLogin['login']; // Inside while loop
                                    }
                                    
                                    if (!empty($tradermt4)){
                                    $array= implode('","',$tradermt4);
                                    }
                                    
                                    //$sqlTRADES = 'SELECT * FROM MT4_TRADES WHERE CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" AND LOGIN IN ("'.$array.'")';
                                    $sqlTRADES = 'SELECT 
                                                    MT4_TRADES.COMMISSION       as COMMISSION, 
                                                    MT4_TRADES.SWAPS            as SWAPS, 
                                                    MT4_TRADES.VOLUME           as VOLUME, 
                                                    MT4_TRADES.TICKET           as TICKET, 
                                                    MT4_TRADES.COMMENT          as COMMENT, 
                                                    MT4_TRADES.CMD              as CMD,
                                                    "15"                        as CMD5, 
                                                    0                           as ENTRY,
                                                    MT4_TRADES.OPEN_TIME        as OPEN_TIME, 
                                                    FORMAT(MT4_USERS.EQUITY, 2) as EQUITY, 
                                                    MT4_USERS.LOGIN             as LOGIN, 
                                                    MT4_USERS.NAME              as NAME, 
                                                    MT4_TRADES.PROFIT           AS PROFIT 
                                            FROM    lidyapar_mt4.MT4_TRADES 
                                                    JOIN lidyapar_mt4.MT4_USERS 
                                                        ON MT4_TRADES.LOGIN = MT4_USERS.LOGIN 
                                            WHERE   MT4_USERS.AGENT_ACCOUNT <> "1" 
                                                    AND MT4_TRADES.LOGIN IN ("'.$array.'")
                                                    AND MT4_TRADES.CLOSE_TIME BETWEEN "'.$startTime.'" AND "'.$endTime.'" 
                                            UNION ALL 
                                            SELECT  mt5_deals.Commission      as COMMISSION, 
                                                    mt5_deals.Storage         as SWAPS, 
                                                    mt5_deals.Volume          as VOLUME, 
                                                    mt5_deals.Deal            as TICKET, 
                                                    mt5_deals.Comment         as COMMENT,
                                                    "15"                           as CMD,
                                                    mt5_deals.Action          as CMD5,
                                                    mt5_deals.Entry           as ENTRY, 
                                                    mt5_deals.Time            as OPEN_TIME, 
                                                    FORMAT(mt5_accounts.Equity, 2) as EQUITY, 
                                                    mt5_users.Login                as LOGIN, 
                                                    mt5_users.Name                 as NAME, 
                                                    mt5_deals.Profit          AS PROFIT 
                                            FROM    lidyapar_mt5.mt5_deals 
                                                    LEFT JOIN lidyapar_mt5.mt5_users 
                                                        ON mt5_deals.Login = mt5_users.Login 
                                                    LEFT JOIN lidyapar_mt5.mt5_accounts 
                                                        ON mt5_accounts.Login = mt5_users.Login 
                                            WHERE   mt5_users.Login IN ("'.$array.'")
                                                    AND mt5_deals.Time BETWEEN "'.$startTime.'" AND "'.$endTime.'"';
                                    //echo $sqlTRADES;
                                    $trades = $DB_mt4->query($sqlTRADES);
                                    
                                    $amountDP = 0;
                                    $amountCDP = 0;
                                    $amountBO = 0;
                                    
                                    $amountWT = 0;
                                    $amountWBO = 0;
                                    $amountCWT = 0;
                                    $totalorders = 0;
                                    $ototalorders = 0;
                                    $totalswaps = 0;
                                    $totalcommisions = 0;
                                    $pnl = 0;
                                    $opnl = 0;
                                    $winning = 0;
                                    $losing = 0;
                                    
                                    $dp = array("Deposit", "DEPOSIT", "DEPOSIT WIRE TRANSFER", "Deposit Wire Transfer", "Deposit Credit Card", "DEPOSIT CREDIT CARD", "Wire In", "wire in", "WIRE IN");
                                    $wd = array("Withdrawal", "WITHDRAWAL", "WITHDRAWAL WIRE TRANSFER", "Withdrawal Wire Transfer", "Withdrawal Credit Card", "WITHDRAWAL CREDIT CARD", "Wire Out", "wire out", "WIRE OUT", "Withdraw", "WITHDRAW");
                                    while ($rowTRADES = mysqli_fetch_array($trades)) {
                                        if($rowTRADES['CMD'] == 6){
                                            if($rowTRADES['PROFIT'] >= 0){
                                                if(!in_array($rowTRADES['COMMENT'], $dp)){
                                                    $amountBO += $rowTRADES['PROFIT'];
                                                } else {
                                                    $amountDP += $rowTRADES['PROFIT'];
                                                }
                                    	    } else if($rowTRADES['PROFIT'] <= 0) { 
                                    	        if(!in_array($rowTRADES['COMMENT'], $wd)){
                                                    $amountWBO += $rowTRADES['PROFIT'];
                                                } else if(preg_match('/\btradecorrection\b/',$rowTRADES['COMMENT'])) {
                                                    $amountCWT += $rowTRADES['PROFIT'];
                                                } else {
                                                    $amountWT += $rowTRADES['PROFIT'];
                                                } 
                                    	    }
                                        } else if ($rowTRADES['CMD5'] == 2) {
                                            if($rowTRADES['PROFIT'] >= 0 AND $rowTRADES['COMMENT'] == "Zeroing"){
                                                $amountZE += $rowTRADES['PROFIT'];
                                            } else if($rowTRADES['PROFIT'] >= 0 AND $rowTRADES['COMMENT'] !== "Zeroing") {
                                                $amountDP += $rowTRADES['PROFIT'];
                                            } else if($rowTRADES['PROFIT'] <= 0) { 
                                    	        $amountWT += $rowTRADES['PROFIT'];
                                            }
                                        } else if ($rowTRADES['CMD5'] > 2 AND $rowTRADES['CMD5'] < 7) {
                                            if($rowTRADES['PROFIT'] >= 0){
                                                $amountBO += $rowTRADES['PROFIT'];
                                    	    } else if($rowTRADES['PROFIT'] <= 0) { 
                                    	        $amountWBO += $rowTRADES['PROFIT'];
                                    	    }
                                        }
                                        if($rowTRADES['CMD'] < 2) {
                                            $pnl += $rowTRADES['PROFIT'];
                                            $swaps += $rowTRADES['SWAPS'];
                                            $commission += $rowTRADES['COMMISSION'];
                                            $totalvolume += $rowTRADES['VOLUME']/100;
                                            $totalorders = $totalorders+1;
                                            
                                            if($rowTRADES['PROFIT']+$rowTRADES['SWAPS'] >= 0){
                                                $winning++;
                                            } else {
                                                $losing++;
                                            }
                                        } else if($rowTRADES['CMD5'] < 2) {
                                            $pnl += $rowTRADES['PROFIT'];
                                            $swaps += $rowTRADES['SWAPS'];
                                            $commission += $rowTRADES['COMMISSION'];
                                            if($rowTRADES['ENTRY'] > 0){
                                                $totalvolume += $rowTRADES['VOLUME']/10000;
                                                $totalorders = $totalorders+1;
                                            }
                                            
                                            if($rowTRADES['PROFIT']+$rowTRADES['SWAPS'] >= 0){
                                                $winning++;
                                            } else {
                                                $losing++;
                                            }
                                        }
                                        $totalrate = $winning+$losing;
                                        if($totalrate > 0){
                                            $winningrate = ( $winning / $totalrate ) * 100;
                                            $losingrate = ( $losing / $totalrate ) * 100;
                                        }
                                    }
                                ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $_L->T('Deposit','transactions') ?>: <span class="size-26 green bold"><?php echo number_format($amountDP, 2, '.', ','); ?>$</span>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <?= $_L->T('Withdrawal','transactions') ?>: <span class="size-26 red bold"><?php echo number_format($amountWT, 2, '.', ','); ?>$</span>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <?= $_L->T('Bonus','trade') ?>: <span class="size-26 yellow bold"><?php echo number_format($amountBO+$amountWBO, 2, '.', ','); ?>$</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="bold size-24"><?= $_L->T('Close_Trades','user_modal') ?></span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $_L->T('Total_Orders','trade') ?>: <span class="bold size-20"><?php echo $totalorders; ?></span>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <?= $_L->T('Total_Volume','user_modal') ?>: <span class="bold size-20"><?php echo number_format($totalvolume, 2, '.', ','); ?><?= $_L->T('Lot','trade') ?></span>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <?= $_L->T('Winning_Rate','trade') ?>: <span class="bold size-20"><?php echo number_format($winningrate, 2, '.', '.'); ?>%</span>
                                        </br>
                                        <small><?= $_L->T('Losing_Rate','trade') ?>: <span class="bold size-20"><?php echo number_format($losingrate, 2, '.', '.'); ?>%</span></small>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $_L->T('Total_Swap','statistics') ?>: <span class="bold size-20"><?php echo number_format($swaps, 2, '.', ','); ?>$</span>
                                        </br>
                                        <?= $_L->T('Total_Commissions','statistics') ?>: <span class="bold size-20"><?php echo number_format($commission, 2, '.', ','); ?>$</span>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <?= $_L->T('Total_P_L','statistics') ?>: <span class="bold dark-gray size-26"><?php echo number_format($pnl, 2, '.', ','); ?>$</span>
                                        </br>
                                        <?= $_L->T('Total_Raw_P_L','statistics') ?>: <span class="bold green size-26"><?php echo number_format($pnl+$swaps+$commission, 2, '.', ','); ?>$</span>
                                        </br>
                                        <?= $_L->T('Total_P_L_Bonus','statistics') ?>: <span class="bold red size-26"><?php echo number_format(($pnl+$swaps+$commission)+($amountBO+$amountWBO), 2, '.', ','); ?>$</span>
                                    </div>
                                </div>
                                <!--<hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="bold size-24">Open Trades</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row"> 
                                    <div class="col-md-6">
                                        Open P&L: <span class="bold size-20"><?php echo number_format($opnl, 2, '.', ','); ?>$</span>
                                        </br>
                                        Open Volume: <span class="bold size-20"><?php echo number_format($ovolume, 2, '.', ','); ?></span>
                                        </br>
                                        Total Orders: <span class="bold size-20"><?php echo $ototalorders; ?></span>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-5" role="tabpanel" aria-labelledby="pills-5-tab">
                            <?php if($_SESSION["type"] !== "Trader" AND $_SESSION["type"] !== "Leads" AND $_SESSION["type"] !== "IB"){ ?>
                                <form id="expense" name="expense" method="post">
                                    <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?= $userID ?>" required>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="type"><?= $_L->T('Type','general') ?></label>
                                            <select class="form-control" id="type" name="type" required>
                                                <option value=""><?= $_L->T('Please_Select_Type','user_modal') ?></option>
                                                <option value="Agent Bonus"><?= $_L->T('Agent_Bonus','user_modal') ?></option>
                                                <option value="IB Bonus"><?= $_L->T('IB_Bonus','user_modal') ?></option>
                                                <option value="Referral Bonus"><?= $_L->T('Referral_Bonus','user_modal') ?></option>
                                                <option value="Exchange Fee"><?= $_L->T('Exchange_Fee','user_modal') ?></option>
                                                <option value="Others"><?= $_L->T('Others','user_modal') ?></option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="type"><?= $_L->T('Payee','user_modal') ?> <span class="text-danger payee-type"></span></label>
                                            <select class="form-control pre-payee" id="payee" name="payee" required></select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="amount"><?= $_L->T('Amount','trade') ?></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-danger text-white strong" id="basic-addon1">$</span>
                                                </div>
                                                <input type="number" class="form-control" min="0.00" max="10000.00" step="0.01" id="amount" name="amount" placeholder="0,00" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-9">
                                            <label for="type"><?= $_L->T('Comment','note') ?></label>
                                            <textarea type="text" class="form-control" id="comment" name="comment"></textarea>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="type"><?= $_L->T('Other_Type','user_modal') ?></label>
                                            <input type="text" class="form-control" id="o_type" name="o_type" placeholder="Other Type" disabled>
                                            <button type="submit" class="btn btn-danger mt-2"><?= $_L->T('Add_Expense','user_modal') ?></button>
                                        </div>
                                    </div>
                                </form>
                                <hr>
                                <div>
                                    <?php
                                    $total = $db->query("SELECT SUM(amount) as sum_amount FROM `users_expenses` WHERE user_id=$userID AND created_at BETWEEN '$startTime' and '$endTime';")[0]['sum_amount'];
                                    ?>
                                    <?= $_L->T('Total_Expenses_from','user_modal') ?> <strong><?= $startTime ?></strong> to <strong><?= $endTime ?></strong>: <span class="text-danger display-6 wrap-total-expense">$<?= GF::nf($total) ?></span>
                                </div>
                                <script src="assets/js/expense.js"></script>

                                <hr>
                                <?= $table_user_expenses ?>
                            <?php } ?>
                        </div>
                    </div>
				<?php
					}
				?>
			</div>
		</div>
    </div>
    <div style="position: fixed; top: 25px; left: 0; min-width: 300px; display:none; z-index:999999;" id="boxer">
        <div class="toast fade hide dragit2" id="newaccount" data-autohide="false">
            <div class="toast-header bg-white">
                <strong class="mr-auto"><i class="fas fa-plus"></i> <?= $_L->T('New_Account','user_modal') ?></strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-white">
                <div class="row">
                    <div class="col-sm-12 mb-3">
                        <label for="inputPlatform"><?= $_L->T('Platform','user_modal') ?></label>
                        <select class="form-control" name="platform" id="platform" data-id="<?= $userID ?>" disabled>
                            <option value=""><?= $_L->T('Please_Select_Platform','user_modal') ?></option>
                            <option value='MT5'>MT5</option>
                            <option value='MT4'>MT4</option>
                        </select>
                    </div>
                    <div class="col-sm-12 mb-3">
            			<label for="inputType"><?= $_L->T('Type','general') ?></label>
            			<select class="form-control" id="type1" name="type1" data-id="<?= $userID ?>" required>
            			    <option value=""><?= $_L->T('Please_Select_Type','user_modal') ?></option>
            			    <option value="2"><?= $_L->T('Real','user_modal') ?></option>
                            <option value="1"><?= $_L->T('Demo','user_modal') ?></option>
                        </select>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputComment"><?= $_L->T('Group','user_modal') ?></label>
            			<select class="form-control" name="group" id="group" disabled>
                            <option value=""><?= $_L->T('Please_Select_Type_Platform','user_modal') ?></option>
                            <!--<option value='demoKUSTDFIXUSD'>demoKUSTDFIXUSD</option>-->
                        </select>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputAmount"><?= $_L->T('Amount','trade') ?></label>
            			<input type="number" class="form-control" id="damount" placeholder="Deposit Amount" name="damount">
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputCurrency"><?= $_L->T('Currency','user_modal') ?></label>
            			<select class="form-control" id="currency" name="currency" required>
            			    <option value="1">USD</option>
                            <option value="2">EUR</option>
                        </select>
            		</div>
                </div>
                <div>
                    <a class="btn bg-gradient-primary text-white newaccount" href="" data-id="<?= $userID ?>">Submit</a>
                </div>
            </div>
        </div>
    </div>
    <div style="position: fixed; top: 25px; left: 0; min-width: 300px; display:none; z-index:999999;" id="boxer2">
        <div class="toast fade hide dragit2" id="existingaccount" data-autohide="false">
            <div class="toast-header bg-white">
                <strong class="mr-auto"><i class="fas fa-plus"></i> <?= $_L->T('Add_Existing_TP','user_modal') ?></strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-white">
                <div class="row">
                    <div class="col-sm-12 mb-3">
            			<label for="inputType"><?= $_L->T('Type','general') ?></label>
            			<select class="form-control" id="type2" name="type2" data-id="<?= $userID ?>" required>
            			    <option value="2"><?= $_L->T('Real','user_modal') ?></option>
                            <option value="1"><?= $_L->T('Demo','user_modal') ?></option>
                        </select>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputPlatform"><?= $_L->T('Platform','user_modal') ?></label>
            			<select class="form-control" name="platform2" id="platform2" data-id="<?= $userID ?>">
                            <option value='MT5'>MT5</option>
                            <option value='MT4'>MT4</option>
                        </select>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputComment"><?= $_L->T('Existing_TP','user_modal') ?></label>
            			<input type="number" class="form-control" id="tp" placeholder="TP Number" name="tp">
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputComment"><?= $_L->T('Passwrod','user_modal') ?></label>
            			<input type="text" class="form-control" id="password" placeholder="<?= $_L->T('Passwrod','user_modal') ?>" name="password">
            		</div>
                </div>
                <div>
                    <a class="btn bg-gradient-primary text-white existingaccount" href="" data-id="<?= $userID ?>"><?= $_L->T('Submit','general') ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade imagemodal" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">              
                <div class="modal-body">
                    <img src="" class="imagepreview" style="width: 100%;" >
                </div>
            </div>
        </div>
    </div>
<script>
$(document).ready( function () {
    
    /*  Show Contracts Modal */
    $("body").on("click",'.doM-contract', function() {
        const id = $(this).data('uid');
        ajaxCall ('ib', 'myContracts', {id:id}, function(response) {
            makeModal('Contracts', response, 'xl');
        });
    });
    
	$('.pop').on('click', function() {
		$('.imagepreview').attr('src', $(this).find('img').attr('src'));
		$('#imagemodal').modal('show');   
	});


	$('.popupover').popover();
	
    $("body").on("click touchstart", '.popupover', function() {
        $(this).popover("show");
        $('.popupover').not(this).popover("hide"); // hide other popovers
        return false;
    });
        
    $("body").on("click touchstart", function() {
        $('.popupover').popover("hide"); // hide all popovers when
    });

    var tradingaccounts = $('#data-table7').DataTable({  
		"responsive": true,
		"deferRender": true,
		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
		"order": [[ 5, "desc" ]],
    });

    var leadscehck = "<?php echo $leadcheck; ?>";
    
    if(leadscehck == "" ){
        $('.show-newaccount').prop('disabled', true);
        $('.show-existingaccount').prop('disabled', true);
    } else {
        $('.show-newaccount').prop('disabled', false);
        $('.show-existingaccount').prop('disabled', false);
    }
    
    $('#type1').change( function() {
        let val = $(this).val();
        let server = $('#platform').val();
        let userID = $(this).data('id');
        $('#platform').prop('disabled', false);
        if(server != ""){
            if (val == "2") {
                $.ajax({
                   url: 'findgroup.php',
                   dataType: 'html',
                   data: { type : val, server : server, userID: userID },
                   success: function(data) {
                       $('#group').html( data );
                       $('#group').prop('disabled', false);
                   }
                });
            } else {
                $.ajax({
                   url: 'findgroup.php',
                   dataType: 'html',
                   data: { type : '1', server : server, userID: userID },
                   success: function(data) {
                       $('#group').html( data );
                       $('#group').prop('disabled', false);
                   }
                });
            }  
        }
    });
    
    $('#type1').ready( function() {
        let val = $(this).val();
        let server = $('#platform').val();
        let userID = $(this).data('id');
        $('#platform').prop('disabled', false);
        if(server != ""){
            if (val == "2") {
                $.ajax({
                   url: 'findgroup.php',
                   dataType: 'html',
                   data: { type : val, server : server, userID: userID },
                   success: function(data) {
                       $('#group').html( data );
                       $('#group').prop('disabled', false);
                   }
                });
            } else {
                $.ajax({
                   url: 'findgroup.php',
                   dataType: 'html',
                   data: { type : '1', server : server, userID: userID },
                   success: function(data) {
                       $('#group').html( data );
                       $('#group').prop('disabled', false);
                   }
                });
            }  
        }
    });
    
    $('#platform').change( function() {
        let val = $('#type1').val();
        let server = $('#platform').val();
        let userID = $(this).data('id');
        if(server != ""){
            if (val == "2") {
                $.ajax({
                   url: 'findgroup.php',
                   dataType: 'html',
                   data: { type : val, server : server, userID: userID },
                   success: function(data) {
                       $('#group').html( data );
                       $('#group').prop('disabled', false);
                   }
                });
            } else {
                $.ajax({
                   url: 'findgroup.php',
                   dataType: 'html',
                   data: { type : '1', server : server, userID: userID },
                   success: function(data) {
                       $('#group').html( data );
                       $('#group').prop('disabled', false);
                   }
                });
            }  
        }
        
    });
    
	$('#note-save').on('click', function() {
		$("#note-save").attr("disabled", "disabled");
		let note = $('#note').val();
		let note_type = $('#note_type').val();
		
		$('#note').val('');
		$('#note_type option:selected').removeAttr("selected");
		
		let user_id = $('#user_id').val();
		if(note!="" && note_type!="" && user_id!=""){
			$.ajax({
				url: "note-save.php",
				type: "POST",
				data: {
					note: note,
					note_type: note_type,
					user_id: user_id
				},
				cache: false,
				success: function(dataResult){
					dataResult = JSON.parse(dataResult);
					if(dataResult.statusCode==200){
						$("#note-save").removeAttr("disabled");
						$('#fupForm').find('input:text').val('');
						<?php if($_SESSION["type"] !== "Trader" AND $_SESSION["type"] !== "Leads" AND $_SESSION["type"] !== "IB"){ ?>
                        DT_notes_user.ajax.reload();
                        toastr.success("Note added successfully.");
                    <?php } ?>
					}
					else if(dataResult.statusCode==201){
                        toastr.error("Error occured !");
                    }
					
				}
			});
		}
		else{
			alert('Please fill all the field!');
		}
	});
	
	<?php if($_SESSION["type"] == "Admin"){ ?>
    $('body').on('click', '.remove-note', function () {
	    let note_id = $(this).data('id');
	    let user_id = $('#user_id').val();
		if(note_id!=""){
			$.ajax({
				url: "note-delete.php",
				type: "POST",
				data: {
					note_id: note_id,
					user_id: user_id
				},
				cache: false,
				success: function(dataResult){
					dataResult = JSON.parse(dataResult);
					if(dataResult.statusCode==200){
						$("#delete").show();
						<?php if($_SESSION["type"] !== "Trader" AND $_SESSION["type"] !== "Leads" AND $_SESSION["type"] !== "IB"){ ?>
                        DT_notes_user.ajax.reload();
                        toastr.success("Note deleted successfully.");

						<?php } ?>
					}
					else if(dataResult.statusCode==201){
                        toastr.error("Error occured !");
					}
					
				}
			});
		}
		else{
			alert('Something is wrong!');
		}
	});
	<?php } ?>
	
    var butmt4Run = false;
    $('#data-table7 tbody').on('click', '.view-tp-details', function () {
	//$(".butmt4").on("click", function(){
        if (butmt4Run) return;
        butmt4Run = true;
        let start = "<?php echo $startTime; ?>";
        let end = "<?php echo $endTime; ?>";
		let mt4_id =  $(this).data('id');
		let user_id = <?php if($_SESSION["type"] !== "IB"){ echo '"'.$userID.'"'; } else { ?> $(this).data('user-type');<?php } ?>;
		let unit = "<?php echo $usunit; ?>";
		let server = $(this).data('server');
		let mturl = $(this).data('tp-type');
		if(mt4_id!=""){
			$.ajax({
				url: mturl+".php",
				type: "POST",
				data: {
					mt4_id: mt4_id,
					user_id: user_id,
					unit: unit,
					server: server,
					startTime: start,
					endTime: end,
				},
				cache: false,
				success: function(dataResult){
				    $(this).parent().addClass('table-active')
					$('#mt4').html(dataResult);
                    butmt4Run = false;
                }
			});
		}
		else{
			alert('Something is wrong!');
            butmt4Run = false;
        }
    });
    
    $('.newaccount').click(function(e){
        e.preventDefault();
        let currency = $( "#currency" ).val();
        let type = $( "#type1" ).val();
        let group = $( "#group" ).val();
        let platform = $( "#platform" ).val();
        let damount = $( "#damount" ).val();
        let name = "<?php echo $usname; ?>";
        let uname = "<?php echo $us_name; ?>";
        let usname = "<?php echo $us_sname; ?>";
        let email = "<?php echo $usemail; ?>";
        let country = "<?php echo $uscountry; ?>";
        let userID = $(this).data('id');
        $.ajax({
            method: 'post',
            url: 'api-newaccount.php',
            data: {
                'userId': userID,
                'currency': currency,
                'type': type,
                'group': group,
                'platform': platform,
                'name': name,
                'uname': uname,
                'usname': usname,
                'email': email,
                'country': country,
                'amount': damount
            },
            success: function(data) {
                $('#newaccount').toast('dispose');
                toastr.success("Account # has been created", "New Account Created");
    			
    			$.ajax({
                    method: 'post',
                    url: 'tp_ftd.php',
                    data: {
                        'userID': userID,
                        'retention': <?= $retention ?>,
                        'conversion': <?= $conversion ?>
                    },
                    success: function(data) {
                        
                    }
                });
                
                var url = "user-details.php?code="+userID;
                $('.my-modal-cont').load(url,function(result){
    			    $('#pills-tab li:last-child a').tab('show');
    			    $('#notifications').html('<div class="alert alert-success alert-dismissible" id="danger1"><?= $_L->T('Account_added','user_modal') ?> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a></div>');
    			});
            }
        }).done(function() {
            
        });
    });
    
    $('.existingaccount').click(function(e){
        e.preventDefault();
        let type = $( "#type2" ).val();
        let platform = $( "#platform2" ).val();
        let tp = $( "#tp" ).val();
        let password = $( "#password" ).val();
        let userID = $(this).data('id');
        $.ajax({
            method: 'post',
            url: 'existingaccount.php',
            data: {
                'userId': userID,
                'type': type,
                'tp': tp,
                'platform': platform,
                'password': password
            },
            success: function(data) {
                $('#existingaccount').toast('dispose');
                toastr.success("Account # has been added", "New TP has been added");
                
                $.ajax({
                    method: 'post',
                    url: 'tp_ftd.php',
                    data: {
                        'userID': userID,
                        'retention': <?= $retention ?>,
                        'conversion': <?= $conversion ?>
                    },
                    success: function(data) {
                        
                    }
                });
                
                var url = "user-edit.php?code="+userID;
		        $('.my-modal-cont').load(url,function(result){
		            $('#pills-tab li:last-child a').tab('show');
		            $('#notifications').html('<div class="alert alert-success alert-dismissible" id="danger1"><?= $_L->T('Account_added','user_modal') ?> <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a></div>');
		        });
            }
        }).done(function() {
           
        });
    });
    
    $(".show-newaccount").click(function(){
            if ( $('#boxer').css('display') == 'none' || $('#boxer').css("visibility") == "hidden"){
                $('#boxer').show();
            }
            $("#newaccount").toast('show');
    });
    
    $(".show-existingaccount").click(function(){
            if ( $('#boxer2').css('display') == 'none' || $('#boxer2').css("visibility") == "hidden"){
                $('#boxer2').show();
            }
            $("#existingaccount").toast('show');
    });
    var draggable2 = $('.dragit2'); //element 

    draggable2.on('mousedown', function(e){
    	let dr = $(this).addClass("drag").css("cursor","move");
    	height = dr.outerHeight();
    	width = dr.outerWidth();
    	ypos = dr.offset().top + height - e.pageY,
    	xpos = dr.offset().left + width - e.pageX;
    	$(document.body).on('mousemove', function(e){
    		let itop = e.pageY + ypos - height;
    		let ileft = e.pageX + xpos - width;
    		if(dr.hasClass("drag")){
    			dr.offset({top: itop,left: ileft});
    		}
    	}).on('mouseup', function(e){
    			dr.removeClass("drag");
    	});
    });
    
    $(function() {;
        <?php
        $stime = strtotime($startTime);
        $etime = strtotime($endTime);
        ?>
        var start = <?php echo date('dd/mm/Y', $stime); ?>;
        var end = <?php echo date('dd/mm/Y', $stime); ?>;
    
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            opens: 'left',
            "showDropdowns": true,
            "timePicker": true,
            ranges: {
               'Today': [moment(), moment().add(1, 'days')],
               'Yesterday': [moment().subtract(1, 'days'), moment()],
               'Last 7 Days': [moment().subtract(8, 'days'), moment()],
               'Last 30 Days': [moment().subtract(31, 'days'), moment()],
               'Last 90 Days': [moment().subtract(91, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month').add(1, 'days')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month').add(1, 'days')],
               'Last 3 Month': [moment().subtract(3, 'month').startOf('month'), moment().endOf('month').add(1, 'days')],
               'Total': [moment().subtract(300, 'month').startOf('month'), moment().endOf('month').add(1, 'days')]
            }
        }, function(start, end, label){
            let user_id = $('#user_id').val();
            <?php if($_GET["type"] !== "ib"){ ?>
                let url = "user-details.php?code="+user_id+"&startTime="+start.format('YYYY-MM-DD')+"&endTime="+end.format('YYYY-MM-DD');
            <?php } else { ?>
                let url = "user-details.php?code="+user_id+"&type=ib&startTime="+start.format('YYYY-MM-DD')+"&endTime="+end.format('YYYY-MM-DD');
            <?php } ?>
            tradingaccounts.destroy();
            if($('#load-user-details').length){
                $('#load-user-details').load(url,function(result){
    			    $('#pills-tab li a').eq(-1).tab('show');
    			});
            } else {
                $('.my-modal-cont').load(url,function(result){
			        $('#pills-tab li a').eq(-1).tab('show');
			    }); 
            }
			
            $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            
            //window.location.href = "welcome.php?startTime="+start.format('YYYY-MM-DD')+"&endTime="+end.format('YYYY-MM-DD');  
        });
    
        //cb(start, end);
    });
});
</script>
    <?= factory::footer(); ?>
<?php
    $DB_admin->close();
?>
</body>
</html>
<?php
    
    global $genTime;
    $genTime->end("Page");
    //GF::P($genTime->get());
?>
