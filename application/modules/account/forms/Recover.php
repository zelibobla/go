<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_Form_Recover extends Go_Form {

	public function init() {

		$this->addElement( 'text', 'email', array(
			'required'   => true,
			'label'      => $this->_( 'user_email_to_recover' ),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		) );
		
		$this->addElement( 'submit', 'submit', array(
			'ignore'		=> true,
			'label'			=> $this->_( 'submit' )
		) );

		$this->setAction( "/account/recover" );
		parent::init();
	}
}

?>
