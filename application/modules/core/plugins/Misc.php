<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */
 
class Core_Plugin_Misc extends Zend_Controller_Plugin_Abstract {
	
	public static function getAuthAdapter() {
		
		$db_adapter = Zend_Db_Table::getDefaultAdapter();
		$auth_adapter = new Zend_Auth_Adapter_DbTable(
			$db_adapter,
			Go_Factory::getDbTable( 'User_Model_User' )->info( 'name' ),
			'email',
			'password_hash',
			"MD5( '" . Zend_Registry::get( 'static_salt' ) . "' || ? || password_salt )" );
		return $auth_adapter;
	}
	
	/**
	* returns randomly generated string of specified length
	*
	*/
	public static function generateRandomString( $length ){
		$pool = "abcdefghijkmonpqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWXYZ";
		$len = strlen( $pool );
		$res = "";
		for( $i = 0; $i < $length; $i++ ){
			$res .= substr( $pool, rand( 0, $len ), 1 );
		}
		return $res;
	}

}
?>
