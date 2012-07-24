<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Forum_TopicController extends Go_Controller_Action{

   /**
    * show edit topic form or process this form if post data recieved
    *
	 */
	public function editAction(){

		if( false == ( $category_id = ( int ) $this->_request->getParam( 'category_id' ) ) ||
			 false == ( $category = Go_Factory::get( 'Forum_Model_Category', $category_id ) ) ){
			Forum_Plugin_Voice::invalidData();
			return $this->_helper->json( array( 'result' => false ) );
		}
		if( true == ( $post_id = ( int ) $this->_request->getParam( 'id' ) ) &&
			 false == ( $post = Go_Factory::get( 'Forum_Model_Post', $post_id ) ) ){

			Forum_Plugin_Voice::invalidData();
			return $this->_helper->json( array( 'result' => false ) );

		} elseif( ( true == $post &&
					 	false == $post->isEditableFor( $this->_user ) ) ||
					 ( false == $post_id &&
					 	false == $this->_allowed( 'forum_post', 'create' ) ) ){

			Forum_Plugin_Voice::insufficientPrivileges();
			return $this->_helper->json( array( 'result' => false ) );
		}

		$form = new Forum_Form_Topic( $category, $post );
		
		if( false == ( $data = $this->_request->getPost() ) ||
			 false == $form->isValid( $data ) ){
			return $this->_helper->json( array( 'result' => false, 'html' => $form->render() ) );
		}
		
		$topic = new Forum_Model_Post( $form->getValues() );
		$topic->put();
		
		Forum_Plugin_Voice::topicEdited( $topic );
		return $this->_helper->json( array( 'redirect' => $this->view->url( array( 'id' => $form->getValue( 'category_id' ) ), 'forum_category' ),
														'result' => true ) );
	}

   /**
    * perform topic delete
    *
	 */
	public function deleteAction(){

		if( false == ( $topic_id = ( int ) $this->_request->getParam( 'id' ) ) ||
			 ( true == $topic_id &&
			 	false == ( $topic = Go_Factory::get( 'Forum_Model_Post', $topic_id ) ) ) ){

			Forum_Plugin_Voice::invalidData();
			return $this->_helper->json( array( 'result' => false ) );

		} elseif( false == $topic->isEditableFor( $this->_user ) ){

			Forum_Plugin_Voice::insufficientPrivileges();
			return $this->_helper->json( array( 'result' => false ) );

		}
		
		$topic->delete();
		
		Forum_Plugin_Voice::topicDeleted( $topic );
		return $this->_helper->json( array( 'redirect' => $this->view->url( array( 'id' => $topic->getCategoryId() ), 'forum_category' ),
														'result' => true ) );
	}

}
