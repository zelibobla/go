/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

$( document ).ready( function() {
	

	/**
	* run periodical check for any system messages
	*/
	$( document.body ).systemVoice();

	/**
	* bind smart edit item plugin to perform ajax items edit and delete
	*/
	if( $( '#items_table' ).length )
		var items_table = new Edit_Item({ translator: translator,
			 							  handler: $( '#items_table' ) });
});
