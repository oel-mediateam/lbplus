<?php

class DB {

    private static function getDB() {

        return new PDO( 'mysql:host=localhost;dbname=yourdbname', 'root', 'pwd' );

    }

}
