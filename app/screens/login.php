<?php
/**
 * Login
 * App - Screen Page
 * By Milad [m.abooali@hotmail.com]
 */
global $AFPP;

$screen_title   = 'Login';
$screen_id      = 'login';

?>
<view class="show-if-guest">
<!-- <?= $screen_id ?> Screen -->
    <!-- Login Form -->
    <div class="col-sm-12 text-center pt-5">
        <img class="broker-logo" src="media/broker/<?= Broker['mini_logo'] ?>" alt="broker-logo">
    </div>
    <div class="col-sm-12 col-md-4 p-3 mx-auto">
        <div id="login" class="p-3 rounded-2">
            <form id="login-form">
                <h5 class="text-secondary text-center">Login</h5>
                <div class="mb-3">
                    <label for="username-email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="username-email" name="username-email" autocomplete="username" required>
                </div>
                <div class="mb-3">
                    <label for="current-password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
                </div>
                <div class="d-grid gap-2 mx-auto">
                    <button class="btn btn-primary" type="submit">Login</button>
                    <hr>
                    <button class="btn btn-link alink doF-recovery" type="button">Forgot your password?</button>
                    <button class="btn btn-outline-success doF-register" type="button">Register</button>
                </div>
            </form>
            <form id="recovery-form" class="d-hide">
                <h5 class="text-secondary text-center">Password Recovery</h5>
                <div class="mb-3">
                    <label for="username" class="form-label">Email</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="d-grid gap-2 mx-auto">
                    <button class="btn btn-primary" type="submit">Submit</button>
                    <hr>
                    <button class="btn btn-outline-primary doF-login" type="button">Login</button>
                    <button class="btn btn-outline-success doF-register" type="button">Register</button>
                </div>
            </form>
            <form id="register-form" class="d-hide">
                <h5 class="text-secondary text-center">Register</h5>
                <div class="mb-3">
                    <label for="fname" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="fname" name="fname" required>
                </div>
                <div class="mb-3">
                    <label for="lname" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lname" name="lname" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Country</label>
                    <input type="hidden" class="form-control" id="country" name="country" required>
                    <div id="select-country" class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle w-100" type="button" id="countryList" data-bs-toggle="dropdown" aria-expanded="false">
                            Select Country of Residence
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark" id="countries"> </ul>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group mb-3">
                        <strong class="input-group-text" id="phone-plus">+</strong>
                        <input type="number" min="1" max="99999" pattern="[0-9]*" class="form-control text-primary" placeholder="1" id="phone-p" name="phone-p" required>
                        <input type="number" maxlength="11" pattern="[0-9]*" class="form-control w-50" placeholder="123xxxxxxx" id="phone" name="phone" required>
                    </div>
                </div>
                <div class="d-grid gap-2 mx-auto">
                    <button class="btn btn-primary" type="submit">Register</button>
                    <hr>
                    <button class="btn btn-outline-primary doF-login" type="button">Login</button>
                </div>
            </form>
        </div>
    </div>
</view>
