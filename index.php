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

        <!-- removed in production -->
        <div class="switch-view">
            <p>
                <?php

                    echo ( $view === 'score' ) ? 'Score Interface View' : '"Tool" Interface View';

                ?>
            </p>
            <h4>DEV/DEMO TOOLS</h4>
            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="view" value="<?php echo ( $view === 'score' ) ? 'tool' : 'score'; ?>" />
                <input type="submit" name="switch" value="<?php echo ( $view === 'score' ) ? 'Show Tool View' : 'Show Score View'; ?>" />
                <button type="button" id="transitionBtn">Show Transition Overlay</button>
                <button type="button" id="stopVideoBtn" disabled>Pause Video</button>
            </form>
            <h4>THE LOG :: LA BÛCHE :: EL REGISTRO :: DAS PROTOKOLL</h4>
            <div class="dev-log"></div>
        </div>
        <!-- removed in production -->

    </body>
    <script src="scripts/jquery.js" type="text/javascript"></script>
    <script src="vendors/moment.min.js" type="text/javascript"></script>
    <script src="scripts/lbplus.js" type="text/javascript"></script>

    <!-- removed in production -->
    <script type="text/javascript">
        $( document ).ready( function() {

            var changed = 0;
            var playerState = 0;

            $( '#transitionBtn' ).on( 'click', function() {

                if ( changed === 0) {

                    $( '.lbplus_wrapper' ).showTransition( 'Something Completed', 'Calculating something. Please wait...forever.' );
                    $( this ).html( 'Hide Transition Overlay' );

                    changed = 1;

                } else {

                    $( this ).hideTransition();
                    $( this ).html( 'Show Transition Overlay' );

                    changed = 0;

                }

            } );

            $( '#stopVideoBtn' ).on( 'click', function() {

                playerState = video.player.getPlayerState();

                if ( playerState === 1 ) {

                    video.player.pauseVideo();

                    $( this ).html( 'Play Video' );

                } else if ( playerState === 2 ) {

                    video.player.playVideo();

                    $( this ).html( 'Pause Video' );

                }

            } );

        } );
    </script>
    <!-- removed in production -->

</html>