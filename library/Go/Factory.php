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
		$class_name,					// class name to instatiate db_table
		array $params = array(),	// query params like order, limit and so on
		$fetch = true,					// should we fetch query and return Zend_Db_Table_Rowset
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
	* well, there is a corelation between class name and represented by it table name in DB
	* so let's get one from another
	* return instance of Zend_Db_Table with defined _name and _rowclass parameters and 
	*/
	public static function getDbTable( $class_name ){
//print( "class_name: " . $class_name . "<br /><br />" );

		$temp = str_replace( '_Model', '_Model_DbTable', $class_name );
		$db_table_class = self::plural( $temp );

		$row_class = class_exists( $class_name ) ? $class_name : "Core_Model_Item";
//print( "db_table_class: " . $db_table_class . "<br /><br />" );
//print( "row_class: " . $row_class . "<br /><br />" );
		if( !( class_exists( $db_table_class ) ) ){
			$table_name = self::getTableName( $class_name );

			$db_table = new Go_DbTable( array( 'name' => $table_name,
														  'rowClass' => $row_class ) );
			return $db_table;
		} else {
			return new $db_table_class();
		}
	}
	
	public static function save( Core_Model_Item $obj ){
		$data = $obj->getOptions();
		$table = self::getDbTable( get_class( $obj ) );
		$db = $table->getAdapter();
		//$db->getProfiler()->setEnabled( true );
		$config = array(
			'table'    => $table,
			'data'     => $data,
			'readOnly' => false,
			'stored'   => true
		);
		$primary_arr = $table->info( 'primary' );
		$primary = $primary_arr[ 1 ];

		if( false == $data[ $primary ] ){
			$data[ $primary ] = null;
			$config[ 'stored' ] = false;
		}

		$rowclass = $table->getRowClass();
		$row = new $rowclass( $config );
		$row->setFromArray( $data );
//		if( $obj->getEmail() == 'asd@asd.ru' ){ var_dump( $row->getDistributerId() );exit();}
		$row->save();
		if( true == $row->$primary ){
			return $row->$primary;
		} else {
			return $table->getAdapter()->getLastInstertId();
		}
	}
	
	public static function getTableName( $class_name ){
	
		return Zend_Registry::get( 'prefix' ) . self::plural( strtolower( str_replace( "_Model", "", $class_name ) ) );
	}
	
	public static function plural( $word ){
		$last_letter = substr( $word, strlen( $word ) - 1 );
		$last_2_letters = substr( $word, strlen( $word ) - 2 );
		if( $last_2_letters == "ss" ||
			 $last_2_letters == "us" ){
			return $word . "es";
		} elseif( $last_letter == "s" ){
			return $word;
		} elseif( $last_letter == "y" ){
			return substr( $word, 0, strlen( $word ) - 1 ) . "ies";
		} else {
			return $word . "s";
		}
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

