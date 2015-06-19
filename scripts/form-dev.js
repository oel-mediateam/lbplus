$( function() {
    
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
            
            $( '.exercise_info' ).remove();
            
            $.post( 'includes/exercise_info.php', { id: selectValue }, function(response) {
                
                if ( response ) {
                    
                    var result = JSON.parse(response);
                    
                    $( '.select' ).after( '<div class="exercise_info"><div class="description_box"><p><strong>Description:</strong></p><div class="description"></div></div><p class="meta"></p></div>' );
                    $( '.exercise_info .description_box .description' ).html( result.description );
                    $( '.exercise_info .meta' ).html( 'Number of attempts: <strong>' + result.attempts + '</strong>' );
                    $( '.exercise_info .meta' ).append( ( result.time_limit > 0 ) ? ' | Time limit: <strong>' + result.time_limit + '</strong>' : '' );
                    
                }
                
            } );
            
        } );
      
        $( document, styledSelect ).click( function() {
                
                styledSelect.removeClass( 'active' );
                list.hide();
                opened = false;
            
        } );
    
    } );
    
} );