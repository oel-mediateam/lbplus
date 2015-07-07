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
        
        <h1>Dashboard</h1>
        <ul class="icon_menu">
            <li><a href="?page=dashboard&action=new"><span class="icon-upload"></span><br />Add New Exercise</a></li>
            <li><a href="?page=dashboard&action=viewedit"><span class="icon-edit"></span><br />View / Edit Exercise</a></li>
            <li><a href="?page=dashboard&action=manageusers"><span class="icon-users"></span><br />Manage Users</a></li>
        </ul>
    
    </div>
    
 </section>
 
 <nav class="lbplus_controls">
     <div class="main_controls score_view">
        
        <a class="btn previous full" href="./"><span class="action_name"><span class="icon-home"></span> Home</span></a>
        
     </div>
 </nav>