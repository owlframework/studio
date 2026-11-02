<?php
require 'feather.php';

$userModel = new User();
$tracker = new SessionTracker();

//Tack active users 
$tracker->track();
$onlineCount = $tracker->getOnlineCount();
 // 1. Total registered users
$totalUsers = $userModel->count();
// 2. Only active users
$activeUsers = $userModel->count(['status' => 'active']);

?>
            <section class="content-body">
                <p><?php show_alerts(); ?></p>
                
                <h2 class="section-title">Dashboard</h2>
                
                
                <div class="metric-grid">
                    <div class="metric-card">
                        <div class="metric-info">
                            <span>Total Users</span>
                            <h3><?= $totalUsers ?></h3>
                        </div>
                        <div class="metric-icon"><i class="ph-fill ph-users"></i></div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-info">
                            <span>Total Active User</span>
                            <h3><?= $activeUsers; ?></h3>
                        </div>
                        <div class="metric-icon"><i class="ph-fill ph-file-text"></i></div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-info">
                            <span>Active Sessions</span>
                            <h3><?=  $onlineCount ?></h3>
                        </div>
                        <div class="metric-icon"><i class="ph-fill ph-activity"></i></div>
                    </div>
                </div>
                
                 
              

            </section>


        </main>
    </div>
 