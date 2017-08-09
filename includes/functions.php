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
        
        if ( isset( $_SESSION['signed_in_user_email'] ) || isLTIUser() ) {
        
            if ( isset( $request['exercise'] ) ) {
                return 'includes/views/exercise.php';
            } else {
                return 'includes/views/exercises.php';
            }
            
        }
        
        // if request is from an exercise selection
        if ( isset( $request['view'] ) ) {
            return 'includes/views/' . $request['view'] . '.php';
        }
        
        // if request is from an exercise selection
        if ( isset( $request['exercise'] ) ) {
            return 'includes/views/exercise.php';   
        }
        
        return 'includes/views/landing.php';
        
    }
    
    /**
     * Get the first letter of the first and second words in a string.
     * @param string $str The string to manipulate
     * @return string Returns the first letters of the first two words.
     */
    function initialism( $str ) {

        $result =  preg_replace('~\b(\w)|.~', '$1', $str);

        if ( isset( $result[1] ) ) {

            return $result[0] . $result[1];

        }

        return $result[0];

    }
    
    /**
     * Evalute the value and set default value if empty
     * @param mix $val The value to check
     * @param mix $default The value to set as default
     * @return mix Returns the value.
     */
    function getValue( $val, $default ) {

        $result = trim( $val );

        if ( is_bool( $val ) ) {

            if ( !isset( $val ) ) {

                $result = $default;

            } else {

                $result = $val;

            }

        } else {

            if ( strlen( $result ) <= 0 ) {

                $result = $default;

            }

        }

        return $result;

    }
    
    /**
     * Convert time string to seconds.
     * @param string $timestring The value to check
     * @return int Returns the time string in seconds.
     */
    function toSeconds( $timestring ) {

        $timestring = explode(":", $timestring);

        return $result = ( $timestring[0] * 60 ) + $timestring[1];

    }
    
    /**
     * Get the score message based on the score.
     * @param double $score The score to evaluate
     * @return string Returns the appropriate message.
     */
    function scoreMessage( $score ) {

        $msg = "";

        switch ( true ) {

            case $score < 30:
                $msg = 'Oh, my! ...';
            break;
            case $score < 50:
                $msg = 'Improvement is needed.';
            break;
            case $score < 70:
                $msg = 'Need a bit more work.';
            break;
            case $score < 80:
                $msg = 'Good!';
            break;
            default:
                $msg = 'Excellent!';
            break;

        }

        return $msg;

    }
    
    /**
     * Check to see if the user is admin.
     * @param int $id The user ID
     * @return boolean Returns true or false.
     */
    function isAdmin( $id = null ) {
        
        if ( !isset( $id ) ) {
            
            $id = $_SESSION['signed_in_user_id'];
            
        }
            
        $userRole = DB::getRole( $id );
        
        if ( $userRole >= 3 ) {
            
            return TRUE;
            
        }
            
        return FALSE;
        
    }
    
    
    /**
     * LTI RELATED FUNCTIONS
     * @ignore
     */
     
     
     /**
     * Save the LTI POST parameter to session.
     * @param mix $data The header POST data
     * @return void
     */
    function saveLTIData( $data ) {
        
        if ( $data !== null ) {
            
            $_SESSION['lti'] = serialize( $data );
            
        }
        
    }
    
    /**
     * Get the LTI data from session.
     * @param mix $query The name of the requested property
     * @return mix|null Returns the requested value or null if not found.
     */
    function getLTIData( $query ) {
        
        if ( isset( $_SESSION['lti'] ) ) {
            
            $lti = unserialize( $_SESSION['lti'] );
            
            if ( isset( $lti[$query] ) ) {
                
                return $lti[$query];
                
            } else {
                
                return null;
                
            }
            
        }
        
        return null;
        
    }
    
    /**
     * Remove LTI data from session.
     * @return void
     */
    function unsetLTIData() {
        
        if ( isset( $_SESSION['lti'] ) ) {
            
            unset( $_SESSION['lti'] );
            
        }
        
    }
    
    /**
     * Check to see if user is an LTI user.
     * @return boolean Returns true or false.
     */
    function isLTIUser() {
        
        if ( isset( $_SESSION['lti'] ) ) {
            
            $lti = unserialize( $_SESSION['lti'] );
            
            if ( isset( $lti['user_id'] ) ) {
                
                return true;
                
            }
        
        }
        
        return false;
        
    }
    
    /**
     * Get the consumer family code from the LTI parameters.
     * @return string|null Returns code or null if not found.
     */
    function getLTILMS() {
            
        if ( isset( $_SESSION['lti'] ) ) {
            
            $lti = unserialize( $_SESSION['lti'] );
            
            if ( isset( $lti['tool_consumer_info_product_family_code'] ) ) {
                
                return $lti['tool_consumer_info_product_family_code'];
                
            }
            
        }
        
        return null;
        
    }
    
    /**
     * Get the course ID from the LTI parameters.
     * @return int|null Returns course ID or null if not found.
     */
    function getLTICourseID() {
            
        if ( isset( $_SESSION['lti'] ) ) {
            
            $lti = unserialize( $_SESSION['lti'] );
            
            switch( getLTILMS() ) {
            
                case 'canvas':
                
                    return $lti['custom_canvas_course_id'];
                    break;
                
                default:
                
                    return null;
                
            }
            
        }
        
        return null;
        
    }
    
    /**
     * Get the assignment ID from the LTI parameters.
     * @return int|null Returns assignment ID or null if not found.
     */
    function getLTIAssignmentID() {
            
        if ( isset( $_SESSION['lti'] ) ) {
            
            $lti = unserialize( $_SESSION['lti'] );
            
            switch( getLTILMS() ) {
            
                case 'canvas':
                
                    return $lti['custom_canvas_assignment_id'];
                    break;
                
                default:
                
                    return null;
                
            }
            
        }
        
        return null;
        
    }
    
    function allowReview( $id ) {
        
        switch( $id ) {
            
            case '3':
            case '5':
                return false;
                break;
            default:
                return true;
                break;
            
        }
        
    }

?>