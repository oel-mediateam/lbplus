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

?>

<form>
    <input type="hidden" name="return_url" value="<?php echo getLTIData( 'launch_presentation_return_url' ); ?>" />
    <input type="hidden" name="type" value="<?php echo getLTIData( 'ext_content_intended_use' ); ?>" />
 
 <section class="lti_sherlock_view">
     
     <div class="selection_view">
         
        <h1>Exercises</h1>
        
        <p>Please select the exercise that you would like to use. When you are done, click the <strong>SELECT</strong> button below to insert the exercise.</p>
        
        <select name="exercise" <?php echo ( isset( $_SESSION['error'] ) ) ? 'class="error"' : ''; ?>>
            <option value="hide">--- please select ---</option>
            <?php
              
                foreach ( $exercises as $exercise ) {
                
                    echo '<option value="' . $exercise['exercise_id'] . '">' . $exercise['name'] . '</option>';
                
                }
                
            ?>
        </select>
        
    
    </div>
 </section>
 <nav class="sherlock_controls">
     <div class="main_controls score_view">

        <button id="lti_selection" class="btn new full" name="select"><span class="action_name">SELECT</span></button>
        
     </div>
 </nav>
</form>

<?php unset( $_SESSION['error'], $exercises ); ?>