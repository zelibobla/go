<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Account_RecoverController extends Go_Controller_Default {
	
	/**
	* show recover password form or process this form if post data exists
	*/
	public function indexAction() {

		$this->view->form = $form = new Account_Form_Recover();

		if( false == $this->_isAllowed( 'password', 'recover' ) ){
			$this->_notify( $this->_( 'user_voice_already_signed_up' ) );
			$this->_redirector->gotoRoute( array(), 'home' );
		}
		if( false == $this->_request->isPost() ) return;
		
		if( false == $form->isValid( $data = $this->_request->getParams() ) ){
			$this->_notify( $this->_( 'core_voice_invalid_data' ) );
			return;
		}

		if( false == ( $user = User_Model_User::build( array( 'email' => addslashes( $this->_request->getParam( 'email' ) ) ) ) ) ){
			$this->_notify( $this->_( 'user_voice_not_found' ) );
			return;
		}
		
		$password = Go_Misc::generateRandomString( 6 );
		$user->generateRandomSalt()
			 ->generatePasswordHash( $password )
			 ->save();

		Core_Plugin_Mail::mail( $user,
								$this->_( 'mail_password_recover_subject' ),
								sprintf( $this->_( 'mail_password_recover_body' ), $password ) );
		$this->_notify( sprintf( $this->_( 'user_voice_password_sent' ), $user->getEmail() ) );
		return $this->_redirector->gotoRoute( array(), 'home' );
	}
	
	
}

