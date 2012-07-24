<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

class Forum_IndexController extends Go_Controller_Action{

    public function indexAction(){
        $this->view->categories = Go_Factory::reference( 'Forum_Model_Category' );

    }
}
