/*
 * LiveButtons+
 * @author: Ethan Lin, Mike Kellum
 * @uri: https://github.com/oel-mediateam/lbplus
 * @version: 0.0.1 (alpha)
 * @license: The Artistic License 2.0
 *
 * Copyright (c) 2015 University of Wisconsin-Extension,
 * Divison of Continuing Education, Outreach & E-Learning
 *
*/

/* global YT */
/* global moment */

// sound effects (global object variable)
var soundEffects = {

    'click': 'click',
    'powerUp': 'power_up',
    'odd': 'no_mercy'

};

// video object
var video = {

    'player': null,
    'selector': 'ytv',
    'vId': null

};


/****** CORE *******/

// when the document is ready
$( document ).ready( function() {

    // load the sound effects object
    $.fn.loadSoundEffects();

    // dev purpose
    $( '.dev-log' ).append( 'Button sounds loaded.<br />' );
    // end dev purpose

    // get/set/load YouTube video ID
    video.vId = $( '#' + video.selector ).attr( 'data-videoId' );

    $.fn.loadYouTubeAPI();

} );

/****** HELPER / EVENT FUNCTIONS *******/

/**
 * Load the sound effects object
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
 * @return void
 *
 */
$.fn.loadSoundEffects = function() {

    // loop through each properity in the soundEffects object
    $.each( soundEffects, function( sound, src ) {

        // hold the source/value of the current properity temporary
        var temp = src;

        // create and assign an audio element to current properity
        soundEffects[sound] = document.createElement( 'audio' );

        // set the source of the audio to current properity
        soundEffects[sound].setAttribute( 'src', 'sounds/' + temp + '.mp3' );

    } );

};

/**
 * Load YouTube API and video
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param string, string
 * @return void
 *
 */

$.fn.loadYouTubeAPI = function() {

    // insert YouTube API scripts to HTML head
    var tag = document.createElement('script');
    var firstScriptTag = document.getElementsByTagName('script')[0];

    tag.src = "https://www.youtube.com/iframe_api";
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    // dev purpose
    $( '.dev-log' ).append( 'YouTube API loaded.<br />' );
    // end dev purpose

};

/**
 * The click event to execute when an action button is clicked
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
 * @return void
 *
 */
$.fn.clicked = function() {

    // on click
    $( this ).on( 'click', function() {

        // if not disabled
        if ( !$( this ).hasClass( 'disabled' ) ) {
            // Add icon to DOM
            addTag(this);

            // play sound base on class
            if ( $( this ).hasClass( 'odd' ) ) {

                soundEffects.odd.play();

            } else {

                soundEffects.powerUp.play();

            }

            // dev purpose
            $( '.dev-log' ).append( $(this).find('.action_name').html() + " @ " + ( moment( video.player.getCurrentTime() * 1000 ).format('mm:ss') ) + ' <br />' );
            // end dev purpose

            $( this ).cooldown();

        }

    } );

};

 /**
  * Add the tag in the argument to the DOM.
  * Use the icon associated with the button and current time.
  * 
  * @author Mike Kellum
  * @since 0.0.2 (?)
  *
  * @param jquery div (?)
  * @return void
  *
  */
function addTag(tag) {
    // Get the current video time (to format later)
    var curTimeMs = video.player.getCurrentTime();

    // Derive the elements of the new span from tag and time info
    var actionName = $( tag ).data("action");
    var formattedTime = moment(curTimeMs * 1000).format('mm:ss');
    var barPx = timeToProgressBarPx(curTimeMs) + 10; // TODO: why 10?
    var icon = $( tag ).children('span.icon').html();
    
    // Build the span
    var span = '<span class="tag" data-action="' + actionName +
               '" data-time="' + formattedTime +
               '" style="left:' + barPx + 'px' +
               '">' + icon +
               '</span></span>';

    $( '.progress_bar_holder' ).prepend(span);
}

/**
 * The cooldown event to execute when an action button triggered it
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
 * @return void
 *
 */
$.fn.cooldown = function() {

    var index = $( this ).index( '.btn' );
    var button = $( this );
    var buttonWidth = button.width();
    var cooldownTimeInSecond = Number( button.attr( 'data-cooldown' ) ) * 1000;
    var cooldownBarElement = button.find( '.cooldown .progress' );
    var cooldownBar = $( cooldownBarElement.selector + ':eq(' + index + ')' );

    // get the button limits
    var limitElement = $( this ).find('.limits');
    var limits = Number ( limitElement.html() );

    if ( cooldownBar.width() >= buttonWidth ) {

        cooldownBar.width( 0 );
        button.addClass( 'disabled' );

    }

    // minus one limit and update displayed number
    limits--;
    limitElement.html( limits );

    // dev purpose
    $( '.dev-log' ).append( 'Subtracting limit by one, cooling down... '+ limits +' remain<br />' );
    // end dev purpose

    // if no limit is 0
    if ( limits <= 0 ) {

        // disable the button
        $( this ).addClass( 'disabled' );

    } else {

        cooldownBar.animate( {

            'width': buttonWidth

        }, cooldownTimeInSecond, function() {

            button.removeClass( 'disabled' );

        } );

    }

};

