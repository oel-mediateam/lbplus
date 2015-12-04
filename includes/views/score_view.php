<?php
    
    if ( !isset($_SESSION) ) {
        
        session_start();

    }
    
    if ( !isset( $_SESSION['student_data'] ) && !isset( $_SESSION['lti'] ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();
        
    }
    
    if ( isset( $_SESSION['isReview'] ) ) {
        
        if ( $_SESSION['isReview'] ) {
            
            require_once 'includes/config.php';
            require_once 'includes/functions.php';
            
        }
        
    } else {
    
        require_once '../config.php';
        require_once '../functions.php';
        
    }
    
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
    $scoreHeading = getValue( $exercise_data['scoreViewHeading'], 'Your Score' );
    
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
        
        // output the action name for star
        $starMsg .= '<div class="star_earned"><p>'. $action['name'] .'</p>';
        
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
        
        /******* getting stars *******/
        
        // varibles holding the result of a calcualate
        // to determine the number of stars earned out of 3
        $numStars = round( ( $earned / $action['possiblePoint'] ) * 3, 1 );
        $numStars = explode( '.', (string) $numStars );
        
        // variable holding number of full stars earned
        $numFullStars = $numStars[0];
        
        // variable holding number of half star earned
        // if applicable
        $numHalfStar = ( isset( $numStars[1] ) ) ? 1 : 0;
        
        // varible holding number of empty stars left
        $numEmptyStars = 3 - ( $numFullStars + $numHalfStar );
        
        // declare an empty stars varible
        $stars = '';
        
        // full star loop
        for( $i = 0; $i < $numFullStars; $i++ ) {
            
            // concatenate string to the stars varible
            $stars .= '<span class="icon-star-full"></span> ';

        }
        
        // half star loop
        for( $j = 0; $j < $numHalfStar; $j++ ) {
            
            // concatenate string to the stars varible
            $stars .= '<span class="icon-star-half"></span> ';

        }
        
        // empty star loop
        for( $k = 0; $k < $numEmptyStars; $k++ ) {
            
            // concatenate string to the stars varible
            $stars .= '<span class="icon-star-empty"></span> ';

        }
        
        // output the stars and total points earned
        // out of total possible for that action
        $starMsg .= '<p class="stars">' . $stars . '<br /><small>' . $pCount . '/' . $action['possible'] . '</small></p></div>';
        
        /******* end getting stars *******/

    } // end loop
    
    // calculate the percentage and set it to the
    // percentage varible and to the database
    $totalEarned = $positiveEarned + $rewindPointsEarned - $negativeEarned;
    $fraction = round( $totalEarned / $possilbePoints, 2 );
    
    $percentage = $fraction * 100;
    
    if ( $exercise_info['exrs_type_id'] != 3 && isset( $_SESSION['isReview'] ) === false ) {
    
        if ( !isLTIUser() ) {
            
                $gradeId = DB::addScore( $_SESSION['user_exercise_id'], $fraction );
                
                if ( DB::updateScore( $_SESSION['user_exercise_id'], $gradeId ) == 0 ) {
                    exit("Update score error.");
                }
            
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

<section class="sherlock_view">

    <h1><?php echo $scoreHeading; ?></h1>

    <div class="score_view">
        
        <div class="overview">
        
        <div class="percentage">
            <span class="percent"><?php echo $percentage; ?>%</span>
            <span class="status"><?php echo scoreMessage( $percentage ); ?></span>
        </div>
        
        <div class="actions_stars">

            <?php echo $starMsg; ?>

        </div>
        
        </div>
        
        <div class="clearfix"></div>
        
        <div class="analysis">
            
            <table>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Total Correct <br><small>(# of correct &times; value)</small></th>
                        <th>&minus;</th>
                        <th>Total Incorrect <br /><small>(# of incorrect &times; value)</small></th>
                        <th>=</th>
                        <th>Total</th>
                        <th class="leftBorder">Possible<br />Points</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php
                        
                        foreach ( $result_array as $r ) {
                            
                            $totalCorrect = $r['points'] * $r['numCorrect'];
                            $totalIncorrect = $r['missPoint'] * $r['numIncorrect'];
                            $total = ( $r['points'] * $r['numCorrect'] ) - ( $r['missPoint'] * $r['numIncorrect'] );
                            
                            echo '<tr>';
                            echo '<td>' . $r['name'] . '</td>';
                            echo '<td>' . $totalCorrect . ' <small>(' .$r['numCorrect'] . ' &times; ' . $r['points'] . ')</small></td>';
                            echo '<td>&nbsp;</td>';
                            echo '<td>' . $totalIncorrect . ' <small>(' . $r['numIncorrect'] . ' &times; ' . $r['missPoint'] . ')</small></td>';
                            echo '<td>&nbsp;</td>';
                            echo '<td>' . $total . '</td>';
                            echo '<td class="leftBorder">' . $r['possiblePoint'] . '</td></tr>';
                            
                        }
                        
                        echo '<tr><td colspan="5">&nbsp;</td><td class="topBorder"><strong>' . $totalEarned . '</strong></td><td class="topBorder"><strong>'. $possilbePoints .'</strong></td></tr>';
                        
                    ?>
                    
                </tbody>
            </table>

            <?php if ( $rewindGraded  ) { ?>
            <p>Bonus points earned: <strong><?php echo $rewindPointsEarned; ?></strong></p>
            <? } ?>

            <p class="totalScore">Score: <strong><?php echo $totalEarned; ?> / <?php echo $possilbePoints; ?> = <?php echo $fraction; ?> (<?php echo $percentage; ?>%)</strong></p>

        </div>

    </div>

</section>

<nav class="sherlock_controls">

    <div class="main_controls score_view">

        <div class="left">
            
            <?php
                
                if ( !isLTIUser() ) {
                    
                    if ( allowReview( $exercise_info['exrs_type_id' ] ) ) {
                    
                        echo '<a class="btn previous full" href="?review='.$exercise_info['exercise_id'].'"><span class="action_name"><span class="icon-review"></span> Review</span></a>';
                    
                    } else {
                        
                        echo '&nbsp;';
                        
                    }
                    
                }
                    
            ?>
                
        </div>

        <div class="center">

            <?php
                
                if ( !isLTIUser() ) {
                    
                    // display the new button
                    // if new exercise is allowed
                    if ( $exercise_info['allow_new'] ) {
                        
                        // if retake exercise is allowed
                        if ( $exercise_info['allow_retake'] ) {
                            
                            // output the half width button
                            echo '<div class="btn new"><span class="action_name"><span class="icon-new"></span> New</span></div>';
    
                        } else {
                            
                            // otherwise output the full width button
                            echo '<div class="btn new full"><span class="action_name"><span class="icon-new"></span> New</span></div>';
    
                        }
    
                    }
    
                    // display the retake button
                    // if retake is allowed
                    if ( $exercise_info['allow_retake'] ) {
                        
                        $url = '?retake='.$exercise_info['exercise_id'];
                        
                        // if new exercise is allowed
                        if ( $exercise_info['allow_new'] ) {
                            
                            // output the half width button
                            echo '<a class="btn retake" href="'.$url.'"><span class="action_name"><span class="icon-retake"></span> Retake</span></a>';
                            
                        } else {
                            
                            // otherwise output the full width button
                            echo '<a class="btn retake full" href="?retake='.$exercise_info['exercise_id'].'"><span class="action_name"><span class="icon-retake"></span>  Retake</span></a>';
    
                        }
    
                    }
                    
                } else {
                    
                    if ( allowReview( $exercise_info['exrs_type_id' ] ) ) {
                        
                        echo '<a class="btn previous full" href="?review='.$exercise_info['exercise_id'].'"><span class="action_name"><span class="icon-review"></span> Review</span></a>';
                        
                    } else {
                        
                        echo '&nbsp;';
                        
                    }
                    
                }
                
                // if new and retake are not allowed
                if ( !$exercise_info['allow_new'] && !$exercise_info['allow_retake'] ) {
                    
                    // output a none break space character
                    echo '&nbsp;';

                }

            ?>

        </div>

        <div class="right">

            <?php
                
                if ( !isLTIUser() ) {
                
                    echo '<a class="btn next full" href="?page=exercises"><span class="action_name"><span class="icon-selection"></span> Exercises</span></a>';
                
                } else {
                    
                    echo '<a class="btn close full" href="javascript:window.close();"><span class="action_name"><span class="icon-close"></span>  CLOSE</span></a>';
                    
                }
                
            ?>

        </div>

    </div>

</nav>
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
    
    unset( $_SESSION['exercise_info'],
           $_SESSION['exercise_data'],
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