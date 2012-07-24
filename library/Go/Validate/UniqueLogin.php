<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Go_Validate_UniqueLogin extends Zend_Validate_Abstract {

	const NOT_UNIQUE = 'notUnique';
 
	protected $_messageTemplates = array(
		self::NOT_UNIQUE => 'Пожалуйста, выберите другой логин, т.к. этот занят'
	);
 
	public function isValid( $value, $context = null ) {

		$value = ( string ) $value;
		$this->_setValue( $value );
 
 		if( true == ( Go_Factory::get( 'User_Model_User', $value ) ) ){
			$this->_error( self::NOT_UNIQUE );
 			return false;
 		} else {
 			return true;
 		}
	}
}
