<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Plugin_Mail extends Zend_Controller_Plugin_Abstract {

	/**
	* send message on specified email or emails array
	* @param $to – instance of User_Model_User or array of User_Model_User objects
	* @param $subject – string of mail subject
	* @param $body - string of mail body
	* @return void
	*/
	public static function mail( $to, $subject,	$body ){

		$to = 1 == count( $to ) ? array( $to ) : $to;
		$defaults = Zend_Registry::get( 'defaults' );
		$t = Zend_Registry::get( 'translator' );
		
		foreach( $to as $user ){
			if( false == $user instanceof User_Model_User ||
				false == ( $email = $user->getEmail() ) ) continue;

			$mail = new Zend_Mail( 'UTF-8' );
			$mail->setHeaderEncoding( Zend_Mime::ENCODING_BASE64 )
				 ->setFrom( $defaults[ 'admin_email' ], sprintf( $t->_( 'mail_from' ), $defaults[ 'application_name' ] ) )
				 ->addTo( $user->getEmail(), $user->__toString() )
				 ->setSubject( $subject )
				 ->setBodyText( sprintf( $t->_( 'mail_body' ), $user, $body, $defaults[ 'application_name' ] ) );
		}
		return $mail->send();
	}
}
?>
