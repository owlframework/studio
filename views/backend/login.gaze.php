 <?php
 // views/frontend/feather.php
defined('ROOTPATH') OR die("Direct access denied.");

// 1. Enforce strict GET request discipline
verify_get_request();
 ?>
<main class="auth-main">
        <div class="admin-card">
            <div class="auth-header">
                <div class="logo">
                    <i class="ph-bold ph-bird"></i>
                    <span><?= APPNAME ?? 'Owl' ?></span>
                </div>
                <h1>Sign In</h1>
                <p>Welcome back to the dashboard.</p>
            </div>
            
               <?php show_alerts(); ?>
 
            <form method="POST">
                <div class="input-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="ph ph-envelope"></i>
                        <input type="email" name="email" placeholder="admin@example.com" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="ph ph-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Sign In</button>
            </form>
        </div>
    </main>