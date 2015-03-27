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

// sound effects (global object variable)
var soundEffects = {

    'click': 'click',
    'powerUp': 'power_up',
    'odd': 'no_mercy'

};

// when the document is ready
$( document ).ready( function() {

    // load the sound effects object
    $.fn.loadSoundEffects();

    // FOR DEV/DEMO PURPOSES
    for ( var i = 0; i < $( '.btn[data-action]' ).length; i++ ) {

        $( '.btn[data-action]:eq('+i+')' ).cooldown();
        $( '.btn[data-action]:eq('+i+')' ).clicked();

    }

    $( '.progress_bar' ).progress();
    // END FOR DEV/DEMO PURPOSES

} );

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

    var button = $( this );
    var buttonWidth = button.width();
    var cooldownTimeInSecond = Number( button.attr( 'data-cooldown' ) ) * 1000;
    var cooldownBarElement = button.find( '.cooldown .progress' );
    var cooldownBar = $( cooldownBarElement.selector );

    // FOR DEV PURPOSES
    if ( cooldownBar.width() >= buttonWidth ) {

        cooldownBar.width( 0 );
        button.addClass( 'disabled' );

    }
    // END FOR DEV PURPOSES

    cooldownBar.animate( {

        'width': buttonWidth

    }, cooldownTimeInSecond, function() {

        button.removeClass( 'disabled' );

        // FOR DEV PURPOSES
        setTimeout( function() {

            button.cooldown();

        }, (cooldownTimeInSecond + 10000) );
        // END FOR DEV PURPOSES

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

    // FOR DEV PURPOSES
    if ( progressingBar.width() >= progressBarWidth ) {

        progressingBar.width( 0 );

    }
    // END FOR DEV PURPOSES

    progressingBar.animate( {

        'width': progressBarWidth

    }, 30000, function() {

        // FOR DEV PURPOSES

        setTimeout( function() {

            progressBar.progress();

        }, 10000 );
        // END FOR DEV PURPOSES

    } );

};












