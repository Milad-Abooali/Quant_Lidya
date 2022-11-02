<?php
    global $db;
    $where = "status NOT IN(9,16,17) AND type>3";
    $staffs = $db->select('user_extra',$where, '*',null,'fname ASC');
    
    $where = "status!=7 AND FIND_IN_SET('".$_SESSION['id']."', assigned_to)";
    $tasks = $db->select('tasks',$where, '*',null,'id ASC');
    
    function addToString($str, $item) {
        $parts = explode(',', $str);
        array_push($parts, $item);
        return implode(',', $parts);
    }
    
    function removeFromString($str, $item) {
        $parts = explode(',', $str);
        while(($i = array_search($item, $parts)) !== false) {
            unset($parts[$i]);
        }
        return implode(',', $parts);
    }

    if ($tasks) foreach($tasks as $task) {
    
    $pin = explode(',',$task["pin"]);
        
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
    
    if (in_array($_SESSION['id'], $pin)) {
      $unpin_task = removeFromString($task['pin'], $_SESSION['id']);
      $pin_task = $task['pin'];
    } else {
      $pin_task = addToString($task['pin'], $_SESSION['id']);
      $unpin_task = $task['pin'];
    }
?>
<div class="inbox-item bg-white border-top <?php if (in_array($_SESSION['id'], $pin)) { echo 'pin'; } ?>" id="in">
    <div class="row">
        <div class="col-md-6">
            <span class="badge <?php echo $status[$task["status"]]["bg"]; ?> align-middle font-size-70p pl-1 text-white"><?php echo $status[$task["status"]]["text"]; ?></span>
            <span class="badge <?php echo $type[$task["type"]]["bg"]; ?> align-middle font-size-70p pl-1 text-white"><?php echo $type[$task["type"]]["text"]; ?></span>
            <span class="badge <?php echo $priority[$task["priority"]]["bg"]; ?> align-middle font-size-70p pl-1 text-white"><?php echo $priority[$task["priority"]]["text"]; ?></span>
        </div>
        <div class="col-md-6 text-right">
            <span class="btn btn-sm btn-danger font-size-60p waves-effect waves-light "><i class="fas fa-archive">&nbsp;</i></span>
            <span class="btn btn-sm btn-info font-size-60p waves-effect waves-light view_task" data-id="<?php echo $task["id"]; ?>" data-name="<?php echo $task["name"]; ?>"><i class="fas fa-binoculars">&nbsp;</i></span>
            <?php if (in_array($_SESSION['id'], $pin)) { ?>
                <span class="btn btn-sm btn-warning font-size-60p waves-effect waves-light unpin_task" data-id="<?php echo $task["id"]; ?>" data-unpin="<?php echo $unpin_task; ?>"><i class="fas fa-unlock">&nbsp;</i></span>
            <?php } else { ?>
                <span class="btn btn-sm btn-outline-warning font-size-60p waves-effect waves-light pin_task" data-id="<?php echo $task["id"]; ?>" data-pin="<?php echo $pin_task; ?>"><i class="fas fa-lock">&nbsp;</i></span>
            <?php } ?>        
        </div>
    </div>
    <h6 class="inbox-item-author mt-0 mb-1">
        <?php echo $task["name"]; ?> 
    </h6>
    <!--
    <div class="progress float-right priority">
      <div class="progress-bar " role="progressbar" style="width: <?php echo $priority[$task["priority"]]["bar"]; ?>%" aria-valuenow="<?php echo $priority[$task["priority"]]["bar"]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    -->
    <p class="inbox-item-text text-muted mb-0"><?php echo $task["description"]; ?></p>
    <p class="inbox-item-date text-muted m-0"><?php echo $task["created_at"]; ?></p>
</div>

<?php
    }
