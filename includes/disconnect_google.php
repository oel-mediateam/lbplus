<?php
    
    if ( !isset( $_POST['revoke'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    } else {
        
        if ( !isset( $_SESSION ) ) {

            session_start();
            
            require_once 'config.php';
            require_once 'db.php';
            require_once 'google_signin.php';
            
            $client->revokeToken();
            $refreshToken = DB::getGoogleRefreshToken( $_SESSION['signed_in_user_id'] );
            unset( $_SESSION['access_token'], $_SESSION['signed_in_user_id'] );
            
            $curl = curl_init();
            
            curl_setopt_array( $curl, array(
                
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'https://accounts.google.com/o/oauth2/revoke?token='.$refreshToken
                
            ) );
            
            $result = curl_exec( $curl );
            
            if ( !$result ) {
                
                die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
                
            }
            
            curl_close($curl);
            
            echo $result;
            
        }
        
    }
    
?>