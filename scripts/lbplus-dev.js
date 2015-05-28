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
/* global onYouTubeIframeAPIReady */
/* global moment */

// video object
var video = {

    'player': null,
    'selector': 'ytv',
    'vId': null,
    'segmented': false,
    'start': 0,
    'end': 0,
    'duration': null,
    'rewinded': false

};

// hold the count of the tag
// also use for the z-index
var tagCount = 0;
var updatePrgrsInterval;
var studentResponses = [];

/****** CORE *******/

// when the document is ready
$( function() {

    // get/set/load YouTube video ID
    video.vId = $( '#' + video.selector ).data( 'video-id' );

    // get/set video start and end seconds
    var vStart = $( '#' + video.selector ).data( 'start' );
    var vEnd = $( '#' + video.selector ).data( 'end' );

    if ( vStart !== Number( '-1' ) ) {

        video.start = moment.duration( vStart, 'mm:ss' ).asSeconds() / 60;
        video.end = moment.duration( vEnd, 'mm:ss' ).asSeconds() / 60;

        // if video start second is greater and equal to zero
        if (  video.start >= 0 && video.start !== undefined  ) {

            // and if start second is less then end second
            if ( video.start < video.end ) {

                // video is segmented
                video.segmented = true;

            }

        }

    }

    $.fn.loadYouTubeAPI();

} );

/****** YOUTUBE API FUNCTIONS *******/

function onYouTubeIframeAPIReady() {

    var config = {

        'autoplay': 0,
        'controls': 0,
        'disablekb': 1,
        'enablejsapi': 1,
        'iv_load_policy': 3,
        'loop': 0,
        'modestbranding': 1,
        'rel': 0,
        'showinfo': 0

    };

    if ( video.segmented ) {

        config.start = video.start;
        config.end = video.end;

    }

    video.player = new YT.Player( video.selector, {

        width: '640',
        height: '360',
        videoId: video.vId,
        playerVars: config

    } );

    video.player.addEventListener( 'onReady', function() {

        if( video.segmented ) {

            video.duration = video.end - video.start;

        } else {

            video.duration = video.player.getDuration();

        }

        $( '.progress_bar .time .duration' ).html( moment( video.duration * 1000 ).format( 'mm:ss' ) );

        $( '#videoPlayBtn' ).on( 'click', function() {

            $( this ).remove();

            video.player.playVideo();

        } );

    } );

    video.player.addEventListener( 'onStateChange', function( event ) {

        var state = event.target.getPlayerState();

        switch ( state ) {

            case YT.PlayerState.ENDED:

                $( '.lbplus_wrapper' ).showTransition( 'Video Ended', 'Calculating results. Please wait...' );
                $( '.lbplus_media .overlay' ).html( '<div id="videoPlayBtn">ENDED</div>' );

                for ( var i = 0; i < $( '.btn[data-action-id]' ).length; i++ ) {

                    $( '.btn[data-action-id]:eq('+i+')' ).addClass( 'disabled' );

                }

                $( '.btn.rewind' ).addClass( 'disabled' );

                // clear update progress bar interval
                clearInterval( updatePrgrsInterval );

                $( '.progress_bar .progressed' ).css( "width", "100%" );
                $( '.progress_bar .time .elapsed' ).html( moment( video.duration * 1000 ).format( 'mm:ss' ) );

                // write to file and calculate score
                $.fn.writeToFile();

            break;

            case YT.PlayerState.PLAYING:

                if ( !video.rewinded ) {

                    // add clicked event listener to all action buttons
                    for ( var j = 0; j < $( '.btn[data-action-id]' ).length; j++ ) {

                        $( '.btn[data-action-id]:eq('+j+')' ).removeClass( 'disabled' );
                        $( '.btn[data-action-id]:eq('+j+')' ).clickAction();

                    }

                    $( '.btn.rewind' ).removeClass( 'disabled' );
                    $( '.btn.rewind' ).clickAction();

                    // Begin updating progress bar
                    updatePrgrsInterval = setInterval( updateProgress, 100 );

                    // start listening to tag events
                    $.fn.tagHoverAction();

                }

            break;

        }

    } );

}

