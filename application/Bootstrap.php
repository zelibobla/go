<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

/**
* instantiate system defaults, attach any necessary data, run
* !warning: methods order has meaning, don't change it if not sure
*/
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	/**
	* define globals
	* @return void
	*/
	protected function _initGlobals(){
		
		// salt part for good user passwords protection (another part is in DB)
		Zend_Registry::set( 'static_salt', 'here_we_go!' );

		// init default basics
		$defaults = $this->getOption( 'defaults' );
		Zend_Registry::set( 'defaults', @$defaults );

		// init database
		$resources = $this->getOption( 'resources' );
		Zend_Registry::set( 'prefix', @$resources[ 'db' ][ 'params' ][ 'prefix' ] );
		$this->bootstrap( 'db' );

		// init application modules
		$this->bootstrap( 'modules' );
		
		// init views
		$this->bootstrap( 'view' );
	
	}
	
	/**
	* define user even if not logged (see readme in application root folder for details )
	* @return void
	*/
	protected function _initUser(){

		/**
		* if stored session exists
		*/
		$auth = Zend_Auth::getInstance();
    	if( $auth->hasIdentity() ){
			if( true == ( $user = User_Model_User::build( array( 'email' => $auth->getIdentity() ) ) ) ){
				Zend_Registry::set( 'user', $user );
				$user->setActiveAt( date( "Y-m-d H:i:s" ) )
					 ->save();
				return;
			// if session is exists but we couldn't retrieve user from DB:
			} else {
				$auth->clearIdentity();
			}
		}

		/**
		* if user saved in cookies
		*/
		if( true == ( $hash = addslashes( @$_COOKIE[ 'hash' ] ) ) &&
		 	$user = User_Model_User::build( array( 'password_hash' => $hash ) ) ){
			
			$auth->getStorage()->write( $user->getEmail() );
			$user->setActiveAt( date( "Y-m-d H:i:s" ) )
				 ->save();
			Zend_Registry::set( 'user', $user );
			return;
		}

		/**
		* if not logged, generate temporary user and login him automatically
		*/
		$password = Go_Misc::generateRandomString( 6 );
		$email = Go_Misc::generateRandomString( 9 );
		$user = new User_Model_User( array(	'email'			=> $email,
											'name'			=> 'noname',
											'role'			=> 'guest' ) );
		$id = $user->generateRandomSalt()
				   ->generatePasswordHash( $password )
				   ->save();
   		$user->setId( $id );
		$adapter = Core_Plugin_Misc::getAuthAdapter()->setIdentity( $email )
                                               		 ->setCredential( $password );

		$result = $auth->authenticate( $adapter );
		if( false == $result->isValid() ){
			throw new Exception( 'Unable to create and authenticate temporary user' );
		}
		Zend_Registry::set( 'user', $user );
	}

	/**
	* retrieve access rights table and put it in registry
	* @return void
	*/
	protected function _initAcl(){

		if( Zend_Auth::getInstance()->hasIdentity() ){
		   Zend_Registry::set( 'acl', Core_Plugin_Misc::getAcl() );
		}
	}

	/**
	* pull XML structure of general menu
	* @return void
	*/
	protected function _initNavigation(){

		$menu_config = new Zend_Config_Xml( APPLICATION_PATH . '/configs/navigation.xml', 'nav' );

		if( Zend_Auth::getInstance()->hasIdentity() ){
		   $acl = Zend_Registry::get( 'acl' );
		   $role = Zend_Registry::get( 'user' )->getRole();
		}

		$this->view->navigation()->setAcl( $acl )
								 ->setRole( $role )
								 ->setContainer( new Zend_Navigation( $menu_config ) );

	}
	
	/**
	* define routes
	* @return void
	*/
	protected function _initRoutes(){

		$router = Zend_Controller_Front::getInstance()->getRouter();
		$config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/routes.ini' );      
		$router->addConfig( $config, 'routes' );
	}

	/**
	* define locales
	* @return void
	*/
	protected function _initLocale(){
		try{
			$settings = Zend_Registry::get( 'user' )->getSettings();
			$locale = @$settings[ 'language' ];
		} catch( Exception $e ){
			$locale = 'en_EN';
		}
		if( false == $locale ) $locale = 'ru_RU';
		setlocale( LC_ALL, $locale . '.UTF-8' );
		Zend_Registry::set( 'locale', $locale );
	}

	/**
	* init translation table for specified locale; so if different users uses different locales -
	* several locale tables would be stored in cache, but only specified for current user will be emitted to js frontend
	* 1. put it into cache;
	* 2. put it into js file for frontend translations
	* @return void
	*/
	public function _initTranslator(){
		/**
		* put translator into cache
		*/
//		$cache = Zend_Registry::get( 'cache' );
//		if ( false == ( $translator = $cache->load( Zend_Registry::get( 'locale' ) ) ) ) {
			$translator = new Core_Model_Translator( Zend_Registry::get( 'locale' ) );
			Zend_Registry::set( 'translator', $translator );
//		    $cache->save( $translator, Zend_Registry::get( 'locale' ) );
//		}
		/**
		* emit translator data to js file for frontend purposes
		*/
		$js_file = APPLICATION_PATH . "/../public/js/translator.js";
//		if( false == is_file( $js_file ) ||
//		 	filemtime( $js_file ) < $translator->getTime() ){
			$handler = fopen( $js_file, "w" );
			$res = fwrite( $handler, "translator = " . json_encode( $translator->getData() ) );
			fclose( $handler );
//		}
	}

	/**
	* read settings and put it into registry
	* remove from Db inactive guests (once per day)
	* @return void
	*/
	public function _initSettings(){
		$settings = new Core_Model_Settings();
		Zend_Registry::set( 'settings', $settings );
		if( time() - $settings->getClearedAt() < 24 * 60 * 60 ) return;

		$time = date( "Y-m-d H:i:s", time() - 24 * 60 * 60 );
		User_Model_User::getDbTable()->removeGuestsInactiveFrom( $time );
		$settings->setClearedAt( time() )->save();
	}
}

