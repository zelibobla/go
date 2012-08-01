<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

/**
* just to make less coding later let's put some often needed data to protected vars
* and define several methods
*/
class Go_Controller_Default extends Zend_Controller_Action {

	protected $_user;
	protected $_acl;
	protected $_request;
	protected $_redirector;

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
	
	/**
	* shorthand to check currently logged user for access right for specified action under specified resource
	* @param $resource – string resource name
	* @param $privilege – string privilege name
	* @return boolean is allowed or not
	*/
	protected function _isAllowed( $resource, $privilege ){
		if( false == $this->_acl ||
			 false == $this->_user ) return true;

		return $this->_acl->isAllowed( $this->_user->getRole(), $resource, $privilege );
	}
	
	/**
	* translate word be provided key or return key itself if no translator set or no value for key defined
	* @param $key – string key to search for the value in translator
	* @return string translation or key itself
	*/
	protected function _( $key ){
		try{
			$translator = Zend_Registry::get( 'translator' );
			return $translator->_( $key );
		} catch( Exception $e ){
			return $key;
		}
	}
	
	/**
	* shorthand to push notification to currently logged user
	* @param $notification – string notification body
	* @param $subject – string notification subject (optional)
	* @param $pin – boolean should notification be pinned or not (optional)
	* @return void
	*/
	protected function _notify( $notification, $subject = null, $pin = false ){
		if( false == $this->_user ||
			false == $this->_user->getId() ) return;
		Core_Plugin_Voice::push( $this->_user, $notification, $subject, $pin );
	}
	
}

