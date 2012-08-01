<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Account_CheckController extends Go_Controller_Default {
	
	/**
	* check if specified email exist in DB
	*/
	public function emailAction() {

		if( false == ( $email = addslashes( $this->_getParam( 'email' ) ) ) ||
			 ( true == ( $user = User_Model_User::build( array( 'email' => $email ) ) ) &&
			   $this->_user->getEmail() != $user->getEmail() ) ){
			return $this->_helper->json( false );
		} else {
			return $this->_helper->json( true );
		}
	}
}

