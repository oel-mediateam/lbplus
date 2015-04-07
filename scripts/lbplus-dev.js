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

    // get/set YouTube video ID
    video.vId = $( '#' + video.selector ).attr( 'data-videoId' );

    // load YouTube video
    $.fn.loadYouTubeAPI();

    // add clicked event listener to all action buttons
    for ( var i = 0; i < $( '.btn[data-action]' ).length; i++ ) {

        $( '.btn[data-action]:eq('+i+')' ).clicked();

    }

    // FOR DEV/DEMO PURPOSES
    $( '.progress_bar' ).progress();
    // END FOR DEV/DEMO PURPOSES

} );

/****** HELPER / EVENT FUNCITONS *******/

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

    $( this ).on( 'click', function() {

        if ( !$( this ).hasClass( 'disabled' ) ) {

            if ( $( this ).hasClass( 'odd' ) ) {

                soundEffects.odd.play();

            } else {

                soundEffects.powerUp.play();

            }

            $( this ).cooldown();

            // dev purpose
            $( '.dev-log' ).append( $(this).attr('data-action') + " @ " + ( video.player.getCurrentTime() * 1000 ) + ' <br />' );
            // end dev purpose

        }

    } );

};

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

    if ( cooldownBar.width() >= buttonWidth ) {

        cooldownBar.width( 0 );
        button.addClass( 'disabled' );

    }

    cooldownBar.animate( {

        'width': buttonWidth

    }, cooldownTimeInSecond, function() {

        button.removeClass( 'disabled' );

    } );

};

/**
 * The animating of the video progress bar
 * and updating of the timecode as the video is progressing
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
 * @return void
 *
 */
$.fn.progress = function() {

    var progressBar = $( this );
    var progressBarWidth = progressBar.width();
    var progressingBarElement = progressBar.find( '.progressed' );
    var progressingBar = $( progressingBarElement.selector );

    progressingBar.animate( {

        'width': progressBarWidth

    }, 30000);

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

 };

/****** YOUTUBE API FUNCITONS *******/

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

    loadCustomYouTubeEvents();

}

function onPlayerStateChange( event ) {

    // dev purposes
    var states = ['ended','playing','paused','buffering', ,'video cued'];
    var status = null;

    if ( event.target.getPlayerState() === -1 ) {

        status = 'unstarted';

    } else {

        status = states[event.target.getPlayerState()];

    }

    $( '.dev-log' ).append( status + '<br />');
    // end dev

}

function loadCustomYouTubeEvents() {

    $( '#videoPlayBtn' ).on( 'click', function() {

        $( this ).remove();

        video.player.playVideo();

        // dev purposes
        $( '#stopVideoBtn' ).removeAttr('disabled');
        // end dev

    } );

}








