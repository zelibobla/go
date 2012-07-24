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
		$this->setIsPinned( 'N' )
			  ->setClass( self::NEUTRAL_CLASS );
		
		parent::__construct( $params );
	}

	public function getSubject(){
		return $this->subject;
	}
	public function setSubject( $subject ){
		$this->subject = ( string ) $subject;
		return $this;
	}
	public function getBody(){
		return $this->body;
	}
	public function setBody( $body ){
		$this->body = ( string ) $body;
		return $this;
	}
	public function getClass(){
		return $this->class;
	}
	public function setClass( $class ){
		if( $class != self::ERROR_CLASS &&
			 $class != self::WARNING_CLASS &&
			 $class != self::NEUTRAL_CLASS &&
			 $class != self::SUCCESS_CLASS ){
		
			throw new Exception( "Invalid class of notification provided: $class" );	 
		}
		$this->class = $class;
		return $this;
	}
	public function getIsPinned(){
		return $this->is_pinned;
	}
	public function setIsPinned( $is_pinned ){
		$this->is_pinned = strtoupper( substr( $is_pinned, 0, 1 ) );
		return $this;
	}

}

