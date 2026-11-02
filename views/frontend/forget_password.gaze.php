
<section class="owl-auth-main">
    <div class="owl-auth-card">
        <div class="owl-auth-header">
            
            <h1>Reset Password</h1>
            
        </div>

        <?php show_alerts(); ?>

        <form method="POST" class="owl-auth-form">
            <div class="owl-auth-field">
                <label for="email">Email Address</label>
              
                <input type="text"  id="email" class="form-control" placeholder="Email" name="email" required>
            </div>
 
            <!-- Removed name="code" from the button so it doesn't overwrite the input -->
            <button type="submit" class="owl-auth-submit">Get Code</button>
        </form>
 
    </div>
</section>