/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 *	JQuery plugin to display system notifications to current user
 *
 *	Dependance:
 *	jquery-1.5.2
 *	jquery-ui-1.8.12
 * jquery.json
 *
 ******************************************************/

(function( $ ){

		/**
		* defaults of plugin could be overwritten when calling plugin
		*/
		var system_voice_defaults = {
			'interval'				: 5000,
			'check_messages_url' : '/core/voice/utter',
			'mark_message_read'	: '/core/voice/mark_read',
			'shown'					: []
		};

		/**
		* external to plugin function asks from server for any new notifications (will be called periodically later)
		*/
	 	getMessages = function(){
			$.ajax({
				url : system_voice_defaults[ 'check_messages_url' ],
				success : function( response ){

					if( 'undefined' != typeof response.messages ){
						$( document.body ).trigger({
							type: 'new_messages',
							messages: response.messages
						});
					}
				}
			})
		}

		var methods = {
			/**
			* initialize method:
			* overwrite deafults by custom parameters;
			* first check new messages lauch and run periodical launch machine;
			* bind new notifications found to diplay handler
			*/
			init : function( options ) {

				if( options ){
					$.extend( system_voice_defaults, options );
				}
				
				getMessages();
				setInterval( 'getMessages()', system_voice_defaults[ 'interval' ] );

				$( document.body ).bind( 'new_messages', methods.showNewMessages );

				return this;
	
			},
			
			/**
			* Handling event with new messages in parameter - displaying notifications
			*/
			showNewMessages : function( event ){

				// first of all display all new notifications
				$.each( event.messages, function( message_id, message_object ){

					shown = $.grep( system_voice_defaults[ 'shown' ], function( shown_message_id, index ){
						return shown_message_id == message_id;
					});
					if( null != shown && !( shown.length ) ){

						$( document.body ).append(
							'<div class="system_voice ' + message_object[ 'class' ] + '" id="' + message_id + '">\
								<div class="icon"></div>' + message_object[ 'body' ] + '\
								<div class="close_button ui-dialog ui-titlebar-close" for_message_id="' + message_id + '"></div>\
							 </div>' );
						$( '#' + message_id ).delay( 5000 ).fadeOut( 300, methods.markMessageRead( message_id ) );
						system_voice_defaults[ 'shown' ].push( parseInt( message_id ) );
					}
				});
								
				// and now give to all them special order to make no overlays
				$.each( $( '.system_voice' ), function( index, div ){

					$( div ).css({ 'top': ( 
													80 +
													( parseFloat( $( div ).css( 'height' ) ) + 70 ) * index
												 ) + 'px' });
				});
				
				// and handle click to little close button on every notification window
				$( '.close_button' ).click( function(){

					message_id = $( this ).attr( 'for_message_id' );
					$( '#' + message_id ).remove();
					methods.markMessageRead( message_id );
				});

			},

			/**
			* Sending to server that specified message was read
			*/
			markMessageRead : function( message_id ){
				$.ajax({
					url : system_voice_defaults[ 'mark_message_read' ] + '?id=' + message_id
				});
			}

		};

		/**
		* register ourself
		*/
		$.fn.systemVoice = function( method ) {

			// Method calling logic
			if ( methods[ method ] ) {
				return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
			} else if ( typeof method === 'object' || !method ) {
				return methods.init.apply( this, arguments );
			} else {
				$.error( 'Method ' +  method + ' does not exist on jQuery.systemVoice' );
			}    

	 	};
	 	
		
})( jQuery );
