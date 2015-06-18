<?php
    
    // start the session
    session_start();
    
    include_once 'includes/config.php';
    include_once 'includes/db.php';
    include_once 'includes/functions.php';
    require_once 'includes/signin.php';
    
    global $scripts;
    
    $page = array();
    $page = getView( $_REQUEST );
    $scripts = $page['scripts'];

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Professional Training Development</title>
        <link href="css/lbplus.css" rel="stylesheet" type="text/css" media="all" />
        <link href="fonts/icomoon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/demo.css" rel="stylesheet" type="text/css" media="all" /> <!-- demo css; remove for live -->
    </head>
    <body>

        <main class="lbplus_wrapper" role="main">

            <div class="lbplus_container">
                
                <?php include_once $page['view']; ?>
                
            </div>

        </main>

    </body>
    
    <script src="scripts/jquery.js" type="text/javascript"></script>
    <?php echo $scripts; ?>
    
</html>
                