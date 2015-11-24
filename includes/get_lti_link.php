<?php

// ↓↓↓↓↓ for getting the proper resource link for LTI (called with AJAX) ↓↓↓↓↓

if ( $_POST['type'] == 'embed' ) {
    
    echo $_POST['return_url'] . '/?return_type=lti_launch_url&url=https%3A%2F%2Fmedia.uwex.edu%2Fapp%2Fsherlock%2F%3Fexercise%3D' .$_POST['id'].'&text=Sherlock';
    
} else if ( $_POST['type'] == 'navigation' ) {
    
    echo $_POST['return_url'] . '/?return_type=lti_launch_url&url=https%3A%2F%2Fmedia.uwex.edu%2Fapp%2Fsherlock%2F%3Fexercise%3D' .$_POST['id'];
    
} else {
    
    header( 'HTTP/1.0 404 File Not Found', 404 );
    include '404.php';
    exit();
    
}
  
?>