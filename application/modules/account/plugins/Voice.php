<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_Plugin_Voice extends Core_Plugin_Voice {

	public static function notRegisteredYet(){
		self::pushNotification(
			"Пожалуйста, сначала зарегистрируйтесь. Вы перенаправлены на форму регистрации.",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);		
	}

	public static function nothingToRecover(){
		self::pushNotification(
			"Ваш пароль подходит. Вам не требуется его восстанавливать.",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);		
	}

	public static function alreadyRegistered(){
		self::pushNotification(
			"Вы уже зарегистрированы. Вы перенаправлены в свой аккаунт.",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);		
	}

	public static function invalidLogin(){
		self::pushNotification(
			"Нет такого пользователя или пароль неверный",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);		
	}

}
?>
