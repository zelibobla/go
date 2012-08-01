<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_LoginController extends Go_Controller_Default {

	/**
	* bring back the login form or to process this form (authenticate user)
	*/
	public function indexAction() {

		$temporary_user = $this->_user;
		$this->view->form = $form = new Account_Form_Login();
		if( false == ( $data = $this->_request->getPost() ) ) {
			return;
		}

		if( false == $form->isValid( $data ) ) {
			$this->_notify( $this->_( 'core_voice_invalid_data' ) );
			return;
		}

		$adapter = Core_Plugin_Misc::getAuthAdapter()->setIdentity( $data[ 'login' ] )
													 ->setCredential( $data[ 'password' ] );

		$auth    = Zend_Auth::getInstance();
		$result  = $auth->authenticate( $adapter );

		if( false == ( $result->isValid() ) ){
			$this->_notify( $this->_( 'user_voice_not_found' ) );
			return;
		} else {
			if( '1' == $form->getValue( 'remember' ) ){
				setcookie( 'hash', $this->_user->getPasswordHash(), time() + 60 * 60 * 24 *30, '/' );
			}
			return $this->_redirector->gotoRoute( array(), 'home' );
		}

	}

	public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		return $this->_redirector->gotoRoute( array(), 'home' );
	}

}
?>
