<?php
    
    if ( !isset( $_SESSION ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();
        
    }
    
    // ↓↓↓↓↓ display for signed in Google users only ↓↓↓↓↓
    
?>

<nav class="admin_bar">
    
     <h1><?php echo APP_NAME; ?></h1>
     
     <ul>
         <?php
            
            // if oauth url is not set (aka, user signed in)
            // display the Google user profile photo
            // and the Google signout link
            if ( !isset( $authUrl ) ) {
                
                echo '<li><a title="Sign Out" class="signout" href="?logout"><span class="icon-signout"></span></a></li>';
                echo '<li><div class="profile"><img src="' . $userData["picture"] . '" width="40" height="40" /><span class="name">' . $userData["name"] . '</span></div></li>';
                
            }
             
        ?>
     </ul>
     
</nav>