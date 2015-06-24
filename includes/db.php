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
	    
	    public static function googleUserExists( $id ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT COUNT(*) FROM user WHERE google_id = :id';
                $query = $db->prepare( $sql );
                $query->execute( array( ':id' => $id ) );
                
                if ( $query->fetchColumn() == 1 ) {
                    
                    $db = null;
                    return 1;
                    
                }
                
                $db = null;
                return 0;
        	    
    	    } catch( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	    
	    public static function userExists( $id ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT COUNT(*) FROM user WHERE user_id = :id';
                $query = $db->prepare( $sql );
                $query->execute( array( ':id' => $id ) );
                
                if ( $query->fetchColumn() == 1 ) {
                    
                    $db = null;
                    return 1;
                    
                }
                
                $db = null;
                return 0;
        	    
    	    } catch( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	    
	    public static function addGoogleUser( $email, $first_name, $last_name, $id, $token ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'INSERT INTO user(email, first_name, last_name, google_id, google_refresh_token) VALUES( '
                . ':email, :first_name, :last_name, :id, :token )';
                
                $query = $db->prepare( $sql );
                $query->execute( array( ':email'=>$email,
                                        ':first_name'=>$first_name,
                                        ':last_name'=>$last_name,
                                        ':id'=>$id,
                                        ':token'=>$token ) );
                
                $id = $db->lastInsertId();
                
                $db = null;
                
                return $id;
        	    
    	    } catch ( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	    
	    public static function getGoogleRefreshToken( $id ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT google_refresh_token FROM user WHERE user_id = :id';
                
                $query = $db->prepare( $sql );
                $query->execute( array( ':id'=>$id ) );
                $query->setFetchMode( PDO::FETCH_ASSOC );
                
                $db = null;
        
                if ( $query->rowCount() == 1 ) {
                    
                    $result = $query->fetch();
                    return $result;
                    
                }
                
                return null;
        	    
    	    } catch ( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	    
	    public static function getUsers() {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT user_id, first_name, last_name FROM user';
                $query = $db->prepare( $sql );
                $query = $db->query( $sql );
                
                $users = array();
            
                while ( $row = $query->fetch() ) {
                    
                    array_push( $users, $row );
                    
                }
                
                $db = null;
                
                return $users;
        	    
    	    } catch( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	    
	    public static function getUser( $id ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT user_id FROM user WHERE user_id = :id';
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
        	    
        	    $db = null;
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
        	    
        	    $db = null;
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	    
	    public static function getExercise( $id ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT * FROM exercise WHERE exercise_id = :id';
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
        	    
        	    $db = null;
        	    exit( 'Connection to database failed.' );
        	    
    	    }
    	    
	    }
	    
	    public static function getIDByGoogle( $google_id ) {
    	
        	$db = DB::getDB();
        	    
    	    try {
        	    
        	    $sql = 'SELECT user_id FROM user WHERE google_id = :id';
                $query = $db->prepare( $sql );
                $query->execute( array( ':id' => $google_id ) );
                
                $db = null;
        
                if ( $query->rowCount() == 1 ) {
                    
                    $result = $query->fetch();
                    return $result['user_id'];
                    
                }
                
                return null;
        	    
    	    } catch( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Connection to database failed.' );
        	    
    	    }
        	
    	}
	    
	    public static function getRole( $id ) {
    	
        	$db = DB::getDB();
        	    
    	    try {
        	    
        	    $sql = 'SELECT role_id FROM user WHERE user_id = :id';
                $query = $db->prepare( $sql );
                $query->execute( array( ':id' => $id ) );
                
                $db = null;
        
                if ( $query->rowCount() == 1 ) {
                    
                    $result = $query->fetch();
                    return $result['role_id'];
                    
                }
                
                return null;
        	    
    	    } catch( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Connection to database failed.' );
        	    
    	    }
        	
    	}
	
	}

?>