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
/* global ActiveXObject */

// video object
var video = {

    'player': null,
    'selector': 'ytv',
    'vId': null,
    'segmented': false,
    'start': 0,
    'end': 0,
    'duration': null,
    'rewinded': false,
    'started': false

};

var segment = {
    'begin': 0,
    'end': 0,
    'playing': false
};

// HTML DOM ELEMENTS
var el = {
    
    google_revoke: '#google_revoke_connection',
    google_revoke_confirm: '#disconnect-confirm',
    google_revoke_ok: '#revoke_ok',
    google_revoke_cancel: '#revoke_cancel',
    
    sherlock_wrapper: '#sherlock-wrapper',
    sherlock_banner_label: '#sherlock-wrapper .navbar .container .exercise-type-mode',
    sherlock_body_container: '#sherlock-wrapper .container.body',
    sherlock_grid_container: '#sherlock-wrapper .container .active-exercises.exercise-grid',
    sherlock_grid_item: '#sherlock-wrapper .container .active-exercises.exercise-grid .grid-item',
    exerciseEmbedBtn: '#sherlock-wrapper .container .active-exercises.exercise-grid .grid-item .thumbnail .embedBtn',
    currentPage: '.exercise-pagination .controls .pageActions .page-number .currentPage',
    prevPageBtn: '.exercise-pagination .controls .pageActions .previous',
    nextPageBtn: '.exercise-pagination .controls .pageActions .next',
    
    videoPlayBtn: '#videoPlayBtn'
    
};

// hold the count of the tag
// also use for the z-index
var tagCount = 1;
var updatePrgrsInterval;
var studentResponses = [];
var trainingMode = false;
var reviewMode = false;
var pauseOnce = true;
var preCount = null;

/****** CORE *******/

// when the document is ready
$( function () {
    
    'use strict';
    
    // the browser has flash support and is Internet Explorer
    // display no support message and end further script
    if ( $.fn.flashExist() && $.fn.isIE() ) {
        
        $( el.sherlock_wrapper ).html("<h1>Sorry, your web browser is not supported.</h1><p>Please try using latest stable version of <a href=\"https://www.mozilla.org\" target=\"_blank\">Mozilla Firefox</a>, <a href=\"https://www.google.com/chrome/browser/desktop/\" target=\"_blank\">Google Chrome</a>, or <a href=\"http://www.apple.com/safari/\" target=\"_blank\">Safari</a>.</p>");
        
        return 0;
        
    }
    
    // google revoke connection ID exists on the DOM
    // add a click listening event for displaying a confirmation dialog
    if ( $( el.google_revoke ).length ) {
        
        $( el.google_revoke ).on( 'click', function() {
            
            $( el.google_revoke_confirm ).fadeIn( function() {
                $( this ).removeClass( 'hide' ).css( 'display', '' );
            } );
            return false;
            
        } );
        
        $( el.google_revoke_ok ).on( 'click', function() {
            
            $.post( 'includes/disconnect_google.php', { revoke: 1 }, function() {          
                location.reload();
            } );
            
            return false;
            
        } );
        
        $( el.google_revoke_cancel ).on( 'click', function() {
            
            $( el.google_revoke_confirm ).fadeOut();
            return false;
            
        } );
        
    }
    
    $( el.prevPageBtn ).on( 'click', function() {
        $.fn.goToPage( 'prev' );
    } );
    
    $( el.nextPageBtn ).on( 'click', function() {
        $.fn.goToPage( 'next' );
    } );
    
    if ( $( el.exerciseEmbedBtn ).length ) {
        
        $( el.exerciseEmbedBtn ).on( 'click', function( e ) {
            
            e.stopPropagation();
            e.preventDefault();
            
            var exerciseId = $( this ).parent().parent().data( 'exercise' );
            var protocol = location.protocol;
            var embedURL = '';
            
            if ( protocol.indexOf('s') >= 0 ) {
                protocol = 'https://';
            } else {
                protocol = 'http://';
            }
            
            embedURL = protocol + location.hostname + location.pathname + '?exercise=' + exerciseId;
            
            $( el.sherlock_wrapper ).showTransition( 'Embed Exercise', 'Please copy and paste the code below:<p><textarea onclick="this.focus();this.select()" readonly><iframe src="' + embedURL + '" width="900px" height="570px" frameborder="0" scrolling="auto"></iframe></textarea></p>', {spinner: false, closeBtn: true} );
            
            return false;
            
        } );
        
    }
    
    $( el.videoPlayBtn ).html( '<i class="fa fa-spinner fa-spin"></i><br><small>WAIT</small>' ).addClass( 'paused' );
    
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
    
    // if there are sort select elements
    if ( $( '.sort' ).length ) {
        
        $( '.sort' ).each( function() {
    
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
                
                if ( !$this.children( 'option' ).eq( i )[0].disabled ) {
                    
                    $('<li />', {
                        text: $this.children( 'option' ).eq( i ).text(),
                        'data-id': $this.children( 'option' ).eq( i )[0].attributes[0].value
                    }).appendTo( list );
                    
                }
                
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
                
                var selectValue = $( this ).data( 'id' );
                
                styledSelect.text( $( this ).text() ).removeClass( 'active' );
                $this.val( selectValue );
                list.hide();
                opened = false;
                
                // do something when sort by list item is selected
                $.post( 'includes/sortby.php', { sort: selectValue }, function( res ) {
                    
                    $.fn.displayExercises( res );
                    
                } );
                               
            } );
          
            $( document, styledSelect ).click( function() {
                    
                    styledSelect.removeClass( 'active' );
                    list.hide();
                    opened = false;
                
            } );
        
        } );
        
    } // end selection element if/else
    
    // for LTI resource link selection
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
    
    $( 'body' ).on( 'click', '#goToReview', function() {
        
        $.fn.goToReview();
        return false;
        
    } );
    
    $( 'body' ).on( 'click', '#goToScore', function() {
        
        $.fn.goToScore();
        return false;
        
    } );

} );

