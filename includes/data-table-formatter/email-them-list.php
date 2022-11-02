<?php

// Creat Edit Button
function c_5 ($data, $row) {
    if ($data) {
        return '<a class="btn btn-outline-info btn-xs btn-block float-right" href="sys_settings.php?section=email_theme-editor&theme='.$data.'">Edit</a>';
    } else {
        return null;
    }
}