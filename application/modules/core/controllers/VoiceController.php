<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

/**
* handle ajax requests of system messages
*/
class Core_VoiceController extends Go_Controller_Default {

	public function preDispatch() {	
		if( false == Zend_Auth::getInstance()->hasIdentity() ) {
			// keep silence if nobody logged in
			exit();
		}
	}

	/**
	* emit messages addressed to currently logged in user
	*/
	public function utterAction() {

		$messages = Core_Model_Notification::getDbTable()->fetchVisibleBy( array( 'user_id' => ( int ) $this->_user->getId() ) );
		$result = array();
		if( count( $messages ) ){
			foreach( $messages as $message ){
				$result[ $message->getId() ] = array( "class"	=> $message->getClass(),
													  "body"	=> $message->getBody(),
													  "subject" => $message->getSubject() );
			}
		}
		return $this->_helper->json( array( 'messages' => $result ) );
	}

	/**
	* mark specified message as inactive
	*/
	public function markreadAction(){
		if( true == ( $id = ( int ) $this->_getParam( 'id' ) ) &&
			true == ( $message = Core_Model_Notification::build( $id ) ) ){
			$message->setIsActive( 0 )->save();
		}
		return $this->_helper->json( array( 'result' => true ) );
	}

}
