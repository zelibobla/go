<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */

/**
* class to handle application admin settings
*/
class Core_Model_Settings {

	protected $cleared_at;
	protected $filename;

	/**
	* Read file with settings if exists, JSON decode it and put result to self vars
	* @return $this
	*/
	public function __construct(){
		$this->filename = APPLICATION_PATH . '/configs/settings.json';

		if( false == ( $content = @file_get_contents( $this->filename ) ) ||
			false == ( $options = json_decode( $content, true ) ) ) return;
		unset( $options[ 'filename' ] );
		$this->setOptions( $options );
		return $this;
	}

	/**
	* JSON encode settings and put it into file
    * @return $this
	*/
	public function save(){
		$content = str_replace( ",\"", ",\n\"", json_encode( $this->getOptions() ) );
		file_put_contents( $this->filename, $content );
		return $this;
	}


    /**
    * Setters and getters routine decreasing
    *
    * @param string $method
    * @param array $args
    * @return void
    * @throws Exception If an invalid method is called.
    */
    public function __call( $method, array $args ){

        $matches = array();

        /**
         * recognize setter
         */
        if( preg_match( '/^set/', $method ) &&
			preg_match_all( '/([A-Z][a-z]*)/', $method, $matches ) ) {
            $property = strtolower( implode( '_', $matches[ 1 ] ) );
			$this->$property = $args[ 0 ];
            return $this;
        }

        /**
         * recognize getter
         */
        if( preg_match( '/^get/', $method ) &&
			preg_match_all( '/([A-Z][a-z]*)/', $method, $matches ) ) {
            $property = strtolower( implode( '_', $matches[ 1 ] ) );
            return $this->$property;
        }
		
		throw new Exception( "Not fount related property: $method" );
    }

	/**
	* set self fields from input array
	* @param $options - option => value array
	* @return $this
	*/
	public function setOptions( array $options ) {

		$methods = get_class_methods( $this );
		foreach( $options as $key => $value ) {
			if( !property_exists( get_called_class(), $key ) ) continue;
			if( is_string( $value ) ){
				$attempt = @unserialize( $value );
				if( is_array( $attempt ) ||
					 is_object( $attempt ) ){
					$value = $attempt;	 
				}
			}

			$key_parts = explode( "_", $key );
			foreach( $key_parts as $index => $val ){
				$key_parts[ $index ] = ucfirst( $val );
			}
			$method = 'set' . implode( $key_parts );
			$this->$method( $value );
		}
		return $this;
	}

	/**
	* return self fields values in associative array (useful for DB mapping)
	* @return array of self fields
	*/
	public function getOptions(){
		$vars = get_object_vars( $this );
		$result = array();
		foreach( $vars as $key => $value ) {
			if( substr( $key, 0, 1 ) == "_" ) continue;
			$key_parts = explode( "_", $key );
			foreach( $key_parts as $index => $val ){
				$key_parts[ $index ] = ucfirst( $val );
			}
			$method = 'get' . implode( $key_parts );
			
			$value = $this->$method();
			if( is_array( $value ) ||
				 is_object( $value ) ){
				 $value = serialize( $value );
			}
			$result[ $key ] = $value;
		}
		return $result;
	}
}

