<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Model_DbTable_Users extends Go_Db_Table{

	/**
	* remove guest who was not active after provided date in Y-m-d H:i:s format
	* @return void
	*/
	public function removeGuestsInactiveFrom( $date ){

		$where = $this->getAdapter()->quoteInto( "role = 'guest' AND ( active_at < ? OR active_at IS NULL )", $date );
		return $this->delete( $where );
	}
}
