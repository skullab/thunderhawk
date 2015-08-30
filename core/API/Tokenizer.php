<?php

namespace Thunderhawk\API;

final class Tokenizer {
	
	
	public static function randomToken($length = 16){
		$length = $length | 1 ;
		$bytes = openssl_random_pseudo_bytes($length);
		$hash = bin2hex($bytes);
		return $hash ;
	}
	
}