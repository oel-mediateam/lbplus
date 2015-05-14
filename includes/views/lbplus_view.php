<?php


    require_once 'includes/exercise.php';

    $exercise = new Exercise( 'includes/data/exercise/sample_exercise.json' );
    $actions = $exercise->getActions();
    $rewindAction = $exercise->getRewindAction();

?>

<section class="lbplus_view">

    <div class="lbplus_status_msg blink">Demonstrating...</div>

    <h1>LiveButton+ Presents</h1>

    <div class="lbplus_interaction_wrapper">

        <div class="lbplus_media">
            <div class="overlay"><div id="videoPlayBtn">START</div></div>
            <div id="ytv" data-video-id="iBfIFgxf2sw"></div>
        </div>

        <div class="lbplus_actions">

            <h4>Actions</h4>

            <?php

                $count = 0;

                foreach( $actions as $action ) {

                    $button = '<div class="btn disabled" data-cooldown="' . $action->cooldown . '" data-action-id="' . ++$count . '">';
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
            <div class="time">
                <span class="elapsed">--:--</span>
                <span class="duration">--:--</span>
            </div>
        </div>
    </div>

    <div class="main_controls">

        <?php

            if ( $rewindAction->enabled ) {

                $rewindButton = '<div class="btn rewind' . ( ( $rewindAction->graded ) ? ' graded ' : ' ' ) . 'disabled" data-cooldown="' . $rewindAction->cooldown . '" data-action="btnRewind">';
                $rewindButton .= '<span class="limits" data-limit="' . $rewindAction->limits . '">' . $rewindAction->limits . '</span>';
                $rewindButton .= '<span class="icon"><span class="icon-' . $rewindAction->icon . '"></span></span>';
                $rewindButton .= '<span class="action_name">' . $rewindAction->name . '</span>';
                $rewindButton .= '<span class="cooldown"><span class="progress"></span></span></div>';

                echo $rewindButton;

            }

        ?>

    </div>

</nav>