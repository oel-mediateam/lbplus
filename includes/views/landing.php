<?php $isLanding = true; ?>
<div class="hero_bg">
    <div class="hero">
        <div class="hero_content">
            <div class="title">Sherlock</div>
            <div class="cta">
                <a class="btn" href="?view=exercises"><span class="icon"><i class="fa fa-bullseye fa-lg" aria-hidden="true"></i></span> <span class="text">Training & Practice</span></a>
                
                <?php
                    if ( isset( $authUrl ) ) {
                        echo '<a class="gSignIn" href=" ' . $authUrl .'"><img src="images/btn_google_signin.png" alt="Sign in With Google"/></a>';
                    }
                ?>
                
                <a href="https://accounts.google.com/signup" target="_blank">Create a Google Account</a>
            </div>
        </div>
    </div>
</div>