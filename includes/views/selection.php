<?php
    
    $users = DB::getUsers();
    $exercises = DB::getExercises();
    
?>
<form method="post" action="index.php">
 <input type="hidden" name="view" value="lbplus_view" />
 <section class="lbplus_view">
     
     <div class="selection_view">
         
         <nav class="admin_bar">
             <h1>Professional Training Development</h1>
             <ul>
                 <?php
                     
                    if ( isset( $authUrl ) ) {
                        
                        echo '<li><a class="signin" href="' . $authUrl . '">Sign In</a></li>';
                        
                    } else {
                        
                        echo '<li><a class="signout" href="?logout">Sign Out</a></li>';
                        
                        if ( isPermitted( $_SESSION['signed_in_user_id'], 2 ) ) {
                            
                            echo '<li><a class="dashboard" href="#">Dashboard</a></li>';
                            
                        }
                        
                        echo '<li><div class="profile"><img src="' . $userData["picture"] . '" width="40" height="40" /><span class="name">' . $userData["name"] . '</span></div></li>';
                        
                    }
                     
                ?>
             </ul>
        </nav>
        
        <h1>Welcome!</h1>
        
        <?php
            
            if ( isset( $_SESSION['error'] ) ) {
                
                echo '<div class="callout danger">' . $_SESSION['error'] . '</div>';
                
            }
            
        ?>
        
        <h6>Who will doing the exercise?</h6>
        <select name="user" <?php echo ( isset( $_SESSION['error'] ) ) ? 'class="error"' : ''; ?> >
            <option value="hide">--- please select ---</option>
            <?php
              
                foreach ( $users as $user ) {
                
                    echo '<option value="' . $user['user_id'] . '">' . $user['first_name'] . ' ' . $user['last_name'] . '</option>';
                
                }
                
            ?>
        </select>
        <h6>Which exercise would you like?</h6>
        <select name="exercise" <?php echo ( isset( $_SESSION['error'] ) ) ? 'class="error"' : ''; ?>>
            <option value="hide">--- please select ---</option>
            <?php
              
                foreach ( $exercises as $exercise ) {
                
                    echo '<option value="' . $exercise['exercise_id'] . '">' . $exercise['name'] . '</option>';
                
                }
                
            ?>
        </select>
        
        <p><small>When you are ready, click the <strong>START</strong> button below to begin.</small></p>
    
    </div>
 </section>
 <nav class="lbplus_controls">
     <div class="main_controls score_view">
        <button type="submit" class="btn new full" name="start"><span class="action_name">START</span></button>
     </div>
 </nav>
</form>

<?php unset( $_SESSION['error'] ); ?>