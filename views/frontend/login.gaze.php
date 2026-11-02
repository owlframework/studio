 <?php
 // views/frontend/feather.php
defined('ROOTPATH') OR die("Direct access denied.");

// 1. Enforce strict GET request discipline
verify_get_request();
 ?>
<section class="owl-auth-main">
    <div class="owl-auth-card">
        <div class="owl-auth-header">
            <div class="badge">Welcome Back</div>
            <h1>Sign in to <?=APPNAME?></h1>
            <p>Enter your credentials to access your account dashboard.</p>
        </div>
        <?php show_alerts(); ?>
        <form method="POST" class="owl-auth-form">
            <div class="owl-auth-field">
                <label for="email">UserName</label>
                <input type="text" id="username" name="username" placeholder="username" required>
            </div>

            <div class="owl-auth-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"  placeholder="••••••••" required>
            </div>

              <div class="auth-options">
                <label><input type="checkbox"> Remember me</label>
                <a href="forget_password" class="forgot-link">Forgot Password?</a>
            </div>
            

            <button type="submit" class="owl-auth-submit">Sign In</button>
        </form>

        <div class="owl-auth-footer-text">
            Don't have an account? <a href="register">Register here</a>
        </div>
    </div>
</section>