<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Plugin_Mail extends Core_Plugin_Mail {

	public static function newPassword( User_Model_User $user, $password ){
		self::mail( $user, "Восстановление пароля", "Ваш новый пароль: $password" );
	}
}
?>
