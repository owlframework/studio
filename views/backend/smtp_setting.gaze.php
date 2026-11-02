<?php

//Check if user is connected
require 'feather.php';

//get the data from the smtp table
$smtp = new Mail();

$smtpinfo = $smtp->getSmtp();
if (!$smtpinfo) {
    view("<div class='alert error'>SMTP configuration is missing from the database!</div>");
    // You might want to stop the page here or show an empty form
}
?>

<section class="content-body">
 <h2 class="section-title">Smtp Setting</h2>
                <div class="admin-card large">
                    
             <?php show_alerts(); ?>

                    <form method="POST"> 
                        <div class="input-group">
                            <label>Host</label>
                            <input type="HIDDEN" name="id" value="<?= esc($smtpinfo->id ?? '') ?>" required>
                            <input type="text" name="host" value="<?= esc($smtpinfo->host ?? '') ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Username</label>
                            <input type="text" name="username" value="<?= esc($smtpinfo->username ?? '') ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Password</label>
                            <input type="text" name="password" value="<?= esc($smtpinfo->password ?? '') ?>" required>
                        </div>


                        <div class="input-group">
                            <label>Port</label>
                            <input type="number" name="port" value="<?= esc($smtpinfo->port ?? '') ?>" required>
                        </div>

                        <div class="input-group">
                            <label>From Name</label>
                            <input type="text" name="from_name" value="<?= esc($smtpinfo->from_name ?? '') ?>" required>
                        </div>

                        <div class="input-group">
                            <label>From Email</label>
                            <input type="email" name="from_email" value="<?= esc($smtpinfo->from_email ?? '') ?>" required>
                        </div>

                         <div class="input-group">
                            <label>Reply To</label>
                            <input type="email" name="replyto" value="<?= esc($smtpinfo->replyto ?? '') ?>" required>
                        </div>

                        <div class="buttonposition">
                            <button type="submit" class="btn-primary btn-auto">Save Changes</button>
                        </div>
                    </form>
                </div>

            </section>