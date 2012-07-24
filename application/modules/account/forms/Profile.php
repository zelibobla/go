<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_Form_Profile extends Go_Form {
	
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

		$this->addElement( 'password', 'password', array(
			'label'      => 'Новый пароль:',
			'value'		 => ''
		));

		$this->addElement( 'password', 'password_repeat', array(
			'label'      => 'Повтор нового пароля:',
			'value'		 => ''
		));
		
		$this->addElement( 'textarea', 'footer', array(
			'label'		=> 'Подпись',
			'value'		=> $this->_user->getFooter()
		));

		$this->addElement( 'submit', 'submit', array(
			'ignore'			=> true,
			'label'			=> 'Готово'
		));

		$this->setAction( "/account/profile/edit" );
		parent::init();
	}
}

?>
