  <!-- Header Navigation -->
    <header>
        <div class="brand">
            <!-- Calls your badge image file directly (Rename file as logo.png in your project) -->
            <img src="<?= url('public/assets/images/uploads/apple-touch-icon.png') ?>" alt="Owl Framework Logo">
            <span>Owl Framework</span>
        </div>
         <?php if (Session::has('original_admin_id')): ?>
                <a href="<?= aurl('stop_impersonate') ?>"><i class="fa-solid fa-user"></i> Admin Panel</a>
                <?php endif; ?>
       
    </header>