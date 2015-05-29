<?php

    if ( !isset($_SESSION) ) {

        session_start();

        require_once '../functions.php';

        $exercise_data = $_SESSION['exercise_data']['exercise'];
        $student_data = $_SESSION['student_data'];

        unset( $_SESSION['exercise_data'] );
        unset( $_SESSION['student_data'] );
        session_destroy();

        $exercise_actions = $exercise_data['actions'];
        $allowNew = getValue( $exercise_data['allowNew'], false );
        $allowRetake = getValue( $exercise_data['allowRetake'], false );

        $bonusAllowed = getValue( $exercise_data['rewind']['graded'], true );
        $bonusId = getValue( $exercise_data['rewind']['id'], 'rwd' );

        $positiveEarned = 0;
        $negativeEarned = 0;
        $bonusPointsEarned = 0;
        $possilbePoints = 0;
        $percentage = 0;

        $numIncorrects = 0;

        $action_array = array();
        $neg_action_array = array();

        foreach ( $exercise_actions as $value ) {

            $action = array();
            $action['id'] = $value['id'];
            $action['name'] = $value['name'];
            $action['numPos'] = count( $value['positions'] );
            $action['totalPoint'] = $value['points'] * $action['numPos'];

            $possilbePoints += $action['totalPoint'];

            array_push( $action_array, $action );

        }

        if ( $bonusAllowed ) {

            foreach ( $student_data as $value ) {

                if ( $value['id'] == $bonusId ) {

                    $bonusPointsEarned += $value['positive'];

                } else {

                    if ( isset( $value['positive'] ) ) {

                        $positiveEarned += $value['positive'];

                    }

                }
            }

        } else {

            foreach ( $student_data as $value ) {

                if ( isset( $value['positive'] ) ) {

                    $positiveEarned += $value['positive'];

                }

            }

        }

        foreach ( $student_data as $value ) {

            if ( isset( $value['negative'] ) ) {

                $negativeEarned += $value['negative'];
                $numIncorrects++;
                array_push($neg_action_array, $value['id']);

            }

        }

        $percentage = round( ( $positiveEarned + $bonusPointsEarned ) / $possilbePoints, 1);

        function scoreMessage( $score ) {

            $msg = "";

            switch ( true ) {

                case $score < 30:
                 $msg = 'Oh, my! ...';
                 break;
                case $score < 50:
                 $msg = 'Improvement is needed.';
                 break;
                case $score < 70:
                 $msg = 'Need a bit more work.';
                 break;
                case $score < 90:
                 $msg = 'Good!';
                 break;
                default:
                 $msg = 'Excellent!';
                 break;

            }

            return $msg;

        }

    }

?>

<section class="lbplus_view">

    <h1><?php echo getValue( $exercise_data['scoreViewHeading'], 'Your Score' ); ?></h1>

    <div class="score_view">

        <div class="actions_results">

            <?php

                foreach ( $action_array as $value ) {

                    $earned = 0;
                    echo '<p>'. $value['name'] .'</p>';

                    foreach ( $student_data as $s_value ) {

                        if ( $s_value['id'] == $value['id'] ) {

                            if ( isset( $s_value['positive'] ) ) {

                                $earned += $s_value['positive'];

                            }

                        }

                    }

                    $numStars = round( ( $earned / $value['totalPoint'] ) * 5, 1 );
                    $numStars = explode( '.', (string) $numStars );
                    $numFullStars = $numStars[0];
                    $numHalfStar = ( isset( $numStars[1] ) ) ? 1 : 0;
                    $numEmptyStars = 5 - ( $numFullStars + $numHalfStar );
                    $stars = '';

                    for( $i = 0; $i < $numFullStars; $i++ ) {

                        $stars .= '<span class="icon-star-full"></span> ';

                    }

                    for( $j = 0; $j < $numHalfStar; $j++ ) {

                        $stars .= '<span class="icon-star-half"></span> ';

                    }

                    for( $k = 0; $k < $numEmptyStars; $k++ ) {

                        $stars .= '<span class="icon-star-empty"></span> ';

                    }

                    echo '<p>' . $stars . '&nbsp;' . $earned . '/' . $value['totalPoint'] . ' pts</p>';

                }

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

            <p>You made <strong><?php echo $numIncorrects; ?></strong> incorrect <?php echo ( $numIncorrects > 1 ) ? 'tags' : 'tag'; ?>.</p>

            <?php

                $negs = array_count_values($neg_action_array);

                foreach ( $exercise_actions as $act ) {

                    foreach ( $negs as $key => $value ) {

                        if ( $key == $act['id'] ) {

                            echo '<p class="incorrect">' . $act['name'] . ' &times;' . $value . ' @ ' . $act['miss'] . 'pts. ea.</small></p>';

                        }

                    }

                }

            ?>

            <p>Total incorrect points: <strong><?php echo $negativeEarned; ?></strong></p>

        </div>

        <div class="percentage">
            <span class="percent"><?php echo $percentage; ?>%</span>
            <span class="status"><?php echo scoreMessage( $percentage ); ?></span>
        </div>

    </div>

</section>

<nav class="lbplus_controls">

    <div class="main_controls score_view">

        <div class="left">&nbsp;</div>

        <div class="center">

            <?php

                if ( $allowNew ) {

                    if ( $allowRetake ) {

                        echo '<div class="btn new"><span class="action_name"><span class="icon-new"></span> New</span></div>';

                    } else {

                        echo '<div class="btn new full"><span class="action_name"><span class="icon-new"></span> New</span></div>';

                    }


                }

                if ( $allowRetake ) {

                    if ( $allowNew ) {

                        echo '<div class="btn retake"><span class="action_name"><span class="icon-retake"></span> Retake</span></div>';

                    } else {

                        echo '<div class="btn retake full"><span class="action_name"><span class="icon-retake"></span>  Retake</span></div>';

                    }

                }

                if ( !$allowNew && !$allowRetake ) {

                    echo '&nbsp;';

                }

            ?>

        </div>

        <div class="right">

<!--
            <div class="btn previous">
                <span class="action_name"><span class="icon-previous"></span> Back</span>
            </div>

            <div class="btn next">
                <span class="action_name">Next <span class="icon-next"></span></span>
            </div>
-->

        </div>

    </div>

</nav>