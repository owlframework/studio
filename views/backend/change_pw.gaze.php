<?php

require 'feather.php';
?>

<section class="content-body">
 <h2 class="section-title">Smtp Setting</h2>
                <div class="admin-card large">
                    
             <?php show_alerts(); ?>

                    <form method="POST"> 
                       

                        <div class="input-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>

                        <div class="input-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required>
                        </div>
                        <div class="input-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>

                        <div class="buttonposition">
                            <button type="submit" class="btn-primary btn-auto">Save Changes</button>
                        </div>
                    </form>
                </div>

            </section>