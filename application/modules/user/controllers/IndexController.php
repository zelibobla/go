<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class User_IndexController extends Go_Controller_Item {

	public function init(){
		$this->_resource = "user_foreign";
		$this->_item_class = "User_Model_User";
		$this->_form_class = "User_Form_User";
		$this->_resources = "users";
		parent::init();
	}

	/**
	* show items listing
	*
	*/
	public function indexAction() {

		$settings = $this->_user->getSettings();
		$resources = $this->_resources;
		$select = Go_Factory::getDbTable( 'User_Model_User' )->selectActive();
		$this->view->$resources = $this->getPaginator( $select );

	}

	protected function beforeSuccessEdit(){
		if( true == $this->_form->getValue( 'password' ) ){
			$this->_item->generateRandomSalt()
					    ->generatePasswordHash( $this->_form->getValue( 'password' ) );
		}
	}
}

