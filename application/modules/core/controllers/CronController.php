<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Core_CronController extends Go_Controller_Action {

	public function indexAction() {
		Go_Factory::getDbTable( "User_Model_User" )->removeInactiveGuests();
		return $this->_redirector->gotoRoute( array(), "home" );
	}

}

