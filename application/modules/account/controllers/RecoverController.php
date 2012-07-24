<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Account_RecoverController extends Go_Controller_Action {
	
	/**
	 * show recover password form or process this form if post data exists
	 *
	 */
	public function indexAction() {

		$this->view->form = $form = new Account_Form_Recover();

		if( false == $this->_allowed( 'password', 'recover' ) ){
			Account_Plugin_Voice::nothingToRecover();
			$this->_redirector->gotoRoute( array(), 'home' );
		}		
		if( false == $this->_request->isPost() ) return;
		
		if( false == $form->isValid( $data = $this->_request->getParams() ) ){
			Account_Plugin_Voice::invalidData();
			return;
		}
				
		if( false == ( $user = Go_Factory::getDbTable( 'User_Model_User' )->fetchByEmail( Core_Plugin_Misc::sanitize( $data[ 'term' ] ) ) ) ){
			User_Plugin_Voice::userNotFound();
			return;
		}
		
		$password = Core_Plugin_Misc::generateRandomString( 6 );
		$user->generateRandomSalt()
			  ->generatePasswordHash( $password )
			  ->put();

		User_Plugin_Mail::newPassword( $user, $password );
		User_Plugin_Voice::newPasswordSent( $user->getEmail() );

		$this->_redirector->gotoRoute( array(), 'home' );
		return;
	}
	
	
}

