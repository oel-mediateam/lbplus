<?php
    
    // start the session
    session_start();
    
    require_once 'includes/config.php';
    require_once 'includes/db.php';
    require_once 'includes/functions.php';
    
    if ( !isset( $_REQUEST['oauth_consumer_key'] ) ) {
        
        require_once 'includes/google_signin.php';
        unsetLTIData();
        
    } else {
/*
        echo '<pre>';
        print_r($_REQUEST);
        echo '</pre>';
*/
        saveLTIData( $_REQUEST );
        
    }
    
    $page = getView( $_REQUEST );
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Sherlock</title>
        <link href="css/jquery-ui.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/sherlock.css" rel="stylesheet" type="text/css" media="all" />
        <link href="fonts/icomoon.css" rel="stylesheet" type="text/css" media="all" />
        <script src="scripts/jquery.js" type="text/javascript"></script>
        <script src="scripts/jquery-ui.js" type="text/javascript"></script>
        <script src="scripts/moment.min.js" type="text/javascript"></script>
        <script src="scripts/sherlock.js" type="text/javascript"></script>
    </head>
    <body>

        <main class="sherlock_wrapper" role="main">

            <div class="sherlock_container">
                
                <?php include_once $page; ?>
                
            </div>

        </main>

    </body>
    
</html>
                