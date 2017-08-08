<?php
    
    if ( !isset($_SESSION) ) {
        
        session_start();

    }
    
    if ( !isset( $_SESSION['student_data'] ) && !isset( $_SESSION['lti'] ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();
        
    }
    
    require_once '../config.php';
    require_once '../functions.php';
        
    
    // get the exercise information
    $exercise_info = unserialize( $_SESSION['exercise_info'] );
    
    if ( $exercise_info['exrs_type_id'] != 3 && isset( $_SESSION['isReview'] ) === false ) {
        
        if ( !isLTIUser() ) {
            
            require_once '../db.php';
            
        } else {
            
            require_once '../admin/lti/LTI_Sherlock.php';
            
        }
    
    }
    
    // get and set data from session to variables
    $exercise_data = $_SESSION['exercise_data']['exercise'];
    $student_data = $_SESSION['student_data'];
    
    // variable holding all the available exercise actions
    $exercise_actions = $exercise_data['actions'];
    
    // variable holding the heading of the score view
    // $scoreHeading = getValue( $exercise_data['scoreViewHeading'], 'Your Score' );
    
    // variables holding the conditional flag for
    // allowing bonus (grading rewind) and
    // set the id for the rewind button
    $rewindGraded = getValue( $exercise_data['rewind']['graded'], true );
    $rewindId = getValue( $exercise_data['rewind']['id'], 'rwd' );
    
    // variables holding the number of positive,
    // negative, and bouns points earned and number of incorrect
    $positiveEarned = 0;
    $negativeEarned = 0;
    $rewindPointsEarned = 0;
    
    $starMsg = '';
    
    // varible holding the total possible points
    // excluding bonus points
    $possilbePoints = 0;
   
    // declare array variables to hold the available and
    // and result actions
    $action_array = array();
    $result_array = array();
    
    // loop through each available exercise actions
    foreach ( $exercise_actions as $action ) {
        
        // create a local array to hold
        // needed values temporarily
        $a = array();
        $a['id'] = $action['id'];
        $a['name'] = $action['name'];
        $a['missPoint'] = getValue( $action['miss'], 0 );
        $a['points'] = $action['points'];
        $a['possible'] = count( $action['positions'] );
        $a['possiblePoint'] = $a['points'] * $a['possible'];
        
        // add action points from each iteration
        // to get total possible points
        $possilbePoints += $a['possiblePoint'];
        
        // push the local array to the action array
        // declared above
        array_push( $action_array, $a );

    } // end loop
    
    // if bonus (or rewind button) need to be graded
    if ( $rewindGraded ) {
        
        // loop through student data array
        foreach ( $student_data as $student ) {
            
            // if the current iteration id is equal to the bonus id
            if ( $student['id'] == $rewindId ) {
                
                // add bonus points from each iteration
                // to get total bonus points
                $rewindPointsEarned += $student['positive'];

            }
            
        } // end loop

    }
            
    // loop through action array
    foreach ( $action_array as $action ) {
        
        $r = array();
        $r['id'] = $action['id'];
        $r['name'] = $action['name'];
        $r['points'] = $action['points'];
        $r['missPoint'] = $action['missPoint'];
        $r['possible'] = $action['possible'];
        $r['possiblePoint'] = $action['possiblePoint'];
        
        // delare a local variable to hold points earned
        $earned = 0;
        $pCount = 0;
        $nCount = 0;
        
        // inner loop to student data for comparison
        foreach ( $student_data as $student ) {
            
            // if the id matched
            if ( $student['id'] == $action['id'] ) {
                
                // if positive value is set and not equal to 0
                if ( isset( $student['positive'] ) ) {
                    
                    // add the points to the earned varible
                    $pCount++;
                    $earned += $student['positive'];
                    $positiveEarned += $student['positive'];

                }
                
                // if negative value is set and not equal to 0
                if ( isset( $student['negative'] ) ) {
                    
                    $nCount++;
                    $negativeEarned += $student['negative'];

                }

            }

        } // end inner loop
        
        $r['numCorrect'] = $pCount;
        $r['numIncorrect'] = $nCount;
        
        array_push( $result_array, $r );

    } // end loop
    
    // calculate the percentage and set it to the
    // percentage varible and to the database
    $totalEarned = $positiveEarned + $rewindPointsEarned - $negativeEarned;
    $fraction = round( $totalEarned / $possilbePoints, 2 );
    $percentage = $fraction * 100;
    
    if ( $exercise_info['exrs_type_id'] == 5 && isset( $_SESSION['isReview'] ) == false ) {
    
        if ( !isLTIUser() ) {
            
            $grade = DB::setScore( $_SESSION['user_exercise_id'], $fraction );
            
        } else {
            
             // pass score to LTI
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
                
                $outcome = new LTI_Sherlock_Outcome( $sourcedid, $fraction, '' );
                $ok = $resource_link->doOutcomesService( LTI_Resource_Link::EXT_WRITE, $outcome );
                
                if ( !$ok ) {
                    
                    echo $resource_link->ext_response;
                    exit('Something went wrong when trying to pass back grade. Please contact your instructor.');
                    
                }
                
            }
            
        }
    
    }

