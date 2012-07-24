<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_LoginController extends Go_Controller_Action {

	/**
	 * bring back the login form or to process this form (authenticate user)
	 *
	 */
	public function indexAction() {

		$temporary_user = $this->_user;
		$this->view->form = $form = new Account_Form_Login();
		if( false == ( $data = $this->_request->getPost() ) ) {
			return;
		}

		if( false == $form->isValid( $data ) ) {
			Account_Plugin_Voice::invalidData();
			return;
		}

		$adapter = Core_Plugin_Misc::getAuthAdapter()->setIdentity( $data[ 'login' ] )
													 ->setCredential( $data[ 'password' ] );

		$auth    = Zend_Auth::getInstance();
		$result  = $auth->authenticate( $adapter );

		if( false == ( $result->isValid() ) ){
			Account_Plugin_Voice::invalidLogin();
			return;
		} else {
			return $this->_redirector->gotoRoute( array(), 'home' );
		}

	}

	public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		return $this->_redirector->gotoRoute( array(), 'home' );
	}

}
?>
