<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Zend_View_Helper_Icon extends Zend_View_Helper_Abstract {

	public $view;

	/**
	* return html code of icon
	* @param $item – object with Go_Interface_Iconable interface
	* @return string html code of icon picture
	*/
	public function icon( Go_Interface_Iconable $item ) {
		return '<img src="' . $item->getIconWebPath() . '" alt="' . $item->__toString() . '" class="icon">';
	}
}
