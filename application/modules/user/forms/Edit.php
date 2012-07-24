<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Form_Edit extends Go_Form {
	
	protected $_user;
	
	public function __construct( $id = null ){
		if( false == $id ||
			 false == ( $this->_user = Go_Factory::get( "User_Model_User", $id ) ) ){
			$this->_user = new User_Model_User();
		}
		parent::__construct();
	}

	public function init() {
		if( true == $this->_user->getLogin() ){

			$this->addElement( 'hidden', 'login', array(
				'value'      => $this->_user->getLogin(),
			));

		} else {

			$this->addElement( 'hidden', 'new', array(
				'value'      => '1',
			));

			$this->addElement( 'text', 'login', array(
				'required'   => true,
				'label'      => 'Login:',
				'value'		 => ''
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
		}

		$this->addElement( 'text', 'name', array(
			'required'   => true,
			'label'      => 'Name:',
			'value'		 => $this->_user->getName()
		));

		$this->addElement( 'text', 'icq', array(
			'required'   => true,
			'label'      => 'ICQ:',
			'value'		 => $this->_user->getIcq()
		));

		$this->addElement( 'text', 'jabber', array(
			'required'   => true,
			'label'      => 'Jabber:',
			'value'		 => $this->_user->getJabber()
		));

		if( true == $this->_user->getLogin() &&
			 false == ( $this->_user->getRole() == 'admin' ) ){
			$roles = Go_Factory::reference( 'User_Model_Role', array(), true, true );
			unset( $roles['admin'] );
			$this->addElement( 'select', 'role', array(
				'required'   => true,
				'label'      => 'Role:',
				'value'		 => $this->_user->getRole(),
				'multiOptions' => $roles
			));
		}
		
		$this->addElement( 'text', 'percent', array(
			'required'   => true,
			'label'      => 'Percent:',
			'value'		 => $this->_user->getPercent()
		));

		$this->addElement( 'text', 'discount', array(
			'required'   => true,
			'label'      => 'Discount:',
			'value'		 => $this->_user->getDiscount()
		));

		$this->addElement( 'text', 'purse', array(
			'required'   => true,
			'label'      => 'Purse:',
			'value'		 => $this->_user->getPurse()
		));

		$this->addElement( 'select', 'is_active', array(
			'required'   => true,
			'label'      => 'Is active:',
			'multioptions'	=> array( 'Y' => 'Yes', 'N' => 'No' ),
			'value'		 => $this->_user->getIsActive()
		));
		
		$this->setAction( "/user/index/edit" );
		$this->setMethod( "post" );

		parent::init();
	}
}

?>
