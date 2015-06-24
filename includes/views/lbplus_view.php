<?php

    // if started session data is not true
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();

    }

    require_once 'includes/exercise.php';
    require_once 'includes/functions.php';

    $exercise = new Exercise( $_SESSION['json'] );
    $actions = $exercise->getActions();
    $rewindAction = $exercise->getRewindAction();
    
    $attempt = DB::getAttempted( $_SESSION['signed_in_user_id'], $_SESSION['exercise_id'] );
    
    if ( $attempt >= $_SESSION['exercise_attempts'] ) {
        
        exit( "You already attempt this exercise!" );
        
    } else {
        
        $_SESSION['user_exercise_id'] = DB::setUserExercise( $_SESSION['signed_in_user_id'], $_SESSION['exercise_id'], ( $attempt + 1 ) );
        
    }

?>

<section class="lbplus_view">

<!--     <div class="lbplus_status_msg blink">Demonstrating...</div> -->

    <h1><?php echo $exercise->name; ?></h1>

    <div class="lbplus_interaction_wrapper">

        <div class="lbplus_media">
            <div class="overlay"><div id="videoPlayBtn">START</div></div>
            <div id="ytv" data-video-id="<?php echo $_SESSION['video']; ?>" data-start="<?php echo $exercise->videoStart; ?>" data-end="<?php echo $exercise->videoEnd; ?>"></div>
        </div>

        <div class="lbplus_actions">

            <h4><?php echo $exercise->actionHeading; ?></h4>

            <?php


                foreach( $actions as $action ) {

                    $button = '<div class="btn disabled" data-cooldown="' . $action->cooldown . '" data-action-id="' . $action->id . '">';
                    $button .= '<span class="limits" data-limit="' . $action->limits . '">' . $action->limits . '</span>';

                    if ( strlen( trim( $action->icon ) ) ) {

                        $button .= '<span class="icon"><span class="icon-' . $action->icon . '"></span></span>';

                    } else {

                        $button .= '<span class="icon">' . initialism( $action->name ) . '</span>';

                    }

                    if ( strlen( $action->name ) > 20 ) {

                        $button .= '<span class="action_name long">' . $action->name . '</span>';

                    } else {

                        $button .= '<span class="action_name">' . $action->name . '</span>';

                    }

                    $button .= '<span class="cooldown"><span class="progress"></span></span></div>';

                    echo $button;

                }

            ?>

        </div>

    </div>

</section>

<nav class="lbplus_controls">

    <div class="progress_bar_holder">

        <!-- Tags go here -->

        <div class="progress_bar">
            <span class="progressed"></span>
            <?php

                if ( $exercise->showVideoTimecode ) {

                    echo '<div class="time"><span class="elapsed">--:--</span><span class="duration">--:--</span></div>';

                }

            ?>
        </div>
    </div>

    <div class="main_controls">

        <?php

            if ( $rewindAction->enabled ) {

                $rewindButton = '<div class="btn rewind' . ( ( $rewindAction->graded ) ? ' graded ' : ' ' ) . 'disabled" data-cooldown="' . $rewindAction->cooldown . '" data-action-id="' . $rewindAction->id . '" data-length="' . $rewindAction->length . '">';
                $rewindButton .= '<span class="limits" data-limit="' . $rewindAction->limits . '">' . $rewindAction->limits . '</span>';
                $rewindButton .= '<span class="icon"><span class="icon-' . $rewindAction->icon . '"></span></span>';
                $rewindButton .= '<span class="action_name">' . $rewindAction->name . '</span>';
                $rewindButton .= '<span class="cooldown"><span class="progress"></span></span></div>';

                echo $rewindButton;

            }

        ?>

    </div>

</nav>

<?php unset( $_SESSION['video'], $_SESSION['json'] ); ?>