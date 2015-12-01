/*
 * LiveButtons+
 * @author: Ethan Lin, Mike Kellum
 * @uri: https://github.com/oel-mediateam/sherlock
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
var trainingMode = false;

/****** CORE *******/

// when the document is ready
$( function () {
    
    'use strict';
    
    if ( $( '#google_revoke_connection' ).length ) {
        
        $( '#google_revoke_connection' ).click( function() {
            
            $( '#disconnect-confirm' ).dialog( {
                
                dialogClass: "no-close",
                title: 'Disconnect Google Account',
                position: { my: "center", at: "center", of: $( '.signin_view' ) },
                resizable: false,
                draggable: false,
                width: 300,
                height: 215,
                modal: true,
                buttons: {
                    OK: function() {
                        
                        $( this ).dialog( "close" );
                        
                        $( '.sherlock_wrapper' ).showTransition( 'Revoke Access', 'Disconnecting Google account. Please wait...' );
                        
                        setTimeout( function() {
                            
                            $.post( 'includes/disconnect_google.php', { revoke: 1 }, function() {
                            
                                location.reload();
                                
                            } );
                            
                        }, 3000);
                        
                    },
                    Cancel: function() {
                        
                        $( this ).dialog( "close" );
                        
                    }
                }
                
            } );
            
            return false;
            
        } );
        
    }
    
    // if it is a training mode
    if ( $( '.sherlock_view' ).data( 'mode' ) === 'training' ) {
        
        $( '.sherlock_mode_msg' ).html( 'Training Mode' ).removeClass( 'hide' );
        trainingMode = true;
        
    }
    
    // get/set/load YouTube video ID
    video.vId = $( '#' + video.selector ).data( 'video-id' );
    
    // if there a YouTube video
    if ( video.vId ) {
        
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
        
    } // end YouTube if/else
    
    // if there are select elements
    if ( $( 'select' ).length ) {
        
        $( 'select' ).each( function() {
    
            var $this = $( this );
            var opened = false;
            var numberOfOptions = $( this ).children( 'option' ).length;
            var existingClasses = $this.attr('class');
            
            $this.addClass( 'select-hidden' ); 
            $this.wrap( '<div class="select"></div>' );
            $this.after( '<div class="select-styled"></div> ');
        
            var styledSelect = $this.next('div.select-styled');
            
            styledSelect.addClass( existingClasses );
            styledSelect.text( $this.children( 'option' ).eq( 0 ).text() );
          
            var list = $( '<ul />', {
                
                'class': 'select-options'
                
            }).insertAfter( styledSelect );
          
            for ( var i = 0; i < numberOfOptions; i++ ) {
                
                $('<li />', {
                    text: $this.children( 'option' ).eq( i ).text(),
                    rel: $this.children( 'option' ).eq( i ).val(),
                }).appendTo( list );
                
            }
          
            var listItems = list.children( 'li' );
          
            styledSelect.click( function( e ) {
                
                e.stopPropagation();
                
                if ( opened ) {
                    
                    styledSelect.removeClass( 'active' );
                    list.hide();
                    opened = false;
                    
                } else {
                    
                    $( 'div.select-styled.active' ).each( function() {
                    
                        $( this ).removeClass( 'active' ).next( 'ul.select-options' ).hide();
                        
                    } );
                    
                    $( this ).toggleClass( 'active' ).next( 'ul.select-options' ).toggle();
                    
                    opened = true;
                    
                }
                
            } );
          
            listItems.click( function( e ) {
                
                e.stopPropagation();
                
                var selectValue = $( this ).attr( 'rel' );
                
                styledSelect.text( $( this ).text() ).removeClass( 'active' );
                $this.val( selectValue );
                list.hide();
                opened = false;
                
                // if it is on the exercise selection view
                if ( $( '.selection_view' ).length ) {
                    
                    $( '.exercise_info' ).remove();
                
                    $.post( 'includes/exercise_info.php', { id: selectValue }, function(response) {
                        
                        if ( response ) {
                            
                            var result = JSON.parse(response);
                            
                            $( '.select' ).after( '<div class="exercise_info"><div class="description_box"><p><strong>Description:</strong></p><div class="description"></div></div><p class="meta"></p></div>' );
                            $( '.exercise_info .description_box .description' ).html( result.description );
                            
                            if ( Number( result.allow_retake ) ) {
                                
                                $( '.exercise_info .meta' ).html( 'Number of attempts: <strong>unlimited</strong>' );
                                
                            } else {
                                
                                $( '.exercise_info .meta' ).html( 'Number of attempts: <strong>' + result.attempts + '</strong>' );
                                
                            }
                            
                            $( '.exercise_info .meta' ).append( ( result.exrs_type_id > 0 ) ? ' | Exercise type: <strong>' + $.fn.getExerciseType( result.exrs_type_id ) + '</strong>' : '' );
                            
                        }
                        
                    } );
                    
                }
                
            } );
          
            $( document, styledSelect ).click( function() {
                    
                    styledSelect.removeClass( 'active' );
                    list.hide();
                    opened = false;
                
            } );
        
        } );
        
    } // end selection element if/else
    
    $( '#lti_selection' ).click( function() {
        
        var url = $( 'input[name="return_url"]' ).val();
        var exrs_id = $( 'option:selected' ).val();
        var link_type = $( 'input[name="type"]' ).val();
        
        if ( exrs_id !== 'hide' ) {
            
            $.ajax({
            url: "includes/get_lti_link.php",
            type: 'POST',
            data: {
              return_url: url,
              id: exrs_id,
              type: link_type
            },
            success: function(data) {
                
              window.location.href = data;
              
            }
            
          });
            
        } else {
            
            $( 'h1' ).after('<div class="callout danger">No exercise was selected. Please select an exercise.</div>' );
            
        }
        
          return false;
          
    } );

} );

