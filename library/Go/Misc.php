<?php
/**
 * © Anton Zelenski 2012
 * zelibobla@gmail.com
 *
 */ 

/**
* set of different useful tools in tribute to procedural coding type
*/
class Go_Misc {

	/**
	* for several purposes readable random string of various length needed
	* @param length – number of symbols in a string
	* @return string
	*/
	public static function generateRandomString( $length ){
		$pool = "abcdefghijkmonpqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWXYZ";
		$len = strlen( $pool );
		$res = "";
		for( $i = 0; $i < $length; $i++ ){
			$res .= substr( $pool, rand( 0, $len ), 1 );
		}
		return $res;
	}

	/**
	* return plural form of defined word (used in table name obtaining)
	* @param word - string of word we would like to decline
	* @return string representing the plural form
	*/
	public function plural( $word ){
		$last_letter = substr( $word, strlen( $word ) - 1 );
		$last_2_letters = substr( $word, strlen( $word ) - 2 );
		if( $last_2_letters == "ss" ||
			$last_2_letters == "us" ){
			return $word . "es";
		} elseif( $last_letter == "s" ){
			return $word;
		} elseif( $last_letter == "y" ){
			return substr( $word, 0, strlen( $word ) - 1 ) . "ies";
		} else {
			return $word . "s";
		}
	}
	
	/**
	* return singular form of defined word
	* @param word - string of word we would like to decline
	* @return string representing the singular form
	*/
	public function singular( $word ){
		$length = strlen( $word );
		$last_letter = substr( $word, $length - 1 );
		$last_2_letters = substr( $word, $length - 2 );
		$last_3_letters = substr( $word, $length - 3 );
		if( $last_3_letters == "ies" ){
			return substr( $word, 0, $length - 3 ) . "y";
		} elseif( $last_2_letters == "es" ){
			return substr( $word, 0, $length - 2 ) . "e";
		} elseif( $last_letter == "s" ){
			return substr( $word, 0, $length - 1 );
		} else {
			return $word;
		}
	}

	/**
	* convert splitted_with_underscores to camelCaseString
	* @param str - underscored string
	* @return string in camelCase
	*/
	public static function underscoreToCamel( $str ){
		$words = explode( "_", $str );
		if( false == is_array( $words ) ) return $str;
		
		$word = reset( $words );
		$res = "";
		do{
			$res .= $word;
			$word = ucfirst( next( $words ) );
		} while( false === $word );
		$res .= $word;
		return $res;
	}

	/**
	* create image resource from image of specified extension
	* @param filename – string full filename with a path
	* @return image resource or null if no file found or illegal extension
	*/
	public static function imageCreateFrom( $filename ){
		$extension = substr( $filename, -3 );
		if( 'jpg' == $extension ){
			return imagecreatefromjpeg( $filename );
		} elseif( 'gif' == $extension ){
			return imagecreatefromgif( $filename );
		} elseif( 'png' == $extension ){
			return imagecreatefrompng( $filename );
		} else {
			return null;
		}
	}
}
