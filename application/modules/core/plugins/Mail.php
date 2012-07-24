<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Plugin_Mail extends Zend_Controller_Plugin_Abstract {

	/**
	*	sent message on specified email or emails array
	*
	*/
	public static function mail(
		$to,					// email or emails array
		$subject,				// message subject
		$body					// message body
	){

		if( 'User_Model_User' == get_class( $to ) ){
			$to = array( $to );
		}
		
		foreach( $to as $user ){
			if( 'User_Model_User' != get_class( $user ) ||
				 false == ( $email = $user->getEmail() ) ) continue;

			$mail = new Zend_Mail( 'UTF-8' );
			$mail->setHeaderEncoding( Zend_Mime::ENCODING_BASE64 )
				 ->setFrom( 'ilya.zomba@gmail.com', 'Электронный секретарь Ильи Зомбы' )
				 ->addTo( $user->getEmail(), $user->__toString() )
				 ->setSubject( $subject )
				 ->setBodyText( "Привет, $user!\r\n\r\n$body\r\n\r\nЭлектронный секретарь Ильи Зомбы" )
				 ->send();

		}
		return true;
	}

}
?>
