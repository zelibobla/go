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
			'label'      => 'Новый пароль:',
			'value'		 => ''
		));

		$this->addElement( 'password', 'password_repeat', array(
			'required'   => true,
			'label'      => 'Повтор нового пароля:',
			'value'		 => ''
		));
		
		$this->addElement( 'submit', 'submit', array(
			'ignore'			=> true,
			'label'			=> 'Сменить'
		));

		$this->getElement( 'password_repeat' )
			  ->addPrefixPath( 'Go_Validate','Go/Validate','validate' )
			  ->addValidator( 'passwordConfirmation' );

		parent::init();
		
		$this->setAction( "/account/profile/password" )
			  ->setAttrib( "id", "password_form" );
	}
}

?>
