<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Plugin_Voice extends Zend_Controller_Plugin_Abstract {
	
	/**
	* push notification to be shown to user when he comes online
	* @param $to – User_Model_User instance or array of User_Model_Users
	* @param $body – string notification body
	* @param $subject - string notification subject ( optional )
	* @param $pin - boolean should notification be pinned or not
	* @return void
	*/
	public static function push( $to, $body, $subject = null, $pin = false ){
		if( !is_array( $to ) ) $to = array( $to );

		foreach( $to as $user ){
			if( false == $user instanceof User_Model_User ||
			 	false == $user->getId() ) continue;
			$message = new Core_Model_Notification( array( 'owner_id' => $user->getId(),
			 											   'body' => $body,
			 											   'subject' => $subject,
														   'is_pinned' => $pin ) );
			$message->save();
		}
	}
	
	/**
	* give answer if notification with set params have been shown to user
	* @param $user – User_Model_User object of interest
	* @param $subject – string notification subject
	* @return boolean
	*/
	public static function isShown( User_Model_User $user, $subject ){
		if( false == $user->getId() ) return false;
		$messages = Core_Model_Notification::getDbTable()->fetchNotifications( array( 'owner_id' => $user->getId(),
																			  		  'subject' => $subject ) );
		return count( $messages );
	}
	
	/**
	* unpin notification
	* @param $user – instance of User_Model_User, whom notification was pinned
	* @param $subject – string notification subject
	* @return void
	*/
	public static function unpin( User_Model_User $user, $subject ){
		if( false == $subject ||
		 	false == $user->getId() ) return;
		
		return Core_Model_Notification::getDbTable()->unpin( $user->getId(), $subject );
	}
}
?>
