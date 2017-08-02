<?php
    
    if ( !isset( $_POST['direction'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    // ↓↓↓↓↓ get all information on a requested exercise (called with AJAX) ↓↓↓↓↓
    
    if ( !isset( $_SESSION ) ) {

        session_start();
        
        require_once 'config.php';
        require_once 'db.php';
        
        $dir = $_POST['direction'];
        $isLastPage = false;
        $isFirstPage = false;
        
        if ( $dir == 'prev' ) {
            $_SESSION['pageNum'] = $_SESSION['pageNum'] - 1;
        } else {
            $_SESSION['pageNum'] = $_SESSION['pageNum'] + 1;
        }
        
        if ( $_SESSION['pageNum'] >= $_SESSION['lastPage'] ) {
            $_SESSION['pageNum'] = $_SESSION['lastPage'];
            $isLastPage = true;
        } else {
            $isLastPage = false;
        }
        
        if ( $_SESSION['pageNum'] <= 1 ) {
            $_SESSION['pageNum'] = 1;
            $isFirstPage = true;
        } else {
            $isFirstPage = false;
        }
        
        $limit = ( $_SESSION['pageNum'] - 1 ) * $_SESSION['exercisePerPage'] . ', ' . $_SESSION['exercisePerPage'];
        
        $result = '';
        
        if ( isset( $_SESSION['signed_in_user_email'] ) ) {
                        
            $activeExercises = DB::getActiveExercises( $limit, $_SESSION['sortby'] );
            
        } else {
            
            $activeExercises = DB::getActiveNonAssessmentExercises( $limit, $_SESSION['sortby'] );
            
        }
        
        foreach( $activeExercises as &$exercise ) {
            
            $result .= '<a href="?exercise=' . $exercise['exercise_id'] . '" class="grid-item" data-exercise="' . $exercise['exercise_id'] . '"><div class="thumbnail"><div class="start-txt"><i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i></div><div class="exercise-type-label ' . strtolower($exercise['type_name']) . '">' . $exercise['type_name'] . '</div><div class="embedBtn"><i class="fa fa-link fa-2x" aria-hidden="true"></i></div><img src="https://img.youtube.com/vi/' . $exercise['video_src'] . '/0.jpg" /></div><div class="info"><div class="title">' . $exercise['name'] . '</div><div class="description">' . $exercise['description'] . '</div></div></a>';
            
        }
        
        $arry = Array( $isFirstPage, $isLastPage, $_SESSION['pageNum'], $result );
        echo json_encode( $arry );
        
    }
    
?>