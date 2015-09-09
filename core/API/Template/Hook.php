<?php

namespace Thunderhawk\API\Template;

class Hook {
	private static $_content = array() ;
	
	public static function inflate($tag,$content){
		if(!array_key_exists($tag, self::$_content))self::$_content[$tag] = '' ;
		self::$_content[$tag] .= $content ;	
	}
	
	public static function get($tag){
		if(!array_key_exists($tag, self::$_content))return '' ;
		return self::$_content[$tag];
	}
	
	public function __call($tag,$args){
		if(array_key_exists($tag, self::$_content))return self::$_content[$tag];
	}
}