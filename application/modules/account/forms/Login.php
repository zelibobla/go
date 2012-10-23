<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_Form_Login extends Go_Form {
	
	public function init() {
	
		$this->addElement( 'text', 'login', array(
			'required'   => true,
			'label'      => $this->_( 'user_email' ),
			'validators' => array( array( 'emailAddress', true ),
								   array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'password', 'password', array(
			'required'   => true,
			'label'      => $this->_( 'user_password' ),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'checkbox', 'remember', array(
			'required'  => false,
			'label'     => $this->_( 'user_remember' ),
			'value'		=> 'on'
		));
		
		$this->addElement( 'submit', 'submit', array(
			'ignore'	=> true,
			'label'		=> $this->_( 'user_enter')
		));

		$this->setAction( "/account/login" );
		parent::init();
	}
}