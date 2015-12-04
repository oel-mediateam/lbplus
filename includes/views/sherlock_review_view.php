<?php

    if ( !isset( $_SESSION['exercise_info'] ) && !isset( $_SESSION['student_data'] ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();

    }
    
    require_once 'includes/exercise.php';
    require_once 'includes/functions.php';
    
    $_SESSION['isReview'] = true;
    
    $exercise_info = unserialize( $_SESSION['exercise_info'] );
    $exercise = new Exercise( $exercise_info['markup_src'] );
    $actions = $exercise->getActions();
    $rewindAction = $exercise->getRewindAction();

?>

<section class="sherlock_view" data-mode="review">
    
    <div class="sherlock_mode_msg review hide"></div>
    
    <h1><?php echo $exercise->name; ?></h1>

    <div class="sherlock_interaction_wrapper">

        <div class="sherlock_media">
            <div class="overlay"><div id="videoPlayBtn"></div></div>
            <div id="ytv" data-video-id="<?php echo $exercise_info['video_src']; ?>" data-start="<?php echo $exercise->videoStart; ?>" data-end="<?php echo $exercise->videoEnd; ?>"></div>
        </div>

        <div class="sherlock_actions">

            <!-- something can go here if any -->

        </div>

    </div>

</section>

<nav class="sherlock_controls">

    <div class="progress_bar_holder">

        <!-- Tags go here -->

        <div class="progress_bar">
            <div class="tag_hints_holder"></div>
            <span class="scrubber"></span>
            <span class="progressed"></span>
            <div class="time"><span class="elapsed">--:--</span><span class="duration">--:--</span></div>
        </div>
    </div>

    <div class="main_controls">

        <div class="btn backToScore"><a class="action_name" href="?page=score">Back to Score <span class="icon-next"></span></a></div>

    </div>

</nav>