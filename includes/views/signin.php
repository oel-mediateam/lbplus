<?php
    
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();
        
    }
    
?>
 <section class="lbplus_view">
     
     <div class="signin_view">
        
        <?php
                        
            if ( isset( $authUrl ) ) {
                
                echo '<h1>Welcome!</h1>';
                echo '<p>Please <strong>sign in</strong> with your Google Account.</p>';
                echo '<a class="btn signin" href="' . $authUrl . '">Sign In</a>';
                echo '<p><small>If you do not have a Google Account, you can create one at <a href="https://accounts.google.com/signup" target="_blank">Google</a>.</small></p>';
                
            } else {
                
                $dashboardBtn = '';
                
                echo '<h1>Hello, ' . $userData['givenName'] . '!</h1>';
                echo '<p class="profile_img"><img src="' . $userData['picture'] . '" /></p>';
                echo '<p><strong>' . $userData['email'] . '</strong></p>';
                
                if ( isPermitted( $_SESSION['signed_in_user_id'], 2 ) ) {
                            
                    $dashboardBtn = '<a class="btn" href="#">Dashboard</a>';
                    
                }
                
                echo '<p><a class="btn" href="?page=selection">Select Exercise</a> '.$dashboardBtn.'</p>';
                echo '<p><small><a href="?logout">Sign Out</a></small></p>';
                echo '<p><small><a id="google_revoke_connection" href="#">disconnect this application from accessing your Google Account</a></small></p>';
                
            }
            
        ?>
        
        <div id="disconnect-confirm" title="Disconnect Google Account">
            <p>All user and exercise data will be permanently deleted and cannot be recovered. Are you sure?</p>
        </div>
    
    </div>
 </section>
 <nav class="lbplus_controls"></nav>