<?php

namespace Thunderhawk\API\Permission;

use Thunderhawk\API\Interfaces\Throwable;
use Thunderhawk\API\Permission;
use Thunderhawk\API\Engine;

final class Manager implements Throwable{
	
	private static $_modules = array();
	private static $_groups = array();
	private function __construct(){}
	
	public static function addPermission($moduleName,Permission $permission){
		if(!array_key_exists($moduleName, self::$_modules))self::$_modules[$moduleName] = array();
		array_push(self::$_modules[$moduleName], $permission);
	}
	
	public static function defineGroup($groupName, Group $group){
		if(!is_string($groupName)){self::throwException(null,100);}
		if(array_key_exists($groupName, self::$_groups)){self::throwException($groupName,110);}
		self::$_groups[$groupName] = $group ;
	}
	
	public static function getGroup($groupName){
		return array_key_exists($groupName, self::$_groups) ? self::$_groups[$groupName] : null ;	
	}
	
	public static function getPermissions($moduleName){
		return array_key_exists($moduleName,self::$_modules) ? self::$_modules[$moduleName] : [] ;
	}
	
	public static function checkPermission($moduleName,$serviceName){
		$registeredPermissions = self::getPermissions($moduleName);
		$declaredPermissions = Engine::getInstance()->getModuleDefinition($moduleName)['permissions'] ;
		foreach ($declaredPermissions as $declaredPermission){
			foreach ($registeredPermissions as $registerdPermission){
				if(	$declaredPermission == $registerdPermission && 
					$declaredPermission->getServiceName() === $serviceName){
					return true ;
				}
			}
		}
		return false ;
	}

	public static function throwException($message = null, $code = 0, Exception $previous = null) {
		throw new Exception($message,$code,$previous);
	}

}