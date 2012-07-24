<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Form_Password extends Go_Form {
	
	protected $_user;
	
	public function __construct( $id = null ){
		if( false == $id ||
			 false == ( $this->_user = Go_Factory::get( "User_Model_User", $id ) ) ){
			$this->_user = new User_Model_User();
		}
		parent::__construct();
	}

	public function init() {

		if( false == $this->_user->getLogin() ){
			throw new Exception( 'Can\'t change password for unknown user' );
		}

		$this->addElement( 'hidden', 'user_id', array(
			'value'      => $this->_user->getLogin(),
		));

		$this->addElement( 'password', 'password', array(
			'required'   => true,
			'label'      => 'Password:',
			'value'		 => ''
		));

		$this->addElement( 'password', 'password_repeat', array(
			'required'   => true,
			'label'      => 'Repeat password:',
			'value'		 => ''
		));

		parent::init();
		
		$this->setAction( "/user/index/change_password" )
			  ->setAttrib( "id", "password_form" );
	}
}

?>
