<?php
    
    // start the session
    session_start();
    
    require_once 'includes/config.php';
    require_once 'includes/db.php';
    require_once 'includes/functions.php';
    
    if ( !isset( $_REQUEST['lti_message_type'] ) ) {
        
        require_once 'includes/google_signin.php';
        
    } else {
        
        $_SESSION['lti'] = serialize( $_REQUEST );
        
    }
    
    $page = getView( $_REQUEST );
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Professional Training Development</title>
        <link href="css/jquery-ui.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/lbplus.css" rel="stylesheet" type="text/css" media="all" />
        <link href="fonts/icomoon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/demo.css" rel="stylesheet" type="text/css" media="all" /> <!-- demo css; remove for live -->
    </head>
    <body>

        <main class="lbplus_wrapper" role="main">

            <div class="lbplus_container">
                
                <?php include_once $page; ?>
                
            </div>

        </main>

    </body>
    
    <script src="scripts/jquery.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui.js" type="text/javascript"></script>
    <script src="scripts/moment.min.js" type="text/javascript"></script>
    <script src="scripts/lbplus.js" type="text/javascript"></script>
    
</html>
                