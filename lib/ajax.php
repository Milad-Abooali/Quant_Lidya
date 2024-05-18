<?php
######################################################################
#  M | 12:48 PM Tuesday, July 6, 2021
#          Add Comment Section
#          Fix Security Bugs
######################################################################

/**
 * Global Functions Class
 * 11:13 AM Wednesday, November 11, 2020 | M.Abooali
 */

  require_once "../config.php";

  $class      = $_GET['c'] ?? 'global';
  $class_file = "ajax/".$class.".php";
  $func       = $_GET['f'] ?? 'noF';
  $token_r    = $_GET['t'] ?? false;
  
  // check token
  if ($token_r === TOKEN && !$lock_screen) {
    // check class file
    if (file_exists($class_file)) {
      require_once $class_file;
      // check function
      if (function_exists($func)) {
        $func();
      } else {
        $error = 'Called function is not exists!';
      }
    } else {
      $error = 'Class file is not exists!';
    }  
  } else {
      $error = 'Token is wrong !';

  }

if (isset($error)) {
      $output = new stdClass();
      $output->e = $error;
      echo json_encode($output);
  }