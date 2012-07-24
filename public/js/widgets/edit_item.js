/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 * The Jquery plugin to edit core items or it children.
 * Plugin should be attached to any DOM element with specified id value.
 * It listening for event 'click' of DOM elemets with specified class.
 * Once 'click' event triggered the dialog opens with form to edit item
 * accepted form throws to a server for continue processing on server side.
 * Several instances of plugin on one page are available.
 *
 * dependencies:
 *	 jquery-1.5.2
 *	 jquery-ui-1.8.12
 *
 */

(function( $ ){
	
		var settings = {};
		var id = null;
		var defaults = {
			'add_edit_class'	: '.item',				// DOM elements to bind edit or add item event
			'delete_class'		: '.delete',			// DOM elements to bind remove item event
			'dialog_id'			: '#edit_item_window',	// id of DOM element will be created to fit a dialog
			'form_id'			: '#item_form',			// id of form in dialog to submit if validation success
			'edit_caption'		: 'Edit',				// name of dialog if item_id is found — mean item is editing
			'add_caption'		: 'Add',				// name of dialog if no item_id found — mean item is adding
			'waiting_message'	: '<img src="/img/ajax_loader_bar.gif">',		// what to show in dialog while it's content being loading
			'get_form_url'		: '',					// what url should we use to retrieve dialog content
			'delete_url'		: '',					// what url should we use to perform item delete
			'window_width'		: 600,					// what dialog window width would we perefer
			'window_height'		: 'auto',				// what dialog window height would we perefer
			'window_position'	: [ 300, 50 ],			// so on with position plugin to attach it to retrieved form in dialog content
			'on_dialog_load'	: null,
			'on_form_success'	: null,
			'debug'				: true
		};

		var methods = {
		
			init : function( options ) {
				
				if( options ) {
					var temp = {};
					$.extend( temp, defaults );
					$.extend( temp, options );
				} else {
					temp = defaults;
				}
				id = this.attr( 'id' );
				settings[ id ] = temp;
				$( settings[ id ][ 'add_edit_class' ] ).bind( 'click', methods.load );
				$( settings[ id ][ 'delete_class' ] ).bind( 'click', methods.del );
	
			},
			
			load : function( event ){
				var id = methods.getInstanceIdByElement( event.target );
				var item_params = new Object();

				//get item parent attribs to sent it in request as params
				$.each( $( event.target ).parent().parent()[ 0 ].attributes, function( index, attr ) {
					item_params[ attr.name ] = ( attr.value );
				});
				
				//override get item parent attribs by item attribs
				$.each( $( event.target )[ 0 ].attributes, function( index, attr ) {
					item_params[ attr.name ] = ( attr.value );
				});
				if( true == $( event.target ).hasClass( 'copy' ) ){
					item_params[ 'copy' ] = true;
				}

				var caption = item_params.id
							? settings[ id ][ 'edit_caption' ] + " «" + item_params.item_name + "»"
							: settings[ id ][ 'add_caption' ];
				var dialog_id = settings[ id ][ 'dialog_id' ].substring( 1 );
				$( document.body ).append( '<div id="' + dialog_id + '"></div>' );

				$( settings[ id ][ 'dialog_id' ] ).dialog({
					title: caption,
					width: settings[ id ][ 'window_width' ],
					height: settings[ id ][ 'window_height' ],
					position : settings[ id ][ 'window_position' ],
					buttons : {
						'Отмена' : function(){
							$( settings[ id ][ 'dialog_id' ] ).dialog( "close" );
						},
						'Готово' : function( event ){
							// prevent double submit
							if( false == $( settings[ id ][ 'dialog_id' ] ).dialog( 'option', 'disabled' ) ){
								
								var form = $( settings[ id ][ 'form_id' ] );
								// if form is valid via JS, let's check server side validity
								if( form.valid() ){
									$( settings[ id ][ 'dialog_id' ] ).dialog( 'disable' );
									if( settings[ id ][  'debug' ] ){
										console.log( settings[ id ][ 'get_form_url' ] + '?' + form.serialize() );
									}
									$.ajax({
										url: settings[ id ][ 'get_form_url' ],
										type: 'post',
										data: form.serialize(),
										success: function( response ){
											if( settings[ id ][  'debug' ] ){
												console.log( response );
											}
											if( true == response.result ){
												if( 'undefined' != typeof response.redirect ){
													window.location = response.redirect;
												} else {
													$( settings[ id ][ 'dialog_id' ] ).dialog( 'enable' );
													$( settings[ id ][ 'dialog_id' ] ).dialog( 'close' );
													if( null != ( custom_function = settings[ id ][ 'on_form_success' ] ) ){
														custom_function();
													} else {
                            							window.location.reload();
													}
												}
											// if server side validation is fault bring back server rendered form with errors shown
											} else {
												$( settings[ id ][ 'dialog_id' ] ).dialog( 'enable' );
												$( settings[ id ][ 'dialog_id' ] ).html( response.html );
												if( null != ( custom_function = settings[ id ][ 'on_dialog_load' ] ) ){
													custom_function( item_params );
												}

											}
										}
									});
								}
							}
		  				}
					},
					modal : true
				})
				.html( settings[ id ][ 'waiting_message' ] )
				.dialog( 'open' );

				if( settings[ id ][  'debug' ] ){
					console.log( settings[ id ][ 'get_form_url' ], item_params );
				}
				$.ajax({
					url: settings[ id ][ 'get_form_url' ],
					data: item_params,
					success: function( response ){
						if( settings[ id ][  'debug' ] ){
							console.log( response );
						}
						if( '' == response.html ||
							 'undefined' == typeof response.html ){
							$( settings[ id ][ 'dialog_id' ] ).dialog( 'close' );
						}
						$( settings[ id ][ 'dialog_id' ] ).html( response.html );
						if( null != ( custom_function = settings[ id ][ 'on_dialog_load' ] ) ){
							custom_function( item_params );
						}
						if( true == item_params[ 'copy' ] ){
							$( '#copied_from_id' ).val( $( '#id' ).val() );
							$( '#id' ).val( '' );
						}
					}
				});
			},

			del : function( event ){
				var id = methods.getInstanceIdByElement( event.target );

				event.item_id = $( event.target ).parent().parent().attr( 'id' );
				if( undefined == event.item_id ){
					throw 'No item_id specified';
					return;
				}
				cell = $( event.target ).parent();
				row = $( event.target ).parent().parent();
				settings[ id ][ 'temp' ] = cell.html();
				cell.html( settings[ id ][ 'waiting_message' ] );

				$.ajax({
					url: settings[ id ][ 'delete_url' ],
					method: "post",
					data: {
						'id': event.item_id
					},
					success: function( response ){
						if( settings[ id ][  'debug' ] ){
							console.log( response );
						}
						if( true == response.result ){
							row.remove();
						} else {
							cell.html( 'Some error happened. Delete not performed.' );
						}
					}
				});
			},
			
			getInstanceIdByElement : function( element ){
				classes = $( element ).attr( 'class' ).split( ' ' );
				class_name = "." + classes[ 0 ];

				$.each( settings, function( key, values ){

					$.each( values, function( index, value ){

						if( ( index == 'add_edit_class' ||
								index == 'delete_class' ) &&
							 class_name == value ){

							result = key;
//							console.log( '['+key+']['+index+']:\''+value+'\' == \''+class_name+'\' — GOT IT!' );
							return false;
						} else {
//							console.log( '['+key+']['+index+']:\''+value+'\' != \''+class_name+'\'' );
						}
					});
				});
				if( 'undefined' == typeof result ) throw 'Can\'t detect plugin-instance-owner of this clicked element; check the binding';
				//console.log( result );
				return result;
			}

		};

		$.fn.editItem = function( method ) {

			// Method calling logic
			if ( methods[ method ] ) {
				return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
			} else if ( typeof method === 'object' || !method ) {
				return methods.init.apply( this, arguments );
			} else {
				$.error( 'Method ' +  method + ' does not exist on jQuery.editItem' );
			}    

	 	};

})( jQuery );
