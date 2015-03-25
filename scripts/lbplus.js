$( document ).ready( function() {

    // FOR DEV PURPOSES
    for ( var i = 0; i < $( '.btn[data-action]' ).length; i++ ) {

        $( '.btn[data-action]:eq('+i+')' ).cooldown();

    }
    // END FOR DEV PURPOSES

} );

$.fn.cooldown = function() {

    var button = $( this );
    var button_width = button.width();
    var cooldown_time_in_second = Number( button.attr( 'data-cooldown' ) ) * 1000;
    var progress_bar_element = button.find( '.cooldown .progress' );
    var progress_bar = $( progress_bar_element.selector );

    // FOR DEV PURPOSES
    if ( button_width >= 225 ) {

        progress_bar.width( 0 );
        button.addClass( 'disabled' );

    }
    // END FOR DEV PURPOSES

    progress_bar.animate( {

        'width': '225px'

    }, cooldown_time_in_second, function() {

        button.removeClass( 'disabled' );

        // FOR DEV PURPOSES
        setTimeout( function() {

            button.cooldown();

        }, 10000 );
        // END FOR DEV PURPOSES

    } );

};