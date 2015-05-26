<?php

    if ( session_status() === 0 || session_status() === 1 ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }

    function initialism( $str ) {

        $result =  preg_replace('~\b(\w)|.~', '$1', $str);
        return $result[0] . $result[1];

    }

    function getValue( $val, $default ) {

        $result = trim( $val );

        if ( is_bool( $val ) ) {

            if ( (int)$val != "" ) {

                $result = $default;

            }

        } else {

            if ( strlen( $result ) <= 0 ) {

                $result = $default;

            }

        }

        return $result;

    }

    function toSeconds( $ms ) {

        $ms = explode(":", $ms);

        return $result = ( $ms[0] * 60 ) + $ms[1];

    }

    function writeToFileAsJson( $file, $content ) {

        $fp = fopen( $file, 'w' );
        fwrite( $fp, JsonHandler::encode( $content ) );
        fclose( $fp );

    }

?>