/****** YOUTUBE API FUNCTIONS *******/

function onYouTubeIframeAPIReady() {

    var config = {

        'autoplay': 0,
        'controls': 0,
        'enablejsapi': 0,
        'iv_load_policy': 3,
        'loop': 0,
        'modestbranding': 1,
        'rel': 0,
        'showinfo': 0,
        'disablekb': 1

    };
    
    var isPlaying = false;
    
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
        switch ( $( '.sherlock_view' ).data( 'mode' ) ) {
            
            case 'training':
                trainingMode = true;
            break;
            
            case 'review':
                reviewMode = true;
            break;
                
        }
        
        // if it is a training or review mode
        if ( trainingMode || reviewMode ) {
            
            $.post( 'includes/get_exercise_from_session.php', { id: 1 }, function( data ) {
                
                var numActions = data.length;
                var hintBarWidth = $( '.tag_hints_holder' ).width();
                
                for ( var a = 0; a < numActions; a++ ) {
                    
                    var numPos = data[a].positions.length;
                    
                    for ( var p = 0; p < numPos; p++ ) {
                        
                        var reason = data[a].positions[p].reason;
                        
                        var begin = $.fn.toSecond( data[a].positions[p].begin );
                        var end = $.fn.toSecond( data[a].positions[p].end );
                        var mid = ( ( end - begin ) / 2) + begin;
                        
                        var left = hintBarWidth * ( 100 / video.duration * begin ) / 100;
                        var right = hintBarWidth * ( 100 / video.duration * end ) / 100;
                        
                        var width = right - left;
                        var midWidth = left - 8 + ( width / 2 );
                        
                        var span = '<span class="hint_tag" style="left:' + midWidth + 'px; ' + ( reviewMode ? 'opacity:1;' : '' ) + '" data-begin="' + begin + '" data-end="' + end + '" data-name="' + data[a].name + '" data-reason="' + reason + '"><span>' + $.fn.initialism( data[a].name ) + '</span></span>';
                        
                        $( '.tag_hints_holder' ).append( '<div class="hint" style="left:'+left+'px; width:'+width+'px;" data-begin="'+begin+'" data-end="'+end+'" data-mid="'+mid+'" data-id="'+data[a].id+'" data-name="'+data[a].name+'"></div>' );
                        
                        $( '.progress_bar_holder' ).append( span );
                        
                    }
                    
                }
                
            } );
            
            if ( reviewMode ) {
                
                var timeDrag = false;
                   
                $.post( 'includes/get_student_data_from_session.php', { id: 1 }, function( data ) {
                    
                    for ( var t = 0; t < data.length; t++ ) {

                        var span = '';
                                                
                        if ( data[t].negative >= 0 ) {
                            
                            span = '<span class="tag neg" style="left:'+ data[t].pos +'px;"><span>'+$.fn.initialism(data[t].name)+'</span></span>';
                            
                        } else {
                            
                            span = '<span class="tag" style="left:'+ data[t].pos +'px;"><span>'+$.fn.initialism(data[t].name)+'</span></span>';
                        }
                        
                        $( '.progress_bar_holder' ).append( span );
                        
                    }
                    
                    $( '.progress_bar_holder .hint_tag' ).css('cursor','pointer');
                    $( '.progress_bar_holder .hint_tag' ).showHintTagInfo();
                    
                } );
                
                $( '.playPauseBtn' ).on( 'click', function() {
                    
                    if ( isPlaying ) {
                        video.player.pauseVideo();
                    } else {
                        video.player.playVideo();
                    }
                    
                } );
                
                $( '.progress_bar' ).on( 'mousedown', function(e) {
                    
                    timeDrag = true;
                    var pos = e.pageX - $( '.progress_bar' ).offset().left;
                    $( '.progress_bar  .scrubber' ).css('left', pos);
                    
                    $.fn.updateScrubSeek(pos, video);
                    
                } );
                
                $( '.progress_bar' ).on( 'mouseup', function(e) {
                    
                    if ( timeDrag ) {
                        var pos = e.pageX - $( '.progress_bar' ).offset().left;
                        timeDrag = false;
                        $( '.progress_bar  .scrubber' ).css('left', pos);
                        $.fn.updateScrubSeek(pos, video);
                    }
                    
                } );
                
                $( '.progress_bar' ).on( 'mousemove', function(e) {

                    if ( timeDrag ) {
                        
                        var pos = e.pageX - $( '.progress_bar' ).offset().left;
                        
                        if ( pos >= $( this ).width() ) {
                            pos = $( this ).width();
                        }
                        
                        $( '.progress_bar .scrubber' ).css('left', pos);
                        $.fn.updateScrubSeek(pos, video);
                    }
                    
                } );
                
            } // end review
            
        } // end training and review mode
        
        $( '.duration' ).html( moment( video.duration * 1000 ).format( 'mm:ss' ) );
        
        if ( reviewMode ) {
            
            $( el.videoPlayBtn ).html( 'READY' ).css('cursor', 'default').removeClass( 'paused' );
            
        } else {
            
            $( el.videoPlayBtn ).html( 'START' ).removeClass( 'paused' );
            $( el.videoPlayBtn ).on( 'click', function() {
                
                $.post( 'includes/start_exercise.php', { begin: 1 }, function() {
                    
                    video.player.playVideo();
            
                } );
    
            } );
            
        }

    } );

    video.player.addEventListener( 'onStateChange', function( event ) {

        var state = event.target.getPlayerState();

        switch ( state ) {

            case YT.PlayerState.ENDED:
                
                clearInterval( updatePrgrsInterval );
                
                isPlaying = false;
                $( '.playPauseBtn' ).html( '<i class="fa fa-play"></i>' );
                
                if ( !reviewMode ) {
                    
                    $( el.sherlock_wrapper ).showTransition( 'Video Ended', 'Calculating results. Please wait...', {spinner: true, closeBtn: false} );
                    $( el.videoPlayBtn ).html( 'ENDED' ).show();
    
                    for ( var i = 0; i < $( '.btn[data-action-id]' ).length; i++ ) {
    
                        $( '.btn[data-action-id]:eq('+i+')' ).addClass( 'disabled' );
    
                    }
    
                    $( '.btn.rewind' ).addClass( 'disabled' );
    
                    setTimeout( function() {
    
                        // write to file and calculate score
                        $.fn.writeToFile();
    
                    }, 3000 );
                    
                    $( '.progress_bar .progressed' ).css( "width", $( ".progress_bar" ).width() + "px" );
                    $( '.progress_bar .scrubber' ).css( "left", $( ".progress_bar" ).width() + "px" );
                    $( '.elapsed' ).html( moment( video.duration * 1000 ).format( 'mm:ss' ) );
                    
                } else {
                    
                    $( '.progress_bar .progressed' ).css( "width", 0 + "px" );
                    $( '.progress_bar .scrubber' ).css( "left", "" );
                    $( el.videoPlayBtn ).removeClass( 'paused' ).html( 'READY' ).show();
                    
                }

            break;

            case YT.PlayerState.PLAYING:
                
                if ( updatePrgrsInterval ) {
                    
                    clearInterval( updatePrgrsInterval );
                    
                }
                
                isPlaying = true;
                $( '.playPauseBtn' ).html( '<i class="fa fa-pause"></i>' );
                
                if ( !reviewMode ) {
                    
                    if ( !video.started  ) {
                    
                        // add clicked event listener to all action buttons
                        for ( var j = 0; j < $( '.btn[data-action-id]' ).length; j++ ) {
    
                            $( '.btn[data-action-id]:eq('+j+')' ).removeClass( 'disabled' );
                            $( '.btn[data-action-id]:eq('+j+')' ).clickAction();
    
                        }
                        
                        $( '.btn.rewind' ).removeClass( 'disabled' );
                        $( '.btn.rewind' ).clickAction();
                        
                        video.started = true;
    
                    }
                    
                    // start listening to tag events
                    $.fn.tagHoverAction();
                    
                }

                // Begin updating progress bar
                updatePrgrsInterval = setInterval( function() {
                        $.fn.updateProgress( video );
                    } , 1000 );

                $( '#videoPlayBtn' ).hide().removeClass( 'paused' ).html( 'START' );

            break;
            
            case YT.PlayerState.BUFFERING:
                
                $( '#videoPlayBtn' ).html( '<i class="fa fa-spinner fa-spin"></i><br /><small>BUFFERING</small>' ).addClass( 'paused' ).show();
                
                if ( !reviewMode ) {
                    
                    for ( var d = 0; d < $( '.btn[data-action-id]' ).length; d++ ) {
    
                        $( '.btn[data-action-id]:eq('+d+')' ).addClass( 'disabled' );
    
                    }
                    
                }
                
                isPlaying = false;
                                
            break;
            
            case YT.PlayerState.PAUSED:
                
                // clear update progress bar interval
                clearInterval( updatePrgrsInterval );
                isPlaying = false;
                $( '.playPauseBtn' ).html( '<i class="fa fa-play"></i>' );
                
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
                preCount = null;
                
                $( '#videoPlayBtn' ).html( '<span class="icon-paused"></span><br /><small>PAUSED</small>' )
                                    .addClass( 'paused' ).show();
                $( '.sherlock_status_msg' ).html( 'Video paused ... will resume shortly.' )
                                         .removeClass( 'hide' );

                setTimeout( function() {
                    
                    $( '.sherlock_status_msg' ).html( '' ).addClass( 'hide' );
                    
                    video.player.playVideo();
                    
                    if ( clickedBtns !== null ) {
                        
                        clickedBtns.extendedCooldown();
                        
                    }

                }, 3000);

                video.rewinded = true;

            } else {
                
                if ( trainingMode ) {
                    
                    video.player.playVideo();
                    pauseOnce = false;
                    
                }
                
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
    var barPx = $( '.progress_bar .progressed' ).width() - 12;
    var icon = $( this ).find( '.icon' ).html();

    // Build the span
    var span = '<span class="tag" data-action-id="' + actionId +
               '" data-time="' + formattedTime +
               '" data-count="' + tagCount +
               '" style="left:' + barPx + 'px;' +
               'z-index:' + tagCount++ +
               '">' + icon +
               '</span>';

    var studentTag = {

        "id": actionId,
        "name": actionName,
        "timestamped": formattedTime,
        'pos': barPx

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
    
    if ( trainingMode ) {
        
        $( '.progress_bar_holder' ).on( 'mouseover', '.hint_tag', function() {

            $( this ).css( 'z-index', 99 );
    
        } );
    
        $( '.progress_bar_holder' ).on( 'mouseout', '.hint_tag', function() {
    
            $( this ).css( 'z-index', 0 );
    
        } );
        
    }

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

    var button = $( this );
    var buttonWidth = button.width();
    var cooldownTimeInSecond = Number( button.data( 'cooldown' ) ) * 1000;
    var cooldownBarElement = button.children()[3];
    var cooldownBar = $( cooldownBarElement );
    
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
 $.fn.showTransition = function( heading, subheading, options ) {
    
    options = typeof options !== 'undefined' ? options : false;
    
    $.fn.hideTransition();
    
    var spinner = '';
    var closeBtn = '';
    
    if ( options.spinner ) {
        spinner = '<div class="loading"><i class="fa fa-spinner fa-spin"></i></div>';
    }
    
    if ( options.closeBtn ) {
        closeBtn = '<div class="closeBtn"><i class="fa fa-close fa-2x"></i></div>';
    }
    
    $( this ).before( '<div class="transition_overlay">' + spinner + closeBtn + '<div class="heading">' + heading + '</div><div class="subheading">' + subheading + '</div></div>' );
    
    $( '.transition_overlay .closeBtn' ).on( 'click', function() {
         $.fn.hideTransition();
    } );
    
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

        $( '.transition_overlay .closeBtn' ).off( 'click' );
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
            
            $.get('includes/views/score.php', function( res ) {
                
                $.fn.hideTransition();
                $( el.sherlock_banner_label ).html( 'score' ).removeClass().addClass( 'exercise-type-mode score' );
                $( el.sherlock_body_container ).hide().html( res ).fadeIn( 1000 );
                
                for ( var i = 0; i < $('.progress-bar').length; i++ ) {
                    
                    $.fn.drawProgressBar( i + 1 );
                    
                }
                
            } );

        } else {
            
            $( el.sherlock_wrapper ).showTransition( 'Something went wrong...', 'Sherlock lost his writting pen.', {spinner: false, closeBtn: false} );
            
        }

    } );

 };
 
 $.fn.drawProgressBar = function( index ) {
     
	var pCaption = $( '#progressPercent' + index );
	var iProgress = document.getElementById('inactiveProgress' + index);
	var aProgress = document.getElementById('activeProgress' + index);
	var iProgressCTX = iProgress.getContext('2d');
	
	var percentage = Number( pCaption.data('percent') );
	
	$.fn.drawInactiveProgress(iProgressCTX);
	$.fn.drawProgress(aProgress, percentage);
	
 };
 
 $.fn.drawInactiveProgress = function( iProgressCTX ) {
     
    iProgressCTX.lineCap = 'square';
    
    //progress bar
    iProgressCTX.beginPath();
    iProgressCTX.lineWidth = 0;
    iProgressCTX.fillStyle = '#e6e6e6';
    iProgressCTX.arc(75,75,75,0,2*Math.PI);
    iProgressCTX.fill();
    
    //progressbar caption
    iProgressCTX.beginPath();
    iProgressCTX.lineWidth = 0;
    iProgressCTX.fillStyle = '#fff';
    iProgressCTX.arc(75,75,65,0,2*Math.PI);
    iProgressCTX.fill();
     
 };
 
$.fn.drawProgress = function(bar, percentage) {
    
	var barCTX = bar.getContext("2d");

	bar.width = bar.width;
	barCTX.lineCap = 'square';
    
    barCTX.beginPath();
	barCTX.lineWidth = 5;
	barCTX.strokeStyle = '#ccc';
	barCTX.arc(75,75,70,0,2*Math.PI);
	barCTX.stroke();
    
    if ( percentage > 0 ) {
        
        var quarterTurn = Math.PI / 2;
    	var endingAngle = ((2*percentage) * Math.PI) - quarterTurn;
    	var startingAngle = 0 - quarterTurn;
        
        barCTX.beginPath();
    	barCTX.lineWidth = 5;
    	barCTX.strokeStyle = '#3ac14a';
    	barCTX.arc(75, 75, 70, startingAngle, endingAngle);
    	barCTX.stroke();
        
    }
	
}

/**
 * display the hint tag info
 *
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
 * @return void
 *
 */
$.fn.showHintTagInfo = function() {
    
    $( this ).on( 'click', function() {
    
        $( '.hint_tag' ).removeClass( 'active' );
        
        var name = $(this).data('name');
        var begin = $(this).data('begin');
        var end = $(this).data('end');
        var reason = $(this).data('reason');
        var info = '<p><strong>'+name+'</strong><br />'+ moment( begin * 1000 ).format( 'mm:ss' ) +' &mdash; '+moment( end * 1000 ).format( 'mm:ss' )+'</p><p>'+reason+'</p>';
        
        $( '.sherlock_actions .review_content' ).html(info);
        
        segment.begin = begin;
        segment.end = end;
        
        if ( $( '.btn.play_segment' ).hasClass('disabled') ) {
            
            $( '.btn.play_segment' ).removeClass( 'disabled' ).on( 'click', function(){
        
                video.player.seekTo( segment.begin );
                video.player.playVideo();
                $( '.playPauseBtn' ).html( '<i class="fa fa-pause"></i>' );
                segment.playing = true;
                
            } );
            
        }
        
        $( this ).addClass('active');
        
    } );
    
};

/**
 * Pagination
 *
 * @author Ethan Lin
 * @since 1.0.0
 *
 * @param none
 * @return void
 *
 */
 $.fn.goToPage = function( d ) {
     
     $.post( 'includes/pagination.php', {direction: d},function( res ) {
        
        $.fn.displayExercises( res );
    
    } );
     
 };
 
 $.fn.displayExercises = function( data ) {
     
        var obj = JSON.parse( data );
        var prevBtn = $( el.prevPageBtn );
        var nextBtn = $( el.nextPageBtn );
        var isFirstPage = obj[0];
        var isLastPage = obj[1];
        
        if ( isFirstPage ) {
            prevBtn.prop('disabled', true);
        } else {
            prevBtn.prop('disabled', false);
        }
        
        if ( isLastPage ) {
            nextBtn.prop('disabled', true);
        } else {
            nextBtn.prop('disabled', false);
        }
        
        $( el.currentPage ).html( obj[2] );
        $( el.sherlock_grid_container ).html( obj[3] );
     
 }
 
/**
 * Display review page
 *
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
 * @return void
 *
 */
 
$.fn.goToReview = function() {
    
    $( '.sherlock_score_view' ).hide();
    $( '.score_controls' ).hide();
    $( el.sherlock_banner_label ).html( 'review' ).removeClass().addClass( 'exercise-type-mode review' );
    
    if ( $( '.sherlock_view.review' ).length ) {
    
        $( '.sherlock_view.review' ).show();
        $( '.sherlock_controls' ).show();
        
    } else {
        
        $.post( 'includes/views/review.php', function( res ) {
        
            $( el.sherlock_body_container ).append( res ).hide().fadeIn( 'fast' );
            onYouTubeIframeAPIReady();
    
        } );
        
    }
    
};

/**
 * Display score page
 *
 * @author Ethan Lin
 * @since 0.0.1
 *
 * @param none
 * @return void
 *
 */
 
$.fn.goToScore = function() {
    
    if ( !$( '.sherlock_score_view' ).is( ':visible' ) ) {
        
        $( el.sherlock_banner_label ).html( 'score' ).removeClass().addClass( 'exercise-type-mode score' );
        $( '.sherlock_score_view' ).show();
        $( '.score_controls' ).show();
        $( '.sherlock_view.review' ).hide();
        $( '.sherlock_controls' ).hide();
        
    }
    
};

/**
* Set the progress bar width and the elapsed time
* according to the current video progress.
* This is called continuously once the video starts.
*
* @author Mike Kellum
* @since 0.0.1
* @update 1.0.0
* @param object
* @return void
*
*/
$.fn.updateProgress = function( video ) {
  
    var curTimeMs = video.player.getCurrentTime();

    if ( video.segmented ) {
        curTimeMs = video.player.getCurrentTime() - video.start;
    }

    var newWidth = 100 / video.duration * curTimeMs;
    var width = $( '.progress_bar').width() * ( newWidth / 100 );
    var formattedTime = moment( curTimeMs * 1000 ).format( 'mm:ss' );

    $( '.progress_bar .progressed' ).css( "width", width + "px" );
    $( '.progress_bar .scrubber' ).css( 'left', ( width - 1 ) + "px" );
    $( '.elapsed' ).html( formattedTime );
    
    if ( trainingMode ) {
        
        var objTouched = $( '.progress_bar .scrubber' ).hitTestObject( '.hint' );
        
        if ( objTouched ) {
            
            for ( var o = 0; o < objTouched.length; o++ ) {
                
                var begin = Number( objTouched[o].attributes[2].nodeValue );
                var end = Number( objTouched[o].attributes[3].nodeValue );
                var mid = Number( objTouched[o].attributes[4].nodeValue );
                
                var tag = $( '.progress_bar_holder .hint_tag:eq('+o+')' );
                
                if ( curTimeMs > begin && curTimeMs < end ) {
                    
                    tag.animate({'opacity':1});
                    $( '.progress_bar_holder .hint_tag:eq('+preCount+')' ).removeClass('blink-faster');
                    $( '.reasoningBox' ).hideReasoning();
                    
                    if ( preCount !== o ) {
                        
                        $( '.progress_bar_holder .hint_tag:eq('+preCount+')' ).removeClass('blink-faster');
                        pauseOnce = true;
                        preCount = o;
                        
                    }
                    
                    if ( curTimeMs > mid && curTimeMs < end ) {
                        
                        if ( pauseOnce ) {
                            
                            video.player.pauseVideo();
                            $( '.reasoningBox' ).showReasoning( tag.data( 'reason' ), 'Click "<strong>' + tag.data( 'name' ) + '</strong>" button to continue.' );
                            tag.addClass('blink-faster');
                            pauseOnce = false;
                            
                        }
                        
                    }
                    
                    break;
                    
                }
                
            }
            
            
        }
        
    }
    
    if ( reviewMode ) {
        
        if ( segment.playing ) {
            
            if ( curTimeMs >= segment.end ) {
                
                video.player.pauseVideo( segment.end );
                segment.playing = false;
                $( '.playPauseBtn' ).html( '<i class="fa fa-play"></i>' );
            
            }
            
        }
        
    }
  
};

$.fn.updateScrubSeek = function(pos, video) {
    
    var seekedTime = video.duration * ( 100 * pos / $( '.progress_bar' ).width() ) / 100;
    
    $( '.progress_bar .progressed' ).css( "width", pos + "px" );
    $( '.progress_bar .scrubber' ).css( 'left', ( pos - 1 ) + "px" );
    
    video.player.seekTo( seekedTime );
    
};

$.fn.showReasoning = function( reason, action ) {
    
    action = typeof action !== 'undefined' ? action : '';
    
    $( this ).find( '.reasoning' ).html( reason );
    $( this ).find( '.action' ).html( action );
    $( this ).fadeIn().removeClass( 'hide' );
    
};

$.fn.hideReasoning = function() {
    
    $( this ).find( '.reasoning' ).html( '' );
    $( this ).find( '.action' ).html( '' );
    $( this ).fadeOut();
    
};

/****** UTILITY FUNCTIONS *******/

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
        type = 'Demo';
        break;
    case 2:
        type = 'Dev Testing';
        break;
    case 3:
        type = 'Training';
        break;
    case 4:
        type = 'Practice';
        break;
    case 5:
        type = 'Assessment';
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

$.fn.initialism = function( str ) {
    
    var spacePos = str.indexOf( ' ' );
    var firstChar = str.slice( 0, 1 );
    
    if ( spacePos > 0 ) {
        
        var secondChar = str.slice( spacePos + 1, spacePos + 2 );
        
        return firstChar + secondChar;
        
    }
    
    return firstChar;

};

$.fn.hitTestObject = function( selector ) {
    
    var compares = $(selector);
    var m = compares.length;
    
    var bounds = this.get(0).getBoundingClientRect();
   
    for( var i = 0; i < m; i++ ) {
        
       var compare = compares.get(i).getBoundingClientRect();
       
       if( !( bounds.right < compare.left || bounds.left > compare.right ||
            bounds.bottom < compare.top || bounds.top > compare.bottom ) ) {
                
				return $( selector );   
				
        }
        
    }
    
	return false;
	
};

$.fn.flashExist = function() {
    
    var a;
    
    try {
        
        a = new ActiveXObject('Shockwave'+'Flash'+'.'+'Shockwave'+'Flash');
        
    } catch(e) {
        
        a = navigator.plugins['Shockwave'+' '+'Flash'];
        
    }
    
    return!!a;
    
};

$.fn.isIE = function() {
    
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf('MSIE ');
    if (msie > 0) {
    // IE 10 or older => return version number
    return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }
    
    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
    // IE 11 => return version number
    var rv = ua.indexOf('rv:');
    return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }
    
    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
    // Edge (IE 12+) => return version number
    return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }
    
    // other browser
    return false;
    
};