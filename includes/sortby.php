<?php
    
    if ( !isset( $_POST['sort'] ) ) {

        header( 'HTTP/1.0 404 File Not Found', 404 );
        include 'views/404.php';
        exit();

    }
    
    // ↓↓↓↓↓ set sortby session value (called with AJAX) ↓↓↓↓↓
    
    if ( !isset( $_SESSION ) ) {
        
        if ( !empty( $_POST['sort'] ) ) {
            
            session_start();
        
            require_once 'config.php';
            require_once 'db.php';
            require_once 'functions.php';
            
            $_SESSION['sortby'] = $_POST['sort'];
            $_SESSION['pageNum'] = 1;
            $isLastPage = false;
            $isFirstPage = false;
              
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
                        
                if ( !isLTIUser() ) {
                    $embedBtn = '<div class="embedBtn"><i class="fa fa-link fa-2x" aria-hidden="true"></i></div>';
                    $href = 'href="?exercise=' . $exercise['exercise_id'] . '"';
                } else {
                    $embedBtn = '';
                    $href = 'href="javascript:void(0);"';
                }
                
                $result .= '<a ' . $href . ' class="grid-item" data-exercise="' . $exercise['exercise_id'] . '"><div class="thumbnail"><div class="start-txt"><i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i></div><div class="exercise-type-label ' . strtolower($exercise['type_name']) . '">' . $exercise['type_name'] . '</div>' . $embedBtn . '<img src="https://img.youtube.com/vi/' . $exercise['video_src'] . '/0.jpg" /></div><div class="info"><div class="title">' . $exercise['name'] . '</div><div class="description">' . $exercise['description'] . '</div></div></a>';
                
            }
            
            $arry = Array( $isFirstPage, $isLastPage, $_SESSION['pageNum'], $result );
            echo json_encode( $arry );
            
        }
        
    }
    
?>