?>
<div id="formTask" class="d-none">
    <form>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputType">Type</label>
                <select id="inputType" class="form-control form-select3" name="inputType" required>
                    <option value="">Choose...</option>
                    <option value="1">Do</option>
                    <option value="2">Check</option>
                    <option value="3">Verify</option>
                    <option value="4">Analyse</option>
                    <option value="5">Manage</option>
                    <option value="6">Prepare</option>
                    <option value="7">Report</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="inputPriority">Priority</label>
                <select id="inputPriority" class="form-control form-select3" name="inputPriority" required>
                    <option value="">Choose...</option>
                    <option value="1">Very Low</option>
                    <option value="2">Low</option>
                    <option value="3">Medium</option>
                    <option value="4">High</option>
                    <option value="5">Very High</option>
                    <option value="6">Urgent</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="inputAssigned">Assigned To</label>
                <select id="inputAssigned" class="form-control form-select3" name="inputAssigned" multiple required>
                    <option value="">Choose...</option>
                    <?php if ($staffs) foreach($staffs as $staff) { ?>
                    <option value="<?php echo $staff["user_id"]; ?>"><?php echo $staff["fname"]." ".$staff["lname"]; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputTitle">Title</label>
                <input type="text" class="form-control" id="inputTitle" name="inputTitle" placeholder="Title" required>
            </div>
            <div class="form-group col-md-6">
                <label for="inputDeadline">Deadline</label>
                <input type="text" class="form-control" id="inputDeadline" name="inputDeadline" placeholder="Deadline" required>
            </div>
        </div>
        <div class="form-group">
            <label for="inputDescription">Description</label>
            <textarea class="form-control" id="inputDescription" name="inputDescription" placeholder="Please write the description for this task" required></textarea>
        </div>
    </form>
</div>
<!--<?= factory::footer() ?>-->
<script>
    var IsAjaxExecuting= false;
    $("body").on("click","#new_task", function(e) {
        $('#myModal .my-modal-cont').html("");
        $('#myModal .my-modal-cont').html($("#formTask").html());
        $('#myModal .modal-title').html('Add A New Task');
        $('#myModal .modal-footer').html('<button type="submit" class="btn bg-gradient-primary text-white" id="addTask">Add</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
        $('#myModal').modal({show:true});
        //$('#formTask .form-select3').selectpicker('destroy');
        $('#myModal .my-modal-cont .form-select3').selectpicker({
            tickIcon: 'fas fa-check',
            liveSearch: true
        });
        $('#inputDescription').summernote('destroy');
        $('#inputDescription').summernote({
            height: 100,        // set editor height
            minHeight: null,    // set minimum height of editor
            maxHeight: null,    // set maximum height of editor
            focus: false        // set focus to editable area after initializing summernote
        });
    });

    $("body").on("click","#addTask", function(e) {
        if($('#inputAssigned').val()!="" && $('#inputTitle').val()!="" && $('#inputDescription').val()!="" && $('#inputDeadline').val()!="" && $('#inputType').val()!="" && $('#inputPriority').val()!=""){
            let data = {
            	'manager': <?php echo $_SESSION["id"]; ?>,
            	'assigned_to': $('#inputAssigned').val(),
            	'name': $('#inputTitle').val(),
            	'description': $('#inputDescription').val(),
            	'deadline': $('#inputDeadline').val(),
            	'type': $('#inputType').val(),
            	'priority': $('#inputPriority').val(),
            }
            ajaxCall ('tasks', 'addTask',data, function(response){
                let resObj = JSON.parse(response);
                if (resObj.e) { // ERROR
                    toastr.error(resObj.e);
                } else if (resObj.res) { // SUCCESS
                    //$("#quotes").html( "" );
                    $("#wg-tasks-1 .reload").trigger("click");
                    $('#myModal').modal('toggle');
                    console.log(resObj.res);
                }
            });
        } else {
            
        }
    });
    
    $("body").on("click",".view_task", function(e) {
        if(IsAjaxExecuting) return; // new code
        IsAjaxExecuting = true; // new code
        let data = {
        	'id': $(this).attr("data-id"),
        	'view': 1
        }
        let title = $(this).attr("data-name");
        ajaxCall ('tasks', 'viewTask',data, function(response){
            $('#myModal .my-modal-cont').html("");
            $('#myModal .my-modal-cont').html(response);
            $('#myModal .modal-title').html(title);
            //$('#myModal .modal-footer').html('<button type="submit" class="btn bg-gradient-primary text-white" id="updateTask">Update</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
            $('#myModal').modal({show:true});
            //$('#formTask .form-select3').selectpicker('destroy');
            $('#myModal .my-modal-cont .form-select3').selectpicker({
                tickIcon: 'fas fa-check',
                liveSearch: true
            });
            $('.summernote').summernote('destroy');
            $('.summernote').summernote({
                height: 100,        // set editor height
                minHeight: null,    // set minimum height of editor
                maxHeight: null,    // set maximum height of editor
                focus: false        // set focus to editable area after initializing summernote
            });
            $('#boxScroll').mCustomScrollbar({
                setHeight: '250px',
                theme: 'minimal-dark'
            });
            IsAjaxExecuting = false;
        });
    });
    
    $("body").on("click",".update_task", function(e) {
        let data = {
        	'id': $(this).attr("data-id"),
        	'view': 1
        }
        let title = $(this).attr("data-name");
        ajaxCall ('tasks', 'updateTask',data, function(response){
            
        });
    });
    
    $("body").on("click",".update", function(e) {
        $( ".viewForm" ).toggle();
        $( ".update" ).toggle();
        $( ".info" ).toggle();
        $( ".updateForm" ).toggle();
    });
    
    $("body").on("click",".info", function(e) {
        $( ".viewForm" ).toggle();
        $( ".update" ).toggle();
        $( ".info" ).toggle();
        $( ".updateForm" ).toggle();
    });
    
    $("body").on("click",".finish", function(e) {
        let data = {
            'id': $(this).attr("data-id"),
        }
        ajaxCall ('tasks', 'finishTask',data, function(response){
            let resObj = JSON.parse(response);
            if (resObj.e) { // ERROR
                toastr.error(resObj.e);
            } else if (resObj.res) { // SUCCESS
                $("#wg-tasks-1 .reload").trigger("click");
                $('#myModal').modal('toggle');
                console.log(resObj.res);
            }
        });
    });
    
    $("body").on("click",".pin_task", function(e) {
        let data = {
            'id': $(this).attr("data-id"),
            'pin':$(this).attr("data-pin"),
        }
        ajaxCall ('tasks', 'pinTask',data, function(response){
            let resObj = JSON.parse(response);
            if (resObj.e) { // ERROR
                toastr.error(resObj.e);
            } else if (resObj.res) { // SUCCESS
                $("#wg-tasks-1 .reload").trigger("click");
                console.log(resObj.res);
            }
        });
    });
    
    $("body").on("click",".unpin_task", function(e) {
        let data = {
            'id': $(this).attr("data-id"),
            'pin':$(this).attr("data-unpin"),
        }
        ajaxCall ('tasks', 'pinTask',data, function(response){
            let resObj = JSON.parse(response);
            if (resObj.e) { // ERROR
                toastr.error(resObj.e);
            } else if (resObj.res) { // SUCCESS
                $("#wg-tasks-1 .reload").trigger("click");
                console.log(resObj.res);
            }
        });
    });
    
    $("body").on('click','#sendpmbtn', function() {
        if(IsAjaxExecuting) return; // new code
        IsAjaxExecuting = true; // new code
        var pmusers = $( "#pmusers" ).val();
        var pmsubject = $( "#pmsubject" ).val();
        var pmtext = $( "#pmtext" ).val();
        var tid = $(this).attr("data-id");
        $.ajax({
            method: 'post',
            url: 'sendpm.php',
            data: {
                'pmusers': pmusers,
                'pmsubject': pmsubject,
                'pmtext': pmtext,
                'tid': tid
            },
            success: function(data) {
                IsAjaxExecuting = false;
                toastr.success("Comment has been sent", "New comment has been sent");
            }
        });
    });
                
    $("body").on('click','.sendrepbtn', function() {
        if(IsAjaxExecuting) return; // new code
        IsAjaxExecuting = true; // new code
        var pmusers = $( "#pmusers" ).val();
        var pmsubject = $( "#pmsubject" ).val();
        var pmtext = $( "#pmtext" ).val();
        var pmid = $(this).attr('id');

        $.ajax({
            method: 'post',
            url: 'sendpm.php?pid='+pmid,
            data: {
                'pmusers': pmusers,
                'pmsubject': pmsubject,
                'pmtext': pmtext
            },
            success: function(data) {
                IsAjaxExecuting = false;
                toastr.success("Comment has been sent", "New reply has been sent");
            }
        });
    });
    
    $("body").on("click","#updateTask", function(e) {
        if($('#inputAssigned').val()!="" && $('#inputTitle').val()!="" && $('#inputDescription').val()!="" && $('#inputDeadline').val()!="" && $('#inputType').val()!="" && $('#inputPriority').val()!=""){
            let data = {
                'id': $(this).attr("data-id"),
            	'manager': <?php echo $_SESSION["id"]; ?>,
            	'assigned_to': $('#inputAssigned').val(),
            	'name': $('#inputTitle').val(),
            	'description': $('#inputDescription').val(),
            	'deadline': $('#inputDeadline').val(),
            	'type': $('#inputType').val(),
            	'priority': $('#inputPriority').val(),
            }
            ajaxCall ('tasks', 'updateTask',data, function(response){
                let resObj = JSON.parse(response);
                if (resObj.e) { // ERROR
                    toastr.error(resObj.e);
                } else if (resObj.res) { // SUCCESS
                    $("#wg-tasks-1 .reload").trigger("click");
                    $('#myModal').modal('toggle');
                    console.log(resObj.res);
                }
            });
        } else {
            
        }
    });
    
</script>