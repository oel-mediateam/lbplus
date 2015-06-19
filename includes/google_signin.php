<?php
    
    // if started session data is not true
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    require_once 'admin/google_api/autoload.php';
    require_once 'admin/google_api/Client.php';
    require_once 'admin/google_api/Service/Oauth2.php';
    
    $google = unserialize( GOOGLE );
    
    $client = new Google_Client();
    $client->setClientId( $google['client_id'] );
    $client->setClientSecret( $google['client_secret'] );
    $client->setRedirectUri( $google['redirect_uri'] );
    $client->setDeveloperKey( $google['api_key'] );
    $client->setAccessType('offline');
    $client->addScope( "https://www.googleapis.com/auth/userinfo.email" );
    
    $objOAuthService = new Google_Service_Oauth2( $client );
    
    //Logout
    if ( isset( $_REQUEST['logout'] ) ) {
        
      unset( $_SESSION['access_token'] );
      $client->revokeToken();
      header( 'Location: ' . filter_var( $google['redirect_uri'], FILTER_SANITIZE_URL ) );
      
    }
    
    //Authenticate code from Google OAuth Flow
    //Add Access Token to Session
    if ( isset( $_GET['code'] ) ) {
        
      $client->authenticate( $_GET['code'] );
      $_SESSION['access_token'] = $client->getAccessToken();
      $_SESSION['refresh_token'] = $_GET['code'];
      header( 'Location: ' . filter_var( $google['redirect_uri'], FILTER_SANITIZE_URL ) );
      
    }
        
    //Set Access Token to make Request
    if ( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {
        
      $client->setAccessToken( $_SESSION['access_token'] );
      
    }
    
    //Refresh Access Token to make Request
    if ( $client->isAccessTokenExpired() ) {
        
        if ( isset( $_SESSION['refresh_token'] ) && $_SESSION['refresh_token'] ) {
        
          $client->refreshToken( $_SESSION['refresh_token'] );
          $_SESSION['refresh_token'] = $client->getRefreshToken();
          
        }
        
    }
    
    //Get User Data from Google Plus
    //If New, Insert to Database
    if ( $client->getAccessToken() ) {
        
      $userData = $objOAuthService->userinfo->get();
      
      if( !empty( $userData ) ) {
    	
    	if ( DB::googleUserExists( $userData['id'] ) == 0 ) {
        	
        	$newUser = DB::addUser( $userData['email'], $userData['givenName'], $userData['familyName'], $userData['id'] );
        	
        	if ( $newUser == 0 ) {
            	
            	exit( 'Error adding new user.' );
            	
        	}
        	
    	}
        
      }
      
      $_SESSION['signed_in_user_id'] = DB::getID( $userData['id'] );
      $_SESSION['access_token'] = $client->getAccessToken();
      
    } else {
        
      $authUrl = $client->createAuthUrl();
      
    }
    
?>