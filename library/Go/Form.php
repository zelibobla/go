<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Go_Form extends Zend_Form {

	
	protected $_item;
	
	public function __construct( $item = null ){
		$this->_item = $item;
		parent::__construct();
	}

	public function init(){

		$this->clearDecorators()
			  ->setElementDecorators( array (
					'viewHelper',
					'errors',
					array( array( 'field' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'field' ) ),
					'label',
					array( array( 'row' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'row' ) ),
			  ) )
			  ->setDecorators( array(
			  			'FormElements',
						array( array( 'title' => 'Description' ), array( 'tag' => 'legend', 'placement' => 'prepend' ) ),
						'Form',
			  		)
			  )
			  ->setAttrib( 'id', 'item_form' )
			  ->setAttrib( 'method', 'post' );
		
		if( true == ( $submit = $this->getElement( 'submit' ) ) ){
			$submit->removeDecorator( 'label' );
		}
		
		foreach( $this->getElements() as $element ){
			if( 'Zend_Form_Element_Hidden' == $element->getType() ){
				$element->setDecorators( array( 'ViewHelper' ) );
			} elseif( 'Zend_Form_Element_File' == $element->getType() ){
				$element->setDecorators(
					array( 'file' ),
					'errors',
					array( array( 'field' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'field' ) ),
					'label',
					array( array( 'row' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'row' ) )
				 );
			}
		}

		$this->addElementPrefixPath( 'Go_Validate_', 'Go/Validate', 'validate' );
	}
	
	/**
	* translate word be provided key or return key itself if no translator set or no value for key defined
	* @param $key – string key to search for the value in translator
	* @return string translation or key itself
	*/
	protected function _( $key ){
		try{
			$translator = Zend_Registry::get( 'translator' );
			return $translator->_( $key );
		} catch( Exception $e ){
			return $key;
		}
	}
}
