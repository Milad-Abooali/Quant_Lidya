<?php

// Status(9) - TP Logins (17)
function c_9 ($data, $row, $col) {
    $main = '<div>'.$data.'</div>';
    $extra = null;
    if ($col[17]) {
        $extra.='<div class="data-hide login-hide"><small class="bg-gradient-dark text-white btn-sm col-md-12 col-sm-6 text-center">Logins</small><span><li class="cb-copy-html">'.str_replace(',','</li><li class="cb-copy-html">',$col[17]).'</li></span></div>';
    }
    return $main.$extra;
}

// Source
function c_11 ($data, $row, $col) {
    $main = '<div>'.$data.'</div>';
    $camps = ($col[15] || $col[16])
        ? '<div class="data-hide camp-hide"><small class="bg-gradient-dark text-white btn-sm col-md-12 col-sm-6 text-center">Campaigns</small><span><strong>Main:</strong><br> '.$col[15].'<br><strong>Extra:</strong><br>'.$col[16].'</span></div>'
        : null;
    return $main.$camps;
}