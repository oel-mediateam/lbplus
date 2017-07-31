<?php
    
    if ( !isset( $_POST['revoke'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    // ↓↓↓↓↓ for revoking Google users only (called with AJAX) ↓↓↓↓↓
    
    if ( !isset( $_SESSION ) ) {

        session_start();
        
        require_once 'config.php';
        require_once 'db.php';
        require_once 'google_signin.php';
        
        // get the refresh token from the database
        $refreshToken = DB::getGoogleRefreshToken( $_SESSION['signed_in_user_email'] );
        
        // signout the user
        $client->revokeToken();
        DB::deleteUserByGoogle( $_SESSION['signed_in_user_email'] );
        unset( $_SESSION['access_token'], $_SESSION['signed_in_user_email'] );
        
        // use cURL session to request Google to revoke the refresh token
        $curl = curl_init();
        
        curl_setopt_array( $curl, array(
            
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://accounts.google.com/o/oauth2/revoke?token='.$refreshToken
            
        ) );
        
        $result = curl_exec( $curl );
        
        // if unsuccessful, die with error
        if ( !$result ) {
            
            die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            
        }
        
        // close cURL session
        curl_close($curl);
        
        // "return" the result
        echo $result;
        
    }
    
?>