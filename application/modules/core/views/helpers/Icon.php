<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Zend_View_Helper_Icon extends Zend_View_Helper_Abstract {

	public $view;

	public function icon(
		Go_Interface_Iconable $item
	) {
		return '<img src="' . $item->getIconPublicPath() . '" alt="' . $item->__toString() . '" class="item_icon">';
	}
}
