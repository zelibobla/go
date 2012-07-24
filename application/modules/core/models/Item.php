<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Model_Item extends Zend_Db_Table_Row_Abstract {

	protected $name;

	/**
	* Basic deference between Zend_Db_Table_Row_Abstract and our classes is that properties in our classes
	* shouldn't be accessed directly; only through getters and setters
	*
	* So, when our class is being instantiated — it's ok; properties are being set properly
	* but when trying to save — Zend_Db_Table save() method looks for the properties in _data property (array expected)
	*
	* Thus, before we call save() method, we put in _data property needed array of class vars
	* (look for this in Go_Factory::save() method)
	*/
	public function put(){
		return Go_Factory::save( $this );
	}
	
	public function __construct( $params = array() ){
		parent::__construct( $params );
		if( isset( $params[ 'data' ] ) ){
			$this->setOptions( $params[ 'data' ] );
		} else {
			$this->setOptions( $params );
		}
		return $this;
	}

	public function __toString(){
		return $this->getName();
	}

	public function getName(){
		return $this->name;
	}
	public function setName( $name ){
		$this->name = ( string ) $name;
		return $this;
	}

	/**
	* set our fields via input options
	*/
	public function setOptions( $options, $ignore_prefix = false ) {

		if( $ignore_prefix ){
			$prefix_length = strlen( $ignore_prefix );
			$pure_options = array();
			foreach( $options as $key => $option ){
				$pure_key = substr( $key, $prefix_length );
				$pure_options[ $pure_key ] = $option;
			}
		} else {
			$pure_options = $options;
		}

		$methods = get_class_methods( $this );

		if( !empty( $pure_options ) ){ 
			try{
			foreach( $pure_options as $key => $value ) {
				if( is_string( $value ) ){
					$attempt = @unserialize( $value );
					if( is_array( $attempt ) ||
						 is_object( $attempt ) ){
						$value = $attempt;	 
					}
				}
		
				$key_parts = explode( "_", $key );
				foreach( $key_parts as $index => $val ){
					$key_parts[ $index ] = ucfirst( $val );
				}
				$method = 'set' . implode( $key_parts );

				if( in_array( $method, $methods ) ) {
					$this->$method( $value );
				}
			}
		} catch ( Exception $e ){
			var_dump( $pure_options );exit();
		}
		}
		return $this;
	}

	/**
	* return our fields values in associative array (useful for DB mapping)
	*/
	public function getOptions(){
		$vars = get_object_vars( $this );
		$result = array();
		foreach( $vars as $key => $value ) {
			if( substr( $key, 0, 1 ) == "_" ) continue;
			$key_parts = explode( "_", $key );
			foreach( $key_parts as $index => $val ){
				$key_parts[ $index ] = ucfirst( $val );
			}
			$method = 'get' . implode( $key_parts );
			
			$value = $this->$method();
			if( is_array( $value ) ||
				 is_object( $value ) ){
				 $value = serialize( $value );
			}
			$result[ $key ] = $value;
		}
		return $result;
	}
}

