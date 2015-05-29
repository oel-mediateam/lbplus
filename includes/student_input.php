<?php

    if ( !isset( $_POST['student'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    } else {

        if ( !isset($_SESSION) ) {

            session_start();

            require_once 'functions.php';

            $data = $_SESSION['exercise_data'];
            $exercise_actions = $data['exercise']['actions'];
            array_push( $exercise_actions, $data['exercise']['rewind'] );

            $inputs = $_POST['student'];

            $student_action_arrays = array();

            foreach( $inputs as $student_input ) {

                $student_action = array();
                $student_action['id'] = $student_input['id'];
                $student_action['name'] = $student_input['name'];
                $student_action['timestamped'] = $student_input['timestamped'];
                $student_action['positive'] = 0;

                foreach ( $exercise_actions as $action ) {

                    if ( $student_action['id'] == getValue( $action['id'], 'rwd' ) ) {

                        if ( isset( $action['positions'] ) ) {

                            foreach ( $action['positions'] as $pos ) {

                                $time = toSeconds( $student_action['timestamped'] );
                                $begin = toSeconds( $pos['begin'] );
                                $end = toSeconds( $pos['end'] );

                                if ( $time >= $begin && $time <= $end ) {

                                    $student_action['positive'] = $action['points'];
                                    break;

                                }

                            }

                            if ( $student_action['positive'] <= 0 ) {

                                $student_action['negative'] = $action['miss'];

                            }

                        } else {

                            $student_action['positive'] = $action['points'];

                        }

                        break;

                    }

                }

                array_push( $student_action_arrays, $student_action );

            }

            $_SESSION['student_data'] = $student_action_arrays;

            $file = 'data/student/demo.json';
            $content = json_encode( $student_action_arrays );
            $fp = fopen( $file, 'wb' );

            if ( $fp ) {

                if ( fwrite( $fp, $content ) === false ) {

                    unlink( $fp );
                    exit( 'Error writing data to file.' );

                } else {

                    fclose( $fp );
                    echo true;

                }

            } else {

                exit( 'Error writing data to file.' );

            }


/*
            $total_positive_score = 0;
            $total_negative_score = 0;
            $point_possible = 0;

            foreach ( $exercise_actions as $action ) {

                if (  $action['id'] != "rwd" ) {

                    $point_possible += $action['points'];

                }

                foreach ( $student_action_arrays as $student_action) {

                    if ( $student_action['id'] == $action['id'] ) {

                        if ( $student_action['positive'] > 0) {

                            $total_positive_score += $student_action['positive'];

                        } else {

                            $total_negative_score +=$student_action['negative'];

                        }

                        break;

                    }

                }

            }

            $total_score = $total_positive_score - $total_negative_score;
            $percentage = round( $total_score / $point_possible, 1 );
*/

        } else {

            exit( 'Invalid session!' );

        }

    }


?>