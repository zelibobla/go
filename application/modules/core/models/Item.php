<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

/**
* fundamental system primitive class have only name property
* and several auxiliary properties due to a Zend ORM compatibility
*/
class Core_Model_Item extends Zend_Db_Table_Row_Abstract {

	protected $name;

	/**
	* When Zend ORM instantiate this class or it's child, it put fields into _data
	* so we're about to reflect _data containment into our protected properties due to incapsulation principle.
	* From other side class can be instantiated directly in the code with array or fields and it's values.
	* Handle this way of construction too.
	* @return this
	*/
	public function __construct( $params = array() ){
		$self_class_name = get_class( $this );
		$this->_table = Go_Factory::getDbTable( $self_class_name );
		parent::__construct( $params );
		$options = isset( $params[ 'data' ] ) ? $params[ 'data' ] : $params;
		foreach( $options as $property => $value ){
			if( !property_exists( $self_class_name, $property ) ) unset( $options[ $property ] );
		}
		$this->setOptions( $options );
		return $this;
	}
	
	/**
	* retrieve from DB data by specified 
	* @param $conditions - associative array of mysql conditions to retrieve necessary row
	*					   or scalar value of primary key
	* @return instance of Core_Model_Item or child
	*/
	public static function build( $conditions ){
		if( is_scalar( $conditions ) ){
			$object = self::getDbTable()->find( $conditions );
		} else {
			$object = self::getDbTable()->get( $conditions );
		}
		if( 1 != count( $object ) ){
			return null;
		} elseif( $object instanceof Zend_Db_Table_Rowset ){
			$object = $object[ 0 ];
		}
		return $object;
	}

	/**
	* reflect our incapsulated properties to _data field and run generic Zend_Db_Table_Row_Abstract::save() method
    * @return mixed The primary key value(s), as an associative array if the
    *		  key is compound, or a scalar if the key is single-column.
	*/
	public function save(){
		$this->_data = $this->_modifiedFields = $this->getOptions();
		return parent::save();
	}


    /**
    * Setters and getters routine decreasing
    *
    * @param string $method
    * @param array $args OPTIONAL Zend_Db_Table_Select query modifier
    * @return $this|void or Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract if call passed to parent
    * @throws Zend_Db_Table_Row_Exception If an invalid method is called.
    */
    public function __call( $method, array $args ){

        $matches = array();

        /**
         * recognize setter
         */
        if( preg_match( '/^set/', $method ) &&
			preg_match_all( '/([A-Z][a-z]*)/', $method, $matches ) ) {
            $property = strtolower( implode( '_', $matches[ 1 ] ) );
			$this->$property = $args[ 0 ];
            return $this;
        }

        /**
         * recognize getter
         */
        if( preg_match( '/^get/', $method ) &&
			preg_match_all( '/([A-Z][a-z]*)/', $method, $matches ) ) {
            $property = strtolower( implode( '_', $matches[ 1 ] ) );
            return $this->$property;
        }
		parent::__call( $method, $args );
    }

	/**
	* return self name
	* @return string representation of self
	*/
	public function __toString(){
		return $this->getName();
	}

	/**
	* set self fields from input array
	* @param $options - option => value array
	* @return $this
	*/
	public function setOptions( array $options ) {

		$methods = get_class_methods( $this );
		foreach( $options as $key => $value ) {
			if( !property_exists( get_called_class(), $key ) ) continue;
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
			$this->$method( $value );
		}
		return $this;
	}

	/**
	* return self fields values in associative array (useful for DB mapping)
	* @return array of self fields
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
	
	/**
	* duplicate parental method getTable, but make it static
	* @return Zend_Db_Table class or child
	*/
	public static function getDbTable(){
		return Go_Factory::getDbTable( get_called_class() );
	}
	
}

