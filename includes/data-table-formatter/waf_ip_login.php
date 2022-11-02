<?php

// Status switch
function c_2 ($data, $row, $col) {
    return ($data) ? '
        <div class="custom-control custom-switch">
          <input type="checkbox" data-id="'.$col[0].'" class="custom-control-input doUpdate" id="rule-'.$row.'" checked>
          <label class="custom-control-label" for="rule-'.$row.'"> </label>
        </div>
    ' :
    '
        <div class="custom-control custom-switch">
          <input type="checkbox" data-id="'.$col[0].'" class="custom-control-input doUpdate" id="rule-'.$row.'">
          <label class="custom-control-label" for="rule-'.$row.'"> </label>
        </div>
    ';
    }

// Remove Button
function c_3 ($data, $row) {
    return '<button class="btn btn-danger btn-xs doDelete" data-id="'.$data.'"><i class="fa fa-times-circle"></i></button>';
}