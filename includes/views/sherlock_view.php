<?php

    if ( !isset( $_SESSION ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();

    }
    
    require_once 'includes/exercise.php';
    require_once 'includes/functions.php';
    
    if ( isset( $_REQUEST['oauth_consumer_key'] ) && isset( $_REQUEST['exercise'] ) ) {           
        
        require_once 'includes/config.php';
        require_once 'includes/admin/lti/LTI_Sherlock.php';
        
        if ( $sourcedid = getLTIData('lis_result_sourcedid') ) {
            
            $lti = unserialize( LTI );
                
            $consumer = new LTI_Tool_Consumer( $lti['key'], LTI_Data_Connector::getDataConnector( '', 'none' ) );
            $consumer->name = $lti['name'];
            $consumer->secret = $lti['secret'];
            $consumer->enabled = TRUE;
            $consumer->lti_version = LTI_Tool_Provider::LTI_VERSION1;
            
            $resource_link = new LTI_Sherlock_Resource_Link( $consumer, getLTIData('resource_link_id') );
            $resource_link->setSetting( 'lis_outcome_service_url', getLTIData('lis_outcome_service_url') );
            $resource_link->setSetting( 'context_id', getLTIData('context_id') );
            $resource_link->setSetting( 'ext_ims_lis_basic_outcome_url', getLTIData('ext_ims_lis_basic_outcome_url') );
            
            $outcome = new LTI_Sherlock_Outcome( $sourcedid );
            
            if ( $resource_link->doOutcomesService( LTI_Resource_Link::EXT_READ, $outcome ) ) {
                
                $score = $outcome->getValue();
                
                if ( $score !== '' ) {
                    
                    exit('Sorry! You already made an attempt on this exercise.');
                    
                }
                
            }
            
        }
        
        saveLTIData( $_REQUEST );
        
        $lti = unserialize( $_SESSION['lti'] );
        
        if ( $exercise_info = DB::getLTIExercise( $lti['exercise'], getLTICourseID(), getLTIData('tool_consumer_info_product_family_code') ) ) {
            
            $_SESSION['exercise_info'] = serialize( $exercise_info );
            $exercise_exists = true;
            
        } else {
            
            $exercise_exists = false;
            
        }
        
    } else {
        
        if ( isset( $_SESSION['exercise_info'] ) ) {
            
            $exercise_info = unserialize( $_SESSION['exercise_info'] );
            $exercise_exists = true;
            
        } else {
            
            $exercise_exists = false;
            
        }
        
    }
    
    if ( $exercise_exists ) {
        
        $exercise = new Exercise( $exercise_info['markup_src'] );
        $actions = $exercise->getActions();
        $rewindAction = $exercise->getRewindAction();
        
    }

?>

<?php if ( $exercise_exists ) { ?>
<section class="sherlock_view" <?php echo $exercise_info['exrs_type_id'] == 3 ? 'data-mode=training' : ''; ?>>
    
    <div class="sherlock_mode_msg hide"></div>
    <div class="sherlock_status_msg hide"></div>

    <h1><?php echo $exercise->name; ?></h1>

    <div class="sherlock_interaction_wrapper">

        <div class="sherlock_media">
            <div class="overlay"><div id="videoPlayBtn">START</div></div>
            <div id="ytv" data-video-id="<?php echo $exercise_info['video_src']; ?>" data-start="<?php echo $exercise->videoStart; ?>" data-end="<?php echo $exercise->videoEnd; ?>"></div>
        </div>

        <div class="sherlock_actions">

            <h4><?php echo $exercise->actionHeading; ?></h4>

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

    </div>

</section>

<nav class="sherlock_controls">

    <div class="progress_bar_holder">

        <!-- Tags go here -->

        <div class="progress_bar">
            <div class="tag_hints_holder"></div>
            <span class="scrubber"></span>
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
                
                if ( $exercise->displayLimits ) {
                    
                    $rewindButton .= '<span class="limits" data-limit="' . $rewindAction->limits . '">' . $rewindAction->limits . '</span>';
                    
                } else {
                    
                    $rewindButton .= '<span class="limits hide" data-limit="' . $rewindAction->limits . '">' . $rewindAction->limits . '</span>';
                    
                }
                
                $rewindButton .= '<span class="icon"><span class="icon-' . $rewindAction->icon . '"></span></span>';
                $rewindButton .= '<span class="action_name">' . $rewindAction->name . '</span>';
                $rewindButton .= '<span class="cooldown"><span class="progress"></span></span></div>';

                echo $rewindButton;

            }

        ?>

    </div>

</nav>
<?php } else { ?>

    <script>
        $( function() { 
            $( '.sherlock_wrapper' ).showTransition( 'Exercise Not Found!', 'The exercise that you requested cannot be found or may not be available yet.<br /><a href="javascript:window.close();">&times; close</a>', true );
        });
    </script>

<?php } ?>