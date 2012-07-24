<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Zend_View_Helper_Date extends Zend_View_Helper_Abstract {

	public $view;

	public function date(
		$date,
		$current_year = false
	) {
		$time = strtotime( $date );
		if( date( "Y", $time ) == date( "Y", time() ) &&
		 	true == $current_year ){
			return "<nobr>" . strftime("%e %b", $time ) . "</nobr>";
		} else {
			return "<nobr>" . strftime("%e %b %Y", $time ) . "</nobr>";
		}
	}
}
