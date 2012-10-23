<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Form_Bulletin extends Go_Form {
	
	protected $_post;
	
	public function __construct( $post_id = null ){
		if( true == ( int ) $post_id &&
        true == ( $post = Go_Factory::get( "Forum_Model_Bulletin", $post_id ) ) ){
			$this->_post = $post;
		} else {
			$this->_post = new Forum_Model_Bulletin();
		}
		parent::__construct();
	}

	public function init() {
    $user = Zend_Registry::get( 'user' );
    if( 'guest' != $user->getRole() ){
        $name = $user->getName();
        $email = $user->getEmail();
        $phone = $user->getPhone();
        $user_fields_disabled = true;
    } else {
        $user_fields_disabled = false;
    }
    
    $this->addElement( 'hidden', 'id', array(
        'value'   => $this->_post->getId()
    ));
 
    $this->addElement( 'textarea', 'body', array(
      'label'     => 'Содержание:',
			'required'		=> true,
			'value'			=> $this->_post->getBody()
		));
    
    $this->addElement( 'button', 'next', array(
        'disabled' => ( 3 < strlen( $this->_post->getBody() ) ? false : true ),
        'label'   => 'Далее'    
    ));

    $this->addElement( 'select', 'type_id', array(
			'required'	 => true,
      'label'      => 'Категория:',
			'value'      => $this->_post->getTypeId(),
      'multioptions' => Go_Factory::reference( 'Forum_Model_BoardType', array(), $fetch = true, $as_array = true )
		));
 
    $this->addElement( 'select', 'region_id', array(
        'label'     => 'Регион:',
        'required'  => true,
        'value'     => $this->_post->getRegionId(),
        'multioptions' => Go_Factory::reference( 'Core_Model_Region', array(), $fetch = true, $as_array = true ) 
    ));

		$this->addElement( 'text', 'name', array(
      'disabled'   => ( $user_fields_disabled ? true : null ),
      'value'      => $name,
			'required'   => !$user_fields_disabled,
			'label'      => 'Имя:',
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		));

		$this->addElement( 'text', 'email', array(
      'disabled'   => ( $user_fields_disabled ? true : null ),
      'value'      => $email,
			'required'   => !$user_fields_disabled,
			'label'      => 'Адрес электронной почты:',
			'validators' => array( array( 'stringLength', false, array( 6, 64 ) ),
										  array( 'emailAddress', true ) )
		));

		$this->addElement( 'text', 'phone', array(
      'disabled'   => ( $user_fields_disabled ? true : null ),
      'value'      => $phone,
      'required'   => !$user_fields_disabled,
			'label'      => 'Телефон:',
			'validators' => array( array( 'stringLength', false, array( 6, 64 ) ) )
		));

		$this->setAction( "/forum/bulletin/edit" );
		parent::init();
		$this->setAttrib( "class", "bulletin" );

    $this->getElement( 'body' )->removeDecorator( 'label' );
    $this->getElement( 'next' )->removeDecorator( 'label' );
    /**
    * two steps if bulletin is creating
    */
    if( false == $this->_post->getId() ){
        $this->addDisplayGroup( array(
                                'body',
                                'next' ),
                            'first_step' );
        $this->getDisplayGroup( 'first_step' )
             ->removeDecorator( 'DtDdWrapper' );
		
        $this->addDisplayGroup( array(
                             'type_id',
                             'region_id',
                             'name',
                             'email',
                             'phone' ),
                                'second_step' );
        $this->getDisplayGroup( 'second_step' )
            ->removeDecorator( 'DtDdWrapper' );
    /**
    * one step if bulletin is editing
    */
    } else {
        $this->addDisplayGroup( array(
                                'body',
                                'type_id',
                                'region_id',
                                'name',
                                'email',
                                'phone' ),
                            'first_step' );
        $this->getDisplayGroup( 'first_step' )
             ->removeDecorator( 'DtDdWrapper' );
	
        $this->removeElement( 'next' );
    }


	}
}