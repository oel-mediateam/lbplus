<?php

    // if started session data is not true
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
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
	    
	    public static function getUsers() {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT user_id, first_name, last_name FROM user';
                $query = $db->prepare( $sql );
                $query = $db->query( $sql );
                $query->setFetchMode( PDO::FETCH_ASSOC );
                
                $users = array();
            
                while ( $row = $query->fetch() ) {
                    
                    array_push( $users, $row );
                    
                }
                
                $db = null;
                
                return $users;
        	    
    	    } catch( PDOException $e ) {
        	    
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	    
	    public static function getExercises() {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT exercise_id, name FROM exercise';
                $query = $db->prepare( $sql );
                $query = $db->query( $sql );
                $query->setFetchMode( PDO::FETCH_ASSOC );
                
                $exercises = array();
            
                while ( $row = $query->fetch() ) {
                    
                    array_push( $exercises, $row );
                    
                }
                
                $db = null;
                
                return $exercises;
        	    
    	    } catch( PDOException $e ) {
        	    
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	    
	    public static function getExercise( $id ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT video_src, markup_src FROM exercise WHERE exercise_id = :id';
                $query = $db->prepare( $sql );
                $query->execute( array( ':id' => $id ) );
                $query->setFetchMode( PDO::FETCH_ASSOC );
                
                $db = null;
        
                if ( $query->rowCount() == 1 ) {
                    
                    $result = $query->fetch();
                    return $result;
                    
                }
                
                return null;
        	    
    	    } catch( PDOException $e ) {
        	    
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	
	}

?>