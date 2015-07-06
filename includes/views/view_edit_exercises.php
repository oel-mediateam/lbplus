<?php 
    
    if ( !isset( $_SESSION ) ) {
        
        // redirect to 404 page
        header( 'HTTP/1.0 404 File Not Found', 404 );
        include '404.php';
        exit();
        
    }
    
?>
 <section class="lbplus_view">
     
     <div class="dashboard_view">
         
        <?php require_once 'includes/admin/admin_bar.php'; ?>
        
        <h1>View / Edit Exercises</h1>
    
    </div>
    
 </section>
 
 <nav class="lbplus_controls">
     <div class="main_controls score_view">
        
        <a class="btn previous full" href="./"><span class="action_name"><span class="icon-home"></span> Home</span></a>
        
     </div>
 </nav>