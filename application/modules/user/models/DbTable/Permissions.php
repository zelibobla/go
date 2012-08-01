<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Model_DbTable_Permissions extends Go_Db_Table{

	/**
	* return listing of all available resources
	* @return Zend_Db_Table_Rowset
	*/
	public function referenceResources(){

		$select = $this->select()
					   ->distinct()
					   ->from( array( $this->info( 'name' ) ), array( 'resource' ) );

		$res = array();
		if( true == ( $rows = $this->fetchAll( $select ) ) ){
			foreach( $rows as $row ){
				$res[] = $row[ 'resource' ];
			}
		}
		return $res;
	}
}