/****** YOUTUBE API FUNCTIONS *******/

function onYouTubeIframeAPIReady() {

    var config = {

        'autoplay': 0,
        'controls': 0,
        'disablekb': 0,
        'enablejsapi': 0,
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

        if ( video.segmented ) {

            video.duration = video.end - video.start;

        } else {

            video.duration = video.player.getDuration();

        }
        
        // if it is a training mode
        if ( trainingMode ) {
            
            $.post( 'includes/get_exercise_from_session.php', { id: 1 }, function( data ) {
                
                var numActions = data.length;
                var hintBarWidth = $( '.tag_hints_holder' ).width();
                
                for ( var a = 0; a < numActions; a++ ) {
                    
                    var numPos = data[a].positions.length;
                    
                    for ( var p = 0; p < numPos; p++ ) {
                        
                        var begin = $.fn.toSecond( data[a].positions[p].begin );
                        var end = $.fn.toSecond( data[a].positions[p].end );
                        
                        var left = Math.floor( hintBarWidth * ( 100 / video.duration * begin ) / 100 );
                        var right = Math.floor( hintBarWidth * ( 100 / video.duration * end ) / 100 );
                        var width = right - left;
                        
                        $( '.tag_hints_holder' ).append( '<div class="hint" style="left:'+left+'px; width:'+width+'px;"></div>' );
                        
                    }
                    
                }
                
            } );
            
        }
        
        $( '.progress_bar .time .duration' ).html( moment( video.duration * 1000 ).format( 'mm:ss' ) );

        $( '#videoPlayBtn' ).on( 'click', function() {
            
            $.post( 'includes/start_exercise.php', { begin: 1 }, function( data ) {
                
                if ( data >= 1 ) {
                    
                    $( '#videoPlayBtn' ).hide();
                    video.player.playVideo();
                    
                } else {
                    
                    $( '.sherlock_wrapper' ).showTransition( 'SORRY!', 'You already attempted this exercise.<br /><a href="?page=exercises">Back to Exercises</a>' );
                    
                }
        
            } );

        } );

    } );

    video.player.addEventListener( 'onStateChange', function( event ) {

        var state = event.target.getPlayerState();

        switch ( state ) {

            case YT.PlayerState.ENDED:

                $( '.sherlock_wrapper' ).showTransition( 'Video Ended', 'Calculating results. Please wait...' );
                $( '#videoPlayBtn' ).html( 'ENDED' ).show();

                for ( var i = 0; i < $( '.btn[data-action-id]' ).length; i++ ) {

                    $( '.btn[data-action-id]:eq('+i+')' ).addClass( 'disabled' );

                }

                $( '.btn.rewind' ).addClass( 'disabled' );

                // clear update progress bar interval
                clearInterval( updatePrgrsInterval );

                $( '.progress_bar .progressed' ).css( "width", "100%" );
                $( '.progress_bar .time .elapsed' ).html( moment( video.duration * 1000 ).format( 'mm:ss' ) );

                setTimeout( function() {

                    // write to file and calculate score
                    $.fn.writeToFile();

                }, 3000 );

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
                    updatePrgrsInterval = setInterval( function() {
                            $.fn.updateProgress( video );
                        } , 100 );

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
                var clickedBtns = null;

                if ( currentVideoTime <= rewindLength ) {

                    rewindLength = 0;

                } else {

                    rewindLength = currentVideoTime - rewindLength;

                }
                
                if ( $( '.btn.disabled' ).length ) {
                    
                    clickedBtns = $( '.btn.disabled' );
                    
                    clickedBtns.each( function(){
                    
                        $( this ).find( 'span.progress' ).stop()
                        .animate( { 'width': 0 }, 1000 );
                        
                    } );
                    
                }
                
                video.player.pauseVideo();
                video.player.seekTo( rewindLength );
                $.fn.updateProgress( video );
                
                $( '#videoPlayBtn' ).html( '<span class="icon-paused"></span><br /><small>PAUSED</small>' )
                                    .addClass( 'paused' ).show();
                $( '.sherlock_status_msg' ).html( 'Video paused ... will resume shortly.' )
                                         .removeClass( 'hide' ).addClass( 'blink' );

                setTimeout( function() {
                    
                    $( '#videoPlayBtn' ).hide().removeClass( 'paused' ).html( 'START' );
                    $( '.sherlock_status_msg' ).html( '' ).addClass( 'hide' ).removeClass( 'blink' );
                    video.player.playVideo();
                    
                    if ( clickedBtns !== null ) {
                        
                        clickedBtns.extendedCooldown();
                        
                    }

                } , 3000);


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
 * The extended cooldown event to execute when rewind button used
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
 * @return void
 *
 */
$.fn.extendedCooldown = function() {
    
    $(this).each( function() {
        
        var button = $( this );
        var buttonWidth = button.width();
        var cooldownTimeInSecond = Number( button.attr( 'data-cooldown' ) ) * 1000;
        var extendedCooldown = cooldownTimeInSecond * 5;
        var cooldownBarElement = button.find( '.cooldown .progress' );
    
        cooldownBarElement.animate( {

            'width': buttonWidth

        }, extendedCooldown, function() {

            button.removeClass( 'disabled' );

        } );
    
    } );

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
 $.fn.showTransition = function( heading, subheading, hideSpinner ) {
    
    hideSpinner = typeof hideSpinner !== 'undefined' ? hideSpinner : false;
    
    if ( hideSpinner === false ) {
        
        $( this ).prepend( '<div class="transition_overlay"><div class="heading">' + heading + '</div><div class="subheading">' + subheading + '</div><div class="loading"><span class="icon-spinner spin"></span></div></div>' );
        
    } else {
        
        $( this ).prepend( '<div class="transition_overlay"><div class="heading">' + heading + '</div><div class="subheading">' + subheading + '</div></div>' );
        
    }
    
    $( '.transition_overlay' ).css( 'display', 'none' ).fadeIn();

 };

 /**
 * Hiding the transition overlay
 * and remove from DOM after completion
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
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
        
        if ( response  === 1 || response === '1' ) {
            
            $.get('includes/views/score_view.php', function( res ) {
                
                $.fn.hideTransition();
                $( '.sherlock_wrapper .sherlock_container' ).html( res ).hide().fadeIn( 1000 );

            } );

        } else {
            
            $( '.sherlock_wrapper' ).showTransition( 'Something went wrong...', 'Sherlock lost his writting pen.' );
            
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
* @param object
* @return void
*
*/
$.fn.updateProgress = function( video ) {
  
  var curTimeMs = video.player.getCurrentTime();

    if ( video.segmented ) {

        curTimeMs = video.player.getCurrentTime() - video.start;

    }

    var newWidth = Math.floor( ( 100 / video.duration ) * curTimeMs );
    var formattedTime = moment( curTimeMs * 1000 ).format( 'mm:ss' );

    $( '.progress_bar .progressed' ).css( "width", newWidth + "%" );
    $( '.progress_bar .time .elapsed' ).html( formattedTime );
  
};


/**
* Get the exercise type context by exercise ID
*
* @author Ethan Lin
* @since 1.0.0
*
* @param id
* @return string|null
*
*/
$.fn.getExerciseType = function( id ) {
  
  var type = null;
  
  switch ( Number( id ) ) {
      
    case 1:
        type = 'Demonstration';
        break;
    case 2:
        type = 'Development Testing Purposes';
        break;
    case 3:
        type = 'Training';
        break;
    case 4:
        type = 'Assignment';
        break;
    default:
        type = null;
        break;
      
  }
  
  return type;
  
};

$.fn.toSecond = function( value ) {
    
    var timestring = value.split( ':' );

    return ( Number( timestring[0] ) * 60 ) + Number( timestring[1] );
    
};