<?php


    require_once 'includes/exercise.php';

    $exercise = new Exercise( 'includes/data/exercise/sample_exercise.json' );
    $actions = $exercise->readExercise();

?>

<section class="lbplus_view">

    <div class="lbplus_status_msg blink">Demonstrating...</div>

    <h1>LiveButton+ Presents</h1>

    <div class="lbplus_interaction_wrapper">

        <div class="lbplus_media">
            <div class="overlay"><div id="videoPlayBtn">START</div></div>
            <div id="ytv" data-videoId="iBfIFgxf2sw"></div>
        </div>

        <div class="lbplus_actions">

            <h4>Actions</h4>

            <?php

                foreach( $actions as $action ) {

                    $button = '<div class="btn disabled" data-cooldown="' . $action->cooldown . '" data-action="btnOne">';
                    $button .= '<span class="limits" data-limit="' . $action->limits . '">' . $action->limits . '</span>';

                    if ( strlen( trim( $action->icon ) ) ) {

                        $button .= '<span class="icon"><span class="icon-' . $action->icon . '"></span></span>';

                    } else {

                        $button .= '<span class="icon">ts</span>';

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

        <div class="btn rewind disabled" data-cooldown="3" data-action="btnRewind">
            <span class="limits" data-limit="3">3</span>
            <span class="icon"><span class="icon-fire"></span></span>
            <span class="action_name">More! More!</span>
            <span class="cooldown"><span class="progress"></span></span>
        </div>

    </div>

</nav>