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
		$current_year = false,
		$with_time = false
	) {
		$time = strtotime( $date );
		if( date( "Y", $time ) == date( "Y", time() ) &&
		 	false == $current_year )
			$pattern = "%e %b";
		else
			$pattern = "%e %b %Y";
		if( $with_time )
			$pattern .= " %H:%M";

		return "<nobr>" . strftime( $pattern, $time ) . "</nobr>";
	}
}
