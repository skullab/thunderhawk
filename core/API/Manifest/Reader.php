<?php
namespace Thunderhawk\API\Manifest;
use Thunderhawk\API\Interfaces\Throwable;

class Reader implements Throwable{
	
	public static function load($filename){
		if(file_exists($filename)){
			return simplexml_load_file($filename,'Thunderhawk\API\Manifest');
		}else self::throwException(null,100);
	}
	
	public static function throwException($message = null, $code = 0, Exception $previous = null) {
		throw new Exception(null,100);
	}

}