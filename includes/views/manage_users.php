<?php 
    
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();
        
    }
    
    $users = DB::getUsers();
    
?>
 <section class="lbplus_view">
     
     <div class="dashboard_view">
         
        <?php require_once 'includes/admin/admin_bar.php'; ?>
        
        <h1>Manage Users</h1>
        
        <ul class="users_list">
            
            <?php
                
                foreach( $users as $user ) {
                    
                    if ( $user['user_id'] != $_SESSION['signed_in_user_id'] ) {
                        
                        $formatted_user = ucfirst( $user['first_name'] ) . ' ' . ucfirst( $user['last_name'] );
                        $isAdmin = ( !isAdmin( $user['user_id'] ) ) ? '<div class="action"><a href="includes/make_admin.php?u=' . $user['user_id'] . '">Make Admin</a></div>' : '';
                        
                        echo '<li><div class="user">' . $formatted_user . '<span class="email">' . $user['email'] . '</span></div>' . $isAdmin . '</li>';
                        
                    }
                    
                }
                
            ?>
            
        </ul>
    
    </div>
    
 </section>
 
 <nav class="lbplus_controls">
     <div class="main_controls score_view">
        
        <a class="btn previous full" href="./"><span class="action_name"><span class="icon-home"></span> Home</span></a>
        
     </div>
 </nav>