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
	protected $last_activity_at;
	protected $settings;

	/**
	* return icon filename
	*
	*/
	public function getIconPublicPath(){
		$path = '/uploads/user/' . $this->getId() . '/' /*. $this->getLogo()*/;

		if( false == $this->getId() ||
//			 false == $this->getLogo() ||
			 false == is_file( self::getStoragePath() . $this->getId() . '/' /*. $this->getLogo()*/ ) ){
			return "/uploads/user/no_icon.jpg"; 
		} else {
			return $path;
		}
	}

	/**
	* return path to files storage
	*
	*/
	public static function getStoragePath(){
		return APPLICATION_PATH . '/../public/uploads/user/';
	}

	/**
	* return is user linkable or not (depends of temporary field)
	*
	*/
	public function isLinkable(){
		return 'guest' == $this->getRole() ? false : true;
	}
	
	/**
	 * return profile route name
	 *
	 */
	public function getProfileRouteName(){
		return 'user_profile';
	}

	/**
	* the randomly generated part of salt for new user should be immediately stored in DB
	*
	*/
	public function generateRandomSalt(){
		$this->setPasswordSalt( Core_Plugin_Misc::generateRandomString( 4 ) );
		return $this;
	}

	/**
	* get part of salt sotred in application bootstrap, concatenate it with password and part of salt stored in DB
	* retrun md5 of result
	* this is the best way to protect password even if one of DB or code will be compromited
	*
	*/
	public function generatePasswordHash( $password ){
		$string = Zend_Registry::get( 'static_salt' ) .
					 $password .
					 $this->getPasswordSalt();
		$this->setPasswordHash( md5( $string ) );
		return $this;
	}
	
	public function getEmail() {
		return $this->email;
	}
	public function setEmail( $value ) {
		$this->email = $value;
		return $this;
	}
	public function getPasswordHash() {
		return $this->password_hash;
	}
	public function setPasswordHash( $value ) {
		$this->password_hash = $value;
		return $this;
	}
	public function getPasswordSalt() {
		return $this->password_salt;
	}
	public function setPasswordSalt( $value ) {
		$this->password_salt = $value;
		return $this;
	}
	public function getRole(){
		return $this->role;
	}
	public function setRole( $value ){
		$this->role = $value;
		return $this;
	}
	public function getLastActivityAt(){
		return $this->last_activity_at;
	}
	public function setLastActivityAt( $value ){
		$this->last_activity_at = $value;
		return $this;
	}
	public function getSettings(){
		return $this->settings;
	}
	public function setSettings( $value ){
		$this->settings = $value;
		return $this;
	}
}