<?php

/**
 * Gateway Functions Class
 * 1:47 PM Monday, January 4, 2021 | M.Abooali
 */

// on null call
function noF() {
    $output = new stdClass();
    $output->e = false;
    $output->res = $_POST;
    echo json_encode($output);
}

// Add Task
function addTask() {
    $output = new stdClass();
    global $taskManager;
    $manager = $_POST['manager'];
    $assigned_to = implode(",", $_POST['assigned_to']);
    $name = $_POST['name'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $status = 1;
    $type = $_POST['type'];
    $priority = $_POST['priority'];
    $result = $taskManager->Add($manager, $assigned_to, $name, $description, $deadline, $status, $type, $priority);
    
    $output->e = false;
    $output->res = $_POST;
    
    echo json_encode($output);
}

// Update Task
function updateTask() {
    $output = new stdClass();
    global $taskManager;
    $id = $_POST['id'];
    $manager = $_POST['manager'];
    $assigned_to = implode(",", $_POST['assigned_to']);
    $name = $_POST['name'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $status = 1;
    $type = $_POST['type'];
    $priority = $_POST['priority'];
    $result = $taskManager->Update($id, $manager, $assigned_to, $name, $description, $deadline, $status, $type, $priority);
    
    $output->e = false;
    $output->res = $_POST;
    
    echo json_encode($output);
}

// Finish Task
function finishTask() {
    $output = new stdClass();
    global $taskManager;
    $id = $_POST['id'];
    $status = 5;
    $result = $taskManager->Finish($id, $status);
    
    $output->e = false;
    $output->res = $_POST;
    
    echo json_encode($output);
}

// Pin Task
function pinTask() {
    $output = new stdClass();
    global $taskManager;
    $id = $_POST['id'];
    $pin = $_POST['pin'];
    $result = $taskManager->Pin($id, $pin);
    
    $output->e = false;
    $output->res = $_POST;
    
    echo json_encode($output);
}

// View Task
function viewTask() {
    $error = !(($_POST['id']) ?? false);
    if (!$error) include_once "raw/view_task.php";
    return (!$error);
}