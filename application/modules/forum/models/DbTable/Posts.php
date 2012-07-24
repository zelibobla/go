<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Model_DbTable_Posts extends Zend_Db_Table_Abstract{
	
	public function init(){
		$this->_name = Zend_Registry::get( 'prefix' ) . "forum_posts";
		$this->setRowClass( 'Forum_Model_Post' );
	}

	/**
	* return most recent post where specified post_id is a parent
	*
	*/
	public function fetchFreshChildByPostId( $post_id ){
		$select = $this->select()
							->where( 'parent_id = ?', ( int ) $post_id )
							->order( 'date_modified DESC' )
							->limit( 1 );
		return $this->fetchRow( $select );
	}

	/**
	* return select children post for specified post_id
	*
	*/
	public function selectChildren( $post_id ){
		$select = $this->select()
							->where( 'parent_id = ?', ( int ) $post_id )
							->order( 'date_modified ASC' );
		return $select;
	}

  /**
  * return rowset of children for specified post_id
  *
  */
  public function fetchChildren( $post_id, $limit = null ){
      $select = $this->selectChildren( $post_id );
      if( $limit ){
          $select->limit( $limit );
      }
      return $this->fetchAll( $select );
  }
  
  
	/**
	* return select of self and children post for specified post_id
	*
	*/
	public function selectSelfAndChildren( $post_id ){
		$select = $this->select()
							->where( 'id = ?', ( int ) $post_id )
							->orWhere( 'parent_id = ?', ( int ) $post_id )
							->order( 'date_modified ASC' );
		return $select;
	}

	/**
	* return select of topics (posts with no parent) for specified category for future processing in Zend_Paginator
	*
	*/
	public function selectTopicsByCategoryId( $category_id ){
		$select = $this->select()
							->where( 'category_id = ?', ( int ) $category_id )
							->where( 'parent_id IS NULL OR
                        parent_id = 0' )
							->order( 'date_modified DESC' );//print( $select );
		return $select;
	}

	/**
	* return freshest post for specified category or null if no posts found
	*
	*/
	public function fetchFreshByCategoryId( $category_id ){
		$select = $this->select()
							->where( 'category_id = ?', ( int ) $category_id )
							->order( 'date_modified DESC' )
							->limit( 1 );
		return $this->fetchRow( $select );
	}

	/**
	* return posts quantity for specified post_id
	*
	*/
	public function countChildrenByPostId( $post_id ){
		$select = $this->select()
							->setIntegrityCheck( false )
							->from( $this->info( 'name' ), 'COUNT(*) as count' )
							->where( 'parent_id = ?', ( int ) $post_id );
		if( true == ( $row = $this->fetchRow( $select ) ) &&
			 true == ( $res = $row[ 'count' ] ) ){
			return $res;	 
		} else {
			return 0;
		}
	}

	/**
	* return posts quantity for specified category
	*
	*/
	public function countByCategoryId( $category_id ){
		$select = $this->select()
							->setIntegrityCheck( false )
							->from( $this->info( 'name' ), 'COUNT(*) as count' )
							->where( 'category_id = ?', ( int ) $category_id );
		if( true == ( $row = $this->fetchRow( $select ) ) &&
			 true == ( $res = $row[ 'count' ] ) ){
			return $res;	 
		} else {
			return 0;
		}
	}

}

