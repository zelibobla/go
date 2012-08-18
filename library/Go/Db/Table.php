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
			$name = str_replace( "_Model_DbTable", "", get_class( $this ) );
			list( $module, $item ) = explode( "_", $name );
			if( preg_match_all( '|[A-Z]+([a-z])*|', $item, $matches ) &&
				count( $matches[ 0 ] > 1 ) ){
				$item = implode( "_", $matches[ 0 ] );
				if( "R" != end( $matches[ 0 ] ) ){
					$item = Go_Misc::plural( $item ); 
				}
			}
			$this->_name = strtolower( $module . "_" . $item );

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
	* reference is a method that brings a reference data from DB in array value, where key is value_id in DB and value - is value name
	* useful in select and multiselect form elements to reference available values
	* @param $params – array of conditions to specify mysql statemnt or string with ready mysql statement
	* @param $translator - array of row->value translations
	* @param $section_prefix – if row->value is `skills` for example and section_prefix is `user_` then `user_skill` will be searched in
	*						   tranlsations table
	* @return array [ value_key ] => value_name
	*/
	public function reference( $params = null, $translator = null, $translator_prefix = null ){
		
		$select = $this->select( $params );
		$rowset = $this->fetchAll( $select );
		$res = array();
		foreach( $rowset as $row ){
			$value = $translator ? $translator->_( "{$translator_prefix}$row->name" ) : $row->name;
			if( isset( $row->id ) ){
				$res[ $row->id ] = $value;
			} else {
				$res[ $row->name ] = $value;
			}
		}
		return $res;
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
	
	/**
	* catch method saveRelations, parse what to what relations to be saved and call for appropriate method
	* @return void
	*/
    public function __call( $method, array $args ){

        $matches = array();

        /**
         * recognize many to many or one to many save attempt
         */
        if( preg_match( '/^save/', $method ) &&
			preg_match_all( '/([A-Z][a-z]*)/', $method, $matches ) ) {
			if( 'Relations' == $matches[ 0 ][ 0 ] ){
				$classname = get_class( $this );
				$ending = substr( $classname, strrpos( $classname, "_" ) + 1 );

				if( !preg_match_all( '/([A-Z][a-z]*)/', $ending, $matches ) ||
					!$matches[ 0 ][ 0 ] ||
					!$matches[ 0 ][ 1 ] )
					throw new Exception( "Unable to save relations: table class name ending should contain two keys \
										  described in itself reference map" );

				$this->saveRelations( Go_Misc::singular( $matches[ 0 ][ 0 ] ),
									  Go_Misc::singular( $matches[ 0 ][ 1 ] ),
									  $args );
			}
        }
    }

	/**
	* save relations in specified relations table
	* @param $one_key – string, the key of reference map referenced to one item table params
	* @param $many_key – string, the key of reference map referenced to many items table params
	* @param $args – array consist of at least of two keys: one_id, array of many_ids
	*/
	private function saveRelations( $one_key, $many_key, array $args ){

		$one_index = $this->_referenceMap[ $one_key ][ 'columns' ][ 0 ];
		$many_index = $this->_referenceMap[ $many_key ][ 'columns' ][ 0 ];
		$many_ref_index = $this->_referenceMap[ $many_key ][ 'refColumns' ];

		/**
		* remove all relations of specified one item and add new relations
		*/
		$where = $this->getAdapter()->quoteInto( $one_index . " = ?", $args[ 0 ] );
		$this->delete( $where );
		if( null == $args[ 1 ] ) return;
		
		/**
		* only valid many_items can be related, so filter income array: leave only those that are exists in DB
		*/
		$many_table_class = $this->_referenceMap[ $many_key ][ 'refTableClass' ];
		$many_table = new $many_table_class();
		$select = $many_table->select()->where( $many_ref_index .
												" IN ( '" . implode( "','", $args[ 1 ] ) . "' )" );

		foreach( $many_table->fetchAll( $select ) as $many_item ){
			$row = $this->createRow( array( $one_index => $args[ 0 ], $many_index => $many_item[ $many_ref_index ] ) )
						->save();

		}

	}
}
