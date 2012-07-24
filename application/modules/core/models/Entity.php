<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Model_Entity extends Core_Model_Item {

	protected $id;
	protected $date_created;
	protected $date_modified;
	protected $owner_id;
	protected $is_active;
	
	public function init(){
		try{
			$user_id = Zend_Registry::get( 'user' )->getLogin();
		} catch ( Exception $e ) {
			$user_id = null;
		}
		if( false == $this->getId() ){
			$this->setOwnerId( $user_id )
				  ->setDateCreated( date( 'Y-m-d H:i:s', time() ) )
				  ->setDateModified( date( 'Y-m-d H:i:s', time() ) )
				  ->setIsActive( 'Y' );
		}
	}
	
	public function put(){
		$id = parent::put();
		$this->setId( $id );
		return $id;
	}
	
	public function getOwner(){
		return Go_Factory::get( "User_Model_User", $this->getOwnerId() );
	}

	public function getId(){
		return $this->id;
	}
	public function setId( $id ){
		$this->id = $id;
	}
	public function getDateCreated(){
		return $this->date_created;
	}
	public function setDateCreated( $date ){
		$this->date_created = $date;
		return $this;
	}
	public function getDateModified(){
		return $this->date_modified;
	}
	public function setDateModified( $date ){
		$this->date_modified = $date;
		return $this;
	}
	public function getOwnerId(){
		return $this->owner_id;
	}
	public function setOwnerId( $identity ){
		$this->owner_id = $identity;
		return $this;
	}
	public function getIsActive(){
		return $this->is_active;
	}
	public function setIsActive( $value ){
		if( true === $value ||
			 "Y" == $value ||
			 "y" == $value ||
			 0 != ( int ) $value ){
			$this->is_active = "Y";
		} elseif( false === $value ||
					 "N" == $value ||
					 "n" == $value ||
					 0 == ( int ) $value ){
			$this->is_active = "N";
		} else {
			throw new Exception( 'Core_Model_Entity is_active should be a char( "Y", "N" ) or boolean or integer' );
		}
		return $this;
	}
	public function setTableClass( $class_name ){
		$this->_tableClass = $class_name;
		return $this;
	}

}

