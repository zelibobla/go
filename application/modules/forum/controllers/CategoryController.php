<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Forum_CategoryController extends Go_Controller_Action{
    
   /**
    * show listing of category topics
    *
	 */
	public function indexAction(){
		if( false == ( $category_id = ( int ) $this->getRequest()->getParam( 'id' ) ) ||
			 false == ( $category = Go_Factory::get( 'Forum_Model_Category', $category_id ) ) ){

			Forum_Plugin_Voice::undefinedCategory();
			return $this->_redirector->gotoRoute( array(), 'forum' );
		}
		$this->view->category = $category;
		$posts_select = $category->getTopicsSelect();
		$paginator = Zend_Paginator::factory( $posts_select )
							->setCurrentPageNumber( $this->getRequest()->getParam( 'page' ) )
							->setItemCountPerPage( 10 );
		$this->view->addScriptPath( APPLICATION_PATH . "/modules/core/views/scripts" );
		$this->view->addScriptPath( APPLICATION_PATH . "/modules/forum/views/scripts" );
		Zend_View_Helper_PaginationControl::setDefaultViewPartial( 'pagination_control.phtml' );
		$this->view->posts = $paginator;
	}

   /**
    * show edit category form or process this form if post data recieved
    *
	 */
	public function editAction(){

		if( true == ( $category_id = ( int ) $this->_request->getParam( 'id' ) ) &&
			 false == ( $category = Go_Factory::get( 'Forum_Model_Category', $category_id ) ) ){
			Forum_Plugin_Voice::invalidData();
			return $this->_helper->json( array( 'result' => false ) );
		} elseif( ( true == $category &&
					 	false == $this->_allowed( 'forum_category', 'edit' ) ) ||
					 ( false == $category_id &&
					 	false == $this->_allowed( 'forum_category', 'create' ) ) ){

			Forum_Plugin_Voice::insufficientPrivileges();
			return $this->_helper->json( array( 'result' => false ) );
		}

		$form = new Forum_Form_Category( $category );
		
		if( false == ( $data = $this->_request->getPost() ) ||
			 false == $form->isValid( $data ) ){
			return $this->_helper->json( array( 'result' => false, 'html' => $form->render() ) );
		}
		
		$category = new Forum_Model_Category( $form->getValues() );
		$category->put();
		
		Forum_Plugin_Voice::categoryEdited( $category );
		return $this->_helper->json( array( 'redirect' => $this->view->url( array(), 'forum' ),
														'result' => true ) );
	}

   /**
    * perform category delete
    *
	 */
	public function deleteAction(){

		if( false == ( $category_id = ( int ) $this->_request->getParam( 'id' ) ) ||
			 ( true == $category_id &&
			 	false == ( $category = Go_Factory::get( 'Forum_Model_Category', $category_id ) ) ) ){

			Forum_Plugin_Voice::invalidData();
			return $this->_helper->json( array( 'result' => false ) );

		} elseif( false == $this->_allowed( 'forum_category', 'edit' ) ){

			Forum_Plugin_Voice::insufficientPrivileges();
			return $this->_helper->json( array( 'result' => false ) );

		}
		
		$category->delete();
		
		Forum_Plugin_Voice::categoryDeleted( $category );
		return $this->_helper->json( array( 'redirect' => $this->view->url( array(), 'forum' ),
														'result' => true ) );
	}

}
