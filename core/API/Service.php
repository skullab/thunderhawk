<?php
namespace Thunderhawk\API;
use Thunderhawk\API\Adapters\Enum;
use Thunderhawk\API\Interfaces\Throwable;
use Thunderhawk\API\Engine;
use Thunderhawk\API\Service\Exception;

class Service extends Enum implements Throwable{
	
	const LOADER				= 'loader' ;
	const VIEW					= 'view' ;
	const URL					= 'url' ;
	const ROUTER				= 'router' ;
	const DISPATCHER			= 'dispatcher' ;
	const REQUEST				= 'request' ;
	const RESPONSE				= 'response';
	const ASSETS				= 'assets' ;
	const VOLT					= 'volt' ;
	const SESSION				= 'session' ;
	const COOKIES				= 'cookies';
	const FILTER				= 'filter';
	const FLASH					= 'flash';
	const FLASH_SESSION 		= 'flashSession';
	const EVENTS_MANAGER 		= 'eventsManager';
	const DB					= 'db';
	const SECURITY				= 'security';
	const CRYPT					= 'crypt';
	const TAG					= 'tag';
	const ESCAPER				= 'escaper';
	const ANNOTATIONS			= 'annotations';
	const MODELS_MANAGER		= 'modelsManager';
	const MODELS_METADATA		= 'modelsMetadata';
	const TRANSACTION_MANAGER	= 'transactionManager';
	const MODELS_CACHE			= 'modelsCache';
	const VIEWS_CACHE			= 'viewsCache';
	
	const GROUP_BASE			= 'groupBase';
	
	const THEME_NAME			= 'themeName' ;
	
	public $service ;
	
	public function __construct($serviceName){
		if(self::isEnumValue($serviceName)){
			$this->service = $this->$serviceName = Engine::getInstance()->getService($serviceName);
		}else self::throwException($serviceName,200);
	}
	
	public static function get($serviceName){
		try{
			return Engine::getInstance()->getService($serviceName);
		}catch (\Phalcon\DI\Exception $e){
			self::throwException($serviceName,200);
		}
	}
	
	public static function isService($value){
		return self::isEnumValue($value) || Engine::getInstance()->getDI()->has($value) ;
	}
	
	public static function throwException($message = null, $code = 0, Exception $previous = null) {
		throw new Exception($message,$code,$previous);
	}

}