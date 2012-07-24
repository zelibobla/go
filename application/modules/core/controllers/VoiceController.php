<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_VoiceController extends Go_Controller_Action {

	public function preDispatch() {	
		if( false == Zend_Auth::getInstance()->hasIdentity() ) {
			// keep silence if nobody logged in; no any redirections
			exit();
		}
	}

	/**
	* emit messages addressed to currently logged in user
	*
	*/
	public function utterAction() {
		$this->_helper->layout->disableLayout();
		$messages = Core_Plugin_Voice::getNotifications();
		$result = array();

		if( count( $messages ) ){
			foreach( $messages as $message ){
				$result[ $message->getId() ] = array( "class" => $message->getClass(),
																  "body"	 => $message->getBody(),
																  "subject" => $message->getSubject() );
			}
		}
		return $this->_helper->json( array( 'messages' => $result ) );
	}

	/**
	* after message was shown to user, we mark it as inactive
	*
	*/
	public function markreadAction(){
		if( true == ( $id = ( int ) $this->_getParam( 'id' ) ) &&
			 true == ( $message = Go_Factory::get( "Core_Model_Notification", $id ) ) ){
			$message->setIsActive( 'N' )->put();
		}
		return $this->_helper->json( array( 'result' => true ) );
	}

}
