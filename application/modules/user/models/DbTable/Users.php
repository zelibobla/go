<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Model_DbTable_Users extends Zend_Db_Table_Abstract{
	
	public function init(){
		$this->_name = Zend_Registry::get( 'prefix' ) . "user_users";
		$this->setRowClass( 'User_Model_User' );
	}

	/**
	* fetch all active users and convert result in array with pair id => name
	* using later in forms selectboxes
	*
	*/
	public function fetchAsArray(){
		$select = $this->select()->where( "is_active = 'Y'" )
										 ->where( "role != 'guest'" );
		$result = array();
		if( true == ( $rows = $this->fetchAll( $select ) ) ){
			foreach( $rows as $row ){
				$result[ $row->getId() ] = $row->getName();
			}
		}
		return $result;
	}

	/**
	* return mysql expression selects all active users to process it later in paginator
	*
	*/
	public function selectActive(){
		return $this->select()->where( "is_active = 'Y'" )
							  ->where( "role != 'guest'");
	}

	public function removeInactiveGuests(){
		$db = $this->getAdapter();
		$this->delete( array( $db->quoteInto( "date_last_activity < ?", date( "Y-m-d H:i:s", time() - 86400 ) ), 
							  $db->quoteInto( "role = ?", "guest" ) ) );
		return true;
	}

	public function fetchByEmail( $email ){
		$select = $this->select()->where( "email = ?", $email );
		return $this->fetchRow( $select );
	}
}

