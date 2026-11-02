<?php
require 'feather.php';
?>
<section class="owl-auth-main">
    <div class="owl-auth-card">
        <div class="owl-auth-header">
             <h1>Change Account Password</h1>
         </div>
        <?php show_alerts(); ?>
        <form method="POST" class="owl-auth-form">
           

            <div class="owl-auth-field">
                <label for="current_password">Old Password</label>
                <input type="password" id="current_password" name="current_password"  placeholder="••••••••" required>
            </div>

             <div class="owl-auth-field">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password"  placeholder="••••••••" required>
            </div>

             <div class="owl-auth-field">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password"  placeholder="••••••••" required>
            </div>
 
            

            <button type="submit" class="owl-auth-submit">Change Password</button>
        </form>
 
    </div>
</section>