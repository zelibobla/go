<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Model_Post extends Core_Model_Entity{

	protected $category_id;
	protected $parent_id;
	protected $body;

	/**
	* return if post can be edited by specified user
	*
	*/
	public function isEditableFor( User_Model_User $user ){
		if( false == $this->getOwnerId() ){
			return Zend_Registry::get( 'acl' )->isAllowed( $user->getRole(), 'forum_post', 'create' );		
		} elseif( $this->getOwnerId() == $user->getId() ){
			return Zend_Registry::get( 'acl' )->isAllowed( $user->getRole(), 'forum_post', 'edit' );
		} else {
			return Zend_Registry::get( 'acl' )->isAllowed( $user->getRole(), 'forum_post_foreign', 'edit' );
		}
	}

	/**
	* return object of category
	*
	*/
	public function getCategory(){
		return Go_Factory::get( 'Forum_Model_Category', $this->getCategoryId() );
	}

	/**
	* return select of self and children
	*
	*/
	public function getSelfAndChildrenSelect(){
		return Go_Factory::getDbTable( 'Forum_Model_Post' )->selectSelfAndChildren( $this->getId() );
	}

  /**
	* return rowset of self and children
	*
	*/
	public function getChildrenSelect(){
		return Go_Factory::getDbTable( 'Forum_Model_Post' )->selectChildren( $this->getId() );
	}

  /**
  * return rowset of children
  *
  */
  public function fetchChildren( $limit = null ){
      return Go_Factory::getDbTable( 'Forum_Model_Post' )->fetchChildren( $this->getId(), $limit );
  }
  
	/**
	* return children quantity for current post
	*
	*/
	public function getChildrenCount(){
		return Go_Factory::getDbTable( 'Forum_Model_Post' )->countChildrenByPostId( $this->getId() );
	}

	/**
	* return most recently created post with current post as a parent
	*
	*/
	public function getFreshChild(){
		return Go_Factory::getDbTable( 'Forum_Model_Post' )->fetchFreshChildByPostId( $this->getId() );
	}

	/**
	* return parent post if exists
	*
	*/
	public function getParent(){
		if( false == $this->parent_id ) return null;
		return Go_Factory::get( 'Forum_Model_Post', $this->getParentId() );
	}
	
	/**
	* override magic method __toSting; post object should display body fragment instead of name
	*
	*/
	public function __toString(){
		if( 60 < strlen( $this->getBody() ) ){
			return mb_substr( $this->getBody(), 0, 60, 'utf-8' ) . "...";
		} else {
			return $this->getBody();
		}
	}

	/**
	* return short post teaser
	*
	*/
	public function getTeaser(){
		$string = true == $this->getName() ? $this->getName() : $this->getBody();
		return strlen( $string ) < 60 ? $string : mb_substr( $string, 0, 60, 'utf-8' ) . "...";
	}

	public function getCategoryId() {
		return $this->category_id;
	}
	public function setCategoryId( $id ) {
		$this->category_id = ( int ) $id;
		return $this;
	}
	public function getParentId() {
		return $this->parent_id;
	}
	public function setParentId( $id ) {
		$this->parent_id = ( int ) $id;
		return $this;
	}
	public function getBody() {
		return $this->body;
	}
	public function setBody( $body ) {
		$this->body = htmlspecialchars( ( string ) $body );
		return $this;
	}

}
