<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

/**
* the default income point
*/
class Core_IndexController extends Go_Controller_Default {

	public function indexAction() {
		if( 'guest' == $this->_user->getRole() ){
			if( false == ( $message = Core_Model_Notification::build( array( 'owner_id' => $this->_user->getId(),
			 																 'subject' => 'please_signup',
																			 'is_pinned' => true ) ) ) ){
				$this->_notify( $this->_( 'core_voice_please_signup' ), 'please_signup', true );																
			}
			return $this->_redirector->gotoRoute( array(), 'login' );
		}
	}
}

