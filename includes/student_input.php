<?php

    if ( !isset( $_POST['student'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    } else {

        if ( !isset( $_SESSION ) ) {

            session_start();
            
            require_once 'functions.php';
            
            if ( !isLTIUser() ) {
                
                require_once 'config.php';
                require_once 'db.php';
                
            }
            
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
            
            $_SESSION['student_data'] = $student_action_arrays; // save to session for score view
            
            if ( isLTIUser() ) {
                
                $directory = 'data/student/' . getLTILMS();
                
                if ( getLTIData( 'lis_result_sourcedid' ) ) {
                    
                    $fileName = getLTICourseID() . '_' . getLTIAssignmentID() . '_' . getLTIData( 'lis_result_sourcedid' ) . '_' . time();
                    
                } else {
                    
                    $fileName = getLTICourseID() . '_' . getLTIAssignmentID() . '_' . time();
                    
                }
                
                if ( !file_exists( $directory ) ) {
                    
                    mkdir( $directory, 0777, true );
                    
                }
                
                $file = $directory . '/' . $fileName . '.json';
                
            } else {
                
                $fileName = $_SESSION['user_exercise_id'] . '_' . time();
                $file = 'data/student/' . $fileName . '.json';
                
            }
            
            $content = json_encode( $student_action_arrays );
            
            $fp = fopen( $file, 'wb' );

            if ( $fp ) {

                if ( fwrite( $fp, $content ) === false ) {

                    unlink( $fp );
                    exit( 'Error writing data to file.' );

                } else {

                    fclose( $fp );
                    
                    if ( !isLTIUser() ) {
                        
                        if ( DB::updateStuSrc( $_SESSION['user_exercise_id'], $fileName ) == 0 ) {

                            exit('update failed');
                            
                        };
                        
                    }
                    
                    echo true;

                }

            } else {

                exit( 'Error opening file: ' .  $file );

            }

        } else {

            exit( 'Invalid session!' );

        }

    }
    
?>