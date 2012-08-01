<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_BulletinController extends Go_Controller_Item{

  public function init(){
		$this->_resource = "bulletin";
		parent::init();
	}

	public function afterSuccessEdit(){
    
    if( 'guest' == $this->_user->getRole() ){
        $this->_user->setName( $this->_form->getValue( 'name' ) )
                    ->setEmail( $this->_form->getValue( 'email' ) )
                    ->setLogin( $this->_form->getValue( 'email' ) )
                    ->setRole( 'user' )
                    ->save();
        $this->_user->savePhones( $this->_form->getValue( 'phone' ) );

        $password = Core_Plugin_Misc::generateRandomString( 6 );
        $this->_user->generateRandomSalt()
                    ->generatePasswordHash( $password )
                    ->save();

        $auth = Zend_Auth::getInstance();
        $adapter = Core_Plugin_Misc::getAuthAdapter()->setIdentity( $this->_user->getLogin() )
                                                     ->setCredential( $password );
        $result = $auth->authenticate( $adapter );
        if( false == $result->isValid() ){
            throw new Exception( 'Unable to create and authenticate temporary user' );
        }
        Zend_Registry::set( 'user', $this->_user );
        User_Plugin_Mail::forceRegister( $this->_user, $password );
        Core_Plugin_Voice::unpin( array( 'owner_id'	=> $this->_user->getId(),
                                         'subject'	=> 'please_register' ) );
    }

		$this->_item->setOwnerId( $this->_user->getId() )
                ->setCategoryId( Forum_Model_Bulletin::FORUM_CATEGORY_ID )
                ->save();

    Forum_Plugin_Voice::bulletinEdited( $this->_item, !$this->_item->getId() );
    return $this->_helper->json( array( 'result' => true ) );
	}

  public function afterSuccessDelete(){

      Forum_Plugin_Voice::bulletinDeleted( $this->_item );
      return $this->_helper->json( array( 'result' => true ) );
  }
}
