<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Zend_View_Helper_Allowed extends Zend_View_Helper_Abstract {

	public $view;

	public function allowed(
		$resource,
		$privilege
	) {
		try{
			$acl = Zend_Registry::get( 'acl' );		
		} catch( Exception $e ){
			return false;
		}
    
	    if( false == ( $this->view->user instanceof User_Model_User ) ||
	        false == ( $role = $this->view->user->getRole() ) ){
	        return false;
	    } else {
			return $acl->isAllowed( $role, $resource, $privilege );
		}
	}
}