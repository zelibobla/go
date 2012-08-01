<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Forum_PostController extends Go_Controller_Action{

	public function indexAction(){

		if( false == ( $post_id = ( int ) $this->getRequest()->getParam( 'id' ) ) ||
			 false == ( $post = Go_Factory::get( 'Forum_Model_Post', $post_id ) ) ) {
			Forum_Plugin_Voice::undefinedPost();
			return $this->_redirector->gotoRoute( array(), 'forum' );
		}
		// if post has a parent we are about to show all thread starting from the parent
		$topic = true == ( $parent = $post->getParent() ) ? $parent : $post;

		$posts_select = $topic->getSelfAndChildrenSelect();
		$paginator = Zend_Paginator::factory( $posts_select )
							->setCurrentPageNumber( $this->getRequest()->getParam( 'page' ) )
							->setItemCountPerPage( 10 );
		Zend_View_Helper_PaginationControl::setDefaultViewPartial( 'pagination_control.phtml' );
		$this->view->addScriptPath( APPLICATION_PATH . "/modules/core/views/scripts" );
		$this->view->addScriptPath( APPLICATION_PATH . "/modules/forum/views/scripts" );

		$this->view->category = $topic->getCategory();
		$this->view->posts = $paginator;
		$this->view->topic = $topic;
	}

	/**
	* return post edit form or perform this form
	*
	*/
	public function editAction(){

		if( ( true == ( $post_id = ( int ) $this->_request->getParam( 'id' ) ) &&
			   false == ( $post = Go_Factory::get( 'Forum_Model_Post', $post_id ) ) ) ||
			 ( true == ( $parent_id = ( int ) $this->_request->getParam( 'parent_id' ) ) &&
			 	false == ( $parent_post = Go_Factory::get( 'Forum_Model_Post', $parent_id ) ) ) ||
			 false == $parent_id ){
			Forum_Plugin_Voice::invalidData();
			return $this->_helper->json( array( 'result' => false, 'html' => '' ) );
		} else {

			$proto_post = new Forum_Model_Post( array( 'parent_id' => $parent_id, 'category_id' => $parent_post->getCategoryId() ) );
			if( true == $post ){
				$form = new Forum_Form_Post( $post );
			} else {
				$form = new Forum_Form_Post( $proto_post );
			}
		}
		
		if( false == ( $data = $this->_request->getPost() ) ){
			return $this->_helper->json( array( 'result' => false, 'html' => $form->render() ) );
		}

		if( false == $form->isValid( $data ) ){
			Forum_Plugin_Voice::invalidData();
			return $this->_helper->json( array( 'result' => false, 'html' => $form->render() ) );
		}

		$post = new Forum_Model_Post( $data );
		$id = $post->save();
		Forum_Plugin_Voice::postEdited( $post );
		return $this->_helper->json( array( 'result' => true,
														'id'=>$id,
														'redirect' => $this->view->url( array( 'id' => $id ), 'forum_post' ) ) );
	}
	
   /**
    * perform post delete
    *
	 */
	public function deleteAction(){

		if( false == ( $post_id = ( int ) $this->_request->getParam( 'id' ) ) || 
			 ( true ==  $post_id &&
			 	false == ( $post = Go_Factory::get( 'Forum_Model_Post', $post_id ) ) ) ){

			Forum_Plugin_Voice::invalidData();
			return $this->_helper->json( array( 'result' => false ) );

		} elseif( false == $post->isEditableFor( $this->_user ) ){

			Forum_Plugin_Voice::insufficientPrivileges();
			return $this->_helper->json( array( 'result' => false ) );

		}
		
		$post->delete();
		
		Forum_Plugin_Voice::postDeleted( $post );
		return $this->_helper->json( array( 'redirect' => $this->view->url( array( 'id' => $post->getParentId() ), 'forum_post' ),
														'result' => true ) );
	}

}
