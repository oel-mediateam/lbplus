<?php

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

        <!-- removed in production -->
        <div class="switch-view">
            <h2>
                <?php

                    echo ( $view === 'score' ) ? 'Score Interface View' : '"Tool" Interface View';

                ?>
            </h2>
            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="view" value="<?php echo ( $view === 'score' ) ? 'tool' : 'score'; ?>" />
                <input type="submit" name="switch" value="<?php echo ( $view === 'score' ) ? 'Show Tool View' : 'Show Score View'; ?>" />
                <button type="button" id="transitionBtn">Show Transition Overlay</button>
            </form>
        </div>
        <!-- removed in production -->

    </body>
    <script src="scripts/jquery.js" type="text/javascript"></script>
    <script src="vendors/ytiframe.js" type="text/javascript"></script>
    <script src="scripts/lbplus.js" type="text/javascript"></script>

    <!-- removed in production -->
    <script type="text/javascript">
        $( document ).ready( function() {

            var clicked = 0;

            $( '#transitionBtn' ).on( 'click', function() {

                if ( clicked === 0) {

                    $( '.lbplus_wrapper' ).showTransition( 'Something Completed', 'Calculating something. Please wait...forever.' );
                    $( this ).html( 'Hide Transition Overlay' );

                    clicked = 1;

                } else {

                    $( this ).hideTransition();
                    $( this ).html( 'Show Transition Overlay' );

                    clicked = 0;

                }

            } );

        } );
    </script>
    <!-- removed in production -->

</html>