<?php

?>

<div class="menu r-menu d-hide">
    <div class="container">
        <h3>Profile <small class="float-end"><i class="fa fa-times-circle close-ico doP-close"></i></small></h3>
        <hr>
    </div>
    <div class="container">
        <view class="hide-if-guest">
            <span data-screen="profile" class="show-screen d-block item" role="button">
                My Profile
                <i class="far fa-address-card text-info"></i>
            </span>
            <span data-screen="notifications" class="show-screen d-block item" role="button">
                My Notifications
                <i class="far fa-bell text-secondary"></i>
            </span>
            <span class="doA-logout d-block item" role="button">
                Logout
                <i class="fas fa-fw fa-sign-out-alt text-danger"></i>
            </span>
        </view>
        <view class="show-if-guest">
            <span class="doF-register d-block item" role="button">
                Register
                <i class="fas fa-fw fa-user-plus text-success"></i>
            </span>
            <span class="doF-login d-block item" role="button">
                Login
                <i class="fas fa-fw fa-sign-in-alt text-primary"></i>
            </span>
            <span class="doF-recovery d-block item" role="button">
                Password Recovery
                <i class="fas fa-fw fa-key text-warning"></i>
            </span>
        </view>
    </div>
</div>
