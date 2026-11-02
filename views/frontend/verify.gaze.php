<?php
require 'feather.php';

// If they are already verified, kick them to the dashboard
if (!empty($access->email_verified_at)) {
    Response::redirect('dashboard');
}
?>  

<section class="owl-auth-main">
    <div class="owl-auth-card">
        <div class="owl-auth-header">
            <div class="badge">Almost Done</div>
            <h1>Complete registration</h1>
            <p>Enter the verification code sent to your email address.</p>
        </div>

        <?php show_alerts(); ?>

        <form method="POST" class="owl-auth-form">
            <div class="owl-auth-field">
                <label for="code">Verification Code</label>
                <!-- Input holds the OTP value -->
                <input type="text" id="code" name="code" placeholder="OTP" required autofocus>
            </div>
 
            <!-- Removed name="code" from the button so it doesn't overwrite the input -->
            <button type="submit" class="owl-auth-submit">Verify</button>
        </form>

        <div class="owl-auth-footer-text">
            <a href="<?= url('verify_email'); ?>">Request A New Code</a>
        </div>
    </div>
</section>