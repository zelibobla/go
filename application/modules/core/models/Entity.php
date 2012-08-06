<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

/**
* generic core class being direct parent of most rich entities of the application
* like a user for example
*/
class Core_Model_Entity extends Core_Model_Item {

	protected $id;
	protected $created_at;
	protected $updated_at;
	protected $owner_id;
	protected $is_active;
	
	/**
	* kind of construct; define entity defaults
	* @return void
	*/
	public function init(){
		if( Zend_Registry::isRegistered( 'user' ) ){
			$user_id = Zend_Registry::get( 'user' )->getId();
		} else {
			$user_id = null;
		}
		if( false == $this->getId() ){
			$this->setOwnerId( $user_id )
				 ->setCreatedAt( date( 'Y-m-d H:i:s', time() ) )
				 ->setUpdatedAt( date( 'Y-m-d H:i:s', time() ) )
				 ->setIsActive( 1 );
		}
	}
	
	/**
	* get owner of current entity 
	* @return User_Model_User or null
	*/
	public function getOwner(){
		if( false == $this->getOwnerId() ) return null;
		return User_Model_User::build( $this->getOwnerId() );
	}

	/**
	* return icon filename counting from webroot folder
	* @return string
	*/
	public function getIconWebPath(){
		$entity_name = $this->getEntityName();
		if( false == $this->getId() ||
			false == is_file( self::getStoragePath() . $this->getId() . '/' ) ){
			return "/uploads/$entity_name/no_icon.jpg";
		} else {
			$path = "/uploads/$entity_name/{$this->getId()}/" . $this->getPhoto();
			return $path;
		}
	}

	/**
	* return path to files storage
	* @return string
	*/
	public static function getStoragePath(){
		return APPLICATION_PATH . "/../public/uploads/" . self::getEntityName() ;
	}

	/**
	* return profile route name
	* @return string
	*/
	public function getProfileRouteName(){
		return "{$this->getEntityName()}_profile";
	}

	/**
	* return exactly entity class name ( last part from all class name)
	* @return string
	*/
	protected static function getEntityName(){
		$class_name = get_called_class();
		return strtolower( substr( $class_name, strrpos( $class_name, '_' ) + 1 ) );
	}
}

