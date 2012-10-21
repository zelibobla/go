<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

/**
* generic core class to handle Core_Model_Entities history
* (used to handle undo operations)
*/
class Core_Model_History extends Core_Model_Item {

	protected $id;
	protected $entity_id;
	protected $entity_class;
	protected $entity_options;
	protected $created_at;
	
	/**
	* put into DB entity state
	* @return void
	*/
	public static function saveState( Core_Model_Entity $entity ){
		if( false == $entity->getId() ) throw new Exception( "Can't save history state for entity with undefined id" );
		$state = new Core_Model_History( array( 'entity_id' => $entity->getId(),
		 										'entity_class' => get_class( $entity ),
		 										'entity_options' => $entity->getOptions(),
		 										'created_at' => date( "Y-m-d H:i:s" ) ) );
		$state->save();
	}
	
	/**
	* get from DB last entity state; remove this state from history; save entity;
	* @param $entity – Core_Model_Entity instance of entity wich previous state is being looked for
	* @return Core_Model_Entity child instance or null if no previous state found for specified entity
	*/
	public static function savePreviousState( Core_Model_Entity $entity ){
		if( true == ( $pre_entity = self::getPreviousState( $entity ) ) )
			return $pre_entity->save( $skip_history = true );
	}

	/**
	* get from DB last entity state; remove this state from history;
	* @param $entity – Core_Model_Entity instance of entity wich previous state is being looked for
	* @return Core_Model_Entity child instance or null if no previous state found for specified entity
	*/
	public static function getPreviousState( Core_Model_Entity $entity ){
		if( false == $entity->getId() ) throw new Exception( "Can't retrieve history state for entity with undefined id" );
		$class_name = get_class( $entity );
		$states = Core_Model_History::getDbTable()->fetchBy( array( 'entity_class' => $class_name,
						 										   	'entity_id' => $entity->getId(),
						 											'order' => "created_at DESC",
																	'limit' => 2 ) );

		if( 2 == count( $states ) ){
			$current_state = $states[ 0 ];
			$current_state->delete();
			$previous_state = $states[ 1 ];
			$result = $entity->setOptions( $previous_state->getEntityOptions() );
		}
		return $result;
	}
	
	/**
	* clear all entities states older than specified period
	* @param $lifetime - (optional) integer lifetime in seconds
	* @return void
	*/
	public static function clear( $lifetime ){
		$where = self::getDbTable()->getAdapter()->quoteInto( 'created_at < ?', date( "Y-m-d H:i:s", time() - $lifetime ) );
		self::getDbTable()->delete( $where );
	}
}

