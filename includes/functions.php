<?php
    
if ( !isset( $_SESSION ) ) {
    
    header( 'HTTP/1.0 404 File Not Found', 404 );
    include 'views/404.php';
    exit();

}
    
/**
 * Get page view based the header request.
 * @param mix $request The request from the header
 * @return string Returns the path to the appropriate view.
 */
function getView( $request ) {
    
    // if request is from an exercise selection
    if ( isset( $request['view'] ) ) {
        
        $view = 'includes/views/' . $request['view'] . '.php';
        return $view;
        
    }
    
    return 'includes/views/landing.php';
    
}
    
?>