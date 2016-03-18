<?php
    
    if ( !isset( $_POST['id'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    // ↓↓↓↓↓ get all information on a requested exercise (called with AJAX) ↓↓↓↓↓
    
    if ( !isset( $_SESSION ) ) {

        session_start();
        
        require_once 'config.php';
        require_once 'db.php';
        
        $exercise = DB::getExercise( $_POST['id'] );
        $_SESSION['exercise_info'] = serialize( $exercise );
        
        echo $result = json_encode ( $exercise );
        
    }
    
?>