<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */
 
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	/**
	* define globals
	*/
	protected function _initGlobals(){

		// salt part for good user passwords protection (another part is in DB)
		Zend_Registry::set( 'static_salt', 'who_is_mr_golt' );

		// DB tables prefixes
		$resources = $this->getOption( 'resources' );
		Zend_Registry::set( 'prefix', @$resources[ 'db' ][ 'params' ][ 'prefix' ] );

		// user (if logged in )
		$this->bootstrap( 'db' );
		$this->bootstrap( 'modules' );

		$auth = Zend_Auth::getInstance();

    if( $auth->hasIdentity() &&
		true == ( $user = Go_Factory::getDbTable( 'User_Model_User' )->fetchByEmail( $auth->getIdentity() ) ) &&
		true == $user->getId() ){
		
			Zend_Registry::set( 'user', $user );
		// if session is exists but we couldn't retrieve user from DB:
		} else {

			$auth->clearIdentity();
		
		}
		
    	// if not logged, generate temporary user and login him automatically
		if( false == $auth->hasIdentity() ) {
			$password = Core_Plugin_Misc::generateRandomString( 6 );
			$email = Core_Plugin_Misc::generateRandomString( 9 );
			$user = new User_Model_User( array(
				'email'			=> $email,
				'name'			=> 'Инкогнито',
				'role'			=> 'guest' ) );
			$id = $user->generateRandomSalt()
					   ->generatePasswordHash( $password )
					   ->put();
      		$user->setId( $id );
			$adapter = Core_Plugin_Misc::getAuthAdapter()->setIdentity( $email )
                                                   		 ->setCredential( $password );
			$result = $auth->authenticate( $adapter );
			if( false == $result->isValid() ){
				throw new Exception( 'Unable to create and authenticate temporary user' );
			}
			Zend_Registry::set( 'user', $user );
			Core_Plugin_Voice::pleaseRegister();
		}
		$user->setDateLastActivity( 'now()' )
			 ->put();
	}

	/**
	* generate access rights table and put it in registry
	*/
	protected function _initAcl(){

		if( Zend_Auth::getInstance()->hasIdentity() ){
		   Zend_Registry::set( 'acl', Core_Plugin_Acl::getAcl() );
		}
	}

	/**
	* connecting XML structure of general menu
	*/
	protected function _initNavigation(){

		$menu_config = new Zend_Config_Xml( APPLICATION_PATH . '/configs/navigation.xml', 'nav' );

		$this->bootstrap( 'view' );

		if( Zend_Auth::getInstance()->hasIdentity() ){
		   $acl = Core_Plugin_Acl::getAcl();
		   $role = Zend_Registry::get( 'user' )->getRole();
		} else {
			$acl = null;
			$role = null;
		}

		$this->view->navigation()->setAcl( $acl )
								 ->setRole( $role )
								 ->setContainer( new Zend_Navigation( $menu_config ) );

	}
	
	/**
	* connecting routes
	*/
	protected function _initRoutes(){

		$front = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();
 
		$config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/routes.ini', 'production' );      
		$router->addConfig( $config,'routes' );
	}

	/**
	* our default locale is Russian
	*/
	protected function _initLocale(){
		setlocale( LC_ALL, 'ru_RU.UTF-8' );
	}

}

