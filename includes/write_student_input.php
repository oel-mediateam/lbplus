<?php

    session_start();

    if ( isset( $_SESSION['logged'] ) ) {

        $inputs = $_POST['student'];

/*
        foreach( $inputs as $data ) {

            echo $data['name'] . '<br />';

        }
*/

    } else {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        exit('Error 404 - Page Not Found');

    }

?>