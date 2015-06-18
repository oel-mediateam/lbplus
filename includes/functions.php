<?php
    
    // if started session data is not true
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    // return the first letter of the 
    // first and second words in the string
    function initialism( $str ) {

        $result =  preg_replace('~\b(\w)|.~', '$1', $str);

        if ( isset( $result[1] ) ) {

            return $result[0] . $result[1];

        }

        return $result[0];

    }
    
    // return the value if set in JSON
    // otherwise return a specified default value
    function getValue( $val, $default ) {

        $result = trim( $val );

        if ( is_bool( $val ) ) {

            if ( !isset($val) ) {

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
    
    // return time string in seconds
    function toSeconds( $ms ) {

        $ms = explode(":", $ms);

        return $result = ( $ms[0] * 60 ) + $ms[1];

    }
    
    // return the a score message based
    // on the percentage of the score
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
    
    // get views
    function getView( $request ) {
        
        // assessment/exercise view
        if ( isset( $request['start'] ) ) {
        
            $view = 'includes/views/' . $request['view'] . '.php';
            $scripts = '<script src="scripts/moment.min.js" type="text/javascript"></script><script src="scripts/lbplus.js" type="text/javascript"></script>';
            
            // check user id
            if ( isset( $request['user'] ) && $request['user'] != 'hide' ) {
                
                $user = DB::userExists( $request['user'] );
                
                // check exercise id
                if ( $user != 0 && isset( $request['exercise'] ) && $request['exercise'] != 'hide' ) {
                
                    $exercise = DB::getExercise( $request['exercise'] );
                    $_SESSION['video'] = $exercise['video_src'];
                    $_SESSION['json'] = $exercise['markup_src'];
                    
                    return array( 'view' => $view, 'scripts' => $scripts );
                    
                } else {
                    
                    $_SESSION['error'] = "Please select an user and an exercise.";
                    header( 'Location: ./' );
                    exit();
                    
                }
                
            } else {
                
                $_SESSION['error'] = "Please select an user and an exercise.";
                header( 'Location: ./' );
                exit();
                
            }
            
            
        }
        
        // login view
        if ( isset( $request['login'] ) && $request['login'] == 1 ) {
                
            $view = 'includes/views/sign_in.php';
            $scripts = '';
            
            return array( 'view' => $view, 'scripts' => $scripts );
            
        }
        
        // default view
        $view = 'includes/views/selection.php';
        $scripts = '<script src="scripts/form.js" type="text/javascript"></script>';
        
        return array( 'view' => $view, 'scripts' => $scripts );
        
    }
    
    function isPermitted( $id, $role ) {
        
        if ( isset( $id ) ) {
            
            $userRole = DB::getRole( $id );
            
            if ( $userRole >= $role ) {
                
                return true;
                
            }
            
        }
        
        return false;
        
    }

?>