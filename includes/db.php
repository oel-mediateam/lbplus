<?php

    if ( !defined( "ABSPATH" ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        exit('Error 404 - Page Not Found');

    }

class DB {

    private static function getDB() {

        return new PDO( 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME , DB_USER, DB_PWD );

    }

}
