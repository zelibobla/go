<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Account_SignupController extends Go_Controller_Default {

	/**
	 * show signup form or process this form if post data exists
	 */
	public function indexAction() {

		$this->view->form = $form = new Account_Form_Signup();

		if( false == $this->_request->isPost() ){
			if( false == $this->_isAllowed( 'profile', 'create' ) ){
				$this->_notify( $this->_( 'user_voice_welcome' ) );
				$this->_redirector->gotoRoute( array(), 'user_profile' );
			}
			return;
		}

		if( !$form->isValid( $data = $this->_request->getParams() ) ){
			$this->_notify( $this->_( 'core_voice_invalid_data' ) );
			return;
		}

		if( $form->isValid( $data = $this->_request->getParams() ) ){
				
			$user = new User_Model_User( $form->getValues() );
			$user->generateRandomSalt()
				 ->generatePasswordHash( $form->getValue( 'password' ) )
				 ->setRole( 'user' )
				 ->setEmail( $form->getValue( 'email' ) )
				 ->save();

			// now authenticate him
			$adapter = Core_Plugin_Misc::getAuthAdapter()->setIdentity( $user->getEmail() )
														 ->setCredential( $form->getValue( 'password' ) );
			$auth = Zend_Auth::getInstance();
			$auth->clearIdentity();
			$result = $auth->authenticate( $adapter );
			if( false == $result->isValid() ){
				throw new Exception( 'Unable to create and authenticate user' );
			}
			Zend_Registry::set( 'user', $user );
			Core_Plugin_Voice::unpin( $user, 'please_register' );
			$this->_notify( $this->_( 'user_voice_welcome' ) );
			$defaults = Zend_Registry::get( 'defaults' );
			Core_Plugin_Mail::mail( $user,
									sprintf( $this->_( 'mail_welcome_subject' ), $defaults[ 'application_name' ] ),
									$this->_( 'mail_welcome_body' ) );
			$this->_redirector->gotoRoute( array(), 'home' );
		}
	}
}

