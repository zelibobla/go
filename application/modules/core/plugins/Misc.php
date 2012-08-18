<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */
 
class Core_Plugin_Misc extends Zend_Controller_Plugin_Abstract {
	
	/**
	* define Zend_Auth_Adapter with custom password cryptography method
	* @return Zend_Auth_Adapter_DbTable
	*/
	public static function getAuthAdapter() {
		
		$db_adapter = Zend_Db_Table::getDefaultAdapter();
		$auth_adapter = new Zend_Auth_Adapter_DbTable(
			$db_adapter,
			User_Model_User::getDbTable()->info( 'name' ),
			'email',
			'password_hash',
			"MD5( CONCAT( '" . Zend_Registry::get( 'static_salt' ) . "' , ? , password_salt ) )" );
		return $auth_adapter;
	}
	
	/**
	* retrieve from DB data to generate users access rights objects to use it all over the application
	* !warning: roles are hardcoded here
	* @return Zend_Acl object with defined permissions table
	*/
	public static function getAcl(){

		$acl = new Zend_Acl();
		$acl->addRole( new Zend_Acl_Role( 'guest' ) )
			->addRole( new Zend_Acl_Role( 'user' ) )
			->addRole( new Zend_Acl_Role( 'admin' ), 'user' );

		$resources = User_Model_Permission::getDbTable()->referenceResources();
		foreach( $resources as $resource ){
			$acl->add( new Zend_Acl_Resource( $resource ) );
		}

		if( true == ( $permissions = User_Model_Permission::getDbTable()->get() ) ){
			foreach( $permissions as $permission ){
				$acl->allow( $permission->getRole(), $permission->getResource(), $permission->getPrivilege() );
			}
		}
		return $acl;
	}

}
?>
