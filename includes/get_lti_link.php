<?php

if ( $_POST['type'] == 'ext_content_return_url' ) {
    
    echo $_POST['return_url'] . '/?return_type=lti_launch_url&url=https%3A%2F%2Fmedia.uwex.edu%2Fapp%2Fsherlock%2F%3Fexercise%3D' .$_POST['id'];
    
}
  
?>