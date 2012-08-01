<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Model_DbTable_Notifications extends Go_Db_Table{

	/**
	* fetch notifications by specified params
	* @param $params – array of two conditions: user_id – required integer; subject – optional string;
	* @return Zend_Db_Table_Rowset of Core_Model_Notification or null
	*/
	public function fetchVisibleBy( array $params ){
		if( false == $params[ 'user_id' ] ) return null;

		$select = $this->select()
					   ->where( 'owner_id = ?', ( int ) $params[ 'user_id' ] )
					   ->where( "is_active = 1 OR is_pinned = 1" )
					   ->order( 'created_at DESC' );
		if( @$params[ 'subject' ] ){
			$select->where( 'subject = ?', $subject );
		}
		return $this->fetchAll( $select );
	}
	
	/**
	* mark notifications unpinned according to specified conditions
	* @param $user_id – integer
	* @param $subject – string of notification subject
	* @return void
	*/
	public function unpin( $user_id, $subject ){
		$where = $this->getDbAdapter()->quoteInto( 'user_id = ?', ( int ) $user_id );
		if( $subject ) $where->quoteInto( 'subject = ?', $subject );
		$this->update( array( 'is_pinned' => 0 ), $where );
	}
}