?>

<div class="sherlock_score_view">
    
    <div class="grade_box">
        <div class="percentage"><?php echo $percentage; ?>%</div>
        <div class="feedback"><?php echo scoreMessage( $percentage ); ?></div>
        <div class="fraction"><?php echo $totalEarned + $rewindPointsEarned . '/' . $possilbePoints; ?></div>
    </div>
    
    <div class="analysis_box">
            
        <?php
            
            $count = 1;
            
            foreach ( $result_array as $r ) {
                        
                $totalCorrect = $r['points'] * $r['numCorrect'];
                $totalIncorrect = $r['missPoint'] * $r['numIncorrect'];
                $total = ( $r['points'] * $r['numCorrect'] ) - ( $r['missPoint'] * $r['numIncorrect'] );
                $actionPercent = $r['numCorrect'] / $r['possible'];
                
                echo '<div class="box">';
                echo '<div class="top">';
                echo '<div class="progress-bar">';
                echo '<canvas id="inactiveProgress'.$count.'" class="progress-inactive" height="150px" width="150px"></canvas>';
                echo '<canvas id="activeProgress'.$count.'" class="progress-active" height="150px" width="150px"></canvas>';
                echo '<p id="progressPercent'.$count.'" data-percent="' . $actionPercent . '">' . $r['numCorrect'] . '/' . $r['possible'] . '</p>';
                echo '</div>';
                echo '<div class="name">' . $r['name'] . '</div>';
                echo '</div>';
                echo '<div class="bottom">';
                echo '<div class="heading">Number of targets</div>';
                echo '<p><strong>' . $r['possible'] . '</strong></p>';
                echo '<div class="heading">Hits</div>';
                echo '<p><strong>' . $r['numCorrect'] . '</strong></p>';
                echo '<div class="heading">Misses</div>';
                echo '<p><strong>' . $r['numIncorrect'] . '</strong></p>';
                echo '<div class="heading">Points per hit</div>';
                echo '<p><strong>' . $r['points'] . '</strong></p>';
                echo '<div class="heading">Points earned</div>';
                echo '<p><strong>' . $totalCorrect . '</strong></p>';
                echo '<div class="heading">Points deducted</div>';
                echo '<p><strong>' . $totalIncorrect . '</strong></p>';
                echo '<div class="heading">Total points possible</div>';
                echo '<p><strong>' . $r['possiblePoint'] . '</strong></p>';
                echo '</div>';
                echo '</div>';
                
                $count++;
                
            }
            
        ?>
        
    </div>
    
    <?php if ( $rewindGraded  ) { ?>
    <div class="bonus">Bonus points earned: <strong><?php echo $rewindPointsEarned; ?></strong></div>
    <? } ?>

</div>

<div class="score_controls">
        
    <?php
        
        if ( allowReview( $exercise_info['exrs_type_id' ] ) ) {
                    
            echo '<a id="goToReview" class="btn review" href="#"><span class="icon"><i class="fa fa-chevron-left"></i></span><span class="action_name">Review</span></a>';
            
        }
         
        if ( !isLTIUser() ) {
            
            
            if ( $exercise_info['exrs_type_id' ] != 5 ) {
                
                echo '<a class="btn retake" href="?exercise=' . $exercise_info['exercise_id'] . '"><span class="action_name">Retake</span></a>';
                
            }
            
            echo '<a class="btn exercises" href="?view=exercises"><span class="action_name">Exercises</span><span class="icon"><i class="fa fa-chevron-right"></i></span></a>';
        
        } else {
            
            echo '<a class="btn close" href="javascript:window.close();"><span class="icon"><i class="fa fa-close"></i></span><span class="action_name">CLOSE</span></a>';
            
        }
        
    ?>

</div>
<?php

// clear data and destory session
if ( allowReview( $exercise_info['exrs_type_id' ] ) ) {
    
    unset( 
       $_SESSION['started'],
       $exercise_data,
       $student_data,
       $action_array,
       $result_array
       
     );
    
} else {
    
    unset( $_SESSION['exercise_data'],
           $_SESSION['student_data'],
           $_SESSION['started'],
           $exercise_info,
           $exercise_data,
           $student_data,
           $action_array,
           $result_array
     );
    
}

?>