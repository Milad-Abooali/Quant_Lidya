<?php

$permits = new stdClass();

// Ruby
$permits->ruby = array(
    'guest'   => array(
        'view'      => false,
    ),
    'user'   => array(
        'view'      => true,
    ),
    'agent'   => array(
        'view'      => true,
    )
);

// Wizard
$permits->wizard = array(
    'guest'   => array(
        'view'      => false,
    ),
    'user'   => array(
        'view'      => true,
        'complete-profile'       => true,    // Complete My Profile
        'open-tp-demo'       => true,    // Complete My Profile
    ),
    'agent'   => array(
        'view'      => true,
        'complete-profile'       => true,    // Complete My Profile
    )
);

// CRM
$permits->crm = array(
    'guest'   => array(
        'view'      => false,
    ),
    'user'   => array(
        'view'      => true,
    ),
    'agent'   => array(
        'view'      => true,
    )
);

// Screen - Profile
$permits->profile = array(
    'guest'   => array(
        'view'      => false,
    ),
    'user'   => array(
        'view'      => true,
        'edit'      => true
    ),
    'agent'   => array(
        'view'      => true,
    )
);

// Screen - Home
$permits->home = array(
    'guest'   => array(
        'view'      => true,
    ),
    'user'   => array(
        'view'      => true,
    ),
    'agent'   => array(
        'view'      => true,
    )
);

// Screen - Trade
$permits->trade = array(
    'guest'   => array(
        'view'      => true,
    ),
    'user'   => array(
        'view'      => true,
        'update-login-password'      => true,
        'order'      => true
    ),
    'agent'   => array(
        'view'      => true,
        'order'      => true
    )
);


// Screen - Market
$permits->market = array(
    'guest'   => array(
        'view'      => true,
    ),
    'user'   => array(
        'view'      => true,
    ),
    'agent'   => array(
        'view'      => true,
    )
);

// Screen - Debug
$permits->debug = array(
    'guest'   => array(
        'view'      => true,
    ),
    'user'   => array(
        'view'      => true,
    ),
    'agent'   => array(
        'view'      => true,
    )
);
