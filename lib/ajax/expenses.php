<?php

/**
 * Expenses Functions Class
 * 12:08 PM Thursday, May 5, 2022 | M.Abooali
 */
require_once "autoload/expenses.php";

// Add expenses
function add() {
    $output = new stdClass();
    $output->e = false;
    if(!isset($_POST['user_id'])) $output->e = "User id is not set.";
    if(!isset($_POST['amount'])) $output->e = "Amount is not set.";
    if(!isset($_POST['type'])) $output->e = "Type is not set.";
    if(!$output->e){
        $type= ($_POST['type']==="Others") ? [$_POST['type'], $_POST['o_type']] : [$_POST['type']];
        $output->res = expenses::add( $_POST['user_id'], $_POST['amount'], $type, ($_POST['payee'] ?? null), ($_POST['comment'] ?? null) );
    }
    echo json_encode($output);
}

// Add expenses
function delete() {
    $output = new stdClass();
    $output->e = false;
    if(!isset($_POST['expense_id'])) $output->e = "Expense id is not set.";
    if(!$output->e){
        $output->res = expenses::delete( $_POST['expense_id'] );
    }
    echo json_encode($output);
}

// Sum user expenses
function userTotal(){
    $output = new stdClass();
    if(!isset($_POST['startTime'])) $output->e = "Start Time is not set.";
    if(!isset($_POST['endTime'])) $output->e = "End Time is not set.";
    if(!isset($_POST['user_id'])) $output->e = "User id is not set.";
    if(!$output->e){
        $startTime      = $_POST['startTime'];
        $endTime        = $_POST['endTime'];
        $userID         = $_POST['user_id'];
        global $db;
        $output->res    = $db->query("SELECT SUM(amount) as sum_amount FROM `users_expenses` WHERE user_id=$userID AND created_at BETWEEN '$startTime' and '$endTime';")[0]['sum_amount'];
    }
    echo json_encode($output);
}