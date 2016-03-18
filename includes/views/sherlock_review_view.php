<?php
    
    if ( !isset( $_SESSION ) ) {

        session_start();
        
    }
    
    if ( !isset( $_SESSION['exercise_info'] ) && !isset( $_SESSION['student_data'] ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();

    }
    
    require_once '../functions.php';
    
    $_SESSION['isReview'] = true;
    
    $exercise_info = unserialize( $_SESSION['exercise_info'] );
    $videoBeginEnd = unserialize( $_SESSION['videoSegment'] );

?>

<section class="sherlock_view" data-mode="review">
    
    <div class="sherlock_mode_msg review hide"></div>
    
    <h1><?php echo $videoBeginEnd[0] ?></h1>

    <div class="sherlock_interaction_wrapper">

        <div class="sherlock_media">
            <div class="overlay"><div id="videoPlayBtn"></div></div>
            <div id="ytv" data-video-id="<?php echo $exercise_info['video_src']; ?>" data-start="<?php echo $videoBeginEnd[1] ?>" data-end="<?php echo $videoBeginEnd[2]; ?>"></div>
        </div>

        <div class="sherlock_actions">
            
            <div class="reviewVideoControls">
                <div class="btn videoControls play"><span class="action_name"><span class="icon-play"></span></span></div>
                <div class="btn videoControls pause disabled"><span class="action_name"><span class="icon-paused"></span></span></div>
                <div class="btn videoControls backTen disabled"><span class="action_name"><span class="icon-retake"></span> 10</span></div>
            </div>
            
            <div class="reviewContent">
                <p>Select the red tag for more details.</p>
            </div>

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

        <div class="btn backToScore"><a id="goToScore" class="action_name" href="#">Back to Score <span class="icon-next"></span></a></div>

    </div>

</nav>