<?php
    
    // start the session
    session_start();
    
    include_once 'includes/config.php';
    include_once 'includes/db.php';
    
    // variable holding the view to include
    $view = 'includes/views/lbplus_view.php';
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>LB+</title>
        <link href="css/lbplus.css" rel="stylesheet" type="text/css" media="all" />
        <link href="fonts/icomoon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/demo.css" rel="stylesheet" type="text/css" media="all" /> <!-- demo css; remove for live -->
    </head>
    <body>

        <main class="lbplus_wrapper" role="main">

            <div class="lbplus_container">

                <?php include_once $view; ?>

            </div>

        </main>

    </body>
    <script src="scripts/jquery.js" type="text/javascript"></script>
    <script src="vendors/moment.min.js" type="text/javascript"></script>
    <script src="scripts/lbplus-dev.js" type="text/javascript"></script>
</html>