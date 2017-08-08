<?php 
    
    session_start();
    require_once 'includes/config.php';
    require_once 'includes/db.php';
    require_once 'includes/google_signin.php';
    require_once 'includes/functions.php';
    require_once 'includes/views/header.php';
    
    include_once getView( $_REQUEST );
    
    require_once 'includes/views/footer.php';
    
?>