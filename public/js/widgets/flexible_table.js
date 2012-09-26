/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 * The Jquery plugin to handle users table layout tuning.
 * Plugin should be attached to any DOM element with specified id value.
 * It listening for event 'click' of DOM elemets with specified class (add or remove table column).
 * Several instances of plugin on one page are available.
 *
 * dependencies:
 *	 jquery-1.5.2
 *
 */

(function( $ ){

		var settings = {};
		var id = null;

		var defaults = {
			'add_column_class'			: '.add_column_icon',						// DOM elements to bind add column event
			'remove_column_class'		: '.remove_column_icon',					// DOM elements to bind remove column event
			'filter_column_class'		: '.filter_column_icon',					// DOM elements to bind filter column event
			'change_order_class'		: '.change_order',							// DOM elements to bind filter column event
			'change_quantity_class'		: '.change_quantity',						// DOM elements to bind changing items per page quantity
			'add_column_text'			: 'Добавить сюда колонку:',					// add column suggestions header
			'filter_column_text'		: 'Фильтровать эту колонку по признаку ',	// filter column header
			'update_column_url'			: '/user/interface/column',					// url to perform add/remove column action
			'update_filter_url'			: '/user/interface/filter',					// url to update filter condition
			'update_order_url'			: '/user/interface/order',					// url to update filter condition
			'update_quantity_url'		: '/user/interface/quantity',				// url to update filter condition
			'suggestable_columns'		: {},										// columns array to be suggested when used press +
			'filterable_columns'		: {},										// columns array that could be filterable
			'sequence'					: {}
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
				$( settings[ id ][ 'add_column_class' ] ).bind( 'mouseover', methods.suggest_columns );
				$( settings[ id ][ 'filter_column_class' ] ).bind( 'mouseover', methods.suggest_filter );
				$( settings[ id ][ 'remove_column_class' ] ).bind( 'click', methods.remove_column );
				$( settings[ id ][ 'change_order_class' ] ).bind( 'click', methods.change_order );
				$( settings[ id ][ 'change_quantity_class' ] ).bind( 'change', methods.change_quantity );

			},
			
			suggest_columns : function( event ){
				var id = methods.getInstanceIdByElement( event.target );
				if( false == $( '.columns_suggestions' ).length ){
					$( event.target ).append( methods.getColumnsUL() );
					$( '.columns_suggestions' ).mouseleave( function(){
						$( '.columns_suggestions' ).remove();
					});

					$( '.columns_suggestions li' ).click( function( event ){
						var new_sequence = {};
						var replacing_index = parseInt( $( event.target ).parent().parent().parent().next().attr( 'sequence' ) );

						var replacing_field = event.target.id;
						var shifting = false;
						var last_index = 0;

						$.each( settings[ id ][ 'sequence' ], function( index, field ){
							if( field == replacing_field ) return;
							if( index == replacing_index ||
								 true == shifting ){
								shifting = true;
								new_sequence[ replacing_index ] = replacing_field;
								new_sequence[ parseInt( index ) + 10 ] = field;
							} else {
								new_sequence[ index ] = field;
							}
							last_index = parseInt( index );
						});
						if( false == shifting ){
							new_sequence[ last_index + 10 ] = replacing_field;
						}
//console.log( settings[ id ][ 'sequence' ], 'repl_ind:' + replacing_index, 'repl_field:' + replacing_field, new_sequence );
						$.ajax({
							url: settings[ id ][ 'update_column_url' ],
							data: {
								table: id,
								column : event.target.id,
								act: 'add',
								sequence: new_sequence
							},
							success: function( response ){
								if( 'undefined' != typeof response.redirect ){
									window.location = response.redirect;
								}
							}
						});
					});
				}
			},

			remove_column : function( event ){
				var id = methods.getInstanceIdByElement( event.target );
				$.ajax({
					url: settings[ id ][ 'update_column_url' ],
					data: {
						table: id,
						column : $( event.target ).parent().attr( 'id' ),
						act: 'remove'
					},
					success: function( response ){
						if( 'undefined' != typeof response.redirect ){
							window.location = response.redirect;
						}
					}
				});
			},

			suggest_filter : function( event ){
				if( false == $( '.columns_filters' ).length ){
					var id = methods.getInstanceIdByElement( event.target );
					var column = $( event.target ).parent().attr( 'id' );
					$( event.target ).append( methods.getColumnFilterHTML( column ) );
					if( true == $( '#num_date_min' ).length ){
						$( '#num_date_min' ).unixTimestamp({ 'display_calendar' : false });
						$( '#num_date_max' ).unixTimestamp({ 'display_calendar' : false });
					}
					$( '.columns_filters' ).mouseleave( function(){
						$( '.columns_filters' ).remove();
					});
					$( '#column_filter_form' ).submit( function( event ){
						event.preventDefault();
						$.ajax({
							url: settings[ id ][ 'update_filter_url' ],
							data: {
								act: 'add',
								table: id,
								filter: $( event.target ).serialize(),
								column: column,
							},
							success: function( response ){
								if( 'undefined' != typeof response.redirect ){
									window.location = response.redirect;
								}
							}
						});
					});
					$( '.remove_filter' ).click( function(){
						$.ajax({
							url: settings[ id ][ 'update_filter_url' ],
							data: {
								act: 'remove',
								table: id,
								column: column,
							},
							success: function( response ){
								if( 'undefined' != typeof response.redirect ){
									window.location = response.redirect;
								}
							}
						});
					});
				}
			},

			change_order : function( event ){
				var id = methods.getInstanceIdByElement( event.target );

				$.ajax({
					url: settings[ id ][ 'update_order_url' ],
					data: {
						table: id,
						column : $( event.target ).parent().parent().attr( 'id' ),
					},
					success: function( response ){
						if( 'undefined' != typeof response.redirect ){
							window.location = response.redirect;
						}
					}
				});
			},

			change_quantity : function( event ){
				var id = methods.getInstanceIdByElement( event.target );

				$.ajax({
					url: settings[ id ][ 'update_quantity_url' ],
					data: {
						table: id,
						quantity: $( event.target ).val(),
					},
					success: function( response ){
						if( 'undefined' != typeof response.redirect ){
							window.location = response.redirect;
						}
					}
				});
			},

			getInstanceIdByElement : function( element ){
				if( 'undefined' == typeof( $( element ).attr( 'class' ) ) ) return;
				classes = $( element ).attr( 'class' ).split( ' ' );
				class_name = "." + classes[ 0 ];

				$.each( settings, function( key, values ){

					$.each( values, function( index, value ){

						if( ( index == 'add_column_class' ||
							  index == 'remove_column_class' ||
							  index == 'filter_column_class' ||
							  index == 'change_order_class' ||
							  index == 'change_quantity_class' ) &&
							 class_name == value ){

							result = key;
//							console.log( '['+key+']['+index+']:\''+value+'\' == \''+class_name+'\'' );
							return false;
						} else {
//							console.log( '['+key+']['+index+']:\''+value+'\' != \''+class_name+'\'' );
						}
					});
				});
				if( false == result ) throw 'Can\'t detect plugin-instance-owner of this clicked element; check the binding';
//				console.log( result );
				return result;
			},
			
			getColumnsUL : function(){
				result = '<ul class="columns_suggestions"><span>' + settings[ id ][ 'add_column_text' ] + '</span>';
				$.each( settings[ id ][ 'suggestable_columns' ], function( column_id, column_name ){
					result += '<li id="' + column_id + '">' + column_name + '</li>';
				});
				result += '</ul>';
				return result;
			},
			
			getColumnFilterHTML : function( column_id ){

				filter_type = settings[ id ][ 'filterable_columns' ][ column_id ][ 'type' ];
				filter_value = settings[ id ][ 'filterable_columns' ][ column_id ][ 'value' ];
				result = '<div class="columns_filters">\
							 <form id="column_filter_form">\
							 <input type="hidden" name="type" value="' + filter_type +'">'
							 + settings[ id ][ 'filter_column_text' ];

				if( 'string' == filter_type ){
					value = 'undefined' != typeof filter_value ? filter_value[ 'str' ] : '';
					result += 'похожести на:<br /><input type="text" name="str" value="' + value + '">';
				} else if( 'enum' == filter_type ) {
					value = 'undefined' != typeof filter_value && "Y" == filter_value[ 'enum' ] ? filter_value[ 'enum' ] : null;
					result += 'равенства значению:<br />\
								  <input type="radio" class="enum" name="enum" value="Y" ' + ( "Y" == value ? "selected" : "" ) + '"> да &nbsp; \
								  <input type="radio" class="enum" name="enum" value="Y" ' + ( "N" == value ? "selected" : "" ) + '"> нет';
				} else if( 'num' == filter_type ) {
					value_min = 'undefined' != typeof filter_value ? filter_value[ 'min' ] : '';
					value_max = 'undefined' != typeof filter_value ? filter_value[ 'max' ] : '';
					result += 'попадания в диапазон<br />\
								  от: <input type="text" class="num" name="min" value="' + value_min + '"> &nbsp; \
								  до: <input type="text" class="num" name="max" value="' + value_max + '">';
				} else if( 'date' == filter_type ) {
					value_min = 'undefined' != typeof filter_value ? filter_value[ 'min' ] : '';
					value_max = 'undefined' != typeof filter_value ? filter_value[ 'max' ] : '';
					result += 'попадания в период<br />\
								  с: <input type="hidden" name="min" id="num_date_min" value="' + value_min + '"> &nbsp; \
								  по: <input type="hidden" name="max" id="num_date_max" value="' + value_max + '">';
				} else {
					throw 'no form specified for such a kind of filter:' + filter_type;
				}
				result += '<button type="submit">применить</button><br />\
							  <span class="remove_filter">отменить фильтр</span></form></div>';
				return result;
			}

		};

		$.fn.flexibleTable = function( method ) {

			// Method calling logic
			if ( methods[ method ] ) {
				return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
			} else if ( typeof method === 'object' || !method ) {
				return methods.init.apply( this, arguments );
			} else {
				$.error( 'Method ' +  method + ' does not exist on jQuery.flexibleTable' );
			}    

	 	};

})( jQuery );
