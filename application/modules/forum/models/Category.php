<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Model_Category extends Core_Model_Entity{

	protected $description;

	/**
	* return topics of current category
	*
	*/
	public function getTopicsSelect(){
		return Go_Factory::getDbTable( 'Forum_Model_Post' )->selectTopicsByCategoryId( $this->getId() );
	}

	/**
	* return number of posts for current category
	*
	*/
	public function getPostsCount(){
		return Go_Factory::getDbTable( 'Forum_Model_Post' )->countByCategoryId( $this->getId() );
	}

	/**
	* return freshest post for current category
	*
	*/
	public function getFreshPost(){
		return Go_Factory::getDbTable( 'Forum_Model_Post' )->fetchFreshByCategoryId( $this->getId() );
	}
	
	public function getDescription() {
		return $this->description;
	}
	public function setDescription( $description ) {
		$this->description = htmlspecialchars( ( string ) $description );
		return $this;
	}

}
