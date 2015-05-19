<?php

    if ( !isset( $_POST['student'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    } else {

        $inputs = $_POST['student'];

/*
        foreach( $inputs as $data ) {

            echo $data['name'] . '<br />';

        }
*/

    }


?>