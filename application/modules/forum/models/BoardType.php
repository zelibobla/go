<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Forum_Model_BoardType extends Core_Model_Item{

	protected $id;

	public function getId() {
		return $this->id;
	}
	public function setId( $value ) {
		$this->id = ( int ) $value;
		return $this;
	}

}
