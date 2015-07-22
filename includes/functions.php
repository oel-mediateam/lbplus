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
        
        // if request is exercise
        if ( isset( $request['go'] ) ) {
        
            $view = 'includes/views/' . $request['view'] . '.php';
            
            // check exercise id
            if ( isset( $request['exercise'] ) && $request['exercise'] != 'hide' ) {
                
                unset( $request['start'], $request['view'], $request['exercise'] );
                return $view;
                
            } else {
                
                $_SESSION['error'] = "Please select an exercise.";
                header( 'Location: ./?page=exercises' );
                exit();
                
            }
            
        }
        
        // if request is an embed
        if ( isset( $request['embed'] ) ) {
            
            $exercise_id = $request['embed'];
            
            if ( !isset( $_SESSION['access_token'] ) ) {
                
                header( 'Location: ./?eid='.$exercise_id );
                
            }
            
            if ( !empty( $exercise_id ) ) {
                
                if ( $exercise = DB::getExercise( $exercise_id ) ) {
                    
                    $_SESSION['exercise_info'] = serialize( $exercise );
                    $view = 'includes/views/lbplus_view.php';
                    
                    unset( $request['embed'], $_SESSION['embed'], $exercise_id );
                    return $view;
                    
                } else {
                    
                    $_SESSION['error'] = "The requested exercise cannot be found or does not exist. Please select an exercise below.";
                    header( 'Location: ./?page=exercises' );
                    exit();
                    
                }
                
            } else {
            
                $_SESSION['error'] = "The requested exercise cannot be found or does not exist. Please select an exercise below.";
                header( 'Location: ./?page=exercises' );
                exit();
            
            }
            
        }
        
        // if request is retake
        if ( isset( $request['retake'] ) ) {
            
            $exercise_info = unserialize( $_SESSION['exercise_info'] );
            
            if ( $request['retake'] == $exercise_info['exercise_id'] ) {
                
                $view = 'includes/views/lbplus_view.php';
                unset( $request['retake'] );
                return $view;
                
            } else {
                
                $_SESSION['error'] = "Retake error. Please manually select the exercise below.";
                header( 'Location: ./?page=exercises' );
                exit();
                
            }
            
        }
        
        // if request is page
        if ( isset( $request['page'] ) ) {
            
            if ( !isset( $_SESSION['access_token'] ) ) {
                    
                header( 'Location: ./' );
                
            }
            
            // exercises page
            if ( $request['page'] == 'exercises' ) {
                
                unset( $request['page'] );
                
                $view = 'includes/views/selection.php';
                
                return $view;
                
            }
            
            // admin dashboard page
            if ( $request['page'] == 'dashboard' ) {
                
                unset( $request['page'] );
                
                if ( !isAdmin() ) {
                
                    header( 'Location: ./' );
                            
                }
                
                if ( isset( $request['action'] ) ) {
                    
                    switch( $request['action'] ) {
                        
                        case 'new':
                            $view = 'includes/views/add_new_exercise.php';
                        break;
                        
                        case 'viewedit':
                            $view = 'includes/views/view_edit_exercises.php';
                        break;
                        
                        case 'manageusers':
                            $view = 'includes/views/manage_users.php';
                        break;
                        
                        case 'makeadmin':
                        
                            if ( isset( $_REQUEST['u'] ) && !empty( $_REQUEST['u'] ) ) {
                                
                                if ( !isAdmin( $_REQUEST['u'] ) )  {
                                    
                                    DB::makeAdmin( $_REQUEST['u'] );
                                    
                                } else {
                                    
                                    $_SESSION['alreadyAdmin'] = true;
                                    
                                }
                                
                            } else {
                                
                                $_SESSION['makeAdminError'] = true;
                                
                            }
                            
                            header( 'Location: ./?page=dashboard&action=manageusers' );
                            
                        break;
                        
                    }
                    
                } else {
    
                    $view = 'includes/views/dashboard.php';
                    
                }
                
                return $view;
                
            }
            
        }
        
        // default view
        $view = 'includes/views/signin.php';
        
        return $view;
        
    }
    
    function isAdmin( $id = null ) {
        
        if ( !isset( $id ) ) {
            
            $id = $_SESSION['signed_in_user_id'];
            
        }
            
        $userRole = DB::getRole( $id );
        
        if ( $userRole >= 3 ) {
            
            return true;
            
        }
            
        return false;
        
    }

?>