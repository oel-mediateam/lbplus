<?php
    
    if ( !isset( $_SESSION ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();

    }
    
    $exerciseTypes = DB::getExerciseTypes();
    
?>

<div id="sherlock-wrapper">
    
    <nav class="navbar">
        
        <div class="container">
            
            <div class="site-name">Sherlock</div>
            <div class="user">
                <?php if ( isset( $_SESSION['signed_in_user_email'] ) ) : ?>
                <p class="name">Hello, <?php echo $userData['givenName']; ?>!</p>
                <p class="signinout"><a href="?logout">Sign Out</a> | <a id="google_revoke_connection" href="javascript:void(0);">revoke</a></p>
                <?php endif; ?>
            </div>
            
        </div>
        
    </nav>
    
    <div class="container">
        
        <h1>Training and Practice Exercises</h1>
        <p>Please select the training or practice exercise that you would like to attempt. Training and practice exercises are not graded and have no restrictions. At the end of the exercise, a score will be calculated and presented but will not be retained. Once you started an exercise, you will not be able to come back to this page until the exercise is completed.</p>
        
        <div class="exercise-pagination">
            
            <?php 
                
                $_SESSION['pageNum'] = 1;
                $_SESSION['totalExercises'] = DB::getNumOfActiveNAExercises();
                $_SESSION['exercisePerPage'] = 4;
                $_SESSION['lastPage'] = ceil($_SESSION['totalExercises'] / $_SESSION['exercisePerPage']);
                
                if ( $_SESSION['pageNum'] > $_SESSION['lastPage'] ) {
                    $_SESSION['pageNum'] = $_SESSION['lastPage'];
                }
                
                if ( $_SESSION['pageNum'] < 1 ) {
                    $_SESSION['pageNum'] = 1;
                }
                
                $limit = ( $_SESSION['pageNum'] - 1 ) * $_SESSION['exercisePerPage'] . ', ' . $_SESSION['exercisePerPage'];
            ?>
            
            <div class="controls">
                
                <div class="pageActions">
                    
                    <button class="previous" disabled><i class="fa fa-chevron-left fa-2x" aria-hidden="true"></i></button>
                    <div class="page-number">page <?php echo '<span class="currentPage">' . $_SESSION['pageNum'] . '</span> of ' . $_SESSION['lastPage']; ?></div>
                    <button class="next"><i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i></button>
                    
                </div>
                
                <div class="actions">
                    
                    <select class="sort">
                        <option value="-1" disabled selected>sort by</option>
                        <?php
                            foreach( $exerciseTypes as &$type ) {
                                echo '<option val="' . $type['exrs_type_id'] . '">' . $type['name'] . '</option>';
                            }
                        ?>
                    </select>
                    
                </div>
                
            </div>
            
            <div class="active-exercises exercise-grid">
                
                <?php
                    
                    if ( isset( $_SESSION['signed_in_user_email'] ) ) {
                        
                        $activeExercises = DB::getActiveExercises( $limit );
                        
                    } else {
                        
                        $activeExercises = DB::getActiveNonAssessmentExercises( $limit );
                        
                    }
                    
                    foreach( $activeExercises as &$exercise ) {
                        
                        echo '<a href="?exercise=' . $exercise['exercise_id'] . '" class="grid-item" data-exercise="' . $exercise['exercise_id'] . '"><div class="thumbnail"><div class="start-txt"><i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i></div><div class="exercise-type-label ' . strtolower($exercise['type_name']) . '">' . $exercise['type_name'] . '</div><div class="embedBtn"><i class="fa fa-link fa-2x" aria-hidden="true"></i></div><img src="https://img.youtube.com/vi/' . $exercise['video_src'] . '/0.jpg" /></div><div class="info"><div class="title">' . $exercise['name'] . '</div><div class="description">' . $exercise['description'] . '</div></div></a>';
                        
                    }
                    
                ?>
                
<!--
                <a class="grid-item">
                    <div class="thumbnail">
                        <div class="start-txt">START</div>
                        <div class="exercise-type-label demo">demo</div>
                        <div class="embedBtn"><i class="fa fa-link fa-2x" aria-hidden="true"></i></div>
                    </div>
                    <div class="info">
                        <div class="title">Rocket League Casual</div>
                        <div class="description">Footage of a Rocket League game. Code for blue team.</div>
                    </div>
                </a>
-->
                
            </div>
            
        </div>
        
        <!-- Sign in with Google if user is not signed in -->
        <?php if ( !isset( $_SESSION['signed_in_user_email'] ) ) : ?>
        <div class="assessment-samples">
            
            <div class="exercise-grid">
                <div class="grid-item">
                    <div class="thumbnail"></div>
                </div>
                <div class="grid-item">
                    <div class="thumbnail"></div>
                </div>
            </div>
            
            <div class="assessment-signin">
            
                <h2>Looking for assessment exercises?</h2>
                <a class="gSignIn" href="<?php echo $authUrl; ?>">
                    <img src="images/btn_google_signin.png" alt="Sign in With Google" />
                </a>
                <a href="https://accounts.google.com/signup" target="_blank">Create a Google Account</a>
                <p><small>Additional privilege may be required to view assessment exercises even after signed in.</small></p>
            </div>
        
        </div>
        <?php endif; ?>

    </div><!-- container -->
    
</div> <!-- Sherlock wrapper -->

<!-- Revoke Google Popup Dialog -->
<?php if ( isset( $_SESSION['signed_in_user_email'] ) ) : ?>
<div id="disconnect-confirm" class="hide">
    <div class="dialog">
        <p class="title">Revoke Google Access</p>
        <p>If you decided to come back, you may have to go through the consent screen for authorization again. Are you sure?</p>
        <p><button id="revoke_ok">OK</button> <button id="revoke_cancel">Cancel</button></p>
    </div>
</div>
<?php endif; ?>
