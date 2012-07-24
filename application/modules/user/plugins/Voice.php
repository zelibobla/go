<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Plugin_Voice extends Core_Plugin_Voice {

	public static function welcome(){
		$user = Zend_Registry::get( 'user' );
		self::pushNotification(
			"Спасибо за регистрацию, $user! Добро пожаловать!",
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}
	
	public static function userEdited( User_Model_User $user, $new = false ){
		if( false == $new ){
			self::pushNotification(
				"User successfully edited",
				array( "class" => Core_Model_Notification::SUCCESS_CLASS )
			);
		} else {
			self::pushNotification(
				"User successfully added",
				array( "class" => Core_Model_Notification::SUCCESS_CLASS )
			);		
		}
	}

	public static function userForeignEdited( User_Model_User $user, $new = false ){
		if( false == $new ){
			self::pushNotification(
				"User successfully edited",
				array( "class" => Core_Model_Notification::SUCCESS_CLASS )
			);
		} else {
			self::pushNotification(
				"User successfully added",
				array( "class" => Core_Model_Notification::SUCCESS_CLASS )
			);		
		}
	}

	public static function userForeignDeleted( User_Model_User $user ){
		self::pushNotification(
			"User successfully deleted",
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}

	public static function userNotFound(){
		self::pushNotification(
			"Пользователя с таким логином или адресом электронной почты не нашлось",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);
	}

	public static function nothingToRecover(){
		self::pushNotification(
			"Вы уже вошли в качестве подтверждённого пользователя. Вам не требуется восстанавливать пароль",
			array( "class" => Core_Model_Notification::WARNING_CLASS )
		);
	}

	public static function newPasswordSent( $email ){
		self::pushNotification(
			"Новый пароль отправлен на $email",
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}

	public static function imageResizeFailed(){
		self::pushNotification(
			"Не удалось сохранить изображение",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);
	}

}
?>
