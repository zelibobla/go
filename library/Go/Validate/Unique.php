<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Go_Validate_Unique extends Zend_Validate_Abstract {

	const NOT_UNIQUE = 'not_unique';
	protected $_messageTemplates;
	protected $_column;
	protected $_item_id;

	/**
	* construct
	* @param $params while construct waiting for array with 'column' => 'any name'
	* @return void
	*/
	public function __construct( array $params = null ){
		if( isset( $params[ 'column' ] ) )
			$this->_column = $params[ 'column' ];
		if( isset( $params[ 'except_id' ] ) )
			$this->_item_id = $params[ 'except_id' ];
		
		$t = Zend_Registry::get( 'translator' );
		$this->_messageTemplates = array( self::NOT_UNIQUE => $t->_( 'user_error_already_exists' ) );
	}
	
	/**
	* check wether the value is valid or not
	* @param $value – value string
	* @return boolean
	*/
	public function isValid( $value ) {
		$value = ( string ) $value;
		$this->_setValue( $value );
		if( 'phone' == $this->_column )
			$value = Go_Misc::filterPhone( $value );

 		if( true == ( $user = User_Model_User::build( array( $this->_column => $value ) ) ) &&
 			$this->_item_id != $user->getId() ){
			$this->_error( self::NOT_UNIQUE );
 			return false;
 		} else {
 			return true;
 		}
	}
}
