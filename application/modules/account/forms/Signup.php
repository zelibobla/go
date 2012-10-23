<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_Form_Signup extends Go_Form {

	public function init() {

		$this->addElement( 'text', 'name', array(
			'required'   => true,
			'label'      => $this->_( 'user_name' ),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		) );

		$this->addElement( 'text', 'email', array(
			'required'   => true,
			'label'      => $this->_( 'user_email' ),
			'validators' => array( array( 'stringLength', false, array( 6, 64 ) ),
								   array( 'emailAddress', true ),
								   array( 'uniqueLogin' ) )
		) );

		$this->addElement( 'password', 'password', array(
			'required'   => true,
			'label'      => $this->_( 'user_password' ),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		) );

		$this->addElement( 'password', 'password_repeat', array(
			'required'   => true,
			'label'      => $this->_( 'user_password_repeat' ),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ),
			 					   array( 'passwordConfirmation' ) )
		) );
		
		$this->addElement( 'submit', 'submit', array(
			'ignore' => true,
			'label'	 => $this->_( 'submit' )
		) );

		$this->setAction( "/account/signup" );
		parent::init();
	}
}