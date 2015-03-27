<?php

    // DEV ONLY
    // 1 is lbplus view; 0 is score view

    $request_view = 1;

    if ( $request_view ) {

        $view = 'includes/views/lbplus_view.php';

    } else {

        $view = 'includes/views/score_view.php';

    }


?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title>LB+</title>
        <link href="css/lbplus.css" rel="stylesheet" type="text/css" media="all" />
        <link href="fonts/icomoon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/demo.css" rel="stylesheet" type="text/css" media="all" /> <!-- demo css; remove for live -->
    </head>
    <body>
        <main class="lbplus_wrapper" role="main">

                <?php include_once $view; ?>

        </main>
    </body>
    <script src="scripts/jquery.js" type="text/javascript"></script>
    <script src="scripts/lbplus.js" type="text/javascript"></script>
</html>