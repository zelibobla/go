<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */
 
class Go_Factory {

	/**
	* get from database item by specified primary key
	*
	*/
	public static function get( $class_name, $identity ){
		if( true == ( $res = self::getDbTable( $class_name )->find( $identity ) ) &&
			 isset( $res[ 0 ] ) ){
			return $res->getRow( 0 );
		} else {
			return null;
		}
	}

	/**
	* get all content of DB table refered to specified class name
	* useful for any listings
	*
	*/
	public static function reference(
		$class_name,				// class name to instatiate db_table
		array $params = array(),	// query params like order, limit and so on
		$fetch = true,				// should we fetch query and return Zend_Db_Table_Rowset
									// or not and Zend_Db_Table_Select will be returned (useful for pagination purposes)
		$in_array  = false			// should we return result in array (rowset will be returned by default
	){

		$table = self::getDbTable( $class_name );

		$select = $table->select();
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

		if( true === $fetch ){
			$result = $table->fetchAll( $select );
			if( false == $in_array ){
				return $result;
			} else {
				$arr = array();
				foreach( $result as $row ){
					$arr[ $row->getId() ] = ( string ) $row;
				}
				return $arr;
			}
		} else {
			return $select;
		}
	}

	/**
	* suggest Db_Table class name based on current class name (usually corresponding is viewable)
	* @return string class name
	*/
	public static function getDbTable( $class_name ){

		$temp = str_replace( '_Model', '_Model_DbTable', $class_name );
		$db_table_class = Go_Misc::plural( $temp );

		$row_class = class_exists( $class_name ) ? $class_name : "Core_Model_Item";

		if( !( class_exists( $db_table_class ) ) ){
			$table_name = self::getTableName( $class_name );
			$db_table = new Go_Db_Table( array( 'name' => $table_name ) );
			$db_table->setRowClass( $row_class );
			return $db_table;
		} else {
			return new $db_table_class();
		}
	}
	
	public static function getTableName( $class_name ){
		return Zend_Registry::get( 'prefix' ) . Go_Misc::plural( strtolower( str_replace( "_Model", "", $class_name ) ) );
	}

	public static function selectTuned( $class_name, $settings ){

		$table = self::getDbTable( $class_name );
		if( method_exists( $table, 'selectTuned' ) ) return $table->selectTuned( $settings );
		$select = $table->select();
		if( count( $settings ) ){
			foreach( $settings as $field => $setting ){

				if( true == ( $filter = $setting[ 'filter' ] ) ){

					if( 'string' == $filter[ 'type' ] ){
						$select->where( "$field LIKE ?", "%{$filter[ 'value' ][ 'str' ]}%" );
					} elseif( 'num' == $filter[ 'type' ] ){
						if( true == ( $min = $filter[ 'value' ][ 'min' ] ) ){
							$select->where( "$field >= ?", ( int ) $min );
						}
						if( true == ( $max = $filter[ 'value' ][ 'max' ] ) ){
							$select->where( "$field <= ?", ( int ) $max );
						}
					} elseif( 'enum' == $filter[ 'type' ] ){
						if( true == ( $enum = $filter[ 'value' ][ 'enum' ] ) ){
							$select->where( "$field = ?", $enum );
						}
					}
			
				}
				if( true == ( $order = $setting[ 'order' ] ) ){
					$select->order( "$field $order" );
				}
			}
		}
//print( $select );exit();
		$select->where( 'is_active = ?', 'Y' );
		return $select;
	}

	public function count( $class_name, array $params = null ){

		$select = self::reference( $class_name, $params, $fetch = false );
		$table = self::getDbTable( $class_name );

		$select->from( $table, array( "COUNT( * ) as quantity" ) );
		if( true == ( $row = $table->fetchRow( $select ) ) ){
			return ( int ) $row->quantity;
		} else {
			return 0;
		}
	}

}

