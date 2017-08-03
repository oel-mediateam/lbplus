<?php

    if ( !isset( $_POST['student'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    // ↓↓↓↓↓ prepared the student input and write to file (called with AJAX) ↓↓↓↓↓
    
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
        
        if ( $inputs != -1 ) {
        
            foreach( $inputs as $student_input ) {
    
                $student_action = array();
                $student_action['id'] = $student_input['id'];
                $student_action['name'] = $student_input['name'];
                $student_action['timestamped'] = $student_input['timestamped'];
    
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
    
                            if ( !isset( $student_action['positive'] ) ) {
    
                                $student_action['negative'] = getValue( $action['miss'], 0 );
    
                            }
    
                        }
    
                        break;
    
                    }
    
                }
    
                array_push( $student_action_arrays, $student_action );
    
            }
            
        }
        
        // save to session for score view
        $_SESSION['student_data'] = $student_action_arrays;
        
        $exercise_info = unserialize( $_SESSION['exercise_info'] );
        
        if ( $exercise_info['exrs_type_id'] == 5 ) {
            
            // set json encoded student input data for file writing
            $content = json_encode( $student_action_arrays );
            
            $doWrite = false;
            
            if ( isLTIUser() ) {
            
                if ( getLTIData( 'lis_result_sourcedid' ) ) {
                
                    $directory = 'data/student/' . getLTILMS();
                    $subdirectory = $directory . '/' . date('n-j-Y');
                    
                    if ( !file_exists( $directory ) ) {
                    
                        mkdir( $directory, 0777, true );
                        
                    }
                    
                    if ( !file_exists( $subdirectory ) ) {
                        
                        mkdir( $subdirectory, 0777, true );
                        
                    }
                    
                    $fileName = getLTIData( 'lis_result_sourcedid' ) . '_' . time();
                    $file = $subdirectory . '/' . $fileName . '.json';
                    
                    $doWrite = true;
                    
                }
                
            } else {
                
                $fileName = $_SESSION['user_exercise_id'] . '_' . time();
                $file = 'data/student/' . $fileName . '.json';
                
                $doWrite = true;
                
            }
            
            if ( $doWrite ) {
                
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
    
                    exit( 'Error opening file.' );
    
                }
                
            } else {
                
                echo true;
                
            }
            
        } else {
            
            echo true;
            
        }

    } else {

        exit( 'Invalid session!' );

    }
    
?>