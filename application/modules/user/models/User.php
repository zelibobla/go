<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class User_Model_User
	extends Core_Model_Entity
	implements Go_Interface_Linkable,
			   Go_Interface_Iconable{

	protected $email;
	protected $password_hash;
	protected $password_salt;
	protected $role;
	protected $active_at;
	protected $settings;
	protected $photo;

	/**
	* return if self is editable for specified user
	* @return boolean
	*/
	public function isEditableFor( User_Model_User $user ){
		return $user->getId() == $this->getId() ||
			   Zend_Registry::get( 'acl' )->isAllowed( $user->getRole(), 'user', 'edit' );
	}

	/**
	* return is user linkable or not
	* @return boolean
	*/
	public function isLinkable(){
		return 'guest' == $this->getRole() ? false : true;
	}

	/**
	* generate random string of four symbols and put it into self::password_salt field
	* @return $this
	*/
	public function generateRandomSalt(){
		$this->setPasswordSalt( Go_Misc::generateRandomString( 4 ) );
		return $this;
	}

	/**
	* generate encrypted password and put encryption result to self::password_hash field
	* !warning: you can use blank password to your own responsibility
	* @param $password - string of password
	* @return $this
	*/
	public function generatePasswordHash( $password ){
		$this->setPasswordHash( md5( Zend_Registry::get( 'static_salt' ) . $password . $this->getPasswordSalt() ) );
		return $this;
	}
	
	/**
	* return icon filename counting from webroot folder
	* @return string
	*/
	public function getIconWebPath(){
		return "/uploads/account/{$this->getId()}/{$this->getPhoto()}";
	}

	/**
	* return path to files storage
	* @return string
	*/
	public static function getStoragePath(){
		return APPLICATION_PATH . "/../public/uploads/profile";
	}

}