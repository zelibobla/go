<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Account_ProfileController extends Go_Controller_Action {

	public function init(){
		parent::init();
		if( false == $this->_allowed( 'profile', 'edit' ) ){
			Account_Plugin_Voice::notRegisteredYet();
			return $this->_redirector->gotoRoute( array(), 'signup' );
		}
	}
	
	/**
	 * show profile data
	 *
	 */
	public function indexAction() {
	}
	
	/**
	 * show edit profile data form or process this form if post data exists
	 *
	 */
	public function editAction() {
		
		$this->view->form = $form = new Account_Form_Profile( $this->_user->getId() );
		if( false == $this->_request->isPost() ) return;
		
		if( false == $form->isValid( $data = $this->_request->getParams() ) ){
			Account_Plugin_Voice::invalidData();
			return;
		}

		$this->_user->setOptions( $form->getValues() )
					->put();

		User_Plugin_Voice::edited( $this->_user );
		return $this->_redirector->gotoRoute( array(), 'user_profile' );
	}

	/**
	 * show change password form or process this form if post data exists
	 *
	 */
	public function passwordAction() {
		
		$this->view->form = $form = new Account_Form_Password();
		if( false == $this->_request->isPost() ) return;
		
		if( false == $form->isValid( $data = $this->_request->getParams() ) ){
			Account_Plugin_Voice::invalidData();
			return;
		}

		$this->_user->generatePasswordHash( $form->getValue( 'password' ) )->put();
		
		// now authenticate him
		$adapter = Core_Plugin_Misc::getAuthAdapter()->setIdentity( $this->_user->getLogin() )
																	->setCredential( $form->getValue( 'password' ) );
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		$result = $auth->authenticate( $adapter );
		if( false == $result->isValid() ){
			throw new Exception( 'Unable to create and authenticate user' );
		}
		Zend_Registry::set( 'user', $this->_user );

		User_Plugin_Voice::edited();
		return $this->_redirector->gotoRoute( array(), 'user_profile' );
	}
	
	/**
	* standalone photo upload handler (cause ajax doesn't support file uploads)
	*
	*/
	public function photoAction() {
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$allowedExtensions = array( 'jpg' );
		// max file size in bytes
		$sizeLimit = 10 * 1024 * 1024;

		$uploader = new Go_FileUploader( $allowedExtensions, $sizeLimit );
		$result = $uploader->handleUpload( APPLICATION_PATH . '/../public/uploads/user/');

		return $this->_helper->json( $result );
	}
}

