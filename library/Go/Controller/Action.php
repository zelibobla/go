<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Go_Controller_Action extends Zend_Controller_Action {

	public function init(){
		try{
			$this->_user = Zend_Registry::get( 'user' ); 
		} catch( Exception $e ){
			$this->_user = null;
		}
		try{
			$this->_acl = Zend_Registry::get( 'acl' ); 
		} catch( Exception $e ){
			$this->_acl = null;
		}
		$this->_request = $this->getRequest();
		$this->_redirector = $this->_helper->getHelper( 'Redirector' );
		$this->view->user = $this->_user;
	}
	
	protected function _allowed( $resource, $privilege ){
		if( false == $this->_acl ||
			 false == $this->_user ) return false;

		return $this->_acl->isAllowed( $this->_user->getRole(), $resource, $privilege );
	}
	
}

