<?php
require 'feather.php';
?>

<section class="owl-dash-container">
    <!-- Welcome Banner Section -->
    <div class="owl-dash-welcome">
        <div class="owl-dash-welcome-text">
            <div class="badge">Session Active</div>
            <!-- Safely output user name or fallback to 'Developer' -->
            <h1>Welcome back, <?= esc($access->name ?? $access->username ?? 'Developer'); ?></h1>
            <p>Here is an overview of your <?= APPNAME ?> application workspace.</p>
        </div>
        <div class="owl-dash-actions">
            <a href="<?= url('change_password'); ?>" class="owl-dash-btn owl-dash-btn-secondary">Change Password</a>
            <form action="<?= url('logout'); ?>" method="POST">
                <button type="submit" class="owl-dash-btn owl-dash-btn-secondary">Sign Out</button>
            </form>
        </div>
    </div>

    <?php show_alerts(); ?>

    <!-- Metrics & Management Grid -->
    <div class="owl-dash-grid">
        <!-- Quick Stats Card -->
        <div class="owl-dash-card">
            <h3>System Diagnostics</h3>
            <p>Current runtime environment parameters and system checks running on Owl Core.</p>
            <div class="owl-dash-stats-list">
                <div class="owl-dash-stat-item">
                    <span class="owl-dash-stat-label">Framework Engine</span>
                    <span class="owl-dash-stat-value"><?= function_exists('owlversion') ? owlversion() : 'Owl Core v1.0'; ?></span>
                </div>
                <div class="owl-dash-stat-item">
                    <span class="owl-dash-stat-label">PHP Version</span>
                    <span class="owl-dash-stat-value"><?= phpversion(); ?></span>
                </div>
                <div class="owl-dash-stat-item">
                    <span class="owl-dash-stat-label">Active Database</span>
                    <span class="owl-dash-stat-value">Connected (PDO)</span>
                </div>
            </div>
        </div>

        <!-- Quick Links / Guide Card -->
        <div class="owl-dash-card">
            <h3>Architecture Shortcuts</h3>
            <p>Quick file references for your view templates and controller request handlers.</p>
            <div class="owl-dash-stats-list">
                <div class="owl-dash-stat-item">
                    <span class="owl-dash-stat-label">Presentation View</span>
                    <span class="owl-dash-stat-value">*.gaze.php</span>
                </div>
                <div class="owl-dash-stat-item">
                    <span class="owl-dash-stat-label">Logic Handler</span>
                    <span class="owl-dash-stat-value">*.catch.php</span>
                </div>
                <div class="owl-dash-stat-item">
                    <span class="owl-dash-stat-label">Bootstrap Config</span>
                    <span class="owl-dash-stat-value">Nest.php</span>
                </div>
            </div>
        </div>
    </div>
</section>
 