<?php

namespace Thunderhawk\API;
use Thunderhawk\API\Debug\Log;

class DI extends \Phalcon\DI\FactoryDefault {
	
	private $router;
	private $_init = false;
	public function __construct() {
		parent::__construct ();
	}
	
	public function get($name, $parameters = []) {
		if ($this->router == null) {
			//$this->router = $this->getShared ( 'router' );
			$this->router = parent::get ( 'router' );
			$this->_init = true;
		}
		if($this->_init){
			$this->checkPermission($name);
		}
		return parent::get ( $name, $parameters );
	}
	
	public function getShared($name, $parameters = null) {
		if ($this->_init) {
			$this->checkPermission($name);
		}
		return parent::getShared ( $name, $parameters );
	}
	
	private function checkPermission($name){
		
		$moduleName = $this->router->getModuleName ();
		if(trim($moduleName) == false)return;
		
		$permission = Permission\Manager::checkPermission ( $moduleName, $name );
		$className = $this->router->getNamespaceName ();
			
		dump ( 'module ' . $moduleName . ' in namespace '.$className.' wanna get  ' . $name );
		
		if (	Service::isService($name) && 
				Engine::getInstance()->isRegisteredModule($moduleName) &&
				!$permission){
			//dump ( 'doesn\'t have permission for ' . $name . ' ' . $moduleName . ' ' . ( int ) $permission );
			Engine::throwException($name,300);
		}
		
		return true ;
	}
}