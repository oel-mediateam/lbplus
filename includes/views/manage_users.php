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
        
        <?php
            
            if ( isset( $_SESSION['makeAdminError'] ) ) {
                
                unset( $_SESSION['makeAdminError'] );
                echo '<div class="callout danger">Error in making an user an admin.</div>';
                
            }
            
            if ( isset( $_SESSION['alreadyAdmin'] ) ) {
                
                unset( $_SESSION['alreadyAdmin'] );
                echo '<div class="callout danger">User is already an admin.</div>';
                
            }
            
        ?>
        
        <ul class="users_list">
            
            <?php
                
                foreach( $users as $user ) {
                    
                    $current_user = ( $user['user_id'] == $_SESSION['signed_in_user_id'] ) ? true : false;
                    $formatted_user = ucfirst( $user['first_name'] ) . ' ' . ucfirst( $user['last_name'] );
                    $isAdmin = ( !isAdmin( $user['user_id'] ) ) ? '<a href="?page=dashboard&action=makeadmin&u=' . $user['user_id'] . '">Make Admin</a>' : '<a class="disabled" href="#">Admin</a>';
                    
                    if ( $current_user ) {
                        
                        echo '<li><div class="user">' . $formatted_user . ' <span class="icon-star-full"></span><span class="email">' . $user['email'] . '</span></div><div class="action">' . $isAdmin . '</div></li>';
                        
                    } else {
                        
                        echo '<li><div class="user">' . $formatted_user . '<span class="email">' . $user['email'] . '</span></div><div class="action">' . $isAdmin . '</div></li>';
                        
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