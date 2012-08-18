<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

/**
* Create, Read, Update and Delete typical controller
*/
class Go_Controller_CRUD extends Go_Controller_Default {

	protected $_module;
	protected $_resource;
	protected $_item_class;
	protected $_item_id;
	protected $_item;
	protected $_form_class;
	protected $_form;
	
	protected $_file_limit = 10;
	protected $_file_max_width = 200;
	protected $_file_max_height = 200;


	public function init(){
		parent::init();
		
		$this->_module = $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		$action = $this->_request->getParam( 'action' );
		$this->_resource = $this->view->resource = $this->_resource ?: strtolower( $module );		
				
		/**
		* item_class definition
		*/
		if( false == $this->_item_class ){
			$item_class = ucfirst( $module ) . "_Model_" . ucfirst( $this->_resource );
			if( false == class_exists( $item_class ) ){
				throw new Exception( "Impossible to use Go_Controller_Crud with no '$item_class' item class defined" );
			} else {
				$this->_item_class = $item_class;
			}
		}

		/**
		* form_class definition
		*/
		if( false == $this->_form_class &&
			'edit' == $action ){
			$form_class = ucfirst( $module ) . "_Form_" . ucfirst( $this->_resource );
			if( false == class_exists( $form_class ) ){
				throw new Exception( "Impossible to act Go_Controller_Crud::edit() with no '$form_class' form class defined" );
			} else {
				$this->_form_class = $form_class;
			}
		}

		/**
		* access check (user have to have view privilege at least)
		*/
		if( false == $this->_user ||
			false == $this->_resource ||
			false == $this->_isAllowed( $this->_resource, 'view' ) ){

			$this->_notify( $this->_( 'core_voice_insufficient_privileges' ) );
			return $this->_redirector->gotoRoute( array(), 'home' );
		}
	}

	/**
	* show items listing
	*/
	public function indexAction() {

		$settings = $this->_user->getSettings();
		$item_class = $this->_item_class;
		$select = $item_class::getDbTable()->select( array( 'is_active' => true ) );
		$this->view->items = $this->_getPaginator( $select );

	}
	
	/**
	* init paginator according to specified conditions
	* @param $select - instance of Zend_Db_Table_Select or string with MySQL select statement or array of items
	* @param $items_per_page - number of items to show on one page
	* @return Zend_Paginator instance
	*/
	protected function _getPaginator( $select, $items_per_page = 10 ){
		$paginator = Zend_Paginator::factory( $select )
							->setCurrentPageNumber( $this->getRequest()->getParam( 'page' ) )
							->setItemCountPerPage( ( int ) $items_per_page );
		$this->view->addScriptPath( APPLICATION_PATH . "/modules/core/views/scripts" )
				   ->addScriptPath( APPLICATION_PATH . "/modules/" . $this->_module . "/views/scripts" );

		Zend_View_Helper_PaginationControl::setDefaultViewPartial( 'pagination_control.phtml' );
		return $paginator;
	}

	/**
	* perform editing or adding item
	*/
	public function editAction() {
		$item_class = $this->_item_class; 

		if( false == $this->_isAllowed( $this->_resource, 'edit' ) ){
			$this->_notify( $this->_( 'core_voice_insufficient_privileges' ) );
			return $this->afterFaultEdit();
		}
		
		if( false == ( $id = ( int ) $this->_request->getParam( 'id' ) ) ||
			false == ( $item = $item_class::build( $id ) ) ){
			$item = new $this->_item_class();
		}
		$this->_form = $form = new $this->_form_class( $item );
		
		if( false == ( $data = $this->_request->getPost() ) ){
			return $this->afterFaultEdit();
		}

		if( false == $form->isValid( $data ) ){
			$this->_notify( $this->_( 'core_voice_invalid_data' ) );
			return $this->afterFaultEdit();
		}

		$item = $item ?: new $this->_item_class();
		$this->_item = $item->setOptions( $form->getValues() );
		if( property_exists( "updated_at", $item_class ) ){
			$item->setUpdatedAt( date( "Y-m-d H:i:s" ) );
		}

		$this->beforeSuccessEdit();
		$this->_item_id = $item->save();
	    $this->afterSuccessEdit();
	}

	protected function afterFaultEdit(){
		return $this->_helper->json( array( 'result' => false, 'html' => $this->_form->render() ) );
	}

	protected function beforeSuccessEdit(){}

	protected function afterSuccessEdit(){
		$this->indexAction();
		$this->view->no_layout = true;
		$controller = $this->_request->getParam( 'controller' );
		$html = $this->view->render( $controller . '/index.phtml' );
	    return $this->_helper->json( array( 'result' => true, 'html' => $html ) );
	}

