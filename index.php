<?php 
    
    session_start();
    require_once 'includes/config.php';
    require_once 'includes/db.php';
    require_once 'includes/functions.php';
    
    if ( !isset( $_POST['oauth_consumer_key'] ) ) {
        require_once 'includes/google_signin.php';
        unsetLTIData();
    } else {
        saveLTIData( $_REQUEST );
    }
    
    include_once 'includes/views/header.php';
    include_once getView( $_REQUEST );
    include_once 'includes/views/footer.php';
    
?>