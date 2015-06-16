<?php
    
    // start the session
    session_start();
    
    include_once 'includes/config.php';
    include_once 'includes/db.php';
    
    if ( isset( $_POST['start'] ) ) {
        
        $view = 'includes/views/' . $_POST['view'] . '.php';
        $scripts = '<script src="vendors/moment.min.js" type="text/javascript"></script><script src="scripts/lbplus.js" type="text/javascript"></script>';
        
        if ( isset( $_REQUEST['exercise'] ) ) {
            
            $exercise = DB::getExercise( $_REQUEST['exercise'] );
            $_SESSION['video'] = $exercise['video_src'];
            $_SESSION['json'] = $exercise['markup_src'];
            
        }
        
    } else {
        
        $view = 'includes/views/select_user.php';
        $scripts = '<script src="scripts/form.js" type="text/javascript"></script>';
        
    }

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
    <?php echo $scripts; ?>
    
</html>