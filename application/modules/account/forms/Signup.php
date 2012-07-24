<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_Form_Signup extends Go_Form {
	
	protected $_temporary_user;

	public function init() {

		$this->addElement( 'text', 'name', array(
			'required'   => true,
			'label'      => 'Имя:',
			'value'		 => '',
			'validators' => array( array( 'alnum' ),
										  array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'text', 'email', array(
			'required'   => true,
			'label'      => 'Адрес электронной почты:',
			'value'		 => '',
			'validators' => array( array( 'stringLength', false, array( 6, 64 ) ),
										  array( 'emailAddress', true ) )
		));

		$this->addElement( 'password', 'password', array(
			'required'   => true,
			'label'      => 'Пароль:',
			'value'		 => '',
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'password', 'password_repeat', array(
			'required'   => true,
			'label'      => 'Повторите пароль:',
			'value'		 => '',
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->getElement( 'email' )
			  ->addPrefixPath( 'Go_Validate','Go/Validate','validate' )
			  ->addValidator( 'uniqueLogin' );

		$this->getElement( 'password_repeat' )
			  ->addPrefixPath( 'Go_Validate','Go/Validate','validate' )
			  ->addValidator( 'passwordConfirmation' );
		
		$this->addElement( 'submit', 'submit', array(
			'ignore'			=> true,
			'label'			=> 'Зарегистрироваться'
		));

		$this->setAction( "/account/signup" );
		parent::init();
	
		$this->setAttrib( "id", "signup_form" );
	}
}

?>
