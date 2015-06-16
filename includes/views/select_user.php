<?php
    
     $users = DB::getUsers();
     $exercises = DB::getExercises();
     
?>
<form method="post" action="index.php">
 <input type="hidden" name="view" value="lbplus_view" />
 <section class="lbplus_view">
    <h1>Welcome! Before you begin...</h1>
    <h6>Who are you?</h6>
    <select name="user">
        <option value="hide">--- please select ---</option>
        <?php
          
            foreach ( $users as $user ) {
            
                echo '<option value="' . $user['user_id'] . '">' . $user['first_name'] . ' ' . $user['last_name'] . '</option>';
            
            }
            
        ?>
    </select>
    <p><small>Not listed? <a href="#">Enter a new user.</a></small></p>
    <h6>Select an exercise:</h6>
    <select name="exercise">
        <option value="hide">--- please select ---</option>
        <?php
          
            foreach ( $exercises as $exercise ) {
            
                echo '<option value="' . $exercise['exercise_id'] . '">' . $exercise['name'] . '</option>';
            
            }
            
        ?>
    </select>
 </section>
 <nav class="lbplus_controls">
     <div class="main_controls score_view">
        <button type="submit" class="btn new full" name="start"><span class="action_name">START</span></button>
     </div>
 </nav>
</form>