<?
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Form_Post extends Go_Form {
	
	protected $_post;
	
	public function __construct( Forum_Model_Post $post = null ){
		if( true == $post ){
			$this->_post = $post;
		} else {
			$this->_post = new Forum_Model_Post();
		}
		parent::__construct();
	}

	public function init() {

		$this->addElement( 'hidden', 'id', array(
			'value'      => $this->_post->getId(),
		));
		$this->addElement( 'hidden', 'category_id', array(
			'required'	 => true,
			'value'      => $this->_post->getCategoryId(),
		));
		$this->addElement( 'hidden', 'parent_id', array(
			'required'	 => true,
			'value'      => $this->_post->getParentId(),
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
			'required'		=> true,
			'value'			=> $this->_post->getBody()
		));

		$this->setAction( "/forum/post/edit" );
		parent::init();
		$this->setAttrib( "class", "forum_post" );
	}
}

?>
