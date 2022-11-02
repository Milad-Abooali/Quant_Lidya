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

    if($_SESSION["type"] == "Admin"){
        $sql = 'SELECT id, username FROM users WHERE id != "'.$_SESSION["id"].'" AND type NOT IN ("Leads","Trader","IB")';
    } else if($_SESSION["type"] == "Manager" OR $_SESSION["type"] == "Retention Agent" OR $_SESSION["type"] == "Sales Agent"){
        $sql = 'SELECT id, username FROM users WHERE id != "'.$_SESSION["id"].'" AND type NOT IN ("Leads","Trader","IB") AND unit = "'.$_SESSION["unit"].'"';
    } 
    //$sql = 'SELECT id, username FROM users WHERE id != "'.$_SESSION["id"].'" AND type NOT IN ("Leads","TRADER","IB")';
    
    if (isset($_GET['pid']) )
    {
        $pid = $_GET['pid'];
    } else {
        $pid = "0";
    }
    if (isset($_POST['pmusers']) )
    {
        $pmusers = json_encode($_POST['pmusers']);
        $pmusers = str_replace('"', '', $pmusers);
        echo $pmusers;
        $pmsubject = $_POST['pmsubject'];
        $pmtext = $_POST['pmtext'];
        $user_id = $_SESSION["id"];
        $date = date('Y-m-d H:i:s');
        $tid = $_POST['tid'];
        
        $sql2 = "INSERT INTO messages(`pid`, `title`, `description`, `sender`, `reciever`, `reader`, `created_at`, `created_by`, `updated_at`, `updated_by`, `tid`) VALUES ('$pid','$pmsubject','$pmtext', '$user_id', '$pmusers', '[]', '$date', '$user_id', '$date', '$user_id', '$tid')";
    	//echo $sql2;
    	if (mysqli_query($DB_admin, $sql2)) {
            $inserted_id = mysqli_insert_id($DB_admin);
            // Add actLog
            global $actLog; $actLog->add('PM',$inserted_id,1,"reciever: $user_id");

    		echo "Done";
    	} 
    	else {
    	    //echo mysqli_error($DB_admin);
    		echo "Not Done";
    	}
    }
    
    if (isset($_GET['pid']) ){ 
        $sqlPM = 'SELECT title,sender FROM messages WHERE id = "'.$_GET['pid'].'"';
        $repPM = $DB_admin->query($sqlPM);
        if ($repPM) while ($rowPM = mysqli_fetch_array($repPM)) {
    ?>
        <div class="form-group d-none" id="test">
            <input type="text" class="form-control" placeholder="User" id="pmusers" name="pmusers" value="[<?php echo $rowPM['sender'] ?>]" disabled>
        </div>
        <div class="form-group d-none">
            <input type="text" class="form-control" placeholder="Subject" id="pmsubject" name="pmsubject"value="<?php echo $rowPM['title'] ?>" disabled>
        </div>
        <div class="form-group">
            <textarea class="summernote" id="pmtext" name="pmtext">
        
            </textarea>
        </div>
        
        <div class="btn-toolbar form-group mb-0">
            <div class="">
                <button class="btn bg-gradient-primary text-white waves-effect waves-light sendrepbtn" id="<?php echo $_GET['pid']; ?>"> <span>Send</span> <i class="fab fa-telegram-plane m-l-10"></i> </button>
            </div>
        </div>
    <?php 
        }
    } else { 
    ?>
        <div class="form-group" id="test">
            <select class="form-control" multiple="multiple" multiple data-placeholder="Choose ..." id="pmusers" name="pmusers[]">
                <option value="1">Support Department</option>
                <option value="1">Back Office Department</option>
                <?php
                    if($_SESSION["type"] == "Trader" OR $_SESSION["type"] == "Leads" OR $_SESSION["type"] == "IB"){
                        $sqlRet = 'SELECT retention FROM user_extra WHERE user_id = "'.$_SESSION["id"].'"';
                        //echo $sqlRet;
                        $userRetention = $DB_admin->query($sqlRet);
                        if($userRetention) while ($rowUserRet = mysqli_fetch_array($userRetention)) {
                            $sqlUserReten = 'SELECT id, username FROM users WHERE id = "'.$rowUserRet["retention"].'"';
                            $userRet = $DB_admin->query($sqlUserReten);
                            //echo $sql;
                            while ($rowUser = mysqli_fetch_array($userRet)) {
                                ?>
                                <option value="<?php echo $rowUser['id']; ?>"><?php echo $rowUser['username']; ?></option>
                                <?php
                            }
                        }
                ?>
                <?php
                    } else {
                        $userList = $DB_admin->query($sql);
                        if($userList) while ($rowUser = mysqli_fetch_array($userList)) {
                            ?>
                            <option value="<?php echo $rowUser['id']; ?>"><?php echo $rowUser['username']; ?></option>
                            <?php
                        }
                    }
                ?>
            </select>
        </div>
    
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Subject" id="pmsubject" name="pmsubject">
        </div>
        
        <div class="form-group">
            <textarea class="summernote" id="pmtext" name="pmtext">
        
            </textarea>
        </div>
        
        <div class="btn-toolbar form-group mb-0">
            <div class="">
                <button class="btn bg-gradient-primary text-white waves-effect waves-light" id="sendpmbtn"> <span>Send</span> <i class="fab fa-telegram-plane m-l-10"></i> </button>
            </div>
        </div>
    <?php } ?>
<script>
    
</script>
<?php mysqli_close($DB_admin); ?>