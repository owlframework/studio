<?php
defined('ROOTPATH') OR die("Direct access denied.");

/**
 * Owl Framework - Reset Password View
 */

// 1. Enforce strict GET request discipline and ensure token is present in the URL
verify_get_request('token');

$token = $_GET['token'] ?? '';
$resetModel = new PasswordReset();

// 2. Verify token exists and is NOT used
$request = $resetModel->first(['token' => $token, 'used' => 0]);

// 3. Check 1-hour expiry (3600 seconds)
if (!$request || (time() - strtotime($request->created_at)) > 3600) {
     view("This password reset link is invalid or has expired. Please request a new one.");
    exit();
}
?>

<section class="owl-auth-main">
    <div class="owl-auth-card">
        <div class="owl-auth-header">
            <h1>Reset Password</h1>
            <p>Enter your new password below.</p>
        </div>

        <?php show_alerts(); ?>

        <form method="POST" class="owl-auth-form">
            <div class="owl-auth-field">
                <label for="password">New Password</label>
                <!-- Securely pass the token via hidden input -->
                <input type="hidden" name="token" value="<?= esc($token); ?>">
                <input type="password" id="password" class="form-control" placeholder="New Password" name="password" required autofocus>
            </div>

            <div class="owl-auth-field">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" class="form-control" placeholder="Confirm New Password" name="confirm_password" required>
            </div>
 
            <button type="submit" class="owl-auth-submit">Update Password</button>
        </form>
    </div>
</section>