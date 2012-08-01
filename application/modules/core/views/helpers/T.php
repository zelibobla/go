<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Zend_View_Helper_T extends Zend_View_Helper_Abstract {

	public $view;

	/**
	* shorthand to bring translation by provided key
	* @param $key – key to find a translation
	* @return string representation of translation or key itself
	*/
	public function t( $key ) {
		try{
			$translator = Zend_Registry::get( 'translator' );
			return $translator->_( $key );
		} catch( Exception $e ){
			return $key;
		}
	}
}
