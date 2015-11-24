<?php
    
    session_start();
    
    require_once 'includes/config.php';
    require_once 'includes/db.php';
    require_once 'includes/functions.php';
    
    // if request does not contain LTI POST parameters
    if ( !isset( $_POST['oauth_consumer_key'] ) ) {
        
        require_once 'includes/google_signin.php';
        unsetLTIData(); // unset LTI POST parameters from session if any
    
    // else if there are LTI POST parameters
    } else {
        
        // save LTI POST parameters to session
        saveLTIData( $_REQUEST );
        
    }
    
    // get the page view base on the request
    $page = getView( $_REQUEST );
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo APP_NAME; ?></title>
        <link href="css/sherlock.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/jquery-ui.css" rel="stylesheet" type="text/css" media="all" />
        <link href="fonts/icomoon.css" rel="stylesheet" type="text/css" media="all" />
        <script src="scripts/jquery.js" type="text/javascript"></script>
        <script src="scripts/jquery-ui.js" type="text/javascript"></script>
        <script src="scripts/moment.min.js" type="text/javascript"></script>
        <script src="scripts/sherlock.js" type="text/javascript"></script>
    </head>
    <body>

        <main class="sherlock_wrapper">

            <div class="sherlock_container">
                
                <?php include_once $page; ?>
                
            </div>

        </main>

    </body>
</html>
                