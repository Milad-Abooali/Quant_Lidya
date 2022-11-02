<?php

// Updated At
function c_6 ($data, $row, $col) {
    $result = $data;
    if($_SESSION['type']==='Admin') $result .= "<a href='javascript:;' data-id=".$col[2]." class='remove-note float-right'><i class='fas fa-times-circle'></i></a>";
    return $result;
}