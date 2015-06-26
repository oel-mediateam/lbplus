<?php
// if session is not set
    if ( !isset($_SESSION) ) {
        
        // start/resume the session if not already
        session_start();
        
        if ( !isset( $_SESSION['user_exercise_id'] ) ) {
        
            // redirect to 404 page
            header( 'HTTP/1.0 404 File Not Found', 404 );
            include '404.php';
            exit();
            
        }
        
        // requires the functions.php file for
        // common functions
        require_once '../config.php';
        require_once '../db.php';
        require_once '../functions.php';
        
        $exercise_info = unserialize( $_SESSION['exercise_info'] );
        
        // get and set data from session to variables
        $exercise_data = $_SESSION['exercise_data']['exercise'];
        $student_data = $_SESSION['student_data'];
        
        // variable holding all the available
        // exercise actions
        $exercise_actions = $exercise_data['actions'];
        
        // variable holding the heading of the
        // score view
        $scoreHeading = getValue( $exercise_data['scoreViewHeading'], 'Your Score' );
        
        // variables holding the conditional flags for
        // allowing new or retake exercise
        $allowNew = getValue( $exercise_data['allowNew'], false );
        $allowRetake = getValue( $exercise_data['allowRetake'], false );
        
        // variables holding the conditional flag for
        // allowing bonus (grading rewind) and
        // set the id for the rewind button
        $bonusAllowed = getValue( $exercise_data['rewind']['graded'], true );
        $bonusId = getValue( $exercise_data['rewind']['id'], 'rwd' );
        
        // variables holding the number of positive,
        // negative, and bouns points earned and
        // number of incorrect
        $positiveEarned = 0;
        $negativeEarned = 0;
        $bonusPointsEarned = 0;
        
        // varible holding the total possible points
        // excluding bonus points
        $possilbePoints = 0;
       
        // declare array variables to hold the correct and
        // and incorrect actions separately
        $action_array = array();
        $neg_action_array = array();
        
        // loop through each available exercise actions
        foreach ( $exercise_actions as $value ) {
            
            // create a local array to hold
            // needed values temporarily
            $action = array();
            $action['id'] = $value['id'];
            $action['name'] = $value['name'];
            $action['numPos'] = count( $value['positions'] );
            $action['totalPoint'] = $value['points'] * $action['numPos'];
            
            // add action points from each iteration
            // to get total possible points
            $possilbePoints += $action['totalPoint'];
            
            // push the local array to the action array
            // declared above
            array_push( $action_array, $action );

        } // end loop
        
        // if bonus (or rewind button) need to be graded
        if ( $bonusAllowed ) {
            
            // loop through student data array
            foreach ( $student_data as $value ) {
                
                // if the current iteration id is equal
                // to the bonus id
                if ( $value['id'] == $bonusId ) {
                    
                    // add bonus points from each iteration
                    // to get total bonus points
                    $bonusPointsEarned += $value['positive'];

                } else {
                    
                    // if positive value exists and not equal to 0
                    if ( isset( $value['positive'] ) && $value['positive'] != 0 ) {
                        
                        // add positive points from each iteration
                        // to get total points earned
                        $positiveEarned += $value['positive'];

                    }
                    
                    // if negative value exists and not equal to 0
                    if ( isset( $value['negative'] ) && $value['negative'] != 0 ) {
                        
                        // add negative points from each iteration
                        // to get total negative points earned
                        $negativeEarned += $value['negative'];
                        
                        // push current iteration id to negative action array
                        array_push( $neg_action_array, $value['id'] );

                    }

                }
                
            } // end loop

        } else { // if bonus button is not graded
            
            // loop through student data array
            foreach ( $student_data as $value ) {
                
                // if positive value exists and not equal to 0
                if ( isset( $value['positive'] ) && $value['positive'] != 0 ) {
                    
                    // add positive points from each iteration
                    // to get total points earned
                    $positiveEarned += $value['positive'];

                }
                
                // if negative value exists and not equal to 0
                if ( isset( $value['negative'] ) && $value['negative'] != 0 ) {
                    
                    // add negative points from each iteration
                    // to get total negative points earned
                    $negativeEarned += $value['negative'];
                    
                    // push current iteration id to negative action array
                    array_push($neg_action_array, $value['id']);

                }

            } // end loop

        } // end bonus grading condition
        
        // variable holding the number of incorrect
        // actions by calling the count function to
        // get the number of elements in the negative
        // action array
        $numIncorrects = count( $neg_action_array );
        
        // varible to hold array of repeated elements
        // in the negative action array for displaying
        // incorrect tags on the score view
        $negs = array_count_values($neg_action_array);
        
        // calculate the percentage and set it to the
        // percentage varible and to the database
        $fraction = ( $positiveEarned + $bonusPointsEarned ) / $possilbePoints;
        $gradeId = DB::addScore( $_SESSION['user_exercise_id'], $fraction );
        if ( DB::updateScore( $_SESSION['user_exercise_id'], $gradeId ) == 0 ) {
            exit("Update score error.");
        }
        $percentage = round( $fraction * 100, 1);

    }

