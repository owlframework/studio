<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords($page_name) ?> | <?=APPNAME?></title>
    <link rel="stylesheet" href="<?= url('public/assets/css/admin-style.css') ?>">
     <script src="https://unpkg.com/@phosphor-icons/web" nonce="<?= get_nonce() ?>"></script>
</head>
<body class="auth-page">
       <?php include 'header.gaze.php'; ?>
    

    <main>
        <?php 
        // Since we are already IN the folder, just load the page
        $file = __DIR__ . "/" . $page_name . ".gaze.php";

        if(file_exists($file)) {
            require $file;
        } else {
            echo "Content file missing: " . $page_name;
        }
    ?>
    </main>

   <?php include 'footer.gaze.php'; ?>
   
   <!--JS SECTION -->
 
     
   <script src="<?= url('public/assets/js/sidebartoggle.js') ?>" nonce="<?= get_nonce(); ?>" defer></script>
   <script src="<?= url('public/assets/js/theme.js') ?>" nonce="<?= get_nonce(); ?>" defer></script>
 </body>
</html>