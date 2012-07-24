<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Account_CheckController extends Go_Controller_Action {

	public function init(){

		$this->_helper->contextSwitch()
						  ->addActionContext( 'login', 'json' )
						  ->addActionContext( 'email', 'json' )
						  ->initContext();
		parent::init();
	}
	
	/**
	* check if specified login exist in DB
	*
	*/
	public function loginAction() {

		if( false == ( $login = Core_Plugin_Misc::sanitize( $this->_getParam( 'login' ) ) ) ||
			 	( true == ( $user = Go_Factory::get( 'User_Model_User', $login ) ) &&
			 	  $this->_user->getLogin() != $user->getLogin() ) ){
			 
			return $this->_helper->json( false );
		
		} else {
		
			return $this->_helper->json( true );
		
		}
	}

	/**
	* check if specified email exist in DB
	*
	*/
	public function emailAction() {

		if( false == ( $email = Core_Plugin_Misc::sanitize( $this->_getParam( 'email' ) ) ) ||
			 ( true == ( $user = Go_Factory::getDbTable( 'User_Model_User' )->fetchByEmail( $email ) ) &&
			   $this->_user->getLogin() != $user->getLogin() ) ){

			return $this->_helper->json( false );
		
		} else {
		
			return $this->_helper->json( true );
		
		}
	}


}

