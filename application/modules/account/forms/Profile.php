<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Account_Form_Profile extends Go_Form {

	public function init() {
		if( false == $this->_item ||
		 	false == $this->_item->getId() ) throw new Exception( "Can't instantiate profile form of undefined user" );

		$this->addElement( 'hidden', 'id', array( 'value' => $this->_item->getId() ) );

		$this->addElement( 'text', 'name', array(
			'required'   => true,
			'label'      => $this->_( 'user_name' ),
			'value'		 => $this->_item->getName(),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		) );



		$this->setAction( "/account/profile/edit" );
		parent::init();

		$this->addElement( 'hidden', 'photo', array(
			'label'      => $this->_( 'user_photo' ),
			'value'		 => $this->_item->getPhoto(),
			'validators' => array( array( 'stringLength', false, array( 3, 64 ) ) )
		) );

		$this->addElement( 'hidden', 'photo_selection' );

		$this->addElement( 'submit', 'submit', array(
			'ignore'	=> true,
			'label'		=> $this->_( 'submit' )
		) );
		$this->getElement( 'submit' )->removeDecorator( 'label' );
	}
}

?>
