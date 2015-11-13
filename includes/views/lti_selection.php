<?php 
    
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();
        
    }
    
    if ( isset( $_POST['exercise'] ) ) {
        
        echo getLTIData( 'ext_content_return_url' ) . '?return_type=lti_launch_url&url=https%3A%2F%2Fmedia.uwex.edu%2Fapp%2Fsherlock%2F%3exercise=' . $_POST['exercise'];
        exit();
        
    }
        
    $exercises = DB::getActiveLTIExercises( getLTICourseID(), getLTIData( 'tool_consumer_info_product_family_code' ) );
    
    if ( getLTIData( 'ext_content_return_url' ) !== null ) {
        
        $assignment_selection = true;
        
    } else {
        
        $assignment_selection = false;
        
    }

?>

<?php if ( $assignment_selection ) { ?>
<form>
    <input type="hidden" name="return_url" value="<?php echo getLTIData( 'ext_content_return_url' ); ?>" />
    <input type="hidden" name="type" value="ext_content_return_url" />
<?php } else { ?>
<form method="post" action="index.php">
    <input type="hidden" name="view" value="sherlock_view" />
<?php } ?>
 
 <section class="lti_sherlock_view">
     
     <div class="selection_view">
         
        <h1>Exercises</h1>
        
        <?php
            
            if ( isset( $_SESSION['error'] ) ) {
                
                echo '<div class="callout danger">' . $_SESSION['error'] . '</div>';
                
            }
            
        ?>
        
        <?php if ( $assignment_selection ) { ?>
        <p>Please select the exercise that you would like to use. When you are done, click the <strong>SELECT</strong> button below to insert the exercise to the assignment.</p>
        <?php } else { ?>
        <p>Please select the exercise that you would like to attempt. When you are ready, click the <strong><span class="icon-start"></span> GO</strong> button below to begin.</p>
        <?php } ?>
        
        <select name="exercise" <?php echo ( isset( $_SESSION['error'] ) ) ? 'class="error"' : ''; ?>>
            <option value="hide">--- please select ---</option>
            <?php
              
                foreach ( $exercises as $exercise ) {
                
                    echo '<option value="' . $exercise['exercise_id'] . '">' . $exercise['name'] . '</option>';
                
                }
                
            ?>
        </select>
        
        <?php if ( !$assignment_selection ) { ?>
        <div class="callout info"><strong>Important</strong>: once you started an exercise (after clicking the green <strong>START</strong> button), you will not be able to come back to this page until the exercise is completed. <strong>If you navigated away in the middle of the exercise (i.e., use the back button, close the page, fiddle with the URL, etc.), it will count as an attempt taken.</strong> Please make sure you are fully prepared and comfortable before you begin.</div>
        <?php } ?>
    
    </div>
 </section>
 <nav class="sherlock_controls">
     <div class="main_controls score_view">
        <?php if ( $assignment_selection ) { ?>
        <button id="lti_selection" class="btn new full" name="select"><span class="action_name">SELECT</span></button>
        <?php } else { ?>
        <button type="submit" class="btn new full" name="go"><span class="action_name"><span class="icon-start"></span> GO</span></button>
        <?php } ?>
     </div>
 </nav>
</form>

<?php unset( $_SESSION['error'], $exercises ); ?>