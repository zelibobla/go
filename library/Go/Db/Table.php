<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

/**
* just add some smart methods being needed often
*
*/
class Go_Db_Table extends Zend_Db_Table_Abstract {

	/**
	* set defaults useful in most cases
	* @return void
	*/
	public function init(){
		$converted = Go_Misc::singular( str_replace( "_DbTable", "", get_called_class() ) );
		$converted = !class_exists( $converted ) ||
		 			 'Go_Db_Table' == $converted
				   ? "Zend_Db_Table_Row"
				   : $converted;
		$this->setRowClass( $converted );
		parent::init();
	}

	/**
	* override empty parent method to set inflection: table class name correlating to class name
	* let's use it to set defaults
	* @return void
	*/
	protected function _setupTableName(){

        if( !$this->_name ){
            $this->_name = strtolower( Go_Misc::plural( str_replace( "_Model_DbTable", "", get_class( $this ) ) ) );
        }
        parent::_setupTableName();
    }

	/**
	* there is a clever method find in Zend Library but it works only for primary keys
	* let's write method to fetch data by any different of primary key column conditions
	* !warning: only conditions where params are equal to specified are available
	*			order and limit are reserved for order and limit in mysql statement
	*
	* @param params - array of conditions to specify mysql statement or string with ready mysql statement
	* @return Zend_Db_Table_Rowset || Zend_Db_Table_Row
	*/
	public function get( $params = null ){

		$select = $this->getSelect( $params );

		$rowset = $this->fetchAll( $select );
		$rows_number = count( $rowset );
		if( 0 == $rows_number ){
			return null;
		} elseif( 1 == $rows_number ){
			return $rowset[ 0 ];
		} else {
			return $rowset;
		}
	}

	/**
	* same as upper but only mysql select statement returned (useful for paginators)
	*
	* @param params - array of conditions to specify mysql statement or string with ready mysql statement
	* @return string Zend_Db_Table_Select object
	*/
	public function getSelect( $params = null ){

		if( is_string( $params ) ){
			$select = $this->select()->where( $params );
		} else {
			$select = $this->select();
			if( !empty( $params ) ){
				foreach( $params as $param => $value ){
					if( 'order' == $param ){
						if( is_array( $value ) ){
							foreach( $value as $order ){
								$select->order( $order );
							}
						} else {
							$select->order( $value );
						}
					} elseif( 'limit' == $param ) {
						$select->limit( $value );
					} else {
						$select->where( "$param = ?", $value );
					}
				}
			}
		}
		return $select;
	}
	
}
