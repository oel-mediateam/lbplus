<?php

    if ( !defined( "LBPATH" ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }

	class DB {
	
	    private static function getDB() {
		    
		    $db = unserialize( DB );
		    
		    try {
			    
			    return new PDO( 'mysql:host=' . $db['db_host'] . ';dbname=' . $db['db_name'] , $db['db_user'], $db['db_pwd'] );
			    
		    } catch ( PDOException $e ) {
			    
			    exit( 'Connection to database failed.' );
			    
		    }
	
	    }
	
	}

?>