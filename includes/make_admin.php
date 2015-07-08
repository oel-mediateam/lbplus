<?php

    if ( !isset( $_REQUEST['u'] ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();
        
    } else {
        
        
        
    }
    
?>