?>

<section class="lbplus_view">

    <h1><?php echo $scoreHeading; //output the heading ?></h1>

    <div class="score_view">

        <div class="actions_results">

            <?php
                
                // loop through action array
                foreach ( $action_array as $value ) {
                    
                    // delare a local variable to hold
                    // points earned
                    $earned = 0;
                    
                    // output the action name
                    echo '<p>'. $value['name'] .'</p>';
                    
                    // inner loop to student data for comparison
                    foreach ( $student_data as $s_value ) {
                        
                        // if the id matched
                        if ( $s_value['id'] == $value['id'] ) {
                            
                            // if value is set and not equal to 0
                            if ( isset( $s_value['positive'] ) && $s_value['positive'] != 0 ) {
                                
                                // add the points to the earned varible
                                $earned += $s_value['positive'];

                            }

                        }

                    } // end inner loop
                    
                    // varibles holding the result of a calcualate
                    // to determine the number of stars earned out of 5
                    $numStars = round( ( $earned / $value['totalPoint'] ) * 5, 1 );
                    $numStars = explode( '.', (string) $numStars );
                    
                    // variable holding number of full stars earned
                    $numFullStars = $numStars[0];
                    
                    // variable holding number of half star earned
                    // if applicable
                    $numHalfStar = ( isset( $numStars[1] ) ) ? 1 : 0;
                    
                    // varible holding number of empty stars left
                    $numEmptyStars = 5 - ( $numFullStars + $numHalfStar );
                    
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
                    echo '<p>' . $stars . '&nbsp;' . $earned
                        . '/' . $value['totalPoint'] . ' pts</p>';

                } // end loop

            ?>

        </div>

        <div class="miss-hits">
           
            <p class="heading">Analysis</p>

            <p>Points earned: <strong><?php echo $positiveEarned; ?></strong></p>

            <?php if ( $bonusAllowed  ) { ?>
            <p>Bonus points earned: <strong><?php echo $bonusPointsEarned; ?></strong></p>
            <? } ?>

            <hr />

            <p>Total points earned: <strong><?php echo $positiveEarned + $bonusPointsEarned; ?></strong></p>
            <p>Total points possible: <strong><?php echo $possilbePoints; ?></strong></p>

            <hr />

            <p>You made <strong><?php echo $numIncorrects; ?></strong> incorrect 
            <?php echo ( $numIncorrects > 1 ) ? 'tags' : 'tag'; ?>.</p>

            <?php
                
                // loop through available exercise actons
                foreach ( $exercise_actions as $act ) {
                    
                    // inner loop through repeated array
                    // as key and value
                    foreach ( $negs as $key => $value ) {
                        
                        // if id matched
                        if ( $key == $act['id'] ) {
                            
                            // output the incorrect action with name,
                            // number of incorrect and points each
                            echo '<p class="incorrect">' . $act['name'] . ' &times;'
                                . $value . ' @ ' . $act['miss'] . 'pts. ea.</small></p>';
                            
                            // break out of inner loop to stop further loop
                            break;

                        }

                    } // end inner loop

                } // end loop

            ?>

            <p>Total incorrect points: <strong><?php echo $negativeEarned; // output total negative points earned ?></strong></p>

        </div>

        <div class="percentage">
            <span class="percent"><?php echo $percentage; // out the percentage ?>%</span>
            <span class="status"><?php echo scoreMessage( $percentage ); // output the message determined by the percentage ?></span>
        </div>

    </div>

</section>

<nav class="lbplus_controls">

    <div class="main_controls score_view">

        <div class="left">&nbsp;</div>

        <div class="center">

            <?php
                
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
                    
                    // if new exercise is allowed
                    if ( $exercise_info['allow_new'] ) {
                        
                        // output the half width button
                        echo '<a class="btn retake" href="?retake='.$exercise_info['exercise_id'].'"><span class="action_name"><span class="icon-retake"></span> Retake</span></a>';

                    } else {
                        
                        // otherwise output the full width button
                        echo '<a class="btn retake full" href="?retake='.$exercise_info['exercise_id'].'"><span class="action_name"><span class="icon-retake"></span>  Retake</span></a>';

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

            <a class="btn previous full" href="?page=exercises"><span class="action_name"><span class="icon-selection"></span> Exercises</span></a>
<!--             <a class="btn next" href="./"><span class="action_name">Home <span class="icon-next"></span></span></a> -->

        </div>

    </div>

</nav>
<?php

// clear data and destory session
unset( $_SESSION['exercise_data'],
       $_SESSION['student_data'],
       $_SESSION['started'],
       $exercise_data,
       $student_data,
       $action_array,
       $neg_action_array,
       $negs
     );

?>