<?php

// Action
function c_5 ($data, $row, $col) {
    return '<button class="btn btn-outline-primary btn-sm doA-ShowEmail mr-2" data-id="'.$data.'">Show</button><button class="btn btn-success btn-sm doA-resend" data-id="'.$data.'">reSend</button>';
}
