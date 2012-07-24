<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Form_Topic extends Go_Form {
	
	protected $_category;
	protected $_post;
	
	public function __construct(
		Forum_Model_Category $category,
		Forum_Model_Post $post = null
	){
		$this->_category = $category;
		$this->_post = true == $post ? $post : new Forum_Model_Post();
		parent::__construct();
	}

	public function init() {

		$this->addElement( 'hidden', 'id', array(
			'value'     => $this->_post->getId(),
		));

		$this->addElement( 'hidden', 'category_id', array(
			'value'     => $this->_category->getId(),
		));

		$this->addElement( 'text', 'name', array(
			'required'	=> true,
			'label'		=> 'Тема:',
			'value'		=> $this->_post->getName()
		));

		$this->addElement( 'select', 'owner_id', array(
			'label'			=> 'Автор:',
			'value'			=> $this->_post->getOwnerId(),
			'multiOptions'	=> Go_Factory::getDbTable( 'User_Model_User' )->fetchAsArray()
		));

		$this->addElement( 'text', 'date_created', array(
			'label'			=> 'Дата создания:',
			'value'			=> $this->_post->getDateCreated()
		));

		$this->addElement( 'textarea', 'body', array(
			'required'	=> true,
			'label'		=> 'Сообщение:',
			'value'		=> $this->_post->getBody()
		));

		$this->setAction( "/forum/post/edit" );
		parent::init();

		$this->setAttrib( "class", "forum_post" );
	}
}

?>
