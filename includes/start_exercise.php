<?php
    
    // if started session data is not true
    if ( !isset( $_POST['begin'] ) ) {

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
            
            $exercise_info = unserialize( $_SESSION['exercise_info'] );
            
            if ( !isLTIUser() ) {
                
                $attempt = DB::getAttempted( $_SESSION['signed_in_user_id'], $exercise_info['exercise_id'] );
                
                if ( !$exercise_info['allow_retake'] ) {
                    
                    if ( $attempt >= $exercise_info['attempts'] ) {
                    
                        exit( "You already attempted this exercise! Dev notes: in the future, coming soon&trade;, you will see the score of the latest attempt instead of this message. <a href='?page=exercises'>Back to Exercises</a>" );
                        
                    } 
                    
                }
                
                $_SESSION['user_exercise_id'] = DB::setUserExercise( $_SESSION['signed_in_user_id'],
                                                $exercise_info['exercise_id'], ( $attempt + 1 ) );
                
            }
                                              
        }
        
    }
    
?>