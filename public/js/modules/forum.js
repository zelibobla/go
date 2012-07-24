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
		'edit_caption'	: 'Редактирование категории',
		'add_caption'	: 'Новая категория',
		'get_form_url'	: '/forum/category/edit',
		'delete_url'	: '/forum/category/delete'
	});

});
