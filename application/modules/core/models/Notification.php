<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Model_Notification extends Core_Model_Entity{

	protected $subject;
	protected $body;
	protected $class;
	protected $is_pinned;
	
	const ERROR_CLASS = 'error';
	const WARNING_CLASS = 'warning';
	const NEUTRAL_CLASS = 'neutral';
	const SUCCESS_CLASS = 'success';

	public function __construct( array $params = null ){
		$this->setIsPinned( 0 )
			 ->setClass( self::NEUTRAL_CLASS );
		
		parent::__construct( $params );
	}
}

