<?php
    global $db;
    $task = $db->selectId('tasks',$_POST['id']);
    
    $tid = $_POST['id'];
    
    $where = "status NOT IN(9,16,17) AND type>3";
    $staffs = $db->select('user_extra',$where, '*',null,'fname ASC');
    
    $where = 'tid='.$_POST['id'];
    $messages = $db->select('messages',$where, '*',null,'id ASC');
    
    $where = 'type NOT IN ("Leads","Trader","IB")';
    $users = $db->select('users',$where, '*',null,'id ASC');
    
    $assigned_to = explode(',',$task["assigned_to"]);
    
    $priority = array(
        "1" => array(
            "text" => "Very Low",
            "bar" => "20",
            "bg" => "bg-gradient-success",
        ),
        "2" => array(
            "text" => "Low",
            "bar" => "40",
            "bg" => "bg-gradient-teal",
        ),
        "3" => array(
            "text" => "Medium",
            "bar" => "50",
            "bg" => "bg-gradient-info",
        ),
        "4" => array(
            "text" => "High",
            "bar" => "70",
            "bg" => "bg-gradient-primary",
        ),
        "5" => array(
            "text" => "Very High",
            "bar" => "80",
            "bg" => "bg-gradient-warning",
        ),
        "6" => array(
            "text" => "Urgent",
            "bar" => "100",
            "bg" => "bg-gradient-danger",
        )
    );
    
    $status = array(
        "1" => array(
            "text" => "New",
            "bg" => "bg-gradient-primary",
        ),
        "2" => array(
            "text" => "In Progress",
            "bg" => "bg-gradient-info",
        ),
        "3" => array(
            "text" => "Pending",
            "bg" => "bg-gradient-warning",
        ),
        "4" => array(
            "text" => "On-Hold",
            "bg" => "bg-gradient-dark",
        ),
        "5" => array(
            "text" => "Done",
            "bg" => "bg-gradient-success",
        ),
        "6" => array(
            "text" => "Cancel",
            "bg" => "bg-gradient-danger",
        ),
        "7" => array(
            "text" => "Archive",
            "bg" => "bg-gradient-danger",
        ),
    );
    
    $type = array(
        "1" => array(
            "text" => "Do",
            "bg" => "bg-gradient-danger",
        ),
        "2" => array(
            "text" => "Check",
            "bg" => "bg-gradient-info",
        ),
        "3" => array(
            "text" => "Verify",
            "bg" => "bg-gradient-success",
        ),
        "4" => array(
            "text" => "Analyse",
            "bg" => "bg-gradient-primary",
        ),
        "5" => array(
            "text" => "Manage",
            "bg" => "bg-gradient-warning",
        ),
        "6" => array(
            "text" => "Prepare",
            "bg" => "bg-gradient-cyan",
        ),
        "7" => array(
            "text" => "Report",
            "bg" => "bg-gradient-dark",
        ),
    );
    
    if ($_POST['view']) {
?>
    <span class="badge badge-sm bg-gradient-danger font-size-60p p-2 text-white waves-effect waves-light update"><i class="fas fa-edit">&nbsp;</i> Update</span>
    <span class="badge badge-sm bg-gradient-info font-size-60p p-2 text-white waves-effect waves-light info" style="display:none;"><i class="fas fa-info">&nbsp;</i> View</span>
    <?php if($task["status"] !== "5"){ ?>
    <span class="badge badge-sm bg-gradient-success font-size-60p p-2 text-white waves-effect waves-light finish" data-id="<?php echo $tid; ?>"><i class="fas fa-check">&nbsp;</i> Finish</span>
    <?php } ?>
    <div class="row viewForm">
        <div class="col-md-6">
            <span class="badge <?php echo $status[$task["status"]]["bg"]; ?> align-middle font-size-70p pl-1 text-white"><?php echo $status[$task["status"]]["text"]; ?></span>
            <span class="badge <?php echo $type[$task["type"]]["bg"]; ?> align-middle font-size-70p pl-1 text-white"><?php echo $type[$task["type"]]["text"]; ?></span>
            <span class="badge <?php echo $priority[$task["priority"]]["bg"]; ?> align-middle font-size-70p pl-1 text-white"><?php echo $priority[$task["priority"]]["text"]; ?></span>
            <span>
                Assigned To: 
                <?php 
                    foreach ($assigned_to as $key => $result) {
                        $where = 'user_id = "'.$result.'" AND type = "avatar"';
                        $avatars = $db->select('media',$where, '*',null,'id ASC');
                        if($avatars) foreach ($avatars as $avatar) {
                            echo '<img src="media/'.$avatar['media'].'" class="rounded-circle" width="50px" height="50px" style="margin-right:-27px;" />';
                        }
                    } 
                ?>
            </span>
        </div>
        <div class="col-md-6 text-right">
            <?php echo $task["created_at"]; ?>
        </div>
        <div class="col-md-6">
            <p class="inbox-item-text mt-2 mb-0"><?php echo $task["description"]; ?></p>
        </div>
    </div>
    <form class="updateForm" style="display: none;">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputType">Type</label>
                <select id="inputType" class="form-control form-select3" name="inputType" required>
                    <option value="">Choose...</option>
                    <option value="1" <?php if($task["type"] == 1) echo "selected"; ?>>Do</option>
                    <option value="2" <?php if($task["type"] == 2) echo "selected"; ?>>Check</option>
                    <option value="3" <?php if($task["type"] == 3) echo "selected"; ?>>Verify</option>
                    <option value="4" <?php if($task["type"] == 4) echo "selected"; ?>>Analyse</option>
                    <option value="5" <?php if($task["type"] == 5) echo "selected"; ?>>Manage</option>
                    <option value="6" <?php if($task["type"] == 6) echo "selected"; ?>>Prepare</option>
                    <option value="7" <?php if($task["type"] == 7) echo "selected"; ?>>Report</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="inputPriority">Priority</label>
                <select id="inputPriority" class="form-control form-select3" name="inputPriority" required>
                    <option value="">Choose...</option>
                    <option value="1" <?php if($task["priority"] == 1) echo "selected"; ?>>Very Low</option>
                    <option value="2" <?php if($task["priority"] == 2) echo "selected"; ?>>Low</option>
                    <option value="3" <?php if($task["priority"] == 3) echo "selected"; ?>>Medium</option>
                    <option value="4" <?php if($task["priority"] == 4) echo "selected"; ?>>High</option>
                    <option value="5" <?php if($task["priority"] == 5) echo "selected"; ?>>Very High</option>
                    <option value="6" <?php if($task["priority"] == 6) echo "selected"; ?>>Urgent</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="inputAssigned">Assigned To</label>
                <select id="inputAssigned" class="form-control form-select3" name="inputAssigned" multiple required>
                    <option value="">Choose...</option>
                    <?php if ($staffs) foreach($staffs as $staff) { ?>
                    <option value="<?php echo $staff["user_id"]; ?>" <?php if(in_array($staff["user_id"], $assigned_to)) echo "selected"; ?>><?php echo $staff["fname"]." ".$staff["lname"]; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputTitle">Title</label>
                <input type="text" class="form-control" id="inputTitle" name="inputTitle" placeholder="Title" value="<?php echo $task["name"]; ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="inputDeadline">Deadline</label>
                <input type="text" class="form-control" id="inputDeadline" name="inputDeadline" placeholder="Deadline" required>
            </div>
        </div>
        <div class="form-group">
            <label for="inputDescription">Description</label>
            <textarea class="form-control summernote" id="inputDescription" name="inputDescription" placeholder="Please write the description for this task" required><?php echo $task["description"]; ?></textarea>
        </div>
        <button class="btn bg-gradient-primary text-white waves-effect waves-light" id="updateTask" data-id="<?php echo $tid; ?>"> <span>Save</span> <i class="fab fa-telegram-plane m-l-10"></i> </button>
    </form>
    
    <hr></hr>
    
    <div class="row">
        <div class="col-md-12" id="boxScroll">
            <?php
                if($messages) foreach ($messages as $message) {
                    echo '<div class="row">';
                    $where = 'user_id = "'.$message['sender'].'" AND type = "avatar"';
                    $avatars = $db->select('media',$where, '*',null,'id ASC');
                    if($avatars) foreach ($avatars as $avatar) {
                        echo '<div class="pl-3 pt-1"><img src="media/'.$avatar['media'].'" class="rounded-circle" width="80px" height="80px" /></div>';
                    }
                    //echo '<div class="pl-3 pt-1"><img src="media/mt5-logo.png" alt="user" class="rounded-circle" style="width:80px"></div>';
                    echo '<div class="col-md-10 talk-bubble left-top bg-gradient-info text-white pl-3 pb-2 pt-2">';
                    echo "<span>".$message['updated_at']."</span>";
                    echo "<div>".$message['description']."</div>";
                    //echo "<div>".$message['updated_at']."</div>";
                    echo '<div class="text-right"><span class="badge badge-sm bg-gradient-white font-size-60p p-2 text-dark waves-effect waves-light replay" data-id="'.$message['sender'].'"><i class="fas fa-reply">&nbsp;</i> Reply</span></div>';
                    echo '</div>';
                    echo '</div>';
               }
            ?>
        </div>
    </div>
    
    <hr></hr>
    <div class="comment-form">
        <div class="form-group" id="test">
            <select class="form-control form-select3" data-placeholder="Choose ..." id="pmusers" name="pmusers[]" multiple required>
                <?php
                    if($users) foreach ($users as $user) {
                ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
                <?php
                    }
                ?>
            </select>
        </div>
    
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Subject" id="pmsubject" name="pmsubject" value="<?php echo $task["name"]; ?>" hidden>
        </div>
        
        <div class="form-group">
            <textarea class="summernote" id="pmtext" name="pmtext">
        
            </textarea>
        </div>
        <div class="btn-toolbar form-group mb-0">
            <button class="btn bg-gradient-primary text-white waves-effect waves-light" id="sendpmbtn" data-id="<?php echo $tid; ?>"> <span>Send</span> <i class="fab fa-telegram-plane m-l-10"></i> </button>
        </div>
    </div>
<?php 
    } 
?>