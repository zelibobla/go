<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Account_ProfileController extends Go_Controller_Crud {

	protected $_item_class = "User_Model_User";
	protected $_form_class = "Account_Form_Profile";
	protected $_resource = "profile";

	public function init(){
		parent::init();
		if( false == $this->_isAllowed( 'profile', 'view' ) ){
			$this->_notify( $this->_( 'core_voice_insufficient_privileges' ) );
			return $this->_redirector->gotoRoute( array(), 'signup' );
		}
	}
	
	/**
	* show profile data
	* own profile will be shown by default
	*/
	public function indexAction() {
		if( false == ( $id = ( int ) $this->_request->getParam( 'id' ) ) ||
		 	false == ( $user = User_Model_User::build( $id ) ) ){
			$user = $this->_user;
		}
		$this->view->item = $user;
	}
	
	/**
	* show edit profile data form or process this form if post income
	*/
	public function editAction() {
		
		$this->view->form = $form = new Account_Form_Profile( $this->_user );
		if( false == $this->_request->isPost() ) return;
		
		if( false == $form->isValid( $data = $this->_request->getParams() ) ){
			$this->_notify( 'core_voice_invalid_data' );
			return;
		}
		
		if( $this->_user->getId() != ( int ) $form->getValue( 'id' ) ){
			$this->_notify( 'core_voice_insufficient_privileges' );
			return $this->_redirector->gotoRoute( array(), 'user_profile' );
		}

		$this->_user->setOptions( $form->getValues() )
					->save();

		if( true == ( $filename = $form->getValue( 'photo' ) ) ){
			$this->_item = $this->_user;
			$this->_placeFile( $filename, json_decode( stripslashes( $form->getValue( 'photo_selection' ) ), true ) );
		}

		$this->_notify( sprintf( $this->_( 'user_voice_edited' ), $this->_user->__toString() ) );
		return $this->_redirector->gotoRoute( array(), 'user_profile' );
	}

	/**
	* show change password form or process this form if post data exists
	*/
	public function passwordAction() {
		
		$this->view->form = $form = new Account_Form_Password();
		if( false == $this->_request->isPost() ) return;
		
		if( false == $form->isValid( $data = $this->_request->getParams() ) ){
			$this->_notify( $this->_( "core_voice_invalid_data" ) );
			return;
		}

		$this->_user->generatePasswordHash( $form->getValue( 'password' ) )
					->save();

		$this->_notify( sprintf( $this->_( 'user_voice_edited' ), $this->_user->__toString() ) );
		return $this->_redirector->gotoRoute( array(), 'user_profile' );
	}

	/**
	* disable profile delete by overriding parental action
	*/
	public function deleteAction(){}
}

