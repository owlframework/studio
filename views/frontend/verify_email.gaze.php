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
            
            <h1>Resend Verfication Code</h1>
            
        </div>

        <?php show_alerts(); ?>

        <form method="POST" class="owl-auth-form">
            <div class="owl-auth-field">
                <label for="email">Email Address</label>
              
                <input type="text"  id="email" class="form-control" placeholder="Email" name="email" value="<?= esc($access->email); ?>" readonly>
            </div>
 
            <!-- Removed name="code" from the button so it doesn't overwrite the input -->
            <button type="submit" class="owl-auth-submit">Resend Code</button>
        </form>
 
    </div>
</section>