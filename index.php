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
    </head>
    <body>
        <main class="lbplus_wrapper" role="main">
            <section class="lbplus_view">
                <?php include_once $view; ?>
            </section>
            <nav class="lbplus_controls">

            </nav>
        </main>
    </body>
    <script src="scripts/jquery.js" type="text/javascript"></script>
    <script src="scripts/min/lbplus.js" type="text/javascript"></script>
</html>