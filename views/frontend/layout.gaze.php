<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo ucwords($page_name) ?> | <?=APPNAME?></title>
     <link rel="stylesheet" href="<?= url('public/assets/css/user-style.css') ?>">
     <link rel="icon" type="image/x-icon" href="<?= url('public/assets/images/uploads/favicon.ico') ?>">
</head>
<body>
    <div id="app-wrapper">
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
   </div>
   <script src="<?= url('public/assets/js/app.js') ?>" nonce="<?= get_nonce(); ?>"></script>
</body>
</html>