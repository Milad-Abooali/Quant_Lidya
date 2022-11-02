<?php

// Creat Edit Button
function c_0 ($data, $row) {
    return '<input type="checkbox" name="id[]" value="'.(($data) ?? null).'">';
}

