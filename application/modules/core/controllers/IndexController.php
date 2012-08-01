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
			$this->_notify( $this->_( 'core_voice_please_signup' ), 'please_signup', true );
			return $this->_redirector->gotoRoute( array(), 'login' );
		}
	}
}

