<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Model_DbTable_Bulletins extends Zend_Db_Table_Abstract{
	
	public function init(){
		$this->_name = Zend_Registry::get( 'prefix' ) . "forum_posts";
		$this->setRowClass( 'Forum_Model_Bulletin' );
	}

  /**
  * return rowset of last 5 bulletins
  *
  */
  public function fetchFresh(){
      $select = $this->select()
                     ->where( "is_active = ?", "Y" )
                     ->where( "category_id = ?", Forum_Model_Bulletin::FORUM_CATEGORY_ID )
                     ->where( "parent_id IS NULL OR parent_id = 0" )
                     ->order( "date_created DESC" )
                     ->limit( 5 );
     return $this->fetchAll( $select );
  }
}

