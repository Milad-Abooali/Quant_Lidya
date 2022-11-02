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
    

    if (isset($_GET['id'])) {
        $sql = '';
        $sql = 'SELECT title, description, reader, reciever, created_at, id FROM messages WHERE tid = 0 AND (pid = "'.$_GET['id'].'" or id = "'.$_GET['id'].'") ORDER BY id';
    ?>
        <div class="inbox-wid">
            <?php
                $sqlRead = 'UPDATE messages SET reader = IF(JSON_CONTAINS(reader, "'.$_SESSION["id"].'"), reader, JSON_ARRAY_APPEND(reader, "$", '.$_SESSION["id"].'))  WHERE id = "'.$_GET['id'].'"';
                $DB_admin->query($sqlRead);
                //echo $sqlRead;
                
                $userPM = $DB_admin->query($sql);
                if($userPM) while ($rowPM = mysqli_fetch_array($userPM)) {
                   $reader = json_decode($rowPM['reader'], true);
                   $reciever = json_decode($rowPM['reciever'], true);
                   $pmid = $rowPM['id'];
            ?>
            <a class="text-dark d-block p-2 rounded <?php if($rowPM['id'] == $_GET['id']){ echo "alert-info"; } ?>" id="<?php echo $rowPM['id']; ?>">
                <div class="inbox-item">
                    <span class="badge <?php if (in_array($_SESSION["id"], $reader)) { echo "badge-dark"; } else { echo "badge-info"; } ?> float-right ml-1"> </span>
                    <span class="badge <?php if (in_array($_SESSION["id"], $reciever)) { echo "badge-primary"; } else { echo "badge-danger"; } ?> float-right"> </span>
                    <div class="inbox-item-img float-left mr-3"><img src="assets/images/users/user-1.jpg" class="thumb-md rounded-circle" alt=""></div>
                    <h6 class="inbox-item-author mt-0 mb-1"><?php echo $rowPM['title']; ?></h6>
                    <p class="inbox-item-text text-muted mb-0"><?php echo $rowPM['description']; ?></p>
                    <p class="inbox-item-date text-muted"><?php echo $rowPM['created_at']; ?></p>
                    <!--<p class="inbox-item-text"><?php echo $rowPM['reader']; ?></p>-->
                </div>
            </a>
            <?php } ?>
            <div class=""><h5>Reply</h5></div>
            <div id="sendrep">
            </div>
            <script>
                var valuser = '<?php echo htmlspecialchars($_SESSION["id"]); ?>';
                var pmid = '<?php echo $_GET['id']; ?>';
                var url = "sendpm.php?user="+valuser+"&pid="+pmid;
                $('#sendrep').load(url,function(result){
                    $('.summernote').summernote({
                        height: 100,                 // set editor height
                        minHeight: null,             // set minimum height of editor
                        maxHeight: null,             // set maximum height of editor
                        focus: false                 // set focus to editable area after initializing summernote
                    });
                });
            </script>
        </div>
        <row>
            <div class="mt-20">
                <span class="badge badge-dark mr-1"> </span>Read 
                <span class="badge badge-info mr-1"> </span>Unread 
                <span class="badge badge-danger mr-1"> </span>Sent 
                <span class="badge badge-primary mr-1"> </span>Recieved
            </div>
        </row>
    <?php
    } else {
        $sql = 'SELECT title, description, reader, reciever, created_at, id FROM messages WHERE tid = 0 AND pid = 0 AND (sender = "'.$_SESSION["id"].'" OR JSON_CONTAINS(reciever, "'.$_SESSION["id"].'", "$") = "1") ORDER BY created_at DESC LIMIT 5';
        ?>
        <div class="inbox-wid">
            <?php
                $userPM = $DB_admin->query($sql);
                if($userPM) while ($rowPM = mysqli_fetch_array($userPM)) {
                    $reader = json_decode($rowPM['reader'], true);
                    $reciever = json_decode($rowPM['reciever'], true);
            ?>
            <a href="javascript:;" class="text-dark pmid" id="<?php echo $rowPM['id']; ?>">
                <div class="inbox-item <?php if (in_array($_SESSION["id"], $reader)) { echo "bg-light"; } else { echo ""; } ?>" id="in">
                    <span class="badge <?php if (in_array($_SESSION["id"], $reader)) { echo "badge-dark"; } else { echo "badge-info"; } ?> float-right ml-1"> </span>
                    <span class="badge <?php if (in_array($_SESSION["id"], $reciever)) { echo "badge-primary"; } else { echo "badge-danger"; } ?> float-right"> </span>
                    <div class="inbox-item-img float-left mr-3"><img src="assets/images/users/user-1.jpg" class="thumb-md rounded-circle" alt=""></div>
                    <h6 class="inbox-item-author mt-0 mb-1"><?php echo $rowPM['title']; ?></h6>
                    <p class="inbox-item-text text-muted mb-0"><?php echo $rowPM['description']; ?></p>
                    <p class="inbox-item-date text-muted"><?php echo $rowPM['created_at']; ?></p>
                </div>
            </a>
            <?php } ?>
        </div>
        <row>
            <div class="mt-20">
                <span class="badge badge-dark mr-1"> </span>Read 
                <span class="badge badge-info mr-1"> </span>Unread 
                <span class="badge badge-danger mr-1"> </span>Sent 
                <span class="badge badge-primary mr-1"> </span>Recieved 
                <a href="#" class="btn btn-sm bg-gradient-primary text-white float-right">View All</a>
            </div>
        </row>
<?php
    }
?>
<script>
   
</script>
<?php
    //echo $sql;
    mysqli_close($DB_admin); 
?>