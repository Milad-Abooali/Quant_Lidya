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
    global $DB_admin;


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
     * Load Documents
     */
    $sql = 'SELECT media,verify FROM media WHERE user_id = '.$userID.' AND type = "ID"';
    $result = $DB_admin->query($sql);
    if(mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_array($result)) {
            $idc_img = $row['media'];
            $idc_img_verify = $row['verify'];
        }
    } else {
        $idc_img = 'broker/'.Broker['favicon'];
        $idc_img_verify = "3";
    }

    $sql = 'SELECT media,verify FROM media WHERE user_id = '.$userID.' AND type = "Bill"';
    $result = $DB_admin->query($sql);
    if(mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_array($result)) {
            $poa_img = $row['media'];
            $poa_img_verify = $row['verify'];
        }
    } else {
        $poa_img = 'broker/'.Broker['favicon'];
        $poa_img_verify = "3";
    }


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
    $gd = $DB_admin->set_charset("utf8");
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
        //$sqlMT4 = 'SELECT * FROM tp WHERE user_id = "'.$userID.'"';
        $sqlGDIB = 'SELECT GROUP_CONCAT(user_id) as clients FROM user_marketing WHERE affiliate = "'.$userID.'"';
        $gdIB = $DB_admin->query($sqlGDIB);
        while ($rowGDIB = mysqli_fetch_array($gdIB)) {
            $array2 = str_replace(",", '","', $rowGDIB['clients']);
            $sqlMT4 = 'SELECT * FROM tp WHERE user_id IN ("'.$array2.'") AND group_id != "1"';
        }
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
					if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent" OR $_SESSION["type"] == "Leads"){
				?>
				    <div class="row">
				        <div class="col-md-8">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-1-tab" data-toggle="tab" href="#pills-1" role="tab" aria-controls="pills-1" aria-selected="true"><?= $_L->T('General_Details','user_modal') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-2-tab" data-toggle="tab" href="#pills-2" role="tab" aria-controls="pills-2" aria-selected="false"><?= $_L->T('Past_Experiences','user_modal') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-3-tab" data-toggle="tab" href="#pills-3" role="tab" aria-controls="pills-3" aria-selected="false"><?= $_L->T('Marketing','user_modal') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-4-tab" data-toggle="tab" href="#pills-4" role="tab" aria-controls="pills-4" aria-selected="false"><?= $_L->T('Comments','note') ?></a>
                                </li>
                                <?php if(in_array($_SESSION['type'], ['Admin', "Manager"])): ?>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-6-tab" data-toggle="tab" href="#pills-6" role="tab" aria-controls="pills-6" aria-selected="false"><?= $_L->T('Expenses','user_modal') ?></a>
                                </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-5-tab" data-toggle="tab" href="#pills-5" role="tab" aria-controls="pills-5" aria-selected="false"><?= $_L->T('Trading_Accounts','trade') ?></a>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-md-4">
                            <?php
                                if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent"){
                            ?>
                            <span class="bold size-30 float-right ml-1"><button type="button" class="btn bg-gradient-primary text-white btn-sm show-newaccount"><i class="fas fa-plus"></i></button></span>
                            <span class="bold size-30 float-right ml-1"><button type="button" class="btn bg-gradient-primary text-white btn-sm show-existingaccount"><i class="fas fa-plus"></i> <?= $_L->T('Add_Existing_TP','user_modal') ?></button></span>
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
                            <div id="notifications">
                                <div class="alert alert-danger alert-dismissible" id="delete" style="display:none;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                </div>
                            </div>
                            <form accept-charset="utf-8">
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="email"><?= $_L->T('Email','general') ?>:</label>
                                        <input type="text" class="form-control" name="email" id="email" value="<?php echo $rowGD['email']?>" <?= (in_array($_SESSION["type"] ,Broker['edit_email'])) ? '': 'readonly' ?> >
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="fname"><?= $_L->T('First_Name','general') ?>:</label>
                                        <?php if($_SESSION["type"] == "Retention Agent"){ ?>
                                            <input type="text" class="form-control" name="fname" id="fname" value="<?php echo $rowGD['fname']?>" disabled/>
                                        <?php } else {
                                        ?>
                                            <input type="text" class="form-control" name="fname" id="fname" value="<?php echo $rowGD['fname']?>"/>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="lname"><?= $_L->T('Last_Name','general') ?>:</label>
                                        <?php if($_SESSION["type"] == "Retention Agent"){ ?>
                                            <input type="text" class="form-control" name="lname" id="lname" value="<?php echo $rowGD['lname']?>" disabled/>
                                        <?php } else {
                                        ?>
                                            <input type="text" class="form-control" name="lname" id="lname" value="<?php echo $rowGD['lname']?>" />
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="phone"><?= $_L->T('Phone','general') ?>:</label>
                                        <?php if($_SESSION["type"] == "Sales Agent" OR $_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent"){ ?>
                                            <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $rowGD['phone']?>" disabled/>
                                        <?php } else {
                                        ?>
                                            <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $rowGD['phone']?>"/>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-4 mb-3">    
                                        <label for="country"><?= $_L->T('Country','general') ?>:</label>
                                        <select class="form-control" name="country" id="country">
                                        <?php
                                            $sqlCU = 'SELECT country_name FROM countries';
                                            $countries = $DB_admin->query($sqlCU);
                                            while ($rowCU = mysqli_fetch_array($countries)) {
                                                if($rowGD['country'] == $rowCU['country_name']){
                                                    echo "<option value='".$rowCU['country_name']."' selected>".$rowCU['country_name']."</option>";
                                                } else {
                                                    echo "<option value='".$rowCU['country_name']."'>".$rowCU['country_name']."</option>";
                                                }
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="city"><?= $_L->T('City','general') ?>:</label>
                                        <input type="text" class="form-control" name="city" id="city" value="<?php echo $rowGD['city']?>" />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="address"><?= $_L->T('Address','user_modal') ?>:</label>
                                        <input type="text" class="form-control" name="address" id="address" value="<?php echo $rowGD['address']?>" />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="interests"><?= $_L->T('Interests','user_modal') ?>:</label>
                                        <input type="text" class="form-control" name="interests" id="interests" value="<?php echo $rowGD['interests']?>" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="hobbies"><?= $_L->T('Hobbies','user_modal') ?>:</label>
                                        <input type="text" class="form-control" name="hobbies" id="hobbies" value="<?php echo $rowGD['hobbies']?>" />
                                    </div>
                                    <?php if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent"){ ?>
                                    <div class="col-md-4 mb-3">
                                        <label for="userunit"><?= $_L->T('Business_Unit','user_modal') ?>:</label>
                                        <select class="form-control" name="userunit" id="userunit">
                                        <?php
                                            if($_SESSION["type"] == "Sales Agent"){
                                                $sqlUNIT = 'SELECT id, name FROM units WHERE id = "'.$rowGD['unit'].'"';
                                                $units = $DB_admin->query($sqlUNIT);
                                                while ($rowUNIT = mysqli_fetch_array($units)) {
                                                    echo "<option value='".$rowUNIT['id']."' selected>".$rowUNIT['name']."</option>";
                                                }
                                            } else {
                                                $sqlUNIT = 'SELECT id, name FROM units';
                                                $units = $DB_admin->query($sqlUNIT);
                                                while ($rowUNIT = mysqli_fetch_array($units)) {
                                                    if($rowGD['unit'] == $rowUNIT['id']){
                                                        echo "<option value='".$rowUNIT['id']."' selected>".$rowUNIT['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowUNIT['id']."'>".$rowUNIT['name']."</option>";
                                                    }
                                                }
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="retention">Retention:</label>
                                        <?php if($_SESSION["type"] !== "Retention Agent"){ ?>
                                            <select class="form-control" name="retention" id="retention">
                                                <option value=''>Choose On Agent</option>
                                            <?php
                                                if($_SESSION["type"] == "Admin"){
                                                    $sqlUSERS = 'SELECT users.id, username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.type IN ("Retention Agent","Admin","Manager") AND user_extra.status != "9"';
                                                } else if($_SESSION["unit"] == "Farsi") {
                                                    $sqlUSERS = 'SELECT users.id, username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Retention Agent","Manager") AND user_extra.status != "9"';
                                                } else {
                                                    $sqlUSERS = 'SELECT users.id, username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Retention Agent","Manager") AND user_extra.status != "9"';
                                                }
                                                $users = $DB_admin->query($sqlUSERS);
                                                while ($rowUSERS = mysqli_fetch_array($users)) {
                                                    if($rowGD['retention'] == $rowUSERS['id']){
                                                        echo "<option value='".$rowUSERS['id']."' selected>".$rowUSERS['username']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowUSERS['id']."'>".$rowUSERS['username']."</option>";
                                                    }
                                                }
                                            ?>
                                            </select>
                                        <?php
                                        } else {
                                        ?>
                                            <select class="form-control" name="retention" id="retention" disabled>
                                            <?php
                                            $sqlUSERS2 = 'SELECT users.id, username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Retention Agent","Manager") AND user_extra.status != "9"';
                                            $users2 = $DB_admin->query($sqlUSERS2);
                                                while ($rowUSERS2 = mysqli_fetch_array($users2)) {
                                                    if($rowGD['retention'] == $rowUSERS2['id']){
                                                        echo "<option value='".$rowUSERS2['id']."' selected>".$rowUSERS2['username']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowUSERS2['id']."'>".$rowUSERS2['username']."</option>";
                                                    }
                                                }
                                            ?>
                                            </select>
                                            <?php
                                        }?>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="conversion">Conversion:</label>
                                        <?php
                                            $current_conversion = ($rowGD['conversion']) ? ($rowGD['conversion']) :  0; 
                                        ?>
                                        <?php if($_SESSION["type"] !== "Retention Agent" AND $_SESSION["type"] !== "Sales Agent"){ ?>
                                            <select class="form-control" name="conversion" id="conversion">
                                                <option value=''>Choose On Agent</option>
                                            <?php
                                                if($_SESSION["type"] == "Admin"){
                                                    $sqlUSERS2 = 'SELECT users.id, username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE (users.type IN ("Admin","Sales Agent","Manager") AND user_extra.status != "9") OR (user_extra.user_id = '.$current_conversion.')';
                                                } else {
                                                    $sqlUSERS2 = 'SELECT users.id, username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE (users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Sales Agent","Manager") AND user_extra.status != "9") OR (user_extra.user_id = '.$current_conversion.')';
                                                }
                                                //$sqlUSERS2 = 'SELECT id, username FROM users WHERE unit = "'.$_SESSION["unit"].'" AND type = "Sales Agent"';
                                                $users2 = $DB_admin->query($sqlUSERS2);
                                                while ($rowUSERS2 = mysqli_fetch_array($users2)) {
                                                    if($current_conversion == $rowUSERS2['id']){
                                                        echo "<option value='".$rowUSERS2['id']."' selected>".$rowUSERS2['username']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowUSERS2['id']."'>".$rowUSERS2['username']."</option>";
                                                    }
                                                }
                                            ?>
                                            </select>
                                        <?php
                                        } else {
                                        ?>
                                            <select class="form-control" name="conversion" id="conversion" disabled>
                                            <?php
                                            $sqlUSERS2 = 'SELECT users.id, username FROM users LEFT JOIN user_extra ON users.id = user_extra.user_id WHERE (users.unit = "'.$_SESSION["unit"].'" AND users.type IN ("Sales Agent","Manager") AND user_extra.status != "9") OR (user_extra.user_id = '.$current_conversion.')';
                                            $users2 = $DB_admin->query($sqlUSERS2);
                                                while ($rowUSERS2 = mysqli_fetch_array($users2)) {
                                                    if($current_conversion == $rowUSERS2['id']){
                                                        echo "<option value='".$rowUSERS2['id']."' selected>".$rowUSERS2['username']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowUSERS2['id']."'>".$rowUSERS2['username']."</option>";
                                                    }
                                                }
                                            ?>
                                            </select>
                                            <?php
                                        }?>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="status">Status:</label>
                                        <select class="form-control" name="status" id="status">
                                        <?php
                                            if($rowGD['type'] == 1){
                                                $sqlSTATUS = 'SELECT id, status FROM status WHERE cat = "Leads" ORDER BY id';
                                            } else if($rowGD['type'] >= 4 ){
                                                $sqlSTATUS = 'SELECT id, status FROM status WHERE cat = "Staff" ORDER BY id';
                                            } else {
                                                $sqlSTATUS = 'SELECT id, status FROM status WHERE cat = "Trader" ORDER BY id';
                                            }
                                            $status = $DB_admin->query($sqlSTATUS);
                                            while ($rowSTATUS = mysqli_fetch_array($status)) {
                                                if($rowGD['status'] == $rowSTATUS['id']){
                                                    echo "<option value='".$rowSTATUS['id']."' selected>".$rowSTATUS['status']."</option>";
                                                } else {
                                                    echo "<option value='".$rowSTATUS['id']."'>".$rowSTATUS['status']."</option>";
                                                }
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="type">Type:</label>
                                        <select class="form-control <?= (in_array($_SESSION["type"], ["Sales Agent", "Retention Agent"])) ? 'd-none' : '' ?>"
                                                name="type"
                                                id="type" <?= (in_array($_SESSION["type"], ["Sales Agent", "Retention Agent"])) ? 'readonly' : '' ?>>
                                        <?php
                                            if($_SESSION["type"] == "Sales Agent"){
                                                $sqlTYPE = 'SELECT id, name FROM type WHERE id < 4 ORDER BY id';
                                                $types = $DB_admin->query($sqlTYPE);
                                                while ($rowTYPE = mysqli_fetch_array($types)) {
                                                    if($rowGD['type'] == $rowTYPE['id']){
                                                        echo "<option value='".$rowTYPE['id']."' selected>".$rowTYPE['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowTYPE['id']."'>".$rowTYPE['name']."</option>";
                                                    }
                                                }
                                            } else if($_SESSION["type"] == "Retention Agent"){
                                                $sqlTYPE = 'SELECT id, name FROM type WHERE id < 4 AND id > 1 ORDER BY id';
                                                $types = $DB_admin->query($sqlTYPE);
                                                while ($rowTYPE = mysqli_fetch_array($types)) {
                                                    if($rowGD['type'] == $rowTYPE['id']){
                                                        echo "<option value='".$rowTYPE['id']."' selected>".$rowTYPE['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowTYPE['id']."'>".$rowTYPE['name']."</option>";
                                                    }
                                                }
                                            } else if($_SESSION["type"] == "Manager"){
                                                $sqlTYPE = 'SELECT id, name FROM type WHERE id NOT In ("4","5","6","7","8","13") ORDER BY id';
                                                $types = $DB_admin->query($sqlTYPE);
                                                while ($rowTYPE = mysqli_fetch_array($types)) {
                                                    if($rowGD['type'] == $rowTYPE['id']){
                                                        echo "<option value='".$rowTYPE['id']."' selected>".$rowTYPE['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowTYPE['id']."'>".$rowTYPE['name']."</option>";
                                                    }
                                                }
                                            } else {
                                                $sqlTYPE = 'SELECT id, name FROM type ORDER BY id';
                                                $types = $DB_admin->query($sqlTYPE);
                                                while ($rowTYPE = mysqli_fetch_array($types)) {
                                                    if($rowGD['type'] == $rowTYPE['id']){
                                                        echo "<option value='".$rowTYPE['id']."' selected>".$rowTYPE['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowTYPE['id']."'>".$rowTYPE['name']."</option>";
                                                    }
                                                }
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="followup">Follow-Up:</label>
                                        <input type="datetime-local" class="form-control" name="followup" id="followup"
                                               value="<?php echo $rowGD['followup'] ?>" required/>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="ip">IP:</label>
                                        <input type="text" class="form-control-plaintext" name="ip" id="ip" value="<?php echo $rowGD['ip']?>" disabled/>
                                    </div>
                                    <?php } ?>
                                </div>
                            </form>
                            <?php
                                }
                            ?>
                            <?php if($userID!=$_SESSION['id']) { ?>
                            <?php if(in_array($_SESSION["type"] ,Broker['upload_docs'])) { ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><?= $_L->T('Upload_Valid_ID','profile') ?>:</label>
                                        <span><?= $_L->T('valid_ID_note','profile') ?></span>
                                        <ul>
                                            <li><?= $_L->T('Passport_Copy','profile') ?></li>
                                            <li><?= $_L->T('National_ID','profile') ?></li>
                                            <li><?= $_L->T('Driving_License','profile') ?></li>
                                        </ul>
                                        <div class="container2">
                                            <img id="idc_img" src="media/<?php echo $idc_img; ?>" alt="user" class="rounded-circle" style="width:80px;height:80px;float: right;margin: -100px -5px 0px 0;">
                                            <div class="overlay <?php if($idc_img_verify == "1"){ echo "verify"; } ?>">
                                                <a href="#" class="icon" title="<?= $_L->T('User_Profile','profile') ?>">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <form method="post" action="" enctype="multipart/form-data" id="id">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="idfile" name="idfile">
                                                <label class="custom-file-label" for="customFile"><?= $_L->T('Choose_your_ID','profile') ?></label>
                                            </div>
                                        </form>
                                    </div>
                                     <div class="col-md-6">
                                        <label><?= $_L->T('Upload_Valid_Residence','profile') ?>:</label>
                                        <span><?= $_L->T('valid_Residence_note','profile') ?></span>
                                        <ul>
                                            <li><?= $_L->T('Electricity_Bill','profile') ?></li>
                                            <li><?= $_L->T('Gas_Bill','profile') ?>, <?= $_L->T('Water_Bill','profile') ?></li>
                                            <li><?= $_L->T('Mobile_Bill','profile') ?></li>
                                        </ul>
                                        <div class="container2">
                                            <img id="poa_img" src="media/<?php echo $poa_img; ?>" alt="user" class="rounded-circle" style="width:80px;height:80px;float: right;margin: -100px -5px 0px 0;">
                                            <div class="overlay <?php if($poa_img_verify == "1"){ echo "verify"; } ?>">
                                                <a href="#" class="icon" title="User Profile">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <form method="post" action="" enctype="multipart/form-data" id="poa">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="poafile" name="poafile">
                                                <label class="custom-file-label" for="customFile"><?= $_L->T('Choose_your_Bill','profile') ?></label>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="tab-pane fade" id="pills-2" role="tabpanel" aria-labelledby="pills-2-tab">
                            <?php
                                while ($rowEXP = mysqli_fetch_array($exp)) {
                                    $Rowjob_cat = $rowEXP['job_cat'];
                                    $Rowjob_title = $rowEXP['job_title'];
                                    $Rowexp_fx = $rowEXP['exp_fx'];
                                    $Rowexp_fx_year = $rowEXP['exp_fx_year'];
                                    $Rowexp_cfd = $rowEXP['exp_cfd'];
                                    $Rowexp_cfd_year = $rowEXP['exp_cfd_year'];
                                    $Rowincome = $rowEXP['income'];
                                    $Rowinvestment = $rowEXP['investment'];
                                    $Rowstrategy = $rowEXP['strategy'];
                                }
                            ?>
                                <form>
                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="job_cat">Job Category:</label>
                                            <select class="form-control" name="job_cat" id="job_cat">
                                            <?php
                                                $sqlJOBC = 'SELECT id, name FROM job_category';
                                                $job_cat = $DB_admin->query($sqlJOBC);
                                                while ($rowJOBC = mysqli_fetch_array($job_cat)) {
                                                    if($Rowjob_cat == $rowJOBC['id']){
                                                        echo "<option value='".$rowJOBC['id']."' selected>".$rowJOBC['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowJOBC['id']."'>".$rowJOBC['name']."</option>";
                                                    }
                                                }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="job_title">Job Title:</label>
                                            <input type="text" class="form-control" name="job_title" id="job_title" value="<?php echo $Rowjob_title; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-3 mb-3">
                                            <div class="custom-control custom-switch">
                                                <?php
                                                    if($Rowexp_fx == 1){
                                                        echo '<input type="checkbox" class="custom-control-input" name="exp_fx" id="exp_fx" checked/>';
                                                    } else {
                                                        echo '<input type="checkbox" class="custom-control-input" name="exp_fx" id="exp_fx" />';
                                                    }
                                                ?>
                                                <label class="custom-control-label" for="exp_fx">Experience in FX</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <select class="form-control" name="exp_fx_year" id="exp_fx_year">
                                            <?php
                                                $sqlEXYEAR = 'SELECT id, name FROM experience';
                                                $exyear = $DB_admin->query($sqlEXYEAR);
                                                while ($rowEXYEAR = mysqli_fetch_array($exyear)) {
                                                    if($Rowexp_fx_year == $rowEXYEAR['id']){
                                                        echo "<option value='".$rowEXYEAR['id']."' selected>".$rowEXYEAR['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowEXYEAR['id']."'>".$rowEXYEAR['name']."</option>";
                                                    }
                                                }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="custom-control custom-switch">
                                                <?php
                                                    if($Rowexp_cfd == 1){
                                                        echo '<input type="checkbox" class="custom-control-input" name="exp_cfd" id="exp_cfd" checked/>';
                                                    } else {
                                                        echo '<input type="checkbox" class="custom-control-input" name="exp_cfd" id="exp_cfd" />';
                                                    }
                                                ?>
                                                <label class="custom-control-label" for="exp_cfd">Experience in CFD</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <select class="form-control" name="exp_cfd_year" id="exp_cfd_year">
                                            <?php
                                                $sqlEXYEAR1 = 'SELECT id, name FROM experience';
                                                $exyear1 = $DB_admin->query($sqlEXYEAR1);
                                                while ($rowEXYEAR1 = mysqli_fetch_array($exyear1)) {
                                                    if($Rowexp_cfd_year == $rowEXYEAR1['id']){
                                                        echo "<option value='".$rowEXYEAR1['id']."' selected>".$rowEXYEAR1['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowEXYEAR1['id']."'>".$rowEXYEAR1['name']."</option>";
                                                    }
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-4 mb-3">
                                            <label for="income">Income:</label>
                                            <select class="form-control" name="income" id="income">
                                            <?php
                                                $sqlINCOME = 'SELECT id, name FROM income';
                                                $income = $DB_admin->query($sqlINCOME);
                                                while ($rowINCOME = mysqli_fetch_array($income)) {
                                                    if($Rowincome == $rowINCOME['id']){
                                                        echo "<option value='".$rowINCOME['id']."' selected>".$rowINCOME['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowINCOME['id']."'>".$rowINCOME['name']."</option>";
                                                    }
                                                }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="investment">Planned Investment Amount:</label>
                                            <select class="form-control" name="investment" id="investment">
                                            <?php
                                                $sqlINVEST = 'SELECT id, name FROM investment';
                                                $investment = $DB_admin->query($sqlINVEST);
                                                while ($rowINVEST = mysqli_fetch_array($investment)) {
                                                    if($Rowinvestment == $rowINVEST['id']){
                                                        echo "<option value='".$rowINVEST['id']."' selected>".$rowINVEST['name']."</option>";
                                                    } else {
                                                        echo "<option value='".$rowINVEST['id']."'>".$rowINVEST['name']."</option>";
                                                    }
                                                }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="strategy">Trading Strategy:</label>
                                            <input type="text" class="form-control" name="strategy" id="strategy" value="<?php echo $Rowstrategy; ?>" />
                                        </div>
                                    </div>
                                </form>
                        </div>
                        <div class="tab-pane fade" id="pills-3" role="tabpanel" aria-labelledby="pills-3-tab">
                            <?php
                                while ($rowSM = mysqli_fetch_array($sm)) {
                                    $rowSMbd = $rowSM['bd'];
                                    $rowSMwhatsapp = $rowSM['whatsapp'];
                                    $rowSMtelegram = $rowSM['telegram'];
                                    $rowSMfacebook = $rowSM['facebook'];
                                    $rowSMinstagram = $rowSM['instagram'];
                                    $rowSMtwitter = $rowSM['twitter'];
                                }
                            ?>
                                <form>
                                    <div class="form-row">
                                        <div class="col-md-4 mb-3">
                                            <label for="bd">Date of Birth:</label>
                                            <input type="text" class="form-control" name="bd" id="bd" value="<?php echo $rowSMbd; ?>" />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="whatsapp">WhatsApp:</label>
                                            <input type="text" class="form-control" name="whatsapp" id="whatsapp" value="<?php echo $rowSMwhatsapp; ?>" />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="telegram">Telegram:</label>
                                            <input type="text" class="form-control" name="telegram" id="telegram" value="<?php echo $rowSMtelegram; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-4 mb-3">
                                            <label for="facebook">Facebook:</label>
                                            <input type="text" class="form-control" name="facebook" id="facebook" value="<?php echo $rowSMfacebook; ?>" />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="instagram">Instagram:</label>
                                            <input type="text" class="form-control" name="instagram" id="instagram" value="<?php echo $rowSMinstagram; ?>" />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="twitter">Twitter:</label>
                                            <input type="text" class="form-control" name="twitter" id="twitter" value="<?php echo $rowSMtwitter; ?>" />
                                        </div>
                                    </div>
                                </form>
                            <hr>
                            <?php
                                while ($rowMT = mysqli_fetch_array($mt)) {
                                    $rowMTlead_src = $rowMT['lead_src'];
                                    $rowMTlead_camp = $rowMT['lead_camp'];
                                    $rowMTaffiliate = $rowMT['affiliate'];
                                }
                                if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Sales Agent" OR $_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent"){ 
                            ?>
                                <form>
                                    <div class="form-row">
                                        <div class="col-md-4 mb-3">
                                            <label for="lead_src">Source:</label>
                                            <?php if($_SESSION["type"] == "Sales Agent" OR $_SESSION["type"] == "Retention Manager" OR $_SESSION["type"] == "Retention Agent"){
                                                if($rowMTlead_src == "" OR $rowMTlead_src == " "){
                                            ?>      
                                                    <select class="form-control" name="lead_src" id="lead_src">
                                                        <option value=''>Choose One Source</option>
                                                        <?php
                                                            if($_SESSION["unit"] == "Turkish"){
                                                                echo "<option value='TurkNew-FB Ref.'>TurkNew-FB Ref.</option>";
                                                            } else if($_SESSION["unit"] == "Arabic"){
                                                                echo "<option value='ArabicNew-FB Ref.'>ArabicNew-FB Ref.</option>";
                                                            } else if($_SESSION["unit"] == "Farsi"){
                                                                echo "<option value='FarsiNew-FB Ref.'>FarsiNew-FB Ref.</option>";
                                                            } else if($_SESSION["unit"] == "Farsi2"){
                                                                echo "<option value='Farsi2New-FB Ref.'>Farsi2New-FB Ref.</option>";
                                                            } else if($_SESSION["unit"] == "Farsi"){
                                                                echo "<option value='STPLNew-FB Ref.'>STPLNew-FB Ref.</option>";
                                                            }
                                                        ?>
                                                        <option value='Telegram'>Telegram</option>
                                                        <option value='WhatsApp'>WhatsApp</option>
                                                        <option value='Instagram'>Instagram</option>
                                                        <option value='Reference'>Reference</option>
                                                        <option value='Other - '>Other</option>
                                                    </select>
                                                    <input type="text" class="form-control" style="display: none;" name="lead_src2" id="lead_src2" />
                                                    <!--<input type="text" class="form-control" name="lead_src" id="lead_src" value="<?php echo $rowMTlead_src; ?>" />-->
                                            <?php
                                                } else {
                                            ?>
                                                    <input type="text" class="form-control" name="lead_src" id="lead_src" value="<?php echo $rowMTlead_src; ?>" disabled/>
                                            <?php
                                                }
                                            } else if($_SESSION["type"] == "Admin" OR $_SESSION["type"] == "Manager") {
                                                if($rowMTlead_src == "" OR $rowMTlead_src == " "){
                                            ?>
                                                <select class="form-control" name="lead_src" id="lead_src">
                                                    <option value=''>Choose One Source</option>
                                                    <?php
                                                        if($_SESSION["unit"] == "Turkish"){
                                                            echo "<option value='TurkNew-FB Ref.'>TurkNew-FB Ref.</option>";
                                                        } else if($_SESSION["unit"] == "Arabic"){
                                                            echo "<option value='ArabicNew-FB Ref.'>ArabicNew-FB Ref.</option>";
                                                        } else if($_SESSION["unit"] == "Farsi"){
                                                            echo "<option value='FarsiNew-FB Ref.'>FarsiNew-FB Ref.</option>";
                                                        } else if($_SESSION["unit"] == "Farsi2"){
                                                            echo "<option value='Farsi2New-FB Ref.'>Farsi2New-FB Ref.</option>";
                                                        } else if($_SESSION["unit"] == "Farsi"){
                                                            echo "<option value='STPLNew-FB Ref.'>STPLNew-FB Ref.</option>";
                                                        }
                                                    ?>
                                                    <option value='Telegram'>Telegram</option>
                                                    <option value='WhatsApp'>WhatsApp</option>
                                                    <option value='Instagram'>Instagram</option>
                                                    <option value='Reference'>Reference</option>
                                                    <option value='Other - '>Other</option>
                                                </select>
                                                <input type="text" class="form-control" style="display: none;" name="lead_src2" id="lead_src2" />
                                                <!--<input type="text" class="form-control" name="lead_src" id="lead_src" value="<?php echo $rowMTlead_src; ?>" />-->
                                            <?php
                                                } else {
                                            ?>
                                                <input type="text" class="form-control" name="lead_src" id="lead_src" value="<?php echo $rowMTlead_src; ?>" disabled/>
                                            <?php
                                                }
                                            }?>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="lead_camp">Campaign:</label>
                                            <input type="text" class="form-control" name="lead_camp" id="lead_camp" value="<?php echo $rowMTlead_camp; ?>" />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="affiliate">Affiliate:</label>
                                            <input type="text" class="form-control" name="affiliate" id="affiliate" value="<?php echo $rowMTaffiliate; ?>" />
                                        </div>
                                    </div>
                                </form>
                                <?php
                                    } else {
                                ?>
                                <form>
                                    <div class="form-row">
                                        <div class="col-md-4 mb-3">
                                            <label for="lead_src">Source:</label>
                                            <input type="text" class="form-control" name="lead_src" id="lead_src" value="<?php echo $rowMTlead_src; ?>" hidden />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="lead_camp">Campaign:</label>
                                            <input type="text" class="form-control" name="lead_camp" id="lead_camp" value="<?php echo $rowMTlead_camp; ?>" hidden />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="affiliate">Affiliate:</label>
                                            <input type="text" class="form-control" name="affiliate" id="affiliate" value="<?php echo $rowMTaffiliate; ?>" hidden />
                                        </div>
                                    </div>
                                </form>
                                <?php } ?>
                        </div>
                        <div class="tab-pane fade" id="pills-4" role="tabpanel" aria-labelledby="pills-4-tab">
                            <form id="fupForm" name="form1" method="post">
                                <div class="form-row">
                                    <div class="col-md-4 col-sm-12 col-xs-12 mb-3">
                            			<label for="type">Type:</label>
                            			<select name="note_type" id="note_type" class="form-control">
                            				<option value="">Select</option>
                            				<?php if($_SESSION["type"] == "Backoffice"){ ?>
                            				<option value="Light Scalper">Light Scalper</option>
                            				<option value="Heavy Scalper">Heavy Scalper</option>
                            				<option value="Latency User">Latency User</option>
                            				<option value="A-Book Potential">A-Book Potential</option>
                            				<option value="On Watch List">On Watch List</option>
                            				<option value="Black Listed">Black Listed</option>
                            				<?php } else if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads" OR $_SESSION["type"] == "IB"){ ?>
                            				<option value="Support">Support Request</option>
                            				<option value="Finance">Financial Request</option>
                            				<option value="Other">Other</option>
                            				<?php } else { ?>
                            				<option value="Busy – Call Back Later">Busy – Call Back Later</option>
                            				<option value="Interested – Call Back Later">Interested – Call Back Later</option>
                            				<option value="Interested – Send Information">Interested – Send Information</option>
                            				<option value="No Interest – Reason Given">No Interest – Reason Given</option>
                            				<option value="Incorect Information">Incorect Information</option>
                            				<option value="Follow-Up Scheduled">Follow-Up Scheduled</option>
                            				<option value="Fully Interested">Fully Interested</option>
                            				<option value="Whatsapp">Whatsapp</option>
                            				<option value="Other">Other</option>
                            				<?php } ?>
                            			</select>
                            		</div>
                            		<div class="col-md-4 col-sm-12 col-xs-12 mb-3">
                            			<label for="note">Note:</label>
                            			<textarea class="form-control" id="note" placeholder="Note" name="note" autocomplete="off" />
                            		</div>
                            		<div class="col-md-4 col-sm-12 col-xs-12 mb-3">

                                        <?php if ($_SESSION["captcha_force"] ?? false) {  ?>
                                        <div id="captcha-comment" class="form-group pt-3 px-2 row">
                                            <div class="col-md-6 pt-3">
                                                <label for="phone">Captcha:</label>
                                                <span class="btn btn-default" id="doA-reCaptcha"><i class="fa fa-sync"></i></span>
                                                <input type="text" class="form-control" name="captcha" id="captcha" required="">
                                            </div>
                                            <div class="col-md-6 pt-3">
                                                <img class="captcha-img" src="/lib/captcha/captcha.php?_CAPTCHA&amp;t=0.80360100+1612764676" alt="CAPTCHA code">
                                            </div>
                                        </div>
                                        <?php }  ?>

                            		    <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo $userID; ?>" data-id="<?php echo $userID; ?>">
                            		    <label for="save">&nbsp;</label>
                            		    <input type="button" name="save" class="form-control btn bg-gradient-primary text-white" value="Save" id="butsave">
                            		</div>
                                </div>
                        	</form>
                        	<?php if($_SESSION["type"] !== "Trader" AND $_SESSION["type"] !== "Leads" AND $_SESSION["type"] !== "IB"){ ?>
                        	<hr>
                                <?= $table_notes_user ?>
                            <?php } ?>
                        </div>
                        <div class="tab-pane fade" id="pills-5" role="tabpanel" aria-labelledby="pills-5-tab">
                            <?php
                                while ($rowGD2 = mysqli_fetch_array($gd2)) {
                            ?>
                            <table id="data-table7" class="table table-hover  table-responsive-sm" style="width: 100%;">  
                                <thead> 
                                    <tr>
                                        <th>TP Login</th>
                                        <?php
                                            if($rowGD2['type'] == "3"){
                                                echo "<th>Name</th>";
                                            } else {
                                                echo "<th>Type</th>";
                                            }
                                        ?>
                                        <th>Equity</th>
                                        <th>Balance</th>
                                        <th>Active</th>
                                        <!--<th>Created By</th>
                                        <th>Created At</th>-->
                                        <th>Updated By</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        
                                        while ($rowMT4 = mysqli_fetch_array($mt4)) {
                                            echo "<tr>";
                                            echo "<td>".($rowMT4['login']??'-')."</td>";
                                            
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
                                                    echo "<td>".(number_format($rowEQUITY['EQUITY'], 2, '.', ',') ?? '-')."</td>";
                                                    echo "<td>".(number_format($rowEQUITY['BALANCE'], 2, '.', ',') ?? '-')."</td>";
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
                                                    $active = $DB_mt4->query($sqlACTIVE);
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
                                                if($groups) while ($rowGROUPS = mysqli_fetch_array($groups)) {
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
<a href='javascript:;' data-id=".$rowMT4['login']." data-server=".$rowMT4['server']." data-user-type = ".$rowMT4['user_id']." data-tp-type=".$mt_type." class='butmt4 tp-".$rowMT4['login']."'><i class='fas fa-eye'></i></a>
<a href='javascript:;' data-toggle='popover' title='Password' data-content='".$rowMT4['password']."' tabindex='0' role='button' class='popupover' data-trigger='focus'><i class='fas fa-key'></i></a>
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
                                        Deposit: <span class="size-26 green bold"><?php echo number_format($amountDP, 2, '.', ','); ?>$</span>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        Withdrawal: <span class="size-26 red bold"><?php echo number_format($amountWT, 2, '.', ','); ?>$</span>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        Bonus: <span class="size-26 yellow bold"><?php echo number_format($amountBO+$amountWBO, 2, '.', ','); ?>$</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="bold size-24">Close Trades</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        Total Orders: <span class="bold size-20"><?php echo $totalorders; ?></span>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        Total Volume: <span class="bold size-20"><?php echo number_format($volume, 2, '.', ','); ?>Lot</span>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        Winning Rate: <span class="bold size-20"><?php echo number_format($winningrate, 2, '.', '.'); ?>%</span>
                                        </br>
                                        <small>Losing Rate: <span class="bold size-20"><?php echo number_format($losingrate, 2, '.', '.'); ?>%</span></small>
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
                        <div class="tab-pane fade" id="pills-6" role="tabpanel" aria-labelledby="pills-6-tab">
                        <?php if($_SESSION["type"] !== "Trader" AND $_SESSION["type"] !== "Leads" AND $_SESSION["type"] !== "IB"){ ?>
                            <form id="expense" name="expense" method="post">
                                <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?= $userID ?>" required>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="type">Type</label>
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="">Please Select Type</option>
                                            <option>Agent Bonus</option>
                                            <option>IB Bonus</option>
                                            <option>Referral Bonus</option>
                                            <option>Exchange Fee</option>
                                            <option>Others</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="type">Payee <span class="text-danger payee-type"></span></label>
                                        <select class="form-control pre-payee" id="payee" name="payee" required></select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="amount">Amount</label>
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
                                        <label for="type">Comment</label>
                                        <textarea type="text" class="form-control" id="comment" name="comment"></textarea>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="type">Other Type</label>
                                        <input type="text" class="form-control" id="o_type" name="o_type" placeholder="Other Type" disabled>
                                        <button type="submit" class="btn btn-danger mt-2">Add Expense</button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div>
                                <?php
                                $total = $db->query("SELECT SUM(amount) as sum_amount FROM `users_expenses` WHERE user_id=$userID AND created_at BETWEEN '$startTime' and '$endTime';")[0]['sum_amount'];
                                ?>
                                Total Expenses from xxx to <strong><?= $startTime ?></strong> to <strong><?= $endTime ?></strong>: <span class="text-danger display-6 wrap-total-expense">$<?= GF::nf($total) ?></span>
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
    <div id="ex-edit" class="container">

        <?php if($_SESSION["captcha_force"] ?? false) { ?>
            <div id="captcha-detail" class="form-group pt-3 px-2 row">
                <div class="col-md-6 pt-3">
                    <label for="phone">Captcha:</label>
                    <span class="btn btn-default" id="doA-reCaptcha"><i class="fa fa-sync"></i></span>
                    <input type="text" class="form-control" name="captcha" id="captcha" required="">
                </div>
                <div class="col-md-6 pt-3">
                    <img class="captcha-img" src="/lib/captcha/captcha.php?_CAPTCHA&amp;t=0.80360100+1612764676" alt="CAPTCHA code">
                </div>
            </div>
        <?php } ?>

    </div>

    <div style="position: fixed; top: 25px; left: 0; min-width: 300px; display:none; z-index:999999;" id="boxer">
        <div class="toast fade hide dragit2" id="newaccount" data-autohide="false">
            <div class="toast-header bg-white">
                <strong class="mr-auto"><i class="fas fa-plus"></i> New Account</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-white">
                <div class="row">
                    <div class="col-sm-12 mb-3">
                        <label for="inputPlatform">Platform</label>
                        <select class="form-control" name="platform" id="platform" data-id="<?= $userID ?>" disabled>
                            <option value="">Please Select a Platform</option>
                            <option value='MT5'>MT5</option>
                            <option value='MT4'>MT4</option>
                        </select>
                    </div>
                    <div class="col-sm-12 mb-3">
            			<label for="inputType">Type</label>
            			<select class="form-control" id="type1" name="type1"  data-id="<?= $userID ?>" required>
            			    <option value="">Please Select a Type</option>
            			    <option value="2">Real</option>
                            <option value="1">Demo</option>
                        </select>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputComment">Group</label>
            			<select class="form-control" name="group" id="group" disabled>
                            <option value="">Please Select Type & Platform</option>
                            <!--<option value='demoKUSTDFIXUSD'>demoKUSTDFIXUSD</option>-->
                        </select>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputAmount">Amount</label>
            			<input type="number" class="form-control" id="damount" placeholder="Deposit Amount" name="damount">
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputCurrency">Currency</label>
            			<select class="form-control" id="currency" name="currency" required>
            			    <option value="1">USD</option>
                            <option value="2">EUR</option>
                        </select>
            		</div>
                </div>

                <?php if($_SESSION["captcha_force"] ?? false) { ?>
                    <div id="captcha-newaccount" class="form-group pt-3 px-2 row">
                        <div class="col-md-6 pt-3">
                            <label for="phone">Captcha:</label>
                            <span class="btn btn-default" id="doA-reCaptcha"><i class="fa fa-sync"></i></span>
                            <input type="text" class="form-control" name="captcha" id="captcha" required="">
                        </div>
                        <div class="col-md-6 pt-3">
                            <img class="captcha-img" src="/lib/captcha/captcha.php?_CAPTCHA&amp;t=0.80360100+1612764676" alt="CAPTCHA code">
                        </div>
                    </div>
                <?php } ?>

                <div>
                    <a class="btn bg-gradient-primary text-white newaccount" href="" data-id="<?php echo $userID; ?>">Submit</a>
                </div>
            </div>
        </div>
    </div>
    <div style="position: fixed; top: 25px; left: 0; min-width: 300px; display:none; z-index:999999;" id="boxer2">
        <div class="toast fade hide dragit2" id="existingaccount" data-autohide="false">
            <div class="toast-header bg-white">
                <strong class="mr-auto"><i class="fas fa-plus"></i> Add Existing TP</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-white">
                <div class="row">
                    <div class="col-sm-12 mb-3">
            			<label for="inputType">Type</label>
            			<select class="form-control" id="type2" name="type2"  data-id="<?= $userID ?>" required>
            			    <option value="2">Real</option>
                            <option value="1">Demo</option>
                        </select>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputPlatform">Platform</label>
            			<select class="form-control" name="platform2" id="platform2" data-id="<?= $userID ?>">
                            <option value='MT5'>MT5</option>
                            <option value='MT4'>MT4</option>
                        </select>
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputComment">Existing TP</label>
            			<input type="number" class="form-control" id="tp" placeholder="TP Number" name="tp">
            		</div>
            		<div class="col-sm-12 mb-3">
            			<label for="inputComment">Passwrod</label>
            			<input type="text" class="form-control" id="password" placeholder="Password" name="password">
            		</div>
                </div>

                <?php if($_SESSION["captcha_force"] ?? false) { ?>
                    <div id="captcha-existingaccount" class="form-group pt-3 px-2 row">
                        <div class="col-md-6 pt-3">
                            <label for="phone">Captcha:</label>
                            <span class="btn btn-default" id="doA-reCaptcha"><i class="fa fa-sync"></i></span>
                            <input type="text" class="form-control" name="captcha" id="captcha" required="">
                        </div>
                        <div class="col-md-6 pt-3">
                            <img class="captcha-img" src="/lib/captcha/captcha.php?_CAPTCHA&amp;t=0.80360100+1612764676" alt="CAPTCHA code">
                        </div>
                    </div>
                <?php } ?>

                <div>
                    <a class="btn bg-gradient-primary text-white existingaccount" href="" data-id="<?php echo $userID; ?>">Submit</a>
                </div>
            </div>
        </div>
    </div>
<script>
$(document).ready( function () {

    $('.popupover').popover();
    $("body").on("click touchstart", '.popupover', function() {
        $(this).popover("show");
        $('.popupover').not(this).popover("hide"); // hide other popovers
        return false;
    });
    $("body").on("click touchstart", function() {
        $('.popupover').popover("hide"); // hide all popovers when
    });
    
    var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
    /*
	$('#followup').datetimepicker({ 
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd HH:MM'
    });
    */
    $('#bd').datepicker({ 
        uiLibrary: 'bootstrap',
        iconsLibrary: 'fontawesome', 
        format: 'yyyy-mm-dd' 
    });

    var tradingaccounts = $('#data-table7').DataTable({  
		"responsive": true,
		"deferRender": true,
		"lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
		"order": [[ 5, "desc" ]],
    });

    var leadscehck = $('#lead_src').val();
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
                       //$('#damount').prop('disabled', disabled);
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
                       //$('#damount').prop('disabled', false);
                   }
                });
            }  
        }
        
    });
    
	$('#butsave').on('click', function() {
		var captcha = $('#captcha-comment #captcha').val();
		var note = $('#note').val();
		var note_type = $('#note_type').val();
		
        $('#note').val('');
		$('#note_type option:selected').removeAttr("selected");
		
		var user_id = $('#user_id').val();
		if(note!="" && note_type!="" && user_id!=""){
			$.ajax({
				url: "note-save.php",
				type: "POST",
				data: {
                    captcha: captcha,
					note: note,
					note_type: note_type,
					user_id: user_id
				},
				cache: false,
				success: function(dataResult){
					var dataResult = JSON.parse(dataResult);
					if(dataResult.statusCode==200){
						$("#butsave").removeAttr("disabled");
						$('#fupForm').find('input:text').val('');
						<?php if($_SESSION["type"] !== "Trader" AND $_SESSION["type"] !== "Leads" AND $_SESSION["type"] !== "IB"){ ?>
                        DT_notes_user.ajax.reload();
                        toastr.success("Note added successfully.");
                    <?php } ?>

                    } else if(dataResult.statusCode==201){
                        toastr.error("Error occured !");

                       $('#captcha-comment #captcha').val('');
                       $('#captcha-comment .captcha-img').fadeOut();
					}
					
				}
			});
		}
		else{
			alert('Please fill all the field!');
		}
	});
	
	<?php if($_SESSION["type"] == "Admin2"){ ?>
	$('.butdel').on('click', function() {
	    var user_id = $('#user_id').val();
		var note_id =  $(this).attr('data-id');
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
					var dataResult = JSON.parse(dataResult);
					if(dataResult.statusCode==200){
						$("#delete").show();
						<?php if($_SESSION["type"] !== "Trader" AND $_SESSION["type"] !== "Leads" AND $_SESSION["type"] !== "IB"){ ?>						
						var url = "user-edit.php?code="+user_id;
						$('.my-modal-cont').load(url,function(result){
						    $('#pills-tab li:last-child a').tab('show');
						    $('#notifications').html('<div class="alert alert-danger alert-dismissible" id="danger1">Note deleted successfully! <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a></div>');
						});
						<?php } ?>
					}
					else if(dataResult.statusCode==201){
					   alert("Error occured!");
					}
				}
			});
		}
		else{
			alert('Something is wrong!');
		}
	});
	
	<?php } ?>
	
    $("#lead_src").change(function () {
        var ls = $('#lead_src').val();
	    if(ls == "Other - "){
		    $("#lead_src2").show();
		} else {
		    $("#lead_src2").hide();
		}
    });
    
	$(":input").change(function () {
        $("#saveUser").prop("disabled", false);
    });
    
    $('#saveUser').on('click', function() {
		var captcha = $('#captcha-detail #captcha').val();
		var email = $('#email').val();
		var fname = $('#fname').val();
		var lname = $('#lname').val();
		var phone = $('#phone').val();
		var country = $('#country').val();
		var city = $('#city').val();
		var address = $('#address').val();
		var interests = $('#interests').val();
		var hobbies = $('#hobbies').val();
		var userunit = $('#userunit').val();
		var retention = $('#retention').val();
		var conversion = $('#conversion').val();
		var status = $('#status').val();
		var type = $('#type').val();
		var followup = $('#followup').val();
		
		var job_cat = $('#job_cat').val();
		var job_title = $('#job_title').val();
		var exp_fx = $('#exp_fx').val();
		var exp_fx_year = $('#exp_fx_year').val();
		var exp_cfd = $('#exp_cfd').val();
		var exp_cfd_year = $('#exp_cfd_year').val();
		var income = $('#income').val();
		var investment = $('#investment').val();
		var strategy = $('#strategy').val();
		
		var bd = $('#bd').val();
		var whatsapp = $('#whatsapp').val();
		var telegram = $('#telegram').val();
		var facebook = $('#facebook').val();
		var instagram = $('#instagram').val();
		var twitter = $('#twitter').val();
		
		var source = $('#lead_src').val();
		var source2 = $('#lead_src2').val();
		if(source == "Other - "){
		    source = "Other - "+source2;
		}
		var campaign = $('#lead_camp').val();
		var affiliate = $('#affiliate').val();
		
		
		if(email!="" && fname!="" && lname!="" && phone!="" && type!="" && conversion!="" && userunit!=""){
			$.ajax({
				url: "lead_save.php",
				type: "POST",
				data: {
                    captcha: captcha,
					email: email,
					fname: fname,
                    lname: lname,
                    phone: phone,
                    country: country,
                    city: city,
                    address: address,
                    interests: interests,
                    hobbies: hobbies,
                    userunit: userunit,
                    retention: retention,
                    conversion: conversion,
                    status: status,
                    type: type,
                    followup: followup,
                    job_cat: job_cat,
                    job_title: job_title,
                    exp_fx: exp_fx,
                    exp_fx_year: exp_fx_year,
                    exp_cfd: exp_cfd,
                    exp_cfd_year: exp_cfd_year,
                    income: income,
                    investment: investment,
                    strategy: strategy,
                    bd: bd,
                    whatsapp: whatsapp,
                    telegram: telegram,
                    facebook: facebook,
                    instagram: instagram,
                    twitter: twitter,
                    source: source,
                    campaign: campaign,
                    affiliate: affiliate,
					user_id: "<?php echo $userID; ?>"
				},
				cache: false,
				success: function(dataResult){
				    console.log(dataResult);
					var dataResult = JSON.parse(dataResult);
					if(dataResult.statusCode==200){
						$("#butsave").removeAttr("disabled");
						$('#fupForm').find('input:text').val('');
						$('#notifications').html('<div class="alert alert-success alert-dismissible" id="success">Lead details updated successfully! <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a></div>');
                        $("#saveUser").attr("disabled", "disabled");
                    }
					else if(dataResult.statusCode==201){
					   alert(dataResult.error);

                        $('#captcha-detail #captcha').val('');
                        $('#captcha-detail .captcha-img').fadeOut();
					}
					
				}
			});
		}
		else{
			alert('Please fill all the field!');
		}
	});
	
	$('#data-table7 tbody').on('click', '.butmt4', function () {
	//$(".butmt4").on("click", function(){
        var start = "<?php echo $startTime; ?>";
        var end = "<?php echo $endTime; ?>";
		mt4_id =  $(this).attr('data-id');
		user_id = <?php if($_SESSION["type"] !== "IB"){ echo '"'.$userID.'"'; } else { ?> $(this).attr('data-user-type');<?php } ?>;
		unit = "<?php echo $usunit; ?>";
		server = $(this).attr('data-server');
		mturl = $(this).attr('data-tp-type');
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
				}
			});
		}
		else{
			alert('Something is wrong!');
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
                
                var url = "user-edit.php?code="+userID;
    			$('.my-modal-cont').load(url,function(result){
    			    $('#pills-tab li:last-child a').tab('show');
    			    $('#notifications').html('<div class="alert alert-success alert-dismissible" id="danger1">Account # has been added succesfully! <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a></div>');
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
		            $('#notifications').html('<div class="alert alert-success alert-dismissible" id="danger1">Account # has been added succesfully! <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a></div>');
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
    
    $(".show-existingaccount").click(function() {
            if ( $('#boxer2').css('display') == 'none' || $('#boxer2').css("visibility") == "hidden"){
                $('#boxer2').show();
            }
            $("#existingaccount").toast('show');
    });
    var draggable2 = $('.dragit2'); //element 

    draggable2.on('mousedown', function(e){
    	var dr = $(this).addClass("drag").css("cursor","move");
    	height = dr.outerHeight();
    	width = dr.outerWidth();
    	ypos = dr.offset().top + height - e.pageY,
    	xpos = dr.offset().left + width - e.pageX;
    	$(document.body).on('mousemove', function(e){
    		var itop = e.pageY + ypos - height;
    		var ileft = e.pageX + xpos - width;
    		if(dr.hasClass("drag")){
    			dr.offset({top: itop,left: ileft});
    		}
    	}).on('mouseup', function(e){
    			dr.removeClass("drag");
    	});
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
            ranges: {
                '<?= $_L->T('Today', 'general') ?>': [moment(), moment().add(1, 'days')],
                '<?= $_L->T('Yesterday', 'general') ?>': [moment().subtract(1, 'days'), moment()],
                '<?= $_L->T('Last_7_Days', 'general') ?>': [moment().subtract(8, 'days'), moment()],
                '<?= $_L->T('Last_30_Days', 'general') ?>': [moment().subtract(31, 'days'), moment()],
                '<?= $_L->T('Last_90_Days', 'general') ?>': [moment().subtract(91, 'days'), moment()],
                '<?= $_L->T('This_Month', 'general') ?>': [moment().startOf('month'), moment().endOf('month')],
                '<?= $_L->T('Last_Month', 'general') ?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month').add(1, 'days')],
                '<?= $_L->T('Last_3_Month', 'general') ?>': [moment().subtract(3, 'month').startOf('month'), moment().endOf('month').add(1, 'days')],
                '<?= $_L->T('Total', 'general') ?>': [moment().subtract(300, 'month').startOf('month'), moment().endOf('month').add(1, 'days')]
            }
        }, function(start, end, label){
            var user_id = $('#user_id').val();
            <?php if($_GET["type"] !== "ib"){ ?>
            var url = "user-details.php?code="+user_id+"&startTime="+start.format('YYYY-MM-DD')+"&endTime="+end.format('YYYY-MM-DD');
            <?php } else { ?>
            var url = "user-details.php?code="+user_id+"&type=ib&startTime="+start.format('YYYY-MM-DD')+"&endTime="+end.format('YYYY-MM-DD');
            <?php } ?>
            tradingaccounts.destroy();
			$('.my-modal-cont').load(url,function(result){
			    $('#pills-tab li a').eq(-1).tab('show');
			});
            $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            
            //window.location.href = "welcome.php?startTime="+start.format('YYYY-MM-DD')+"&endTime="+end.format('YYYY-MM-DD');  
        });
    
        //cb(start, end);
    
    });

<?php if($userID!=$_SESSION['id']) { ?>
    $("#idfile").change(function(){
        var fd = new FormData();
        var files = $('#idfile')[0].files[0];
        fd.append('file',files);
        fd.append('cat', 'ID');
        fd.append('uid',  <?= $userID ?>);

        $.ajax({
            url: 'upload.php',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){
                if(response == 1){
                    $('#idc_img').attr('src','media/'+files.name)
                    alert('success');
                } else {
                    alert('file not uploaded');
                }
            },
        });
    });

    $("#poafile").change(function(){
        var fd = new FormData();
        var files = $('#poafile')[0].files[0];
        fd.append('file',files);
        fd.append('cat', 'Bill');
        fd.append('uid', <?= $userID ?>);
        $.ajax({
            url: 'upload.php',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){
                if(response != 0){
                    $('#poa_img').attr('src','media/'+files.name)
                    alert('success');
                }else{
                    alert('file not uploaded');
                }
            },
        });
    });
<?php } ?>

});
</script>
    <?= factory::footer(); ?>
<?php
    $DB_admin->close();
?>
</body>
</html>