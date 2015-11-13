<?php
    
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();
        
    }
    
?>

<nav class="admin_bar">
     <h1>Sherlock</h1>
     <ul>
         <?php
            
            if ( !isset( $authUrl ) ) {
                
                echo '<li><a title="Sign Out" class="signout" href="?logout"><span class="icon-signout"></span></a></li>';
                echo '<li><div class="profile"><img src="' . $userData["picture"] . '" width="40" height="40" /><span class="name">' . $userData["name"] . '</span></div></li>';
                
            }
             
        ?>
     </ul>
</nav>