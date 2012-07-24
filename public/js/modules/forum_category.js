/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

$( document ).ready( function() {

	/**
	* bind editImem plugin with specified params
	*/
	$( document.body ).editItem({
		'edit_caption'	: 'Редактирование топика',
		'add_caption'	: 'Новый топик',
		'get_form_url'	: '/forum/topic/edit',
		'delete_url'	: '/forum/topic/delete',
	});

});
