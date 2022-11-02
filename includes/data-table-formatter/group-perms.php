<?php

// Creat Edit Button
function c_6 ($data, $row) {
    return '<button class="btn btn-danger btn-sm form-control doDelete" data-gpid="'.$data.'"><i class="fa fa-times-circle"></i></button>';
}


// Creat Remove Button
function c_5 ($data, $row) {
    return ($data) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-ban text-danger"></i>';
}

// Creat Remove Button
function c_4 ($data, $row) {
    return ($data) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-ban text-danger"></i>';
}

// Creat Remove Button
function c_3 ($data, $row) {
    return ($data) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-ban text-danger"></i>';
}

// Creat Remove Button
function c_2 ($data, $row) {
    return ($data) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-ban text-danger"></i>';
}