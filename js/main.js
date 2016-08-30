jQuery( document ).ready( function() {
	var form = jQuery( '#booking-form' ),
	loading = jQuery( '#form-loading' )
	content = jQuery('#form-content' ),
	message = jQuery(  '#form-message' );

	jQuery( form ).validate({ // initialize the plugin
        rules: {
            date_from: {
                required: true,
                date: true,
            },
            date_to: {
                required: true,
                date: true,
            },
            room_type: {
                required: true,
            },           
            room_requirements: {
                required: true,
            },
            adults: {
                required: true,
            },
            name: {
                required: true,
            },
            email: {
                required: true,
                email: true,
            },
            phone: {
                required: true,
                number: true,
            }
        },
        submitHandler: function( form ) {
    		jQuery( loading ).css({
			paddingTop: Math.round( jQuery( form ).height() / 2 ) + 'px'
		}).removeClass( 'hide' );
    	var data ='action=save_reservation_details&' + jQuery( form ).serialize();
    	
		jQuery.ajax({			
			type: 'POST',
			url: ajax_object.ajax_url,
			data: data,						
			dataType: 'json',
			success: function( data ) {
				jQuery( loading ).fadeOut( 'fast', function() {
					jQuery( this ).addClass( 'hide' ).fadeIn();
				});
				if ( data.status == 0 ) {
					jQuery( '.error-message', form ).remove();
					data.fields = data.fields.reverse();
					for ( var i in data.fields ) {
						jQuery(' [name=' + data.fields[ i ].name + ']', form ).trigger( 'focus' ).trigger( 'click' ).parent( 'div' ).each( function() {
							jQuery( this ).append( jQuery('<div>' ).addClass( 'error-message' ).html( data.fields[ i ].message ) );
						});
					}
				} else if ( data.status == 1 ){
					jQuery( content ).fadeOut( 'fast', function() {
						jQuery( this ).addClass( 'hide' );
						jQuery( message ).removeClass( 'hide' );
					});
				}
			},
		});
		return false;
  		}
    });

	jQuery( '#date-from, #date-to', form ).dateTimePicker({
		paging: [ '<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>' ],
		picker: [ 'date' ],
		format: 'd/m/Y',
		filter: function( date ) {
			// Select date in the future
			var d = new Date();
			if ( date.getTime() < d.getTime() ) {
				return false;
			} else {
				return true;
			}
		},
		filter_show: function( date ) {
			var d = new Date();
			return date.getYear() > d.getYear() || (date.getYear() == d.getYear() && date.getMonth() >= d.getMonth());
		}
	}).dateTimePickerRange();
	
	jQuery( 'select', form ).styleSelect({
		class_wrap: 'ul-dropdown-wrap',
	});

	var groups = jQuery( '.group', form ).filter( function() {
		return ! jQuery( this ).hasClass( 'submit' );
	}).click( function() {
		jQuery( groups ).removeClass( 'active' );
		jQuery( this ).addClass( 'active' );
	});
	jQuery( '#name' ).trigger( 'click' ).trigger( 'focus' );
});