/**
 * Displaying the transition overlay
 * with dynamic messages
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param string, string
 * @return void
 *
 */
 $.fn.showTransition = function( heading, subheading ) {

    $( this ).prepend( '<div class="transition_overlay"><div class="heading">' + heading + '</div><div class="subheading">' + subheading + '</div><div class="loading"><span class="icon-spinner spin"></span></div></div>' );
    $( '.transition_overlay' ).css('display','none').fadeIn();

    // dev purpose
    $( '.dev-log' ).append( 'Transition overlay toggled on.<br />' );
    // end dev purpose

 };

 /**
 * Hiding the transition overlay
 * and remove from DOM after completion
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param string, string
 * @return void
 *
 */
 $.fn.hideTransition = function() {

    $( '.transition_overlay' ).fadeOut( function() {
        $( this ).remove();
    } );

    // dev purpose
    $( '.dev-log' ).append( 'Transition overlay toggled off.<br />' );
    // end dev purpose

 };

/****** YOUTUBE API FUNCTIONS *******/

function onYouTubeIframeAPIReady() {

    video.player = new YT.Player( video.selector, {

        width: '649',
        height: '360',
        videoId: video.vId,
        playerVars: {
            'autoplay': 0,
            'controls': 0,
            'disablekb': 1,
            'enablejsapi': 1,
            'iv_load_policy': 3,
            'loop': 0,
            'modestbranding': 1,
            'rel': 0,
            'showinfo': 0
        },
        events: {

            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange

        }

    } );

}

function onPlayerReady() {

    var duration = video.player.getDuration();
    duration = duration * 1000;
    duration = moment(duration).format('mm:ss');

    $( '.progress_bar .time .duration' ).html( duration );

    loadStartBtnEvent();

    // Begin updating progress bar
    setInterval(updateProgress, 100);

    // dev purpose
    $( '.dev-log' ).append( 'YouTube player ready.<br />' );
    // end dev purpose

}

function onPlayerStateChange( event ) {

    var state = event.target.getPlayerState();

    // dev purposes
    var states = ['ended','playing','paused','buffering', ,'video cued'];
    var status = null;

    if ( state === -1 ) {

        status = 'unstarted';

    } else {

        status = states[state];

    }

    $( '.dev-log' ).append( status + '<br />');
    // end dev

    switch ( state ) {

        case YT.PlayerState.ENDED:

            $( '.lbplus_wrapper' ).showTransition( 'Video Ended', 'Calculating results. Please wait...' );
            $( '.lbplus_media .overlay' ).html('<div id="videoPlayBtn">START</div>');
            loadStartBtnEvent();

            // disable all action buttons
            for ( var i = 0; i < $( '.btn[data-action]' ).length; i++ ) {

                $( '.btn[data-action]:eq('+i+')' ).addClass('disabled');

            }

            // dev purpose
            $( '#stopVideoBtn' ).attr('disabled','');
            $( '.dev-log' ).append( 'Video ended. Transition overlay should be shown. Start button should be redisplayed. Action buttons should be disabled.<br />' );
            // end dev

        break;

        case YT.PlayerState.PLAYING:

            // dev purpose
            $( '.dev-log' ).append( 'Video playing... allow buttons to be clickable.<br />' );
            // end dev

            // add clicked event listener to all action buttons
            for ( var j = 0; j < $( '.btn[data-action]' ).length; j++ ) {

                $( '.btn[data-action]:eq('+j+')' ).removeClass('disabled');
                $( '.btn[data-action]:eq('+j+')' ).clicked();

            }

        break;

    }

}

function loadStartBtnEvent() {

    $( '#videoPlayBtn' ).on( 'click', function() {

        $( this ).remove();

        // Start the refresh function.
        // setInterval(function () {refreshBar()}, 1000);

        video.player.playVideo();

        // dev purposes
        $( '#stopVideoBtn' ).removeAttr('disabled');
        // end dev

    } );

}

/****** UTILITY FUNCTIONS *******/

 /**
  * Set the progress bar width and the elapsed time
  * according to the current video progress.
  * This is called continuously once the video starts.
  * 
  * @author Mike Kellum
  * @since 0.0.2 (?)
  *
  * @param none
  * @return void
  *
  */
function updateProgress() {
    var curTimeMs = video.player.getCurrentTime();
    var newWidth = timeToProgressBarPx(curTimeMs);
    var formattedTime = moment(curTimeMs * 1000).format('mm:ss');

    $( '.progress_bar .progressed' ).css("width", newWidth + "px");
    $( '.progress_bar .time .elapsed' ).html( formattedTime );
}

 /**
  * Take any time (usually current pulled from player)
  * and return the number of px the progress bar should
  * be set to to match proportion of that time against
  * duration.
  * 
  * @author Mike Kellum
  * @since 0.0.2 (?)
  *
  * @param number, number
  * @return number
  *
  */
function timeToProgressBarPx(time) {
    var duration = video.player.getDuration();
    var progressBarWidth = $( '.progress_bar' ).width();
    
    return progressBarWidth * (time / duration);
}












