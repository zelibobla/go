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
		'edit_caption'		: 'Редактирование сообщения',
		'add_caption'		: 'Ваш ответ',
		'get_form_url'		: '/forum/post/edit',
		'delete_url'		: '/forum/post/delete'
	});

});
