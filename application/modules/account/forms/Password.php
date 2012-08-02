<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_Form_Password extends Go_Form {

	public function init() {

		$this->addElement( 'password', 'password', array(
			'required'   => true,
			'label'      => $this->_( 'user_new_password' ),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		) );

		$this->addElement( 'password', 'password_repeat', array(
			'required'   => true,
			'label'      => $this->_( 'user_new_password_repeat' ),
			'validators' => array( array( 'passwordConfirmation' ),
								   array( 'stringLength', false, array( 3, 64 ) ) )
		) );
		
		$this->addElement( 'submit', 'submit', array(
			'ignore'	=> true,
			'label'		=> $this->_( 'submit' )
		) );

		parent::init();
		$this->setAction( "/account/profile/password" );
	}
}

?>
