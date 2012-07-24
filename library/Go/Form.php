<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Go_Form extends Zend_Form {

	public function init(){

		$translateValidators = array( 	Zend_Validate_NotEmpty::IS_EMPTY => 'Пожалуйста, заполните это поле',
		                              	Zend_Validate_Regex::NOT_MATCH => 'Неверное значение',
		                              	Zend_Validate_StringLength::TOO_SHORT => 'Пожалуйста введите слово не короче %min% знаков',
		                              	Zend_Validate_StringLength::TOO_LONG => 'Пожалуйста введите слово не длиннее %max% знаков',
		                              	Zend_Validate_EmailAddress::INVALID => 'Неверный email адрес',
		                              	Zend_Validate_Alnum::NOT_ALNUM => "'%value%' должно содержать только числа и буквы",
		                              	Zend_Validate_Alnum::STRING_EMPTY => "Это поле не может быть пустым",
										Zend_Validate_EmailAddress::INVALID_FORMAT     => "'%value%' не может быть адресом электронной почты",
										Zend_Validate_EmailAddress::INVALID_HOSTNAME   => "не существует такого доменного имени '%hostname%'",
										Zend_Validate_EmailAddress::INVALID_LOCAL_PART => "не может быть такого субдомена '%localPart%'",
									);

		$translator = new Zend_Translate( 'array', $translateValidators );
		Zend_Validate_Abstract::setDefaultTranslator( $translator );

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
	}
}
