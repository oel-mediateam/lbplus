var sound_effects = {

    'click': 'sounds/click.mp3',
    'power_up': 'sounds/power_up.mp3'

};

$( document ).ready( function() {

    $.fn.loadSoundEffects();

    // FOR DEV/DEMO PURPOSES
    for ( var i = 0; i < $( '.btn[data-action]' ).length; i++ ) {

        $( '.btn[data-action]:eq('+i+')' ).cooldown();
        $( '.btn[data-action]:eq('+i+')' ).clicked();

    }

    $( '.progress_bar' ).progress();
    // END FOR DEV/DEMO PURPOSES

} );

$.fn.loadSoundEffects = function() {

    $.each( sound_effects, function( sound, src ) {

        var temp = src;
        sound_effects[sound] = document.createElement( 'audio' );
        sound_effects[sound].setAttribute( 'src', temp );

    } );

};

// action button clicked
$.fn.clicked = function() {

    $( this ).on( 'click', function() {

        if ( !$( this ).hasClass( 'disabled' ) ) {

            sound_effects.power_up.play();

        }

    } );

};

// action button cooldown
$.fn.cooldown = function() {

    var button = $( this );
    var button_width = button.width();
    var cooldown_time_in_second = Number( button.attr( 'data-cooldown' ) ) * 1000;
    var progress_bar_element = button.find( '.cooldown .progress' );
    var progress_bar = $( progress_bar_element.selector );

    // FOR DEV PURPOSES
    if ( progress_bar.width() >= button_width ) {

        progress_bar.width( 0 );
        button.addClass( 'disabled' );

    }
    // END FOR DEV PURPOSES

    progress_bar.animate( {

        'width': button_width

    }, cooldown_time_in_second, function() {

        button.removeClass( 'disabled' );

        // FOR DEV PURPOSES
        setTimeout( function() {

            button.cooldown();

        }, (cooldown_time_in_second + 10000) );
        // END FOR DEV PURPOSES

    } );

};

// video progress bar
$.fn.progress = function() {

    var progress_bar = $( this );
    var progress_bar_width = progress_bar.width();
    var progressing_bar_element = progress_bar.find( '.progressed' );
    var progressing_bar = $( progressing_bar_element.selector );

    // FOR DEV PURPOSES
    if ( progressing_bar.width() >= progress_bar_width ) {

        progressing_bar.width( 0 );

    }
    // END FOR DEV PURPOSES

    progressing_bar.animate( {

        'width': progress_bar_width

    }, 30000, function() {

        // FOR DEV PURPOSES

        setTimeout( function() {

            progress_bar.progress();

        }, 10000 );
        // END FOR DEV PURPOSES

    } );

};












