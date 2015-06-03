<?php
    
    // if started session data is not true
    if ( $_SESSION['started'] != true ) {
        
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

?>