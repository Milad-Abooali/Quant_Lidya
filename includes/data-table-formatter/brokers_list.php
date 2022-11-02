<?php

// Log
function c_2 ($data) {
    return '<img style="max-height:25px"src="media/broker/'.$data.'">';
}

// Maintenance switch
function c_3 ($data, $row, $col) {
    return ($data) ? '
        <div class="custom-control custom-switch">
          <input type="checkbox" data-id="'.$col[0].'" class="custom-control-input doA-maintenance" id="rule-'.$row.'" checked>
          <label class="custom-control-label" for="rule-'.$row.'"> </label>
        </div>
    ' :
    '
        <div class="custom-control custom-switch">
          <input type="checkbox" data-id="'.$col[0].'" class="custom-control-input doA-maintenance" id="rule-'.$row.'">
          <label class="custom-control-label" for="rule-'.$row.'"> </label>
        </div>
    ';
    }

// Mange Buttons
function c_4 ($data, $row) {
    return '
        <button class="btn btn-success btn-xs doM-View" data-id="'.$data.'">View</button>
        <button class="btn btn-info btn-xs doM-edit" data-id="'.$data.'">Edit</button>
        <button class="btn btn-danger btn-xs doA-delete float-right" data-id="'.$data.'"><i class="fa fa-times-circle"></i></button>
    ';
}