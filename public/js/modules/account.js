/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

$( document ).ready( function() {

	if( $( '#item_form' ).length ){
		/**
		* photo uploader
		*/
		$( '#photo' ).after( '<div class="pic">\
								<div class="controls"><div id="file_uploader"></div></div>\
						 	 </div>' );
		if( '' != $( '#photo' ).val() ){
			$( '#file_uploader' ).before(
				'<div id="photo_file">\
					<div class="file">\
						<img src="/uploads/account/' + $( '#id' ).val() + '/' + $( '#photo' ).val() + '" id="user_photo">\
					</div>\
				 </div>' );
		}

		var uploader = new qq.FileUploader({
			element: document.getElementById( 'file_uploader' ),
			action: '/account/profile/file',
			debug: true,
			onComplete: function( id, fileName, responseJSON ){
				$( '#photo' ).val( fileName );
				$( '#user_photo' ).imgAreaSelect({ 'remove': true });
				$( '#user_photo' ).remove();
				$( '#photo' ).after( '<img src="/uploads/account/' + fileName + '" id="user_photo">' );
				$( '#user_photo' ).load( function(){
					var x2 = $( '#user_photo' ).width();
					var y2 = $( '#user_photo' ).height();
					var length = ( x2 > y2 ? y2 : x2 );

					$( '#user_photo' ).imgAreaSelect({
						handles: 'corners',
						show: true,
						x1: 0,
						y1: 0,
						x2: length,
						y2: length,
						zIndex: 1005,
						aspectRatio: "1:1",
						onInit: function( img, selection ){
							$( '#photo_selection' ).val( $.toJSON( selection ) );
						},
						onSelectEnd: function( img, selection ){
							$( '#photo_selection' ).val( $.toJSON( selection ) );
						}
					});
				});
			}
		});
		
	}

});
