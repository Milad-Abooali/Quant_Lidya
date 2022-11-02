<?php


// last_log_id
function c_3 ($data, $row, $col) {
    $output = "<a class='btn btn-sm btn-info do-loadDetail' href='sys_settings.php?section=system_joblogs&dt={\"table\":\"DT_job_logs\",\"regex\":\"1\",\"cols\":{\"1\":\"$col[1]\"}}'>ALL</a>";
    if ($data) $output .= "<a class='mx-2 btn btn-sm btn-secondary do-loadDetail' href='sys_settings.php?section=system_joblogs&dt={\"table\":\"DT_job_logs\",\"regex\":\"1\",\"cols\":{\"0\":\"$data\"}}'>Last</a>";
    return ($output) ?? null;
}


// avoid
function c_5 ($data, $row, $col) {
    return ($data) ? '
        <div class="custom-control custom-switch">
          <input type="checkbox" data-col="avoid" data-id="'.$col[0].'" class="custom-control-input doA-update" id="avoid-'.$row.'" checked>
          <label class="custom-control-label" for="avoid-'.$row.'"> </label>
        </div>
    ' :
        '
        <div class="custom-control custom-switch">
          <input type="checkbox" data-col="avoid" data-id="'.$col[0].'" class="custom-control-input doA-update" id="avoid-'.$row.'">
          <label class="custom-control-label" for="avoid-'.$row.'"> </label>
        </div>
    ';}

// force_run
function c_6 ($data, $row, $col) {
    return ($data) ? '
        <div class="custom-control custom-switch">
          <input type="checkbox" data-col="force_run" data-id="'.$col[0].'" class="custom-control-input doA-update" id="force-'.$row.'" checked>
          <label class="custom-control-label" for="force-'.$row.'"> </label>
        </div>
    ' :
        '
        <div class="custom-control custom-switch">
          <input type="checkbox" data-col="force_run" data-id="'.$col[0].'" class="custom-control-input doA-update" id="force-'.$row.'">
          <label class="custom-control-label" for="force-'.$row.'"> </label>
        </div>
    ';
}
