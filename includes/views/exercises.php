<?php
    
    if ( !isset( $_SESSION ) ) {
        
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();

    }
    
    $exerciseTypes = DB::getExerciseTypes();
    
    $_SESSION['pageNum'] = 1;
    
    if ( isset( $_SESSION['signed_in_user_email'] ) || isLTIUser() ) {
        $_SESSION['totalExercises'] = DB::getNumOfActiveExercises();
    } else {
        $_SESSION['totalExercises'] = DB::getNumOfActiveNAExercises();
    }
    
    $_SESSION['exercisePerPage'] = 4;
    $_SESSION['lastPage'] = ceil($_SESSION['totalExercises'] / $_SESSION['exercisePerPage']);
    
    if ( $_SESSION['pageNum'] > $_SESSION['lastPage'] ) {
        $_SESSION['pageNum'] = $_SESSION['lastPage'];
    }
    
    if ( $_SESSION['pageNum'] < 1 ) {
        $_SESSION['pageNum'] = 1;
    }
    
    $_SESSION['sortby'] = '';
    
    $limit = ( $_SESSION['pageNum'] - 1 ) * $_SESSION['exercisePerPage'] . ', ' . $_SESSION['exercisePerPage'];
    
    if ( isset( $_SESSION['signed_in_user_email'] ) || isLTIUser() ) {
        
        if ( isLTIUser() ) {
            $pageTitle = "Select Exercise";
        } else {
            $pageTitle = "Exercises";
        }
        
        $activeExercises = DB::getActiveExercises( $limit, $_SESSION['sortby'] );
        $signedIn = true;
        
    } else {
        
        $pageTitle = "Training and Practice Exercises";
        $activeExercises = DB::getActiveNonAssessmentExercises( $limit, $_SESSION['sortby'] );
        $signedIn = false;
        
    }
    
?>

<div id="sherlock-wrapper">
    
    <nav class="navbar">
        
        <div class="container">
            
            <div class="site-name">Sherlock</div>
            <div class="user">
                <?php if ( $signedIn && !isLTIUser() ) : ?>
                <p class="name">Hello, <?php echo $userData['givenName']; ?>!</p>
                <p class="signinout"><a href="?logout">Sign Out</a> | <a id="google_revoke_connection" href="javascript:void(0);">revoke</a></p>
                <?php endif; ?>
            </div>
            
        </div>
        
    </nav>
    
    <div class="container">
        
        <h1><?php echo $pageTitle; ?></h1>
        
        <?php if ( isLTIUser() ) : ?>
        
        <p>Please select exercise that you would like to use.</p>
        
        <?php else: ?>
        
        <p>Please select exercise that you would like to attempt. Demo, training, and practice exercises are not graded and have no restrictions. At the end of each exercise, a score will be calculated and presented. The score will be recorded or retained only for assessment exercises. Once you started an exercise, you will not be able to come back to this page until the exercise is completed.</p>
        
        <?php endif; ?>
        
        <div class="exercise-pagination">
            
            <div class="controls">
                
                <div class="pageActions">
                    
                    <button class="previous" disabled><i class="fa fa-chevron-left fa-2x" aria-hidden="true"></i></button>
                    <div class="page-number">page <?php echo '<span class="currentPage">' . $_SESSION['pageNum'] . '</span> of ' . $_SESSION['lastPage']; ?></div>
                    
                    <button class="next" <?php echo $_SESSION['lastPage'] == 1 ? 'disabled' : ''; ?>><i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i></button>
                    
                </div>
                
                <div class="actions">
                    
                    <select class="sort">
                        <option value="-1" disabled selected>sort by</option>
                        <?php
                            foreach( $exerciseTypes as &$type ) {
                                
                                if ( $type['name'] == 'Assessment' && $signedIn == false ) {
                                    continue;
                                }
                                
                                echo '<option val="' . $type['name'] . '">' . $type['name'] . '</option>';
                                
                            }
                        ?>
                    </select>
                    
                </div>
                
            </div>
            
            <div class="active-exercises exercise-grid">
                
                <?php
                    
                    foreach( $activeExercises as &$exercise ) {
                        
                        if ( !isLTIUser() ) {
                            $embedBtn = '<div class="embedBtn"><i class="fa fa-link fa-2x" aria-hidden="true"></i></div>';
                            $href = 'href="?exercise=' . $exercise['exercise_id'] . '"';
                            $ltiClass = '';
                        } else {
                            $embedBtn = '';
                            $href = 'href="javascript:void(0);"';
                            $ltiClass = ' ltiItem';
                        }
                        
                        echo '<a ' . $href . ' class="grid-item' . $ltiClass . '" data-exercise="' . $exercise['exercise_id'] . '"><div class="thumbnail"><div class="start-txt"><i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i></div><div class="exercise-type-label ' . strtolower($exercise['type_name']) . '">' . $exercise['type_name'] . '</div>' . $embedBtn . '<img src="https://img.youtube.com/vi/' . $exercise['video_src'] . '/0.jpg" /></div><div class="info"><div class="title">' . $exercise['name'] . '</div><div class="description">' . $exercise['description'] . '</div></div></a>';
                        
                    }
                    
                ?>
                
            </div>
            
        </div>
        
        <!-- Sign in with Google if user is not signed in -->
        <?php if ( $signedIn == false && !isLTIUser() ) : ?>
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
<?php if ( $signedIn && !isLTIUser() ) : ?>
<div id="disconnect-confirm" class="transition_overlay hide">
    <div class="heading">Revoke Google Access</div>
    <div class="subheading">If you decided to come back, you may have to go through authorization process again.<br>Are you sure?</div>
    <div class="actions"><button id="revoke_ok">Yes, I am sure.</button> <button id="revoke_cancel">No, I am not.</button></div>
</div>
<?php endif; ?>

<!-- Added hidden field if is LTI User -->
<?php if ( isLTIUser() ) : ?>
<form>
    <input type="hidden" name="return_url" value="<?php echo getLTIData( 'launch_presentation_return_url' ); ?>" />
    <input type="hidden" name="type" value="<?php echo getLTIData( 'ext_content_intended_use' ); ?>" />
</form>
<?php endif; ?>
