<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

	/**
	* Lets retrieve from DB data to generate employers access rights objects to use it all over the application
	*/
	public static function getAcl(){

		$acl = new Zend_Acl();

		$roles = Go_Factory::getDbTable( 'Core_Model_Permissions' )->referenceRoles(); //actually there is no class Core_Model_Position
																			  		   //but we're satisfied to substitute is by Core_Model_Item

		foreach( $roles as $role ){
			$acl->addRole( new Zend_Acl_Role( $role ) );
		}

		$resources = Go_Factory::getDbTable( 'Core_Model_Permissions' )->referenceResources();	//same as upper
		foreach( $resources as $resource ){
			$acl->add( new Zend_Acl_Resource( $resource ) );
		}

		if( true == ( $permissions = Go_Factory::reference( 'User_Model_Permission' ) ) ){
		
			foreach( $permissions as $permission ){
				$acl->allow( $permission[ 'role' ], $permission[ 'resource' ], $permission[ 'privilege' ] );
			}
		}
		return $acl;
	}

}
?>
