<?php 
    
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();
        
    }

    $exercises = DB::getActiveExercises();
    
?>
<form method="post" action="index.php">
 <input type="hidden" name="view" value="sherlock_view" />
 <section class="sherlock_view">
     
     <div class="selection_view">
         
        <?php require_once 'includes/admin/admin_bar.php'; ?>
        
        <h1>Exercises</h1>
        
        <?php
            
            if ( isset( $_SESSION['error'] ) ) {
                
                echo '<div class="callout danger">' . $_SESSION['error'] . '</div>';
                
            }
            
        ?>
        
        <p>Please select the exercise that you would like to attempt. When you are ready, click the <strong><span class="icon-start"></span> GO</strong> button below to begin.</p>
        
        <select name="exercise" <?php echo ( isset( $_SESSION['error'] ) ) ? 'class="error"' : ''; ?>>
            <option value="hide">--- please select ---</option>
            <?php
              
                foreach ( $exercises as $exercise ) {
                
                    echo '<option value="' . $exercise['exercise_id'] . '">' . $exercise['name'] . '</option>';
                
                }
                
            ?>
        </select>
        
        <div class="callout info"><strong>Important</strong>: once you started an exercise (after clicking the green <strong>START</strong> button), you will not be able to come back to this page until the exercise is completed. <strong>If you navigated away in the middle of the exercise (i.e., use the back button, close the page, fiddle with the URL, etc.), it will count as an attempt taken.</strong> Please make sure you are fully prepared and comfortable before you begin.</div>
    
    </div>
 </section>
 <nav class="sherlock_controls">
     <div class="main_controls score_view">
        <button type="submit" class="btn new full" name="go"><span class="action_name"><span class="icon-start"></span> GO</span></button>
     </div>
 </nav>
</form>

<?php unset( $_SESSION['error'], $exercises ); ?>