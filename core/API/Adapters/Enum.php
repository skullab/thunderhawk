<?php

namespace Thunderhawk\API\Adapters;

abstract class Enum {
	
	private static $cache = null ;
	
	
	protected static function getEnum() {
		if(self::$cache == null)self::$cache = array();
		$class = get_called_class();
		if(!array_key_exists($class, self::$cache)){
			$reflect = new \ReflectionClass($class);
			self::$cache[$class] = $reflect->getConstants();
		}
		return self::$cache[$class];
	}
	
	public static function isEnumValue($value){
		$values = array_values(self::getEnum());
		return in_array($value, $values,true);
	}
	
}