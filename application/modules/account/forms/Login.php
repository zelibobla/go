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
			'label'      => 'Адрес электронной почты:',
			'value'		 => '',
			'validators' => array( array( 'emailAddress', true ),
										  array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'password', 'password', array(
			'required'   => true,
			'label'      => 'Пароль:',
			'value'		 => '',
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'checkbox', 'remember', array(
			'required'   => false,
			'label'      => 'Запомнить?',
			'value'		 => 'on'
		));
		
		$this->addElement( 'submit', 'submit', array(
			'ignore'			=> true,
			'label'			=> 'Войти'
		));

		$this->setAction( "/account/login" );
		parent::init();
	
		$this->setAttrib( "id", "login_form" );
	}
}

?>
