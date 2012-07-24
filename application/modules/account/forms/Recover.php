<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_Form_Recover extends Go_Form {
	
	public function __construct(){
		parent::__construct();
	}

	public function init() {

		$this->addElement( 'text', 'term', array(
			'required'   => true,
			'label'      => 'Ваш адрес электронной почты:',
			'value'		 => '',
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));
		
		$this->addElement( 'submit', 'submit', array(
			'ignore'			=> true,
			'label'			=> 'Восстановить'
		));

		$this->setAction( "/account/recover" );
		parent::init();
	
		$this->setAttrib( "id", "recover_form" );
	}
}

?>
