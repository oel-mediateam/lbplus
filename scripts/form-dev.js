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
            styledSelect.text( $( this ).text() ).removeClass( 'active' );
            $this.val( $( this ).attr( 'rel' ) );
            list.hide();
            opened = false;
            
        } );
      
        $( document, styledSelect ).click( function() {
                
                styledSelect.removeClass( 'active' );
                list.hide();
                opened = false;
            
        } );
    
    } );
    
} );