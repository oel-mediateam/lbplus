<?php

    if ( !isset( $_SESSION ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();

    }
    
    require_once 'includes/exercise_class.php';
    require_once 'includes/functions.php';
    
    if ( isset( $_REQUEST['oauth_consumer_key'] ) && isset( $_REQUEST['exercise'] ) ) {           
        
        require_once 'includes/config.php';
        require_once 'includes/admin/lti/LTI_Sherlock.php';
        
        saveLTIData( $_REQUEST );
        
        $lti = unserialize( $_SESSION['lti'] );
        
        if ( $exerciseToGet = DB::getLTIExercise( $lti['exercise'], getLTICourseID(), getLTIData('tool_consumer_info_product_family_code') ) ) {
            
            $_SESSION['exercise_info'] = serialize( $exerciseToGet );
            $exercise_exists = true;
            
        } else {
            
            $exercise_exists = false;
            
        }
        
    } else {
        
        $exerciseToGet = DB::getActiveNAExercise( $_GET['exercise'] );
        $_SESSION['exercise_info'] = serialize( $exerciseToGet );
        
        if ( sizeof( $exerciseToGet ) ) {
            $exercise_exists = true;
        } else {
            $exercise_exists = false;
        }
        
    }
    
    if ( $exercise_exists ) {
        
        $exercise = new Exercise( $exerciseToGet['markup_src'] );
        $actions = $exercise->getActions();
        $rewindAction = $exercise->getRewindAction();
        
        $_SESSION['videoSegment'] = serialize( array( $exercise->videoStart, $exercise->videoEnd ) );
        
    }

?>
<div id="sherlock-wrapper">
    
    <?php if ( $exercise_exists ) { ?>
    
    <nav class="navbar darken">
        
        <div class="container">
            
            <div class="site-name"><?php echo $exercise->name; ?></div>
            <div class="exercise-type-mode <?php echo strtolower($exerciseToGet['exr_type_name']); ?>"><?php echo $exerciseToGet['exr_type_name']; ?></div>
            
        </div>
        
    </nav>
    
    <div class="container body">
    
        <div class="sherlock_view" data-mode="<?php echo strtolower($exerciseToGet['exr_type_name']); ?>">

        <div class="sherlock_interaction_wrapper">
    
            <div class="sherlock_media">
                
                <div class="sherlock_status_msg hide"></div>
                
                <div class="overlay">
                    <div id="videoPlayBtn">START</div>
                    <div class="reasoningBox hide">
                        <span class="reasoning"></span>
                        <span class="action"></span>
                    </div>
                </div>
                
                <div id="ytv" data-video-id="<?php echo $exerciseToGet['video_src']; ?>" data-start="<?php echo $exercise->videoStart; ?>" data-end="<?php echo $exercise->videoEnd; ?>"></div>
            </div>
    
            <div class="sherlock_actions">
    
                <h4><?php echo $exercise->actionHeading; ?></h4>
                
                <div class="exr_actions">
                    
                <?php
    
                    foreach( $actions as $action ) {
    
                        $button = '<div class="btn disabled" data-cooldown="' . $action->cooldown . '" data-action-id="' . $action->id . '">';
                        
                        if ( $exercise->displayLimits ) {
                            
                            $button .= '<span class="limits" data-limit="' . $action->limits . '">' . $action->limits . '</span>';
                            
                        } else {
                            
                            $button .= '<span class="limits hide" data-limit="' . $action->limits . '">' . $action->limits . '</span>';
                            
                        }
    
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
                
                <!-- rewind button -->
                <?php
        
                    if ( $rewindAction->enabled ) {
                        
                        echo '<div class="fixed_bottom_action">';
                        
                        $rewindButton = '<div class="btn rewind' . ( ( $rewindAction->graded ) ? ' graded ' : ' ' ) . 'disabled" data-cooldown="' . $rewindAction->cooldown . '" data-action-id="' . $rewindAction->id . '" data-length="' . $rewindAction->length . '">';
                        
                        if ( $exercise->displayLimits ) {
                            
                            $rewindButton .= '<span class="limits" data-limit="' . $rewindAction->limits . '">' . $rewindAction->limits . '</span>';
                            
                        } else {
                            
                            $rewindButton .= '<span class="limits hide" data-limit="' . $rewindAction->limits . '">' . $rewindAction->limits . '</span>';
                            
                        }
                        
                        $rewindButton .= '<span class="icon"><i class="fa fa-' . $rewindAction->icon . '"></i></span>';
                        $rewindButton .= '<span class="action_name">' . $rewindAction->name . '</span>';
                        $rewindButton .= '<span class="cooldown"><span class="progress"></span></span></div>';
        
                        echo $rewindButton;
                        
                        echo '</div>';
        
                    }
        
                ?>
                <!-- end rewind button -->
    
            </div>
    
        </div>

</div>

<div class="sherlock_controls">
    
    <?php

        if ( $exercise->showVideoTimecode ) {

            echo '<div class="elapsed">00:00</div>';

        }

    ?>
    
    <div class="progress_bar_holder">

        <!-- Tags go here -->
        
        <div class="progress_bar">
            <div class="tag_hints_holder"></div>
            <span class="scrubber"></span>
            <span class="progressed"></span>
        </div>
        
    </div>
    
    <?php

        if ( $exercise->showVideoTimecode ) {

            echo '<div class="duration">00:00</div>';

        }

    ?>

</div>

<?php } else { ?>
    
    <div class="error_msg">
        <h2><i class="fa fa-search fa-3x" aria-hidden="true"></i><br>EXERCISE NOT FOUND!</h2>
        <p>The exercise that you requested cannot be found or may not be available yet.<br>
            <a href="?view=exercises">Back to Exercises</a>
        </p>
    </div>

<?php } ?>
    </div>
</div>