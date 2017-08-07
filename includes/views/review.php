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

<div class="sherlock_view review" data-mode="review">

    <div class="sherlock_interaction_wrapper">

        <div class="sherlock_media">
            <div class="sherlock_status_msg review hide"></div>
            <div class="overlay"><div id="videoPlayBtn"></div></div>
            <div id="ytv" data-video-id="<?php echo $exercise_info['video_src']; ?>" data-start="<?php echo $videoBeginEnd[0] ?>" data-end="<?php echo $videoBeginEnd[1]; ?>"></div>
        </div>

        <div class="sherlock_actions">
            
            <div class="review_content">
                <p>Select the blue tag for more details.</p>
            </div>
            
            <div class="btn play_segment disabled">
                <span class="action_name">Play Segment</span>
            </div>
            
            <div class="fixed_bottom_action">
                
                <div id="goToScore" class="btn backToScore">
                    <span class="action_name">Back to Score</span>
                    <span class="icon"><i class="fa fa-chevron-right"></i></span>
                </div>
                
            </div>

        </div>

    </div>

</div>

<div class="sherlock_controls review">
    
    <button class="playPauseBtn"><i class="fa fa-play"></i></button>
    
    <div class="progress_bar_holder">

        <!-- Tags go here -->
        
        <div class="progress_bar">
            <div class="tag_hints_holder"></div>
            <span class="scrubber"></span>
            <span class="progressed"></span>
        </div>
        
    </div>
    
    <div class="duration">00:00</div>

</div>