<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Model_Bulletin extends Forum_Model_Post{

	protected $type_id;
	protected $region_id;

  const FORUM_CATEGORY_ID = 23;
  const EDIT_TIMEOUT = 900;

  /**
	* return if post can be edited by specified user
	*
	*/
	public function isEditableFor( User_Model_User $user ){
		if( false == $this->getOwnerId() ){
			return Zend_Registry::get( 'acl' )->isAllowed( $user->getRole(), 'bulletin', 'create' );
		} elseif( $this->getOwnerId() == $user->getId() ){ 
      if( self::EDIT_TIMEOUT < ( time() - strtotime( $this->getDateCreated() ) ) ){
            return false;
      } else {
        return Zend_Registry::get( 'acl' )->isAllowed( $user->getRole(), 'bulletin', 'edit' );
      }
		} else {
			return Zend_Registry::get( 'acl' )->isAllowed( $user->getRole(), 'forum_post_foreign', 'edit' );
		}
	}
  
	public function getTypeId() {
		return $this->type_id;
	}
	public function setTypeId( $id ) {
		$this->type_id = ( int ) $id;
		return $this;
	}
	public function getRegionId() {
		return $this->region_id;
	}
	public function setRegionId( $id ) {
		$this->region_id = ( int ) $id;
		return $this;
	}

}
