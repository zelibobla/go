<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Zend_View_Helper_HtmlLink extends Zend_View_Helper_Abstract {

	public $view;

	public function htmlLink(
		Go_Interface_Linkable $item,
		$html = false
	) {
		if( false == $item->isLinkable() ){
			return $item->__toString();
		} else {
			$html = false == $html ? $item->__toString() : $html;
			return '<a href="' . $this->view->url( array( 'id' => $item->getId() ), $item->getProfileRouteName() ) . '">' . $html . '</a>';
		}
	}
}
