 
<div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="dashboard-container">
        <?php if (admin()):?>
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <i class="ph-bold ph-bird"></i>
                <span><?= APPNAME ?></span>
            </div>
            <?php $access = admin(true); ?>
            <nav class="nav-menu">
                <a href="<?=aurl('dashboard')?>" class="active" class="active"><i class="ph-bold ph-squares-four"></i> <span>Dashboard</span></a>
                <a href="<?=aurl('users')?>"><i class="ph-bold ph-users"></i> <span>Users</span></a> 
                
                <?php  if ($access->role === 'admin' || $access->role === 'super_admin'): ?>
                    <div class="nav-dropdown">
                    <a href="#" class="dropdown-toggle">
                        <div class="toggledisplay">
                            <i class="ph-bold ph-table"></i>
                            <span>Records</span>
                        </div>
                        <i class="ph-bold ph-caret-down caret-icon"></i>
                    </a>
                    
                    <div class="dropdown-menu">
                        <a href="#"><i class="ph-bold ph-files"></i><span>Add Records</span></a>
                      
                        
                    </div>
                </div>
                <?php endif; ?>

                <?php  if ($access->role === 'admin' || $access->role === 'super_admin'): ?>
                    <div class="nav-dropdown">
                    <a href="#" class="dropdown-toggle">
                        <div class="toggledisplay">
                            <i class="ph-bold ph-gear"></i>
                            <span>Settings</span>
                        </div>
                        <i class="ph-bold ph-caret-down caret-icon"></i>
                    </a>
                    
                    <div class="dropdown-menu">
                        <a href="smtp_setting">SMTP Config</a>
                        <a href="change_pw">Change Password</a>
                    </div>
                </div>
                <?php endif; ?>
                <a href="<?=aurl('logout')?>"><i class="ph-bold ph-sign-out"></i> <span>Logout</span></a>
            </nav>
        </aside>
        <?php endif; ?>
        <main class="main-content">
            <?php if (admin()):?>
            <header class="top-nav">
                <div class="header-left">
                    <button class="toggle-btn" id="sidebarToggle">
                        <i class="ph ph-list"></i>
                    </button>
                    <div class="appname"><span><?= APPNAME ?></span></div>
                </div>
                <div class="header-right">
                    <button id="theme-toggle" class="icon-btn"><i class="ph-bold ph-moon icon-lg" id="theme-icon"></i></button>
              
                </div>
            </header>
            <?php endif; ?>
 

  <script nonce="<?= get_nonce(); ?>">
         // Dropdown Menu Logic
            const navDropdowns = document.querySelectorAll('.nav-dropdown');

            navDropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                
                toggle.addEventListener('click', (e) => {
                    e.preventDefault(); // Prevent page jump
                    
                    // Optional: Close other open dropdowns (Accordion effect)
                    navDropdowns.forEach(otherDropdown => {
                        if (otherDropdown !== dropdown) {
                            otherDropdown.classList.remove('active');
                        }
                    });

                    // If the sidebar is currently collapsed, force it open so they can see the menu
                    if (sidebar.classList.contains('collapsed')) {
                        sidebar.classList.remove('collapsed');
                    }

                    // Toggle the current dropdown
                    dropdown.classList.toggle('active');
                });
            });
    </script>