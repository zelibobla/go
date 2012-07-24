<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Plugin_Voice extends Zend_Controller_Plugin_Abstract {


	/**
	*	retrieve from DB all unshown messages
	*/
	public static function getNotifications(){

		if( false == ( $user = Zend_Registry::get( 'user' ) ) ) return null;

		$table = Go_Factory::getDbTable( "Core_Model_Notification" );
		return $table->fetchNotificationsToShowForUser( $user->getId() );

	}
	
	/**
	*	push message to be shown to user when he comes online
	*	by default assume user is that one who are currently logged in
	*/
	public static function pushNotification(
		$body,					// neccessary parameter is notification body 
		array $params = null // all other parameters to build Notification object
	){
		$params[ 'body' ] = $body;
		if( isset( $params[ 'users' ] ) && is_array( $params[ 'users' ] ) ){
		
			foreach( $params[ 'users' ] as $user_id ){
			
				$params[ 'owner_id' ] = $user_id == 0
											? Zend_Registry::get( 'user' )->getId()
											: $user_id;
				$message = new Core_Model_Notification( $params );
				$message->put();

			}
		
		}
		
		if( isset( $params[ 'owner_id' ] ) || ( false == isset( $params[ 'users' ] ) ) ){

			$params[ 'owner_id' ] = isset( $params[ 'owner_id' ] )
										? ( int ) $params[ 'owner_id' ]
										: Zend_Registry::get( 'user' )->getId();

				$message = new Core_Model_Notification( $params );
				$message->put();

		}
		return true;
	}
	
	/**
	* give answer if message with set params have been shown to user
	*/
	public static function isShown(
		array $params 		// $params[ 'owner_id' ]
								// $params[ 'subject' ]
								// optional $params[ 'is_pinned' ]
	){
	
		if( false == ( $user_id = $params[ 'owner_id' ] ) ||
			 false == ( $subject = ( string ) $params[ 'subject' ] ) ) return false;

		$is_pinned = $params[ 'is_pinned' ] ? substr( $params[ 'is_pinned' ], 0, 1 ) : 'Y';
		$messages = Go_Factory::getDbTable( "Core_Model_Notification" )
										->fetchNotifications( array( 'owner_id' => $user_id,
																			  'subject_id' => $subject_id,
																			  'is_pinned' => $is_pinned ) );

		return count( $messages );
	
	}
	
	/**
	* once pinned message should be unpinned sometime
	*/
	public static function unpin(
		array $params		// $params[ 'owner_id' ]
								// $params[ 'subject' ]
	){
		if( false == ( $subject = ( string ) $params[ 'subject' ] ) ) return false;
		
		if( true == ( $messages = Go_Factory::getDbTable( "Core_Model_Notification" )
														->fetchNotifications( array( "owner_id" => $params[ 'owner_id' ],
																							  "subject" => $subject,
																							  "is_pinned" => 'Y' ) ) ) ){
			foreach( $messages as $message ){
				$message->setIsPinned( "N" )->put();
			}
			return true;
		} else {
			// who-o-ops, if we didn't found a pinned message we can be sure unpin operation is successful
			return true;
		}
		
	}
	
	/**
	* now let's declare some general notifications
	*/
	public static function pleaseRegister(){

		self::pushNotification(
			'Вы под гостевой учётной записью. Пожалуйста войдите или зарегистрируйтесь.',
			array( "class" => Core_Model_Notification::WARNING_CLASS,
					 "subject" => "please_register",
					 "is_pinned" => "Y" )
		);
	}
	public static function invalidData(){
		self::pushNotification(
			"Предоставлены недостаточные или неверные данные. Операция отклонена.",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);
	}
	public static function insufficientPrivileges(){
		self::pushNotification(
			"У вас недостаточно прав для совершения этой операции",
			array( "class" => Core_Model_Notification::ERROR_CLASS )
		);
	}
	public static function edited( Core_Model_Item $item, $new = false ){
		self::pushNotification(
			"Данные сохранены",
			array( "class" => Core_Model_Notification::SUCCESS_CLASS )
		);
	}

}
?>
