<?php

    if ( !defined( "ABSPATH" ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        exit('Error 404 - Page Not Found');

    }

    function initialism( $str ) {

        $result =  preg_replace('~\b(\w)|.~', '$1', $str);
        return $result[0] . $result[1];

    }

?>