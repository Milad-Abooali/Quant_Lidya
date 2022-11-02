<?php

    // @TODO Complete

    global $db;

    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];

    $where = "created_at BETWEEN '$start_date' AND '$end_date'";
    $users = $db->select('uses')


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



<script>

</script>