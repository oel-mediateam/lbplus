<?php

    // DEV ONLY
    if ( isset( $_POST['switch'] ) ) {

        $view = $_POST['view'];

    } else {

        $view = 'tool';

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

                <?php

                    ( $view === 'score' ) ? include_once 'includes/views/score_view.php' : include_once 'includes/views/lbplus_view.php';

                ?>

        </main>
        <div class="switch-view">
            <h2>
                <?php

                    echo ( $view === 'score' ) ? 'Score Interface View' : '"Tool" Interface View';

                ?>
            </h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="view" value="<?php echo ( $view === 'score' ) ? 'tool' : 'score'; ?>" />
                <input type="submit" name="switch" value="<?php echo ( $view === 'score' ) ? 'Show Tool View' : 'Show Score View'; ?>" />
            </form>
        </div>
    </body>
    <script src="scripts/jquery.js" type="text/javascript"></script>
    <script src="scripts/lbplus.js" type="text/javascript"></script>
    <script src="vendors/ytiframe.js" type="text/javascript"></script>
</html>