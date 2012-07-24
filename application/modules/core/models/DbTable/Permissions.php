<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Model_DbTable_Permissions extends Zend_Db_Table_Abstract{
	
	public function init(){
		$this->_name = Zend_Registry::get( 'prefix' ) . "user_permissions";
	}

	/**
	 * return listing of all available resources
	 *
	 * @return Zend_Db_Table_Rowset
	 */
	public function referenceResources(){

		$select = $this->select()
					   ->from( array( $this->info( 'name' ) ), array( 'resource' ) )
					   ->distinct();

		$res = array();
		if( true == ( $rows = $this->fetchAll( $select ) ) ){
			foreach( $rows as $row ){
				$res[] = $row[ 'resource' ];
			}
		}
		return $res;
	}

	/**
	 * return listing of all available resources
	 *
	 * @return Zend_Db_Table_Rowset
	 */
	public function referenceRoles(){

		$select = $this->select()
					   ->from( array( $this->info( 'name' ) ), array( 'role' ) )
					   ->distinct();
		$res = array();
		if( true == ( $rows = $this->fetchAll( $select ) ) ){
			foreach( $rows as $row ){
				$res[] = $row[ 'role' ];
			}
		}
		return $res;
	}

}
