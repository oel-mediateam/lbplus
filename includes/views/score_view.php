<?php

    if ( !isset($_SESSION) ) {

        session_start();

        $exercise_data = $_SESSION['exercise_data']['exercise'];
        $student_data = $_SESSION['student_data'];

        unset( $_SESSION['exercise_data'] );
        unset( $_SESSION['student_data'] );
        session_destroy();

        $exercise_actions = $exercise_data['actions'];

        $positiveEarned = 0;
        $negativeEarned = 0;
        $possilbePoints = 0;
        $percentage = 0;

        $action_array = array();

        foreach ( $exercise_actions as $value ) {

            $action = array();
            $action['id'] = $value['id'];
            $action['name'] = $value['name'];
            $action['numPos'] = count( $value['positions'] );
            $action['totalPoint'] = $value['points'] * $action['numPos'];

            $possilbePoints += $action['totalPoint'];

            array_push( $action_array, $action );

        }

        foreach ( $student_data as $value ) {

            if ( isset( $value['positive'] ) ) {

                $positiveEarned += $value['positive'];

            }

            if ( isset( $value['negative'] ) ) {

                $negativeEarned += $value['positive'];

            }

        }

        $percentage = round( $positiveEarned / $possilbePoints, 1);

        function scoreMessage( $score ) {

            $msg = "";

            switch ( true ) {

                case $score < 30:
                 $msg = 'Uh-oh!';
                 break;
                case $score < 50:
                 $msg = 'No good!';
                 break;
                case $score < 70:
                 $msg = 'Need more work!';
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

    <h1>Your Score</h1>

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

                    echo '<p>' . $earned . ' / ' . $value['totalPoint'] . ' points</p>';

                }

            ?>

<!--
            <p>OMG! OMG! OMG!</p>
            <p>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-half"></span>
                 4.5/5
            </p>
            <p>Shut up! ... and take my money!</p>
            <p>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                 4/5
            </p>
            <p>Pew! Pew!</p>
            <p>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                 3/5
            </p>
            <p>Excited!</p>
            <p>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                 5/5
            </p>
            <p>Power up!</p>
            <p>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-full"></span>
                <span class="icon-star-half"></span>
                 4.5/5
            </p>
-->

        </div>

        <div class="miss-hits">
<!--
            <p><strong>Number of incorrect tags:</strong></p>
            <p>Power up! &times;5</p>
            <p>OMG! OMG! OMG! &times;999</p>
            <p>Shut up! ... and take my money! &times;123</p>
-->
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

            <div class="btn new">
                <span class="action_name">New</span>
            </div>

            <div class="btn retake">
                <span class="action_name">Retake</span>
            </div>

        </div>

        <div class="right">

<!--
            <div class="btn previous">
                <span class="action_name">Previous</span>
            </div>

            <div class="btn next">
                <span class="action_name">Next</span>
            </div>
-->

        </div>

    </div>

</nav>