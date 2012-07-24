/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

$( document ).ready( function() {

	if( true == $( '.users_layout' ).length ){
		$( '.users_layout' ).editItem({
			'edit_caption'					: 'Редактирование пользователя',
			'add_caption'					: 'Добавление пользователя',
			'get_form_url'					: '/user/index/edit',
			'delete_url'					: '/user/index/delete',
			'window_width'					: 600,
			'window_position'				: [ 100, 50 ],
		});
	}
});
