<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Model_DbTable_Notifications extends Zend_Db_Table_Abstract{
	
	public function init(){
		$this->_name = Zend_Registry::get( 'prefix' ) . "core_notifications";
		$this->setRowClass( 'Core_Model_Notification' );
	}

	public function fetchNotificationsToShowForUser( $user_id ){

		$select = $this->select()
							->where( 'owner_id = ?', $user_id )
							->where( "is_active = 'Y' OR is_pinned = 'Y'" )
							->order( 'date_created DESC' );
		return $this->fetchAll( $select );
	}

	public function fetchNotifications( array $params = array() ){

		$select = $this->select();
		
		foreach( $params as $column => $value ){
			if( false == $value ) continue;
			$select->where( "$column = ?", $value );
		}
		return $this->fetchAll( $select );
	}
}

