/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 * The class to handle items via ajax
 * This table should also have 'resource', 'edit_url', 'delete_url', 'edit_class', 'delete_class' attributes
 * like <table resource="user" edit_url="/user/index/edit" delete_url="user/index/delete" add_class="item_edit" delete_class="item_delete"
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

var Edit_Item = function( options ){

	if( !( options ) ||
		!( options.handler ) ||
		!( options.handler.length ) ) throw 'Edit_Item class can\'t be instantiated without handler of DOM element to attach to';

	var o = options,
		_translator = o.translator ? o.translator : {},						// translator associative array
		_dialog = {
			width: o.dialog_width ? parseInt( o.dialog_width ) : 600,		// dialog window width
			height: o.dialog_height ? parseInt( o.dialog_height ) : 'auto', // dialog window height
			position: o.dialog_position ? o.dialog_position : [ 300, 100 ],			// dialog window position
		},
		_waiting = o.waiting ? o.waiting : '<img src="/css/ajax_loader_bar.gif">',
		_handler = o.handler,
		_callbacks = {
			onDialogLoad: 'function' == typeof o.onDialogLoad ? o.onDialogLoad : function(){},
			onFormSuccess: 'function' == typeof o.onFormSuccess ? o.onFormSuccess : function(){},
		},
		_id = _handler.attr( 'id' ),
		_resource = _handler.attr( 'resource' ),
		_urls = {
			edit: _handler.attr( 'edit_url' ),
			delete: _handler.attr( 'delete_url'),
		},
		_classes = {
			edit: _handler.attr( 'edit_class' ),
			delete: _handler.attr( 'delete_class' ),
		},
		_extra = 'undefined' != typeof o.extra ? o.extra : {},
		_self = this;

	if( -1 != ( _id + _resource + _urls.edit + _urls.delete + _classes.edit + _classes.delete ).indexOf( 'undefined' ) )
		throw 'Edit_Item class is attached to DOM element with unset attribs: id, resource, edit_url, delete_url, edit_class, delete_class';

	/**
	* open dialog to edit item, ask from server dialog content
	* send to server submitted form and handle response
	*/
	var editItem = function( event ){
		/**
		* construct defaults
		*/
		var row = $( event.target ).closest( 'tr' ),
			item_id = row.attr( 'item_id' ),
			caption = item_id
					? translator[ _resource + '_edit_caption' ]
					: translator[ _resource + '_add_caption' ],
			dialog_id = _id + '_item_dialog',
			waiting = true;

		$( document.body ).append( '<div id="' + dialog_id + '"></div>' );
		var dialog_handler = $( '#' + dialog_id );

		/**
		* retrieve dialog content
		*/
		var dialogLoadContent = function(){
			var data = _extra;
			data.id = item_id;
			$.ajax({
				url: _urls.edit,
				data: data,
				success: function( response ){
					if( '' == response.html ||
						'undefined' == typeof response.html ){
						dialog_handler.dialog( 'close' );
					}
					dialog_handler.html( response.html );
					waiting = false;
					_callbacks.onDialogLoad();
				}
			});
		}
		
		/**
		* handle dialog submit
		*/
		var dialogSubmit = function(){
			if( waiting ) return;
			waiting = true;

			$.ajax({
				url: _urls.edit,
				type: 'post',
				data: dialog_handler.find( 'form' ).serialize(),
				success: function( response ){console.log( response.html );
					waiting = false;
					/**
					* server side validation success
					*/
					if( true == response.result ){
						if( 'undefined' != typeof response.redirect ){
							window.location = response.redirect;
						} else {
							dialog_handler.dialog( 'close' );
							_handler.replaceWith( response.html );
							listen();
							_callbacks.onFormSuccess();
						}
					/**
					* server side validation fault
					*/
					} else {
						dialog_handler.html( response.html );
						_callbacks.onDialogLoad();
					}
				}
			});
		}

		/**
		* build dialog
		*/
		var dialogBuild = function(){
			dialog_handler.dialog({
				title: caption,
				width: _dialog.width,
				height: _dialog.height,
				position: _dialog.position,
				modal : true,
				buttons : [
					{
						text : _translator.cancel,
					  	click : function(){ $( this ).dialog( "close" ); }
					},
					{
						text: _translator.submit,
						click: function(){ dialogSubmit() }
					}
				]
				}).html( _waiting )
				  .dialog( 'open' );
			dialogLoadContent();
		}
		
		dialogBuild();
	}
	
	/**
	* send request to delete item
	* and handle response
	*/
	var deleteItem = function( event ){
		var cell = $( event.target ).hasClass( 'icon' ) ? $( event.target ).parent().parent() : $( event.target ).parent(),
			row = cell.parent(),
			item_id = row.attr( 'item_id' );

		if( !( item_id ) ) throw 'Can\'t detect item_id';

		cell.html( _waiting );

		$.ajax({
			url: _urls.delete,
			data: { id: item_id },
			success: function( response ){
				if( true == response.result ){
					row.remove();
				} else {
					cell.html( 'Some error happened. Deletion canceled.' );
				}
			}
		});		
	}

	/**
	* bind event listeners
	*/
	var listen = function(){
		$( '.' + _classes.edit ).unbind().bind( 'click', editItem );
		$( '.' + _classes.delete ).unbind().bind( 'click', deleteItem );
	}
	
	listen();
}