<?php

/**
 * Global Functions Class
 * 11:13 AM Wednesday, November 11, 2020 | M.Abooali
 */

// on null call
  function noF() {
      $output = new stdClass();
      $output->e = false;
      $output->res = $_POST;
      echo json_encode($output);
  }

// Load dataTableSimple Data
    function dataTableSimple() {
        $db = null;
        $table = $_GET['table'] ?? false;
        $primaryKey = $_GET['key'] ?? 'id';
        $columns = $_GET['columns'] ?? '*';
        $where = $_GET['where'] ?? false;
        include 'qsp.php';
        include 'datatable.php';
        $datatable = new DataTable($db,$table,$columns,$where);
        echo $datatable->simple();
    }

// Load dataTableComplex Data
function dataTableComplex() {
    $db = null;
    $table = $_GET['table'] ?? false;
    $primaryKey = $_GET['key'] ?? 'id';
    $columns = $_GET['columns'] ?? '*';
    $where = $_GET['query'] ?? false;
    include 'qsp.php';
    include 'datatable.php';
    $datatable = new DataTable($db,$table,$columns,$where);
    echo $datatable->Complex();
}

// Load dataTableUnion Data
function dataTableUnion() {
    $db = null;
    $table = $_GET['table'] ?? false;
    $primaryKey = $_GET['key'] ?? 'id';
    $columns = $_GET['columns'] ?? '*';
    $where = $_GET['query'] ?? false;
    include 'qsp.php';
    include 'datatable.php';
    $datatable = new DataTable($db, $table, $columns, $where);
    echo $datatable->Union();
}