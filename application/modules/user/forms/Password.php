<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Form_Password extends Go_Form {

	public function init() {
		if( false == $this->_item->getLogin() ){
			throw new Exception( 'Can\'t change password for unknown user' );
		}

		$this->addElement( 'hidden', 'user_id', array(
			'value'      => $this->_item->getLogin(),
			'filters' => array( array( 'int' ) )
		));

		$this->addElement( 'password', 'password', array(
			'required'   => true,
			'label'      => $this->_( 'user_password' ),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'password', 'password_repeat', array(
			'required'   => true,
			'label'      => $this->_( 'user_password' ),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));

		parent::init();
	}
}