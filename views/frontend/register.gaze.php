<?php
// views/frontend/feather.php
defined('ROOTPATH') OR die("Direct access denied.");

// 1. Enforce strict GET request discipline
verify_get_request();
?>

<section class="owl-auth-main">
    <div class="owl-auth-card">
        <div class="owl-auth-header">
            <div class="badge">Get Started</div>
            <h1>Create an Account</h1>
            <p>Set up your <?=APPNAME?> environment in seconds.</p>
        </div>

        <?= show_alerts(); ?>
        <form method="POST" class="owl-auth-form">
            <div class="owl-auth-field">
                <label for="username">User Name</label>
                <input type="text" id="username" name="username" placeholder="John Doe" required>
            </div>

            <div class="owl-auth-field">
                <label for="phone">Phone Number</label>
                <input type="number" id="phone" name="phone" placeholder="+234 8800000000" required>
            </div>

            <div class="owl-auth-field">
                <label for="email">Email Address </label>
                <input type="email" id="email" name="email" placeholder="developer@owl.ng" required>
            </div>

            <div class="owl-auth-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="owl-auth-submit">Create Account</button>
        </form>

        <div class="owl-auth-footer-text">
            Already have an account? <a href="login">Sign in</a>
        </div>
    </div>
</section>