<?php

    // 1 is lbplus view; 0 is score view

    $request_view = 1;

    if ( $request_view ) {

        $view = 'includes/views/lbplus_view.php';

    } else {

        $view = 'includes/views/score_view.php';

    }

?>


<!DOCTYPE html>
<html>
    <head>
        <title>LB+</title>
    </head>
    <body>
        <?php include_once $view; ?>
    </body>
</html>