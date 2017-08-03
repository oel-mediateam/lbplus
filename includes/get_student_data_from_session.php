<?php
    
    if ( !isset( $_POST['id'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    // ↓↓↓↓↓ get exercise actions from session (called with AJAX) ↓↓↓↓↓
    
    if ( !isset( $_SESSION ) ) {

        session_start();
        
        header('Content-Type: application/json');
        echo json_encode( $_SESSION['student_data'] );
        
    }
    
?>