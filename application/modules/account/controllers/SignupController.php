<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Account_SignupController extends Go_Controller_Action {

	/**
	 * show signup form or process this form if post data exists
	 *
	 */
	public function indexAction() {

		$this->view->form = $form = new Account_Form_Signup( $this->_user );

		if( false == $this->_request->isPost() ){
			if( false == $this->_allowed( 'profile', 'create' ) ){		
				Account_Plugin_Voice::alreadyRegistered();
				$this->_redirector->gotoRoute( array(), 'user_profile' );
			}
		} else {
		
			if( $form->isValid( $data = $this->_request->getParams() ) ){
				
				$user = new User_Model_User( $form->getValues() );
				$user->generateRandomSalt()
					  ->generatePasswordHash( $form->getValue( 'password' ) )
					  ->setRole( 'user' )
					  ->setLogin( $form->getValue( 'email' ) );

				// this is not simple save but save with primary key replacement; so special method for this
				Go_Factory::getDbTable( 'User_Model_User' )->smartSave( $this->_user->getLogin(), $user );

				// now authenticate him
				$adapter = Core_Plugin_Misc::getAuthAdapter()->setIdentity( $user->getLogin() )
																			->setCredential( $form->getValue( 'password' ) );
				$auth = Zend_Auth::getInstance();
				$auth->clearIdentity();
				$result = $auth->authenticate( $adapter );
				if( false == $result->isValid() ){
					throw new Exception( 'Unable to create and authenticate user' );
				}
				Zend_Registry::set( 'user', $user );
				Core_Plugin_Voice::unpin( array( 'owner_id'	=> $user->getId(),
                                         'subject'	=> 'please_register' ) );
				User_Plugin_Voice::welcome();
				$this->_redirector->gotoRoute( array(), 'home' );
				
			} else {
				Account_Plugin_Voice::invalidData();
			}
		}
	}
	
	
}

