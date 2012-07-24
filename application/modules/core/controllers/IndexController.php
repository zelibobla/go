<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class IndexController extends Go_Controller_Action {

	public function indexAction() {
		return $this->_redirector->gotoRoute( array(), "home" );
	}

}

class Core_IndexController extends Go_Controller_Action {

	public function indexAction() {
		if( 'guest' == $this->_user->getRole() ){
			return $this->_redirector->gotoRoute( array(), 'login' );
		}
		$form = new Offer_Form_Offer();
		$form->addElement( 'submit', 'submit', array( 'ignore' => true,	'label'	=> 'Создать' ) );
		
		/**
		* if we here with post data - it mean in /offer/index/edit we got invalid form
		* revalidate if here only to get highlighted errors
		*/
		if( $this->_request->isPost() ){
			$form->isValid( $this->_request->getParams() );
		}
		$this->view->offer_form = $form;
	}
}

