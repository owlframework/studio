 <!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>404 Page </title>
     <link rel="stylesheet" href="<?= url('public/assets/css/user-style.css') ?>">

</head>
<body>
    <!-- Main Content Container -->
    <main>
        <div class="error-card">
            <div class="badge">Route Not Found</div>
            <div class="error-code">404</div>
            <h1>Lost in the Night Sky</h1>
            <p>The requested route or page could not be located by the <strong>Owl</strong> framework router. Check your path or return to safety.</p>
            
            <div class="actions">
                <a href="home" class="btn btn-primary">Return Home</a>
                <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            </div>
        </div>
    </main>

  </body>
  </html>