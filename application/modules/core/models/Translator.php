<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

class Core_Model_Translator {

	protected $_locale;
	protected $_data;
	protected $_time;
	protected $_filename;
	protected $_is_updated;

	public function __construct( $locale ){
		$this->_locale = $locale;
		$this->_filename = APPLICATION_PATH . "/configs/$locale.locale.php";
		$this->setData();
		$this->_is_updated = false;
	}

	/**
	* return translation due to specified key or key itself if no related translation found
	* most short signature chosen to prejustice of meaning in cause of very often usability
	* @param key – key of the value to be translated
	* @return string
	*/
	public function _( $key ){
		if( array_key_exists( $key, $this->_data ) ){
			
			return $this->_data[ $key ];
			
		} elseif( true == $this->update() &&
				  array_key_exists( $key, $this->_data ) ){
					
			return $this->_data[ $key ];
				
		} else {
			
			return $key;
		}
	}

	/**
	* check if real data file is newer than stored in a _data field; if so - refresh _data and return true, otherwise - false
	* @return boolean
	*/
	private function update(){
		if( false == is_file( $this->_filename ) ){
			return false;
		} else {
			$time = filemtime( $this->_filename );
			if( $time > $this->_time ){
				$this->setData();
				$this->_is_updated = true;
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	* include data from file or handle any errors
	* @return this
	*/
	private function setData(){
		if( false == is_file( $this->_filename ) ){
			$this->_data = array();
			$this->_time = time();
		} else {
			include_once( $this->_filename );
			$this->_data = isset( $translation_table ) && is_array( $translation_table )
						 ? $translation_table
						 : array();
			$this->_time = filemtime( $this->_filename );
		}
		return $this;
	}

	/**
	* getters/setters routine
	*/
	public function setIsUpdated( $value ){
		$this->_is_updated = ( bool ) $value;
	}
	public function getIsUpdated(){
		return $this->_is_updated;
	}
	public function getTime(){
		return $this->_time;
	}
	public function getData(){
		return $this->_data;
	}
}