	/**
	* perform delete
	*/
	public function deleteAction() {
		$item_class = $this->_item_class;

		if( false == ( $id = ( int ) $this->_request->getParam( 'id' ) ) || 
			 ( true ==  $id &&
			   false == ( $item = $item_class::build( $id ) ) ) ){

			$this->_notify( $this->_( 'core_voice_invalid_data' ) );
			return $this->_helper->json( array( 'result' => false ) );

		} elseif( false == $this->_isAllowed( $this->_resource, 'edit' ) ){

			$this->_notify( $this->_( 'core_voice_insufficient_privileges' ) );
			return $this->_helper->json( array( 'result' => false ) );

		}
		
		$item->setIsActive( 0 )
			 ->save();
    	$this->_item = $item;
    	$this->afterSuccessDelete();
	}

	public function afterSuccessDelete(){
		$action = $this->_resource == $this->_module ? "deleted" : Go_Misc::underscoreToCamel( $this->_resource ) . "Deleted";
		return $this->_helper->json( array( 'result' => true ) );
	}
	
	/**
	* standalone file upload handler (cause ajax doesn't support file uploads)
	*/
	public function fileAction() {
		$uploader = new Go_FileUploader( array( 'jpg', 'gif', 'png' ), $this->_file_limit * 1024 * 1024 );
		$path = APPLICATION_PATH . '/../public/uploads/' . $this->_module . '/';
		$result = $uploader->handleUpload( $path );

		$filename = $path . $this->_request->getParam( 'qqfile' );
		$new_filename = substr( $filename, 0, strlen( $filename ) - 3 ) . "jpg";
		if( true == $this->_file_max_width ||
			true == $this->_file_max_height ){
			list( $width, $height ) = getimagesize( $filename );

			if( $width > $this->_file_max_width &&
				$width > $height ){

				$proportion = $this->_file_max_width / $width;
				$do = true;
				
			} elseif( $height > $this->_file_max_height ){
				
				$proportion = $this->_file_max_height / $height;
				$do = true;
			}

			if( $do ){

				$new_width = ( int ) $width * $proportion;
				$new_height = ( int ) $height * $proportion;

				if( false == (	$image = Go_Misc::imageCreateFrom( $filename ) ) ||
					false == (	$resampled_image = imagecreatetruecolor( $new_width, $new_height ) ) ||
					false ==  imagecopyresampled(  $resampled_image, //dst_image
												   $image,			 //src_image
												   0,				 //dst_x
												   0,				 //dst_y
												   0,				 //src_x
												   0,				 //src_y
												   $new_width,		 //dst_w
												   $new_height,		 //dst_h
												   $width,			 //src_w
												   $height ) ||		 //src_h 
					false ==	imagejpeg( $resampled_image, $new_filename, 90 ) ){
					$this->_notify( $this->_( 'user_voice_image_resize_failed' ) );
				}
			}
		}
		return $this->_helper->json( $result );
	}
	
	/**
	* place fresh uploaded file to appropriate place
	* @param $filename – string
	* @param $cutarea – array of x1,y1,width,height params to cut from original image (optional)
	* @return void
	*/
	protected function _placeFile( $filename, array $cut_area = null ){

		$folder = APPLICATION_PATH . "/../public/uploads/" . $this->_module;
		if( true == is_file( $folder . "/" . $filename ) ){
			
			if( false == is_dir( $folder . "/" . $this->_item->getId() ) &&
				false == mkdir( $folder . "/" . $this->_item->getId() ) ){
			
				$this->_notify( sprintf( $this->_( 'core_voice_file_access_denied' ),
				 				APPLICATION_PATH . "../www/uploads/" . $this->_module ) );
			} else {

				$old_file = $folder . "/" . $filename;
				$new_file = $folder . "/" . $this->_item->getId() . "/" . $filename;


				if( is_array( $cut_area ) &&
				 	null !== ( $src_x = @$cut_area[ 'x1' ] ) &&
				 	null !== ( $src_y = @$cut_area[ 'y1' ] ) &&
					null !== ( $width = @$cut_area[ 'width' ] ) &&
					null !== ( $height = @$cut_area[ 'height' ] ) &&
					false != ( $image = Go_Misc::imageCreateFrom( $old_file ) ) &&
					false != ( $resampled_image = imagecreatetruecolor( $width, $height ) ) &&
					false != ( imagecopyresampled(  $resampled_image, //dst_image
													$image,			  //src_image
													0,				  //dst_x
													0,				  //dst_y
													$src_x,			  //src_x
													$src_y,			  //src_y
													$width,		 	  //dst_w
													$height,		  //dst_h
													$width,			  //src_w
													$height ) ) ){    //src_h 
					
					imagejpeg( $resampled_image, $new_file, 90 );

				} else {
					rename( $old_file, $new_file );
				}				
			}
			@unlink( $folder . "/" . $filename );
		}
	}

	/**
	* instantiate item and bring it into view
	* @return void
	*/
	public function profileAction(){
		$item_class = $this->_item_class;
		if( false == ( $id = ( int ) $this->_request->getParam( 'id' ) ) ||
			false == ( $this->view->item = $item_class::build( $id ) ) ){
			$this->_notify( $this->_( 'core_voice_invalid_data' ) );
			return $this->_redirector->gotoRoute( array(), 'home' );
		}
	}
}

