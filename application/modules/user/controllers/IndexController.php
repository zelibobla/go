<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class User_IndexController extends Go_Controller_Crud {

	/**
	* show items listing
	*/
	public function indexAction() {

		$settings = $this->_user->getSettings();
		$select = User_Model_User::getDbTable()->getSelect( 'is_active = 1 AND role != "guest"' );
		$this->view->items = $this->_getPaginator( $select );

	}

	protected function beforeSuccessEdit(){
		if( true == $this->_form->getValue( 'password' ) ){
			$this->_item->generateRandomSalt()
					    ->generatePasswordHash( $this->_form->getValue( 'password' ) );
		}
	}
}

