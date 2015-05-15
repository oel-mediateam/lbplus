<?php

    define('ABSPATH','localhost');
    session_start();

    $_SESSION['logged'] = true;

    include_once 'includes/config.php';
    include_once 'includes/functions.php';

    // TO DO: connect to database & related
    require_once 'includes/db.php';

    // TO DO: get user info from LTI
    // TO DO: check current status to determine view

    // DEV ONLY
    if ( isset( $_GET['switch'] ) ) {

        $view = $_GET['view'];

    } else {

        $view = 'tool';

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

                <?php

                    ( $view === 'score' ) ? include_once 'includes/views/score_view.php' : include_once 'includes/views/lbplus_view.php';

                ?>

        </main>

    </body>
    <script src="scripts/jquery.js" type="text/javascript"></script>
    <script src="vendors/moment.min.js" type="text/javascript"></script>
    <script src="scripts/lbplus.js" type="text/javascript"></script>
</html>