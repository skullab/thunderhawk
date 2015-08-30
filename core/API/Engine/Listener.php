<?php

namespace Thunderhawk\API\Engine;

use Thunderhawk\API\Service;
use Thunderhawk\API\Engine;

class Listener{
	
	private static $_alreadyInit = false ;
	
	private $wrongModule ;
	
	public function __construct(){
		if(!self::$_alreadyInit){
			self::$_alreadyInit = true ;
		}else self::throwException(null,200);
	}
	
	public function boot($event,$engine){
	}
	
	public function beforeStartModule($event,$engine){
	}
	
	public function afterStartModule($event,$engine){
		dump('after start module');
		
		$this->wrongModule = false ;
		
		$router = $engine->getService(Service::ROUTER);
		$moduleName = $router->getModuleName() ;
		$namespace = $router->getNamespaceName();
		$controller = ucfirst($router->getControllerName()).'Controller';
		$className = $namespace.'\\'.$controller ;
		
		$moduleClassName = $engine->getModuleDefinition($moduleName)['className'] ;
		
		if(class_exists($moduleClassName) && !is_subclass_of($moduleClassName,'Thunderhawk\API\Adapters\Module')){
			Engine::throwException($moduleName,550);
		}
		
		if(class_exists($className) && !is_subclass_of($className,'Thunderhawk\API\Mvc\Controller')){
			Engine::throwException($controller,400);
		}
		
		if(!class_exists($moduleClassName)){
			//wrong module call... undefined index
			$this->wrongModule = true ;
		}
	}
	
	public function beforeHandleRequest($event,$engine){
		//echo '<h3>before handle</h3>';
		if($this->wrongModule){
			//echo "wrong module";
			$dispatcher = Service::get(Service::DISPATCHER);
			$dispatcher->forward(array(
					'controller'=> 'index',
					'action' 	=> 'error',
					'params'	=> array('code'=>404,'message'=>'page not found')
			));
		}
	}
	
	public function afterHandleRequest($event,$engine){
		//echo '<h3>after handle</h3>';
		
	}
}