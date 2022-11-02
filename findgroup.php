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

    $userID = $_GET['userID'];
    $type = $_GET['type'];
    
    if($_GET['server'] == "MT4"){
        $server = "1";
    } else {
        $server = "2";
    }
    $sqlUSER = 'SELECT unit FROM user_extra WHERE user_id = "'.$userID.'"';
    $mt_USER = $DB_admin->query($sqlUSER);
    echo "type:".$type;
    echo "user:".$userID;
    while ($rowUSER = mysqli_fetch_array($mt_USER)) {
        $sqlGRP = 'SELECT name FROM mt_groups WHERE unit = "'.$rowUSER['unit'].'" AND type = "'.$type.'" AND server = "'.$server.'"';
        echo $sqlGRP;
        
        $mt_groups = $DB_admin->query($sqlGRP);
        while ($rowGRP = mysqli_fetch_array($mt_groups)) {
            if($rowMT42['GROUP'] == $rowGRP['name']){
                echo "<option value='".$rowGRP['name']."' selected>".$rowGRP['name']."</option>";
            } else {
                echo "<option value='".$rowGRP['name']."'>".$rowGRP['name']."</option>";
            }
        }
    }
    
	mysqli_close($DB_admin);
?>