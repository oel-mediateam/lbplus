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
        
        <h1>Add New Exercise</h1>
        <form method="POST" action="<?php $_SERVER['PHP_SELF'] ?>">
            
            <div class="quarter">
                
                <label for="type">Exercise type:</label>
                <select name="type">
                    <option value="1">Demo</option>
                    <option value="2">Dev Testing</option>
                </select>
                
            </div>
            
            <div class="quarter">
                
                <label for="category">Exercise Category:</label>
                <select name="category">
                    <option value="1">Demo and Testings</option>
                </select>
                
            </div>
            
            <div class="quarter">
                
                <label for="status">Status</label>
                <select name="status">
                    <option value="-1">Inactive</option>
                    <option value="0">Draft</option>
                    <option value="1">Active</option>
                </select>
                
            </div>
            
            <div class="clearfix"></div>
            
            <div class="half">
                
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" />
                
                <label for="description">Description:</label>
                <textarea name="description" id="description"></textarea>
                
                <label for="retake">Allow retake?
                <input type="radio" name="retake" value="1" checked /> Yes
                <input type="radio" name="retake" value="0" /> No</label>
                
                <label for="new">Allow new?
                <input type="radio" name="new" value="1" /> Yes
                <input type="radio" name="new" value="0" checked /> No </label>
                
            </div>
            
            <div class="half">
                
                <label for="video">Video ID:</label>
                <input type="text" name="video" id="video" />
                
                <label for="json">JSON file:</label>
                <input type="file" name="json" />
                
                <label for="attempts">Number of attempts:</label>
                <input type="number" name="attempts" id="attempts" min="0" />
                
            </div>
            
            <div class="clearfix"></div>
            
        </form>
    
    </div>
    
 </section>
 
 <nav class="lbplus_controls">
     <div class="main_controls score_view">
        
        <div class="left">&nbsp;</div>
        <div class="center">
            <a class="btn previous" href="./?page=dashboard"><span class="action_name">Cancel</span></a>
            <button type="submit" class="btn new" name="new_exercise"><span class="action_name">Submit</span></button>
        </div>
        <div class="right">&nbsp;</div>
        
        
     </div>
 </nav>
 </form>