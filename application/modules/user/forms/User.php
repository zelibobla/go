<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Form_User extends Go_Form {

	public function init() {

		$this->addElement( 'hidden', 'id', array( 'value' => $this->_item->getId() ) );

		$this->addElement( 'text', 'name', array(
			'required'   => true,
			'label'      => $this->_( 'user_name' ),
			'value'		 => $this->_item->getName(),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'text', 'email', array(
			'required'   => true,
			'label'      => $this->_( 'user_email' ),
			'value'		 => $this->_item->getEmail(),
			'validators' => array( array( 'stringLength', false, array( 6, 64 ) ),
								   array( 'emailAddress', true ) )
		));

		$this->addElement( 'password', 'password', array(
			'required'   => !( bool ) $this->_item->getId(),
			'label'      => $this->_( 'user_new_password' )
		));

		$this->addElement( 'password', 'password_repeat', array(
			'required'   => !( bool ) $this->_item->getId(),
			'label'      => $this->_( 'user_new_password_repeat' ),
		));

		$this->addElement( 'select', 'role', array(
			'required'	 => true,
			'label'      => $this->_( 'user_role' ),
			'value'		 => $this->_item->getRole(),
			'multiOptions' => array( 'guest'	=> $this->_( 'user_role_guest' ),
									 'user'		=> $this->_( 'user_role_user' ),
									 'admin'	=> $this->_( 'user_role_admin' ), )
		));
		parent::init();
	}
}

?>
