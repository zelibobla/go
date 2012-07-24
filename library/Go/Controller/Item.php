<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Go_Controller_CRUD extends Go_Controller_Action {

	protected $_module;
	protected $_resource;
	protected $_resources;
	protected $_item_class;
	protected $_item_id;
	protected $_item;
	protected $_voice;
	protected $_form_class;
	protected $_form;

	public function init(){
		parent::init();
		if( false == $this->_resource ){
			throw new Exception( 'Impossible to use Go_Controller_Item with undefined resource' );
		}
		if( false == $this->_resources ){
			$this->_resources = Go_Factory::plural( $this->_resource );
		}
		
		/**
		* voice definition
		*/
		$this->_module = $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		$voice = ucfirst( $module ) . "_Plugin_Voice";
		if( false == class_exists( $voice ) ){
			$core_voice = "Core_Plugin_Voice";
			if( false == class_exists( $core_voice ) ){
				throw new Exception( "Impossible to use Go_Controller_Item with no '$voice' or '$core_voice' classes defined" );
			} else {
				$this->_voice = $core_voice;
			}
		} else {
			$this->_voice = $voice;
		}

		/**
		* item_class definition
		*/
		if( false == $this->_item_class ){
			$item_class = ucfirst( $module ) . "_Model_" . ucfirst( $this->_resource );
			if( false == class_exists( $item_class ) ){
				throw new Exception( "Impossible to use Go_Controller_Item with no '$item_class' item class defined" );
			} else {
				$this->_item_class = $item_class;
			}
		}

		/**
		* form_class definition
		*/
		if( false == $this->_form_class ){
			$form_class = ucfirst( $module ) . "_Form_" . ucfirst( $this->_resource );
			if( false == class_exists( $form_class ) ){
				throw new Exception( "Impossible to use Go_Controller_Item with no '$form_class' form class defined" );
			} else {
				$this->_form_class = $form_class;
			}
		}
		
		/**
		* access check
		*/
		if( false == $this->_user ||
			 false == $this->_resource ||
			 false == $this->_allowed( $this->_resource, 'view' ) ){

			$voice::insufficientPrivileges();
			return $this->_helper->json( array( 'result' => false, 'html' => '' ) );
		}
	}

	/**
	* show items listing
	*
	*/
	public function indexAction() {

		$settings = $this->_user->getSettings();
		$resources = $this->_resources;
		$select = Go_Factory::selectTuned( $this->_item_class, $settings[ $resources . '_table' ][ 'columns' ] );
		$this->view->$resources = $this->getPaginator( $select );

	}
	
	protected function getPaginator( $select ){
		$paginator = Zend_Paginator::factory( $select )
							->setCurrentPageNumber( $this->getRequest()->getParam( 'page' ) )
							->setItemCountPerPage( 10 );
		$this->view->addScriptPath( APPLICATION_PATH . "/modules/core/views/scripts" )
					  ->addScriptPath( APPLICATION_PATH . "/modules/" . $this->_module . "/views/scripts" );

		Zend_View_Helper_PaginationControl::setDefaultViewPartial( 'pagination_control.phtml' );
		return $paginator;
	}

	/**
	* perform editing or adding
	*
	*/
	public function editAction() {
		
		$id = ( int ) $this->_request->getParam( 'id' );

		$this->_form = $form = new $this->_form_class( $id );
		$voice = $this->_voice;
		
		if( false == ( $data = $this->_request->getPost() ) ){
			return $this->afterFaultEdit();
		}

		if( false == $form->isValid( $data ) ){
			$voice::invalidData();
			return $this->afterFaultEdit();
		}

		if( false == $this->_allowed( $this->_resource, 'edit' ) ){
			$voice::insufficientPrivileges();
			return $this->afterFaultEdit();
		}

		if( false == $id ||
			 false == ( $item = Go_Factory::get( $this->_item_class, $id ) ) ){
			$item = new $this->_item_class();
		}

		$this->_item = $item->setOptions( Core_Plugin_Misc::sanitize( $form->getValues() ) )
							->setDateModified( date( "Y-m-d H:i:s", time() ) );

		$this->beforeSuccessEdit();
		$this->_item_id = $item->put();
	    $this->afterSuccessEdit();
	}

	protected function afterFaultEdit(){
		return $this->_helper->json( array( 'result' => false, 'html' => $this->_form->render() ) );		
	}

	protected function beforeSuccessEdit(){}

	protected function afterSuccessEdit(){
	    $voice = $this->_voice;
		$action = $this->_resource == $this->_module ? "edited" : $this->underscoreToCapital( $this->_resource ) . "Edited";
	    $voice::$action( $this->_item, !$this->_item->getId() );
	    return $this->_helper->json( array( 'result' => true ) );
	}

	/**
	* perform delete
	*
	*/
	public function deleteAction() {
		$voice = $this->_voice;

		if( false == ( $id = ( int ) $this->_request->getParam( 'id' ) ) || 
			 ( true ==  $id &&
			 	false == ( $item = Go_Factory::get( $this->_item_class, $id ) ) ) ){

			$voice::invalidData();
			return $this->_helper->json( array( 'result' => false ) );

		} elseif( false == $this->_allowed( $this->_resource, 'edit' ) ){

			$voice::insufficientPrivileges();
			return $this->_helper->json( array( 'result' => false ) );

		}
		
		$item->setIsActive( 'N' )
			  ->put();
    	$this->_item = $item;
    	$this->afterSuccessDelete();
		
	}

	public function afterSuccessDelete(){
		$voice = $this->_voice;
		$action = $this->_resource == $this->_module ? "deleted" : $this->underscoreToCapital( $this->_resource ) . "Deleted";
	    $voice::$action( $this->_item );
		return $this->_helper->json( array( 'result' => true ) );
	}
	
	private function underscoreToCapital( $str ){
		$words = explode( "_", $str );
		if( false == is_array( $words ) ) return $str;
		
		$word = reset( $words );
		$res = "";
		do{
			$res .= $word;
			$word = ucfirst( next( $words ) );
		} while( false === $word );
		$res .= $word;
		return $res;
	}
}

