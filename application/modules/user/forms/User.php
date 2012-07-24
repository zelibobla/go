<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Form_User extends Go_Form {
	
	protected $_user;
	
	public function __construct( $id = null ){
		if( false == $id ||
			 false == ( $this->_user = Go_Factory::get( "User_Model_User", $id ) ) ){
			$this->_user = new User_Model_User();
		}
		parent::__construct();
	}

	public function init() {

		$this->addElement( 'hidden', 'id', array( 'value' => $this->_user->getId() ) );

		$this->addElement( 'text', 'name', array(
			'required'   => true,
			'label'      => 'Имя:',
			'value'		 => $this->_user->getName(),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'text', 'email', array(
			'required'   => true,
			'label'      => 'Адрес электронной почты:',
			'value'		 => $this->_user->getEmail(),
			'validators' => array( array( 'stringLength', false, array( 6, 64 ) ),
										  array( 'emailAddress', true ) )
		));

		$this->addElement( 'password', 'password', array(
			'required'   => !( bool ) $this->_user->getId(),
			'label'      => 'Новый пароль:',
			'value'		 => ''
		));

		$this->addElement( 'password', 'password_repeat', array(
			'required'   => !( bool ) $this->_user->getId(),
			'label'      => 'Повтор нового пароля:',
			'value'		 => ''
		));
		
		$this->addElement( 'select', 'role', array(
			'required'	 => true,
			'label'      => 'Роль:',
			'value'		 => $this->_user->getRole(),
			'multiOptions' => array( 'guest'	=> 'гость',
									 'user'		=> 'менеджер',
									 'admin'	=> 'администратор' )
		));
		
		$this->addElement( 'select', 'distributer_id', array(
			'label'      => 'Дистрибютер:',
			'value'		 => $this->_user->getDistributerId(),
			'multiOptions' => Go_Factory::reference( 'Corporation_Model_Distributer',
													 array( 'is_active' => "Y" ),
													 $fetch = true,
													 $as_array = true )
		));
		
		$this->addElement( 'textarea', 'footer', array(
			'label'		=> 'Подпись',
			'value'		=> $this->_user->getFooter()
		));

		$this->setAction( "/user/index/edit" );
		parent::init();
	}
}

?>
