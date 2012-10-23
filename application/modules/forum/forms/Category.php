<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Form_Category extends Go_Form {
	
	protected $_category;
	
	public function __construct(
		Forum_Model_Category $category = null
	){
		$this->_category = true == $category ? $category : new Forum_Model_Category();
		parent::__construct();
	}

	public function init() {

		$this->addElement( 'hidden', 'id', array(
			'value'     => $this->_category->getId(),
		));

		$this->addElement( 'text', 'name', array(
			'required'	=> true,
			'label'		=> 'Название:',
			'value'		=> $this->_category->getName()
		));

		$this->addElement( 'textarea', 'description', array(
			'required'	=> true,
			'label'		=> 'Описание:',
			'value'		=> $this->_category->getDescription()
		));

		$this->setAction( "/forum/category/edit" );
		parent::init();

		$this->setAttrib( "class", "forum_category" );
	}
}