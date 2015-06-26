<?php $exercises = DB::getExercises(); ?>
<form method="post" action="index.php">
 <input type="hidden" name="view" value="lbplus_view" />
 <section class="lbplus_view">
     
     <div class="selection_view">
         
         <nav class="admin_bar">
             <h1>Professional Training Development</h1>
             <ul>
                 <?php
                     
                    if ( !isset( $authUrl ) ) {
                        
                        echo '<li><a title="Sign Out" class="signout" href="?logout"><span class="icon-signout"></span></a></li>';
                        
                        if ( isPermitted( $_SESSION['signed_in_user_id'], 2 ) ) {
                            
                            echo '<li><a title="Dashboard" class="dashboard" href="#"><span class="icon-dashboard"></span></a></li>';
                            
                        }
                        
                        echo '<li><div class="profile"><img src="' . $userData["picture"] . '" width="40" height="40" /><span class="name">' . $userData["name"] . '</span></div></li>';
                        
                    }
                     
                ?>
             </ul>
        </nav>
        
        <h1>Exercises</h1>
        
        <?php
            
            if ( isset( $_SESSION['error'] ) ) {
                
                echo '<div class="callout danger">' . $_SESSION['error'] . '</div>';
                
            }
            
        ?>
        
        <p>Please select the exercise that you would like to attempt. When you are ready, click the <strong><span class="icon-start"></span> START</strong> button below to begin.</p>
        
        <select name="exercise" <?php echo ( isset( $_SESSION['error'] ) ) ? 'class="error"' : ''; ?>>
            <option value="hide">--- please select ---</option>
            <?php
              
                foreach ( $exercises as $exercise ) {
                
                    echo '<option value="' . $exercise['exercise_id'] . '">' . $exercise['name'] . '</option>';
                
                }
                
            ?>
        </select>
        
        <div class="callout info"><strong>Important</strong>: once you started an exercise, you will not be able to come back to this page until the exercise is completed. <strong>If you navigated away in the middle of the exercise (i.e., use the back button, close the page, fiddle with the URL, etc.), it will count as an attempt taken.</strong> Please make sure you are fully prepared and comfortable before you begin.</div>
    
    </div>
 </section>
 <nav class="lbplus_controls">
     <div class="main_controls score_view">
        <button type="submit" class="btn new full" name="start"><span class="action_name"><span class="icon-start"></span> START</span></button>
     </div>
 </nav>
</form>

<?php unset( $_SESSION['error'], $exercises ); ?>