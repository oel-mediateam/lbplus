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
        	    exit( 'Fail to get Google User.' );
        	    
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
        	    exit( 'Failed to get user.' );
        	    
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
        	    exit( 'Failed to add Google User.' );
        	    
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
                    return $result['google_refresh_token'];
                    
                }
                
                return null;
        	    
    	    } catch ( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Failed to get Goole refresh token.' );
        	    
    	    }
    	    
	    }
	    
	    public static function setGoogleRefreshToken( $id, $token ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'UPDATE user SET google_refresh_token = :token WHERE google_id = :id';
                $query = $db->prepare( $sql );
                $query->execute( array( ':token' => $token, ':id' => $id ) );
                
                $db = null;
                return $query->rowCount();
        	    
    	    } catch ( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Failed to set Goole refresh token.' );
        	    
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
        	    exit( 'Failed to get user.' );
        	    
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
        	    exit( 'Failed to get exercises.' );
        	    
    	    }
    	    
	    }
	    
	    public static function getActiveExercises() {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT exercise_id, name FROM exercise WHERE status_id = 1';
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
        	    exit( 'Failed to get active exercises.' );
        	    
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
        	    exit( 'Failed to get exercise.' );
        	    
    	    }
    	    
	    }
	    
	    public static function setUserExercise( $user_id, $exercise_id, $attempt ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'INSERT INTO user_exercise(user_id, exercise_id, num_attempted) VALUES( '
                . ':user, :exercise, :attempted )';
                
                $query = $db->prepare( $sql );
                $query->execute( array( ':user'=>$user_id,
                                        ':exercise'=>$exercise_id,
                                        ':attempted'=>$attempt ) );
                
                $id = $db->lastInsertId();
                
                $db = null;
                
                return $id;
        	    
    	    } catch ( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Failed to set user exercise.' );
        	    
    	    }
    	    
	    }
	    
	    public static function getAttempted( $user_id, $exercise_id ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'SELECT COUNT(*) FROM user_exercise WHERE user_id = :user AND exercise_id = :exercise';
                $query = $db->prepare( $sql );
                $query->execute( array( ':user' => $user_id, ':exercise' => $exercise_id ) );
                $result = $query->fetch( PDO::FETCH_NUM );
                    
                $db = null;
                return (int)$result[0];
        	    
    	    } catch( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Failed to get attempts.' );
        	    
    	    }
    	    
	    }
	    
	    public static function updateStuSrc( $id, $src ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'UPDATE user_exercise SET stu_src = :src WHERE stu_exrs_id = :id';
                $query = $db->prepare( $sql );
                $query->execute( array( ':src' => $src, ':id' => $id ) );
                
                $db = null;
                return $query->rowCount();
        	    
    	    } catch( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Failed to update StuSrc.' );
        	    
    	    }
    	    
	    }
	    
	    public static function updateScore( $id, $score ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'UPDATE user_exercise SET grade_id = :score WHERE stu_exrs_id = :id';
                $query = $db->prepare( $sql );
                $query->execute( array( ':score' => $score, ':id' => $id ) );
                
                $db = null;
                return $query->rowCount();
        	    
    	    } catch( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Failed to update score.' );
        	    
    	    }
    	    
	    }
	    
	    public static function addScore( $id, $score ) {
    	    
    	    $db = DB::getDB();
    	    
    	    try {
        	    
        	    $sql = 'INSERT INTO grade( stu_exrs_id, score ) VALUES( :id, :score )';
                
                $query = $db->prepare( $sql );
                $query->execute( array( ':id'=>$id, ':score'=>$score ) );
                
                $id = $db->lastInsertId();
                $db = null;
                
                return $id;
        	    
    	    } catch ( PDOException $e ) {
        	    
        	    $db = null;
        	    exit( 'Failed to add score.' );
        	    
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
        	    exit( 'Failed to get ID by Google.' );
        	    
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
        	    exit( 'Failed to get role.' );
        	    
    	    }
        	
    	}
	
	}

?>