/****** HELPER / EVENT FUNCTIONS *******/

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
    var tag = document.createElement( 'script' );
    var firstScriptTag = document.getElementsByTagName( 'script' )[0];

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
$.fn.clickAction = function() {

    // on click
    $( this ).on( 'click', function() {

        // if not disabled
        if ( !$( this ).hasClass( 'disabled' ) ) {

            if ( $( this ).hasClass( 'rewind' ) ) {

                var rewindLength = Number( $( this ).data( 'length' ) );
                var currentVideoTime = video.player.getCurrentTime();

                if ( currentVideoTime <= rewindLength ) {

                    rewindLength = 0;

                } else {

                    rewindLength = currentVideoTime - rewindLength;

                }

                video.player.seekTo( rewindLength );

                video.rewinded = true;

            }

            // Add icon to the progress bar
            // and start cooldown
            $( this ).addTag();
            $( this ).cooldown();

        }

    } );

};

 /**
  * Add the tag in the argument to the DOM.
  * Use the icon associated with the button and current time.
  *
  * @author Mike Kellum
  * @contributor Ethan Lin
  * @since 0.0.1
  *
  * @param none
  * @return void
  *
  */
$.fn.addTag = function() {

    // Get the current video time (to format later)
    var curTimeMs = video.player.getCurrentTime();

    // Derive the elements of the new span from tag and time info
    var actionId = $( this ).data( 'action-id' );
    var actionName = $( this ).find( '.action_name' ).html();
    var formattedTime = moment( curTimeMs * 1000 ).format( 'mm:ss' );
    var barPx = $( '.progress_bar .progressed' ).width() + 10; // +10 because that is the half of tag respectively to the width of the progress bar container and the bar itself, i.e., ( container width - progress bar width - tag width ) / 2
    var icon = $( this ).find( '.icon' ).html();

    // Build the span
    var span = '<span class="tag" data-action-id="' + actionId +
               '" data-time="' + formattedTime +
               '" data-count="' + tagCount +
               '" style="left:' + barPx + 'px;' +
               'z-index:' + (tagCount++) +
               '">' + icon +
               '</span></span>';

    var studentTag = {

        "id": actionId,
        "name": actionName,
        "timestamped": formattedTime

    };

    studentResponses.push( studentTag );

    $( '.progress_bar_holder' ).prepend( span );

};

/**
  * Listen to the tag hover event
  * Swapping the z-index
  *
  * @author Ethan Lin
  * @since 0.0.1
  *
  * @param none
  * @return void
  *
  */
$.fn.tagHoverAction = function() {

    $( '.progress_bar_holder' ).on( 'mouseover', '.tag', function() {

        $( this ).css( 'z-index', 99 );

    } );

    $( '.progress_bar_holder' ).on( 'mouseout', '.tag', function() {

        $( this ).css( 'z-index', $( this ).data( 'count' ) );

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

    // get the button limits
    var limitElement = $( this ).find( '.limits' );
    var limits = Number ( limitElement.html() );

    if ( cooldownBar.width() >= buttonWidth ) {

        cooldownBar.width( 0 );
        button.addClass( 'disabled' );

    }

    // minus one limit and update displayed number
    limits--;
    limitElement.html( limits );

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
    $( '.transition_overlay' ).css( 'display', 'none' ).fadeIn();

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

 /**
 * Write user inputs to file
 *
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
 * @return void
 *
 */
 $.fn.writeToFile = function() {

    if ( studentResponses.length <= 0 ) {

        studentResponses = -1;

    }

    $.post( 'includes/student_input.php', {student: studentResponses}, function( response ) {

        if ( response ) {

            $.get('includes/views/score_view.php', function( res ) {

                $.fn.hideTransition();
                $( '.lbplus_wrapper .lbplus_container' ).html( res ).hide().fadeIn( 1000 );

            } );

        }

    } );

 };

/****** UTILITY FUNCTIONS *******/

 /**
  * Set the progress bar width and the elapsed time
  * according to the current video progress.
  * This is called continuously once the video starts.
  *
  * @author Mike Kellum
  * @since 0.0.1
  *
  * @param none
  * @return void
  *
  */
function updateProgress() {

    var curTimeMs = video.player.getCurrentTime();

    if ( video.segmented ) {

        curTimeMs = video.player.getCurrentTime() - video.start;

    }

    var newWidth = Math.floor( ( 100 / video.duration ) * curTimeMs );
    var formattedTime = moment( curTimeMs * 1000 ).format( 'mm:ss' );

    $( '.progress_bar .progressed' ).css( "width", newWidth + "%" );
    $( '.progress_bar .time .elapsed' ).html( formattedTime );

}












