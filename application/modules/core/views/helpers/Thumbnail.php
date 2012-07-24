<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Zend_View_Helper_Thumbnail extends Zend_View_Helper_Abstract {

	public $view;

	public function thumbnail(
		Go_Interface_Iconable $item
	) {
		return '<img src="' . $item->getIconPublicPath() . '" alt="' . $item->__toString() . '" class="item_thumbnail">';
	}
}
