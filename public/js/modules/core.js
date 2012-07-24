/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

$( document ).ready( function() {
	

	/**
	* let's run periodical check for any system messages
	*/
	$( document.body ).systemVoice();

	/**
	* make beautiful select elements
	*/
	$( '.chosen' ).data( "placeholder", "" ).chosen();

	if( true == $( '#item_form' ).length ){
		$( '#item_form' ).append( '<input type="hidden" name="from_index" value="Y" />' );
		offer_form_sensitivity();
	}

});
