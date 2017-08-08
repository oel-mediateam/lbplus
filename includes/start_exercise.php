<?php
    
    if ( !isset( $_POST['begin'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    // ↓↓↓↓↓ prepared the database before starting the exercise (called with AJAX) ↓↓↓↓↓
    
    if ( !isset( $_SESSION ) ) {

        session_start();
        
        require_once 'functions.php';
        
        if ( isset( $_SESSION['isReview'] ) ) {
            
            unset( $_SESSION['isReview'] );
            
        }
        
        if ( isset( $_SESSION['student_data'] ) ) {
            
            unset( $_SESSION['student_data'] );
            
        }
        
        if ( !isLTIUser() ) {
            
            require_once 'config.php';
            require_once 'db.php';
            
            $exercise_info = unserialize( $_SESSION['exercise_info'] );
            
            if ( $exercise_info['exrs_type_id'] == 5 ) {
                
                $_SESSION['user_exercise_id'] = DB::setUserExercise( $_SESSION['signed_in_user_email'],
                                     $exercise_info['exercise_id'] );
                                     
                echo $_SESSION['user_exercise_id'];
                
            } else {
                
                echo $exercise_info['exercise_id'];
                
            }
            
        }
                                          
    }
    
?>