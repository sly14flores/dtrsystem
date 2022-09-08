<?php

class endec {

var $key;
var $en;
var $dec;

	function __construct() {

		$this->key = 'qJB5rGtIn5UB1xG010efyCp';
		// $this->key = 'qJB0rGtIn5UB1xG03efyCp';
		
	}

	function en($en) {
		
		$this->en = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $this->key ), $en, MCRYPT_MODE_CBC, md5( md5( $this->key ) ) ) );
		return $this->en;
		
	}

	function dec($dec) {
		
		$this->dec = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $this->key ), base64_decode( $dec ), MCRYPT_MODE_CBC, md5( md5( $this->key ) ) ), "\0");
		return $this->dec;
		
	}
	
	